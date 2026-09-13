<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuItemImageController;
use App\Livewire\Categories\Manager as CategoryManager;
use App\Livewire\Deals\Manager as DealManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\DeliverySettingController;

use App\Http\Controllers\DeliveryCheckController;
use App\Livewire\CategoryMedia\Manager as CategoryMediaManager;
use App\Http\Controllers\Storefront\MenuController;
use App\Http\Controllers\Storefront\DealController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\CategoryController as StorefrontCategoryController;
use App\Http\Controllers\Storefront\MenuItemController as StorefrontMenuItemController;
use App\Http\Controllers\Storefront\DishController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\Auth\RegisteredCustomerController;
use App\Http\Controllers\Storefront\Auth\AuthenticatedCustomerController;
use App\Http\Controllers\Storefront\Auth\EmailVerificationController;
use App\Http\Controllers\Storefront\Auth\ForgotPasswordController;
use App\Http\Controllers\Storefront\Auth\ResetPasswordController;
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\CustomerAccountController;
use App\Http\Controllers\Storefront\TrackOrderController;





Route::get('/', [HomeController::class, 'index'])->name('storefront.home');
Route::get('/dish/{menuItem:slug}/quickview', [HomeController::class, 'quickview'])
    ->name('storefront.dish.quickview');
Route::get('/deals/{deal}/quickview', [DealController::class, 'quickview'])
    ->name('storefront.deals.quickview');

Route::get('/menu/{category:slug}', [StorefrontCategoryController::class, 'show'])->name('storefront.category');
Route::get('/dish/{menuItem:slug}', [DishController::class, 'show'])->name('storefront.dish');
Route::get('/menu', [MenuController::class, 'index'])->name('storefront.menu.index');
Route::get('/menu/{category:slug}', [CategoryController::class, 'show'])->name('storefront.category');
Route::get('/deals', [DealController::class, 'index'])->name('storefront.deals.index');


//// Add To Cart

Route::post('/cart/add', [CartController::class, 'store'])->name('storefront.cart.add');
Route::patch('/cart/{rowId}', [CartController::class, 'update'])->name('storefront.cart.update');
Route::delete('/cart/{rowId}', [CartController::class, 'destroy'])->name('storefront.cart.remove');
Route::post('/cart/bundle', [CartController::class, 'storeBundle'])
    ->name('storefront.cart.store-bundle');
// Not built yet — routed to a single "coming soon" page so header links
// (wishlist/search/cart/checkout) don't 500. Swap out once those exist.
Route::view('/search', 'storefront.coming-soon')->name('storefront.search');
Route::get('/cart', [CartController::class, 'index'])->name('storefront.cart');
// routes/web.php — remove the old placeholder line and add:
Route::get('/checkout', [CheckoutController::class, 'index'])->name('storefront.checkout');

Route::post('/checkout', [OrderController::class, 'store'])->name('storefront.checkout.store');
Route::get('/order/{order}/confirmation', [OrderController::class, 'confirmation'])->name('storefront.order.confirmation');
 
Route::get('/delivery-check', [DeliveryCheckController::class, 'show'])
    ->name('delivery-check.show');
 
Route::post('/delivery-check', [DeliveryCheckController::class, 'check'])
    ->name('delivery-check.check');
 
Route::get('/track-order', [TrackOrderController::class, 'index'])->name('storefront.track-order');
Route::post('/track-order', [TrackOrderController::class, 'lookup'])->name('storefront.track-order.lookup');


Route::middleware('auth:customer')->group(function () {
    Route::get('/account/verify-notice', [EmailVerificationController::class, 'notice'])->name('storefront.verification.notice');
     Route::get('/account', [CustomerAccountController::class, 'index'])->name('storefront.account');
    Route::post('/account/details', [CustomerAccountController::class, 'updateDetails'])->name('storefront.account.update-details');
    Route::post('/account/password', [CustomerAccountController::class, 'updatePassword'])->name('storefront.account.update-password');
    Route::post('/account/address', [CustomerAccountController::class, 'updateAddress'])->name('storefront.account.update-address');
});



Route::get('/account/verify/{customer}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('storefront.verification.verify');

Route::post('/account/verify/resend', [EmailVerificationController::class, 'resend'])->name('storefront.verification.resend');

Route::middleware('guest:customer')->group(function () {
    Route::get('/account/forgot-password', [ForgotPasswordController::class, 'create'])->name('storefront.password.request');
    Route::post('/account/forgot-password', [ForgotPasswordController::class, 'send'])->name('storefront.password.send');

    Route::get('/account/reset-password/{customer}', [ResetPasswordController::class, 'create'])
        ->middleware('signed')
        ->name('storefront.password.reset.form');
    Route::post('/account/reset-password/{customer}', [ResetPasswordController::class, 'update'])->name('storefront.password.reset.update');


    Route::get('/account/register', [RegisteredCustomerController::class, 'create'])->name('storefront.register');
    Route::post('/account/register', [RegisteredCustomerController::class, 'store']);
    Route::get('/account/login', [AuthenticatedCustomerController::class, 'create'])->name('storefront.login');
    Route::post('/account/login', [AuthenticatedCustomerController::class, 'store']);
});

Route::middleware('auth:customer')->group(function () {
        Route::get('/account/logout', [AuthenticatedCustomerController::class, 'destroy'])->name('storefront.logout');

});
Route::prefix('admin')->group(function () {
// --- Guest-only routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// --- Authenticated routes ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/categories', CategoryManager::class)->name('categories.index');
    Route::get('/deals', DealManager::class)->name('deals.index');
// pdf 


Route::get('/category-media', CategoryMediaManager::class)->name('category-media.index');



    // FIX: force the resource route's wildcard to be {menuItem} instead of
    // the default {menu}, matching every controller method's type-hinted
    // `MenuItem $menuItem` parameter. Without this, implicit route-model
    // binding silently fails and hands the controller an empty, unsaved
    // MenuItem instead of fetching the real one from the database.
    Route::resource('menu', MenuItemController::class)
        ->parameters(['menu' => 'menuItem']);

    Route::patch('menu/{menuItem}/toggle-availability', [MenuItemController::class, 'toggleAvailability'])
        ->name('menu.toggle-availability');
    Route::patch('menu/{menuItem}/toggle-featured', [MenuItemController::class, 'toggleFeatured'])
        ->name('menu.toggle-featured');

    Route::prefix('menu/images')->name('menu.images.')->group(function () {
        Route::post('temp/{draftToken}', [MenuItemImageController::class, 'storeTemp'])->name('temp.store');
        Route::delete('temp/{draftToken}/{filename}', [MenuItemImageController::class, 'destroyTemp'])->name('temp.destroy');
        Route::delete('{image}', [MenuItemImageController::class, 'destroy'])->name('destroy');
        Route::patch('{image}/primary', [MenuItemImageController::class, 'makePrimary'])->name('primary');
    });

    Route::post('menu/{menuItem}/images', [MenuItemImageController::class, 'store'])->name('menu.images.store');
    Route::post('menu/{menuItem}/images/reorder', [MenuItemImageController::class, 'reorder'])->name('menu.images.reorder');


// Delivery setting
 
Route::get('/delivery-settings', [DeliverySettingController::class, 'edit'])
    ->name('admin.delivery-settings.edit');
 
Route::put('/delivery-settings', [DeliverySettingController::class, 'update'])
    ->name('admin.delivery-settings.update');




// Slider section

    Route::resource('sliders', SliderController::class)->except(['show']);
Route::patch('sliders/{slider}/toggle-active', [SliderController::class, 'toggleActive'])->name('sliders.toggle-active');
Route::post('sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');
});

});