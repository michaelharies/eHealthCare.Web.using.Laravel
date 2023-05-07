<?php
/*
 * File name: api.php
 * Last modified: 2022.07.16 at 11:40:24
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

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


Route::prefix('clinic_owner')->group(function () {
    Route::post('login', 'API\ClinicOwner\UserAPIController@login');
    Route::post('register', 'API\ClinicOwner\UserAPIController@register');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\ClinicOwner\UserAPIController@user');
    Route::get('logout', 'API\ClinicOwner\UserAPIController@logout');
    Route::get('settings', 'API\ClinicOwner\UserAPIController@settings');
    Route::get('translations', 'API\TranslationAPIController@translations');
    Route::get('supported_locales', 'API\TranslationAPIController@supportedLocales');
    Route::middleware('auth:api')->group(function () {
        Route::resource('clinics', 'API\ClinicOwner\ClinicAPIController')->only(['index', 'show']);
        Route::get('doctors', 'API\ClinicOwner\DoctorAPIController@index');
        Route::resource('availability_hours', 'API\AvailabilityHourAPIController')->only(['store', 'update', 'destroy']);
        Route::resource('awards', 'API\AwardAPIController')->only(['store', 'update', 'destroy']);
        Route::resource('experiences', 'API\ExperienceAPIController')->only(['store', 'update', 'destroy']);
        Route::get('clinic_levels', 'API\ClinicLevelAPIController@index');
        Route::get('taxes', 'API\ClinicOwner\TaxAPIController@index');
        Route::get('employees', 'API\ClinicOwner\UserAPIController@employees');
        Route::get('patients', 'API\ClinicOwner\PatientAPIController@index');
    });
});




Route::prefix('doctor')->group(function () {
    Route::post('login', 'API\Doctor\UserAPIController@login');
    Route::post('register', 'API\Doctor\UserAPIController@register');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\Doctor\UserAPIController@user');
    Route::get('logout', 'API\Doctor\UserAPIController@logout');
    Route::get('settings', 'API\Doctor\UserAPIController@settings');
    Route::get('translations', 'API\TranslationAPIController@translations');
    Route::get('supported_locales', 'API\TranslationAPIController@supportedLocales');
    Route::middleware('auth:api')->group(function () {
        Route::resource('clinics', 'API\Doctor\ClinicAPIController')->only(['index', 'show']);
        Route::get('doctors', 'API\Doctor\DoctorAPIController@index');
        Route::resource('availability_hours', 'API\AvailabilityHourAPIController')->only(['store', 'update', 'destroy']);
        Route::resource('awards', 'API\AwardAPIController')->only(['store', 'update', 'destroy']);
        Route::resource('experiences', 'API\ExperienceAPIController')->only(['store', 'update', 'destroy']);
        Route::get('clinic_levels', 'API\ClinicLevelAPIController@index');
        Route::get('taxes', 'API\Doctor\TaxAPIController@index');
        Route::get('employees', 'API\Doctor\UserAPIController@employees');
        Route::get('patients', 'API\Doctor\PatientAPIController@index');

    });
});


Route::post('login', 'API\UserAPIController@login');
Route::post('register', 'API\UserAPIController@register');
Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
Route::get('user', 'API\UserAPIController@user');
Route::get('logout', 'API\UserAPIController@logout');
Route::get('settings', 'API\UserAPIController@settings');
Route::get('translations', 'API\TranslationAPIController@translations');
Route::get('supported_locales', 'API\TranslationAPIController@supportedLocales');
Route::get('modules', 'API\ModuleAPIController@index');

Route::resource('clinics', 'API\ClinicAPIController')->only(['index', 'show']);
Route::resource('availability_hours', 'API\AvailabilityHourAPIController')->only(['index', 'show']);
Route::resource('awards', 'API\AwardAPIController')->only(['index', 'show']);
Route::resource('experiences', 'API\ExperienceAPIController')->only(['index', 'show']);

Route::resource('faq_categories', 'API\FaqCategoryAPIController');
Route::resource('faqs', 'API\FaqAPIController');
Route::resource('custom_pages', 'API\CustomPageAPIController');

Route::resource('specialities', 'API\SpecialityAPIController');

Route::resource('doctor_patients', 'API\DoctorPatientsAPIController');
Route::resource('doctors', 'API\DoctorAPIController');
Route::resource('galleries', 'API\GalleryAPIController');

Route::get('doctor_reviews/{id}', 'API\DoctorReviewAPIController@show');
Route::get('doctor_reviews', 'API\DoctorReviewAPIController@index');

Route::get('clinic_reviews/{id}', 'API\ClinicReviewAPIController@show');
Route::get('clinic_reviews', 'API\ClinicReviewAPIController@index');

Route::resource('currencies', 'API\CurrencyAPIController');
Route::resource('slides', 'API\SlideAPIController')->except([
    'show'
]);
Route::resource('appointment_statuses', 'API\AppointmentStatusAPIController')->except([
    'show'
]);


Route::resource('patients', 'API\PatientAPIController');

Route::post('patients/{id}', 'API\PatientAPIController@update');


Route::middleware('auth:api')->group(function () {
    Route::group(['middleware' => ['role:clinic_owner']], function () {
        Route::prefix('clinic_owner')->group(function () {
            Route::post('users/{user}', 'API\UserAPIController@update');
            Route::get('dashboard', 'API\DashboardAPIController@clinic');
            Route::resource('notifications', 'API\NotificationAPIController');
            Route::put('payments/{id}', 'API\PaymentAPIController@update')->name('payments.update');
        });
    });

    Route::group(['middleware' => ['role:doctor']], function () {
        Route::prefix('doctor')->group(function () {
            Route::post('users/{user}', 'API\UserAPIController@update');
            Route::get('dashboard', 'API\DashboardAPIController@doctor');
            Route::resource('notifications', 'API\NotificationAPIController');
            Route::put('payments/{id}', 'API\PaymentAPIController@update')->name('payments.update');
        });
    });


    Route::resource('clinics', 'API\ClinicAPIController')->only([
        'store', 'update', 'destroy'
    ]);
    Route::post('uploads/store', 'API\UploadAPIController@store');
    Route::post('uploads/clear', 'API\UploadAPIController@clear');
    Route::post('users/{user}', 'API\UserAPIController@update');
    Route::delete('users', 'API\UserAPIController@destroy');


    Route::get('payments/byMonth', 'API\PaymentAPIController@byMonth')->name('payments.byMonth');
    Route::post('payments/wallets/{id}', 'API\PaymentAPIController@wallets')->name('payments.wallets');
    Route::post('payments/cash', 'API\PaymentAPIController@cash')->name('payments.cash');
    Route::resource('payment_methods', 'API\PaymentMethodAPIController')->only([
        'index'
    ]);
    Route::post('doctor_reviews', 'API\DoctorReviewAPIController@store')->name('doctor_reviews.store');
    Route::post('clinic_reviews', 'API\ClinicReviewAPIController@store')->name('clinic_reviews.store');


    Route::resource('favorites', 'API\FavoriteAPIController');
    Route::resource('addresses', 'API\AddressAPIController');

    Route::get('notifications/count', 'API\NotificationAPIController@count');
    Route::resource('notifications', 'API\NotificationAPIController');
    Route::resource('appointments', 'API\AppointmentAPIController');

    Route::resource('earnings', 'API\EarningAPIController');

    Route::resource('clinic_payouts', 'API\ClinicPayoutAPIController');

    Route::resource('coupons', 'API\CouponAPIController')->except([
        'show'
    ]);
    Route::resource('wallets', 'API\WalletAPIController')->except([
        'show', 'create', 'edit'
    ]);
    Route::get('wallet_transactions', 'API\WalletTransactionAPIController@index')->name('wallet_transactions.index');

});





