<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OffterController;
use App\Http\Controllers\ProductBinnacleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use App\Http\Controllers\User\Auth\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response('listo');
})->name('verification.verify');

Route::post('checkout', [CheckoutController::class, 'afterpayment'])->name('checkout.credit-card');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::post('login', [UserAuthController::class, 'login']);

Route::post('user/register', [RegisterController::class, 'registerUser']);

Route::namespace('Admin')->prefix('admin')->group(function () {

    Route::post('login', [LoginController::class, 'login']);

    Route::get('first', [AdminController::class, 'adminFirstExist']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('role:admin|employee|user')->group(function () {
        Route::get('logout', [LoginController::class, 'logout']);

        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('brands', BrandController::class)->only('index', 'show');

        Route::apiResource('productTypes', ProductTypeController::class)->only('index', 'show');

        Route::get('product/product_actives', [ProductController::class, 'activeProducts']);

        Route::get('offter/offter_actives', [OffterController::class, 'activeOffters']);
    });

    Route::middleware('role:admin')->group(function () {

        Route::get('birthdate', [EmployeeController::class, 'testingAll']);

        Route::apiResource('admins', AdminController::class);

        Route::apiResource('employees', EmployeeController::class);

        Route::apiResource('users', UserController::class);

        Route::apiResource('brands', BrandController::class);

        Route::apiResource('productTypes', ProductTypeController::class);

        Route::apiResource('products', ProductController::class);

        Route::apiResource('offters', OffterController::class)->except('show');

        Route::apiResource('sales', SaleController::class);

        Route::apiResource('sale_details', SaleDetailController::class)->only('index', 'store');

        Route::apiResource('product_binnacles', ProductBinnacleController::class)->only('index', 'show');

        Route::post('action_product_binnacles/{product}', [ProductBinnacleController::class, 'withAction']);
    });

    Route::middleware('role:admin|employee')->group(function () {

        Route::apiResource('brands', BrandController::class)->only('index', 'store', 'show');

        Route::apiResource('productTypes', ProductTypeController::class)->only('index', 'store', 'show');

        Route::apiResource('products', ProductController::class)->only('index', 'store', 'show');

        Route::apiResource('offters', OffterController::class)->only('index', 'store');

        Route::apiResource('sales', SaleController::class)->only('index', 'show');

        Route::apiResource('sale_details', SaleDetailController::class)->only('index');

        Route::post('add_products/{product}', [ProductController::class, 'addQuantityProduct']);

        Route::post('remove_products/{product}', [ProductController::class, 'removeQuantityProduct']);

        Route::post('sale_actives', [SaleController::class, 'saleActives']);
    });

    Route::middleware('role:user')->group(function () {

        Route::get('car_shop/showCarShop', [CarShopController::class, 'showCarShop']);

        Route::post('car_shop/addItem', [CarShopController::class, 'addItem']);

        Route::post('car_shop/removeItem/{saleDetail}', [CarShopController::class, 'removeItem']);

        Route::post('car_shop/payCarShop', [CarShopController::class, 'payCarShop']);

        Route::get('sale/pastSales', [SaleController::class, 'pastUserSales']);
    });
});
