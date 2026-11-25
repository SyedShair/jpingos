<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Printer Share Name
    |--------------------------------------------------------------------------
    |
    | The name of the printer share on your Windows host. This is the same
    | name you would use in the "Add Printer" wizard on a Windows machine.
    | It is not the printer model or driver name, but the share name you
    | assigned when sharing the printer.
    |
    */

    'printer_share' => env('PRINTER_SHARE', 'StarTSP'),

];