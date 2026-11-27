=====================================================================
PRINT FROM THE LIVE SITE  -  setup checklist
=====================================================================

How it works
  Live site (Linux)  -> builds the receipt, saves a "print job"
  Restaurant PC      -> print-agent.ps1 asks the live site every 4 s,
                        downloads the receipt, sends it to the Star printer

---------------------------------------------------------------------
A) LIVE SERVER  (SSH, in public_html)
---------------------------------------------------------------------
1. Upload / replace these files:
     2026_11_27_000000_create_print_jobs_table.php  -> database/migrations/
     PrintJob.php                                   -> app/Models/
     PrintAgentController.php                       -> app/Http/Controllers/
     OrderPrintController.php                       -> app/Http/Controllers/
     OrderReceiptPrinter.php                        -> app/Services/

2. Make a secret token (copy the output):
     php -r "echo bin2hex(random_bytes(32));"

3. config/restaurant.php  - add these two lines inside the returned array:
     'print_mode'        => env('PRINT_MODE', 'direct'),
     'print_agent_token' => env('PRINT_AGENT_TOKEN'),

4. Live .env  - add:
     PRINT_MODE=queue
     PRINT_AGENT_TOKEN=<the token from step 2>

5. routes/web.php  - add (top-level, NOT inside your admin/auth group):

     use App\Http\Controllers\PrintAgentController;

     Route::prefix('print-agent')
         ->withoutMiddleware([
             \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
             \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
             \App\Http\Middleware\VerifyCsrfToken::class,
         ])
         ->group(function () {
             Route::get('next', [PrintAgentController::class, 'next']);
             Route::post('{job}/done', [PrintAgentController::class, 'done']);
         });

   (If the agent later gets "419", add 'print-agent/*' to your CSRF "except" list.)

6. Run:
     php artisan migrate
     php artisan config:clear
     php artisan route:clear

7. Test the endpoint (should say 401 Unauthorized without the token):
     curl -i https://newmadrascafe.com/print-agent/next

---------------------------------------------------------------------
B) RESTAURANT PC  (Windows)
---------------------------------------------------------------------
1. The Star printer must be: paired, switched on, shared as StarTSP,
   in ESC/POS mode  (exactly as for the localhost tests).

2. Make a folder  C:\PrintAgent  and copy print-agent.ps1 into it.
   Open it in Notepad and set $Token (same value as PRINT_AGENT_TOKEN).

3. First run (to see messages). In PowerShell:
     powershell -ExecutionPolicy Bypass -File C:\PrintAgent\print-agent.ps1

4. In the admin on the LIVE site, click Print on an order.
   The toast says it was sent; within ~4 seconds the receipt prints and
   the agent window shows:  order ORD-... printed

5. Start it automatically when Windows starts:
   Press Win+R, type  shell:startup , Enter. In that folder create a
   shortcut whose Target is:
     powershell -ExecutionPolicy Bypass -WindowStyle Minimized -File "C:\PrintAgent\print-agent.ps1"

---------------------------------------------------------------------
LOCALHOST (your dev PC) keeps working as before:
  leave PRINT_MODE unset (or PRINT_MODE=direct) in the local .env.
---------------------------------------------------------------------
