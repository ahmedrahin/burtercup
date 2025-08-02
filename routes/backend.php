<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Settings\SystemSettingController;
use App\Http\Controllers\Web\Backend\Settings\LinkSocialController;
use App\Http\Controllers\Web\Backend\User\UserController;
use App\Http\Controllers\Web\Backend\User\AdminController;


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

    // Route::get('/test', function(){
    //     return view('emails.custom-verification')->with([
    //         'verificationUrl' => 1,
    //         'userName' => 'rahin',
    //     ]) ;
    // });

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


        // settings
        Route::resource('system-setting', SystemSettingController::class);

        // social link
        Route::resource('/sociallink-setting', LinkSocialController::class);

    });


