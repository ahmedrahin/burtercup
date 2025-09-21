<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\Order\WishlistController;
use App\Http\Controllers\API\User\UserMessage;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\Order\CartController;
use App\Http\Controllers\API\User\UserAddressController;
use App\Http\Controllers\API\Order\OrderController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\API\Game\GameController;
use App\Http\Controllers\API\HomePagesController;
use App\Http\Controllers\API\Charity\ProgrammeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Build your API here.
*/

// =====================
// Auth Routes
// =====================
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');

    // user OTP
    Route::post('/verify/registration', 'verifyRegistrationOtp');
    Route::post('/resend/registration/otp', 'resendRegistrationOtp');
    Route::post('/verify-otp', 'verifyOtp');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
    Route::post('verify-otp-password', 'verifyOtp');
    Route::post('resend-otp-password', 'resendOtp');
});

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        // return redirect()->to('https://bartercup.com/congratulation');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    // return redirect()->to('https://bartercup.com/congratulation');
})->middleware(['signed'])->name('verification.verify');


Route::post('/email/resend', function (Request $request) {
    ini_set('max_execution_time', 60);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification email resent.']);
});


Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('user-message', [UserMessage::class, 'sendMessage']);
    Route::post('onboard-one', [AuthController::class, 'onboard_one']);
    Route::post('onboard-two', [AuthController::class, 'onboard_two']);
    Route::post('share', [UserMessage::class, 'share']);

    // user
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', 'getProfile');
        Route::post('/profile/update', 'updateProfile');
        Route::post('/profile/update-avatar', 'uploadAvatar');
        Route::post('/update-password', 'changePassword');
        Route::delete('/delete-profile', 'deleteAccount');
        Route::post('/inactive-profile', 'inactiveAccount');
        Route::post('/email/update', 'updateEmail');
        Route::post('/phone/update', 'updatePhone');
        Route::post('/concern-report', 'concernReport');

        // coin management
        Route::get('/coin-management', 'coinManagement');
        Route::get('/today-transaction', 'todayTransaction');

    });

    // product
    Route::controller(ProductController::class)->group(function () {
        Route::post('add-product', 'addProduct');
        Route::get('selected-categories', 'selectedCategories');
        Route::get('product-options/{id}', 'productOptions');
        Route::post('update-options/{id}', 'updateOptions');
        Route::get('product-edit/{id}', 'productEdit');
        Route::post('product-update/{id}', 'productUpdate');
        Route::post('gallery-add/{id}', 'galleryAdd');
        Route::post('delete-gallery/{id}', 'galleryDelete');
        Route::post('gallery-update/{id}', 'galleryUpdate');
        Route::get('my-list', 'myItemList');
        Route::get('product-details/{id}', 'productDetails');
        Route::post('product-delete/{id}', 'productDelete');
        Route::get('/search-products', 'searchProducts');
        Route::post('/search-query', 'searchQuery');
        Route::post('/clear-search-history', 'clearSearchHistory');
        Route::get('apply-filter', 'applyFilters');
        Route::get('sort', 'sort');
        Route::get('categories', 'getCategories');
        Route::get('subcategories', 'getSubCategories');
        Route::get('product-list', 'productList');
        Route::get('category-product-list/{category}', 'categoryProductList');
        Route::get('subcategory-product-list/{subcategory}', 'subcategoryProductList');
    });

    // wishlist
    Route::controller(WishlistController::class)->group(function () {
        Route::post('add-wishlist/{id}', 'addWishlist');
        Route::get('wishlist-list', 'wishlistList');
        Route::delete('delete-wishlist/{id}', 'deleteWishlist');
    });

    // cart
    Route::controller(CartController::class)->group(function () {
        Route::post('add-cart/{id}', 'AddCart');
        Route::get('cart-list', 'CartList');
        Route::post('update-quanitty/{id}/{value?}', 'updateQuantity');
        Route::post('delete-cart/{id}', 'deleteCart');
        Route::post('remove-all', 'removeAll');
        Route::get('/order-summary', 'orderSummary');
        Route::post('/apply-coupon', 'applyCoupon');
        Route::post('/remove-coupon', 'removeCoupon');
    });

    // user address
    Route::controller(UserAddressController::class)->group(function () {
        Route::post('/create-new-address', 'newAddress');
        Route::get('/my-addresses', 'myAddresses');
        Route::post('/make-defualt', 'makeDefault');
        Route::get('/address/edit/{id}', 'edit');
        Route::post('/address/update', 'updateAddress');
        Route::delete('/address/delete/{id}', 'deleteAddress');
    });

    // order
    Route::controller(OrderController::class)->group(function () {
        Route::post('/place-order', 'placeOrder');
        Route::get('/delivery-options', 'DeliveryOption');
        Route::get('/order-history', 'orderHistroy');
        Route::get('/order-track/{id}', 'orderTrack');
        Route::get('/order-invoice/{id}', 'orderInvoice');
        Route::get('/order-details/{id}', 'orderDetails');

        // app feedback after order
        Route::post('feedback', 'feedback');
    });

    Route::controller(FaqController::class)->group(function () {
        Route::get('/faq-list', 'faqList');
        Route::get('/faq-search', 'faqSearch');
    });

    Route::controller(GameController::class)->group(function () {
        Route::get('/versus-categories', 'GameCategory');
        Route::get('/games/{id}', 'game');
        Route::get('/game_options/{id}', 'game_options');
        Route::post('/play_game', 'playGame');
        Route::post('/collect_coin', 'collectCoin');
    });

    // home page controller
    Route::controller(HomePagesController::class)->group(function () {
        Route::get('banner-slider', 'homeBanner');
    });

    //charity programme
    Route::controller(ProgrammeController::class)->group(function () {
        Route::get('programme-list', 'programmeList');
        Route::get('programme-details/{id}', 'details');
        Route::post('donate/{id}', 'donate');
    });

});
