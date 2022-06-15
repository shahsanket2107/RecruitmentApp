<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([

    'prefix' => 'auth',

], function () {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('payload', 'AuthController@payload');
    Route::post('resetPassword', 'AuthController@ResetPassword');
    Route::post('register','CreateAccountController@createAccount');
    Route::post('verifyOtp','CreateAccountController@verify_otp');
    Route::post('resendOtp','CreateAccountController@resendOtp');
    Route::post('charges','CreateAccountController@userCharges');
    Route::post('userprofile','CreateAccountController@userProfile');
});
