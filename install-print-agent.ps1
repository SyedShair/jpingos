# =====================================================================
#  Madras Cafe print agent - ONE-STEP INSTALLER
#  Run this ONCE on the RESTAURANT PC (the PC the Star printer is attached to):
#
#     powershell -ExecutionPolicy Bypass -File install-print-agent.ps1
#
#  It will: create C:\print-agent, write the agent script, start it,
#  and make it start automatically every time Windows logs in.
# =====================================================================

$Site  = 'https://newmadrascafe.com'
$Token = '36f9c4dfb21c3dec0f38292e5d7c1c737ce4e590d2a85c01d951bb26b7007b8b'
$Share = 'StarTSP'          # Windows share name of the printer
$Dir   = 'C:\print-agent'

# --- needs administrator rights: relaunch elevated if necessary ---
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Start-Process powershell.exe -Verb RunAs -ArgumentList "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`""
    exit
}

$ErrorActionPreference = 'Stop'
$Script = Join-Path $Dir 'print-agent.ps1'

try {
    Write-Host "1/5  Creating $Dir ..." -ForegroundColor Cyan
    New-Item -ItemType Directory -Force -Path $Dir | Out-Null

    Write-Host "2/5  Writing the agent script ..." -ForegroundColor Cyan
    $agent = @'
# =====================================================================
#  Madras Cafe print agent  -  run this on the RESTAURANT PC
#  (the PC the Star printer is paired to). Needs nothing installed:
#  Windows PowerShell 5.1 is already part of Windows.
#
#  It asks the live website every few seconds "any receipts to print?"
#  and sends each one to the Star printer.
# =====================================================================

# ---- EDIT THESE THREE LINES --------------------------------------
$Server  = "__SERVER__"
$Token   = "__TOKEN__"
$Printer = "\\$env:COMPUTERNAME\__SHARE__"
# ------------------------------------------------------------------

$IntervalSeconds = 4

# Older Windows PowerShell may need this for HTTPS
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$headers   = @{ "X-Print-Token" = $Token; "Accept" = "application/json" }
$userAgent = "MadrasCafePrintAgent/1.0"

# Show messages and also keep them in print-agent.log (capped at 1 MB)
$LogFile = Join-Path $PSScriptRoot "print-agent.log"
function Say($m) {
    Write-Host $m
    try {
        if ((Test-Path $LogFile) -and (Get-Item $LogFile).Length -gt 1MB) { Remove-Item $LogFile -Force }
        Add-Content -Path $LogFile -Value ("{0}  {1}" -f (Get-Date -Format "yyyy-MM-dd"), $m)
    } catch {}
}

Write-Host "Print agent started."
Write-Host "Checking $Server every $IntervalSeconds seconds. Close this window to stop."
Write-Host ""

while ($true) {
    $printedSomething = $false

    try {
        $res = Invoke-WebRequest -Uri "$Server/print-agent/next" -Headers $headers `
                                 -UserAgent $userAgent -UseBasicParsing -TimeoutSec 20

        # 200 = a receipt is waiting; 204 = nothing to print
        if ($res.StatusCode -eq 200) {
            $job   = $res.Content | ConvertFrom-Json
            $bytes = [Convert]::FromBase64String($job.data)
            $tmp   = Join-Path $env:TEMP ("receipt_{0}.bin" -f $job.id)
            [IO.File]::WriteAllBytes($tmp, $bytes)

            # Send the raw ESC/POS bytes to the shared printer
            cmd /c copy /b "$tmp" "$Printer" | Out-Null
            $ok = ($LASTEXITCODE -eq 0)
            Remove-Item $tmp -ErrorAction SilentlyContinue

            $report = @{ ok = $ok; error = $(if ($ok) { "" } else { "copy to printer failed" }) } | ConvertTo-Json
            Invoke-RestMethod -Uri "$Server/print-agent/$($job.id)/done" -Method Post `
                              -Headers $headers -UserAgent $userAgent -ContentType "application/json" -Body $report | Out-Null

            Say ("{0}  order {1}  {2}" -f (Get-Date -Format "HH:mm:ss"), $job.order_number, $(if ($ok) { "printed" } else { "FAILED (is the printer on?)" }))
            $printedSomething = $ok      # after a failure, wait before retrying
        }
    }
    catch {
        Say ("{0}  {1}" -f (Get-Date -Format "HH:mm:ss"), $_.Exception.Message)
    }

    # If we just printed one, check again straight away for the next
    if (-not $printedSomething) {
        Start-Sleep -Seconds $IntervalSeconds
    }
}
'@
    $agent = $agent.Replace('__SERVER__', $Site).Replace('__TOKEN__', $Token).Replace('__SHARE__', $Share)
    Set-Content -Path $Script -Value $agent -Encoding UTF8

    Write-Host "3/5  Checking the printer share and the website ..." -ForegroundColor Cyan
    $printer = $null
    try { $printer = Get-Printer | Where-Object { $_.Shared -and $_.ShareName -eq $Share } } catch {}
    if ($printer) { Write-Host "     Printer share '$Share' found." -ForegroundColor Green }
    else { Write-Warning "Printer share '$Share' not found. In Windows: Printers & scanners > Star TSP > Printer properties > Sharing, tick 'Share this printer' and name it $Share." }

    # Probe with a deliberately WRONG token: it never takes a receipt off the queue.
    # 401 = the site and routes are fine.  404 = the print-agent routes are not registered.
    $code = try {
        (Invoke-WebRequest -Uri "$Site/print-agent/next" -Headers @{ 'X-Print-Token' = 'probe' } -UseBasicParsing -TimeoutSec 20).StatusCode
    } catch {
        if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
    }
    switch ($code) {
        401     { Write-Host "     Website reachable, print-agent route found." -ForegroundColor Green }
        404     { Write-Warning "Website answered 404 - the /print-agent routes are not registered on the live site yet (routes/web.php, then php artisan route:clear)." }
        0       { Write-Warning "Could not reach $Site - check this PC's internet connection." }
        default { Write-Warning "Website answered HTTP $code (expected 401 for the probe)." }
    }

    Write-Host "4/5  Setting it to start automatically at every logon ..." -ForegroundColor Cyan
    Get-ScheduledTask -TaskName 'PrintAgent' -ErrorAction SilentlyContinue | Stop-ScheduledTask -ErrorAction SilentlyContinue
    Get-CimInstance Win32_Process -Filter "Name='powershell.exe'" |
        Where-Object { $_.CommandLine -like '*\print-agent\print-agent.ps1*' -and $_.ProcessId -ne $PID } |
        ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }

    $action    = New-ScheduledTaskAction -Execute 'powershell.exe' -Argument "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$Script`""
    $trigger   = New-ScheduledTaskTrigger -AtLogOn -User "$env:USERDOMAIN\$env:USERNAME"
    $principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest
    $settings  = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable `
                    -RestartCount 999 -RestartInterval (New-TimeSpan -Minutes 1) -ExecutionTimeLimit ([TimeSpan]::Zero)
    Register-ScheduledTask -TaskName 'PrintAgent' -Action $action -Trigger $trigger -Principal $principal -Settings $settings -Force | Out-Null

    Write-Host "5/5  Starting the agent ..." -ForegroundColor Cyan
    Start-ScheduledTask -TaskName 'PrintAgent'
    Start-Sleep -Seconds 8

    $log = Join-Path $Dir 'print-agent.log'
    Write-Host ""
    if (Test-Path $log) {
        Write-Host "Agent log (errors appear here):" -ForegroundColor Yellow
        Get-Content $log -Tail 5
    } else {
        Write-Host "Agent is running with no errors." -ForegroundColor Green
    }
    Write-Host ""
    Write-Host "DONE. Press 'Print Receipt' on the website - the receipt prints within a few seconds." -ForegroundColor Green
    Write-Host "Log file: $log"
    Write-Host "Stop:  schtasks /end /tn PrintAgent        Remove:  schtasks /delete /tn PrintAgent /f"
}
catch {
    Write-Host ""
    Write-Host "INSTALL FAILED: $_" -ForegroundColor Red
}

Write-Host ""
Read-Host "Press Enter to close"
