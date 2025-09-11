<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Settings\SystemSettingController;
use App\Http\Controllers\Web\Backend\Settings\LinkSocialController;
use App\Http\Controllers\Web\Backend\User\UserController;
use App\Http\Controllers\Web\Backend\User\AdminController;
use App\Http\Controllers\Web\Backend\ProductCatelouge\CategoryController;
use App\Http\Controllers\Web\Backend\Product\ProductController;
use App\Http\Controllers\Web\Backend\Product\ProductOption;
use App\Http\Controllers\Web\Backend\Product\AttributeController;
use App\Http\Controllers\Web\Backend\Order\DeliveryController;
use App\Http\Controllers\Web\Backend\Order\OrderController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\Game\VersusCategoryController;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|\
*/

use App\Http\Middleware\isAdmin;

    Route::middleware(['auth', isAdmin::class])->prefix('admin')->group(function () {
        // dashboard page route
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // admin management
        Route::resource('/admin', AdminController::class);
        Route::post('/admin/update-status', [AdminController::class, 'updateStatus'])->name('admin.status');
        Route::post('/change-password', [AdminController::class, 'changePassword'])->name('change.password');

        // user management
        Route::resource('/user', UserController::class);
        Route::get('/user-message', [UserController::class, 'messages'])->name('user.message');
        Route::post('/update/update-status', [UserController::class, 'updateStatus'])->name('user.status');

        // user management
        // Route::controller(UserController::class)->group(function(){
        //     Route::get('/user', 'index')->name('user.index');
        //     Route::post('/update-status', 'updateStatus')->name('user.status');
        //     Route::delete('/delete-user/{user}', 'destroy')->name('delete.user');

        //     Route::get('chat/{id}', 'chat')->name('chat');
        // });

        // order
        Route::resource('order', OrderController::class);
        Route::post('view-order', [OrderController::class, 'viewOrder'])->name('view.order');
        Route::post('order-status', [OrderController::class, 'OrderStatus'])->name('order.status');

        Route::resource('delivery-option', DeliveryController::class);
        Route::post('/delivery-option/update-status', [DeliveryController::class, 'updateStatus'])->name('delivery-option.status');

        Route::resource('category', CategoryController::class);
        Route::post('/category/update-status', [CategoryController::class, 'updateStatus'])->name('category.status');
        Route::post('/category/toggle-featured', [CategoryController::class, 'toggleFeatured'])->name('category.toggleFeatured');
        Route::post('/category/toggle-menu-featured', [CategoryController::class, 'toggleMenuFeatured'])->name('category.toggleMenuFeatured');

        Route::resource('attribute', controller: AttributeController::class);

        // faq
        Route::resource('/faq', FaqController::class);
        Route::post('/update-faq-status', [FaqController::class, 'updateStatus'])->name('faq.status');

         // product management
        Route::resource('product', ProductController::class);
        Route::get('/get-subcategories/{category_id}', [CategoryController::class, 'getSubcategories']);
        Route::post('/product/update-status', [ProductController::class, 'updateStatus'])->name('product.status');
        Route::post('/product/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('product.toggleFeatured');

        // game
        Route::resource('game-category', VersusCategoryController::class);
        Route::post('/game-category/update-status', [VersusCategoryController::class, 'updateStatus'])->name('game-category.status');

        // settings
        Route::resource('system-setting', SystemSettingController::class);

        // social link
        Route::resource('/sociallink-setting', LinkSocialController::class);

    });


