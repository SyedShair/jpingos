<?php
/*
|--------------------------------------------------------------------------
| ADD TO routes/api.php — create that file if your project doesn't have
| one yet (routes/api.php is standard Laravel; if it's missing, run
| `php artisan install:api` or just create the file and Laravel will
| pick it up once registered in bootstrap/app.php — see SETUP.md Step 3).
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::post('/', [BookingController::class, 'store']);
    Route::put('/{id}', [BookingController::class, 'update']);
    Route::patch('/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'destroy']);
});

// Wrap the admin-facing ones (index/show/update/destroy) in your API auth
// middleware once you have one — e.g.:
// Route::middleware('auth:sanctum')->prefix('bookings')->group(function () { ... });
// POST stays public (it's the storefront booking submission).