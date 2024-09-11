<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'current']);

    Route::post('register', [AdminController::class, 'store']);
    Route::get('all_users', [AdminController::class, 'index']);
    Route::get('user/{id}', [AdminController::class, 'show']);
    Route::post('user_update/{id}', [AdminController::class, 'update']);
    Route::post('delete_user/{id}', [AdminController::class, 'destroy']);

    Route::post('create_city', [CityController::class, 'store']);
    Route::get('all_city', [CityController::class, 'index']);
    Route::get('city/{id}', [CityController::class, 'show']);
    Route::post('city_update/{id}', [CityController::class, 'update']);
    Route::post('delete_city/{id}', [CityController::class, 'destroy']);

    Route::post('create_company', [CompanyController::class, 'store']);
    Route::get('all_company', [CompanyController::class, 'index']);
    Route::get('company/{id}', [CompanyController::class, 'show']);
    Route::post('company_update/{id}', [CompanyController::class, 'update']);
    Route::post('delete_company/{id}', [CompanyController::class, 'destroy']);
    
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('filter_services', [ServiceController::class, 'filter_index']);
    Route::post('create_service', [ServiceController::class, 'store']);
    Route::get('services_admin/{id}', [ServiceController::class, 'index_admin']);
    Route::get('services_admin_today/{id}', [ServiceController::class, 'index_admin_today']);
    Route::post('filter_services_admin', [ServiceController::class, 'filter_index_admin']);
    Route::post('create_service_admin', [ServiceController::class, 'store_admin']);
    Route::get('service_edit_admin/{id}', [ServiceController::class, 'servic_edit_admin']);
    Route::post('edit_service_admin/{id}', [ServiceController::class, 'update']);
    Route::post('edit_service_price_admin/{id}', [ServiceController::class, 'update_price']);
    Route::post('edit_service_mission_price_admin/{id}', [ServiceController::class, 'update_mission_price']);
    Route::post('delete_service/{id}', [ServiceController::class, 'destroy']);
    Route::get('services_all_user', [ServiceController::class, 'index_admin_all_service']);
    Route::get('services_all_user_event/{id}', [ServiceController::class, 'index_admin_event_service']);
    Route::post('filter_services_all', [ServiceController::class, 'filter_index_all']);
    Route::post('pay_service', [ServiceController::class, 'pay_service']);
    Route::post('notpay_service', [ServiceController::class, 'notpay_service']);
    
    
    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    // Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});