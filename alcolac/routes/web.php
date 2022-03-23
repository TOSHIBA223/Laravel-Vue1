<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/')->group(function () {
    Route::get('', 'Frontend\Frontend@index')->name('home');
    Route::get('new-declaration', 'Frontend\Frontend@newDeclaration')->name('newDeclaration');
    Route::get('file/{token}', 'Frontend\Frontend@file')->name('file');
    Route::get('poll/show/{id}', 'Frontend\Frontend@pollShow')->name('pollShow');
    Route::get('declaration/show/{id}', 'Frontend\Frontend@declarationShow')->name('declarationShow');
    Route::get('poll/{token}', 'Frontend\Frontend@poll')->name('poll');
    Route::get('dec/{token}/{xyz}', 'Frontend\Frontend@declaration')->name('declaration');
    Route::get('dec/{token}', 'Frontend\Frontend@declaration')->name('declaration');
    //Route::get('/file/{id}', 'Frontend\Files@view');
    Route::get('login', function () {
        return Redirect::route('login');
    });

    Route::prefix('declarations')->group(function () {
        Route::post('dob-validation', 'Frontend\Frontend@dobValidation');
        Route::post('new/single', 'Frontend\Frontend@selfGenerate');
        //Route::post('submit', 'Frontend\Frontend@submitPoll');
        Route::post('submit', 'Frontend\Frontend@submitDeclaration');
    });

    Route::prefix('polls')->group(function () {
        Route::post('submit', 'Frontend\Frontend@submitPoll');
        Route::post('data', 'Frontend\Frontend@submitPoll');
    });

    Route::post('find-address', 'Frontend\Frontend@findAddress');
    Route::post('update-address', 'Frontend\Frontend@updateAddress');

    //QR with Location
    Route::get('qr-code/{location}', 'Frontend\Frontend@qrLocation');
    Route::post('qr-code/{location}', 'Frontend\Frontend@qrLocationPost');
    Route::get('qr-codes/verified', 'Frontend\Frontend@qrVerified');
    Route::get('qr-codes/confirmation', 'Frontend\Frontend@confirmation');
    Route::get('qr-codes/thanks', 'Frontend\Frontend@qrLocationDone');
    Route::get('qr-codes/submit', 'Frontend\Frontend@submitResult');
    Route::match(['GET', 'POST'], 'register', 'Frontend\Frontend@register');
});



Route::prefix('/admin')->group(function () {

         Route::get('/profile', 'Admin\Pages\AdminUsers\AdminUser@profile');

        Route::middleware(['auth:sanctum', 'verified', 'permission'])->group(function () {
        Route::get('/', 'Admin\Pages\Dashboard@index')->name('dashboard');

        Route::prefix('api')->group(function () {

            Route::prefix('menus')->group(function () {
                Route::get('items/{id}', 'Admin\Pages\Menus\MenuItems@get');
                Route::post('items', 'Admin\Pages\Menus\MenuItems@create');
                Route::put('items', 'Admin\Pages\Menus\MenuItems@update');
                Route::delete('items/{menuId}/{id}', 'Admin\Pages\Menus\MenuItems@delete');
            });

            Route::prefix('declarations')->group(function () {
                Route::get('merge', 'Admin\Pages\Declarations\Declaration@merge');

                Route::get('/{id?}', 'Admin\Pages\Declarations\Declaration@get');
                Route::post('create', 'Admin\Pages\Declarations\Declaration@create');
                Route::put('update', 'Admin\Pages\Declarations\Declaration@update');
                Route::delete('delete/{id}', 'Admin\Pages\Declarations\Declaration@delete');

                Route::post('send', 'Admin\Pages\Declarations\Declaration@send');
                Route::post('csv', 'Admin\Pages\Declarations\Declaration@csv');
                Route::post('setcron', 'Admin\Pages\Declarations\Declaration@setcron');
                Route::post('setdeclarationdefault', 'Admin\Pages\Declarations\Declaration@setdeclarationdefault');
                Route::get('getsetting', 'Admin\Pages\Declarations\Declaration@getsetting');
                Route::get('getmenu', 'Admin\Pages\Declarations\Declaration@getmenu');
            });

            Route::prefix('system-crons')->group(function () {

                Route::post('search', 'Admin\Pages\SystemCrons\SystemCron@search');
                Route::post('create', 'Admin\Pages\SystemCrons\SystemCron@create')->name('user_create');
                Route::put('update', 'Admin\Pages\SystemCrons\SystemCron@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\SystemCrons\SystemCron@delete')->name('user_delete');
                Route::post('send', 'Admin\Pages\SystemCrons\SystemCron@send');
                Route::post('csv', 'Admin\Pages\SystemCrons\SystemCron@csv');
                Route::post('setcron', 'Admin\Pages\SystemCrons\SystemCron@setcron');
                Route::get('getsetting', 'Admin\Pages\SystemCrons\SystemCron@getsetting');
                Route::get('getmenu', 'Admin\Pages\SystemCrons\SystemCron@getmenu');
            });

            Route::prefix('employees')->group(function () {
                Route::post('search', 'Admin\Pages\Users\User@search');
                Route::post('search_employee', 'Admin\Pages\Users\User@searchByEmployeeCode');
                Route::get('search', 'Admin\Pages\Users\User@search');
                Route::post('/{userId}/declaration/{decId}', 'Admin\Pages\Users\User@showDeclarationEntries');
                Route::post('create', 'Admin\Pages\Users\User@create')->name('user_create');
                Route::put('update', 'Admin\Pages\Users\User@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\Users\User@delete')->name('user_delete');
            });

            Route::prefix('departments')->group(function () {
                Route::post('search', 'Admin\Pages\Departments\Department@search');
                Route::post('create', 'Admin\Pages\Departments\Department@create')->name('user_create');
                Route::put('update', 'Admin\Pages\Departments\Department@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\Departments\Department@delete')->name('user_delete');
                Route::delete('permandelete/{id}', 'Admin\Pages\Departments\Department@permandelete')->name('permandelete');
            });
            Route::prefix('admin-users')->group(function () {
                Route::post('search', 'Admin\Pages\AdminUsers\AdminUser@search');
                Route::post('create', 'Admin\Pages\AdminUsers\AdminUser@create')->name('user_create');
                Route::put('update', 'Admin\Pages\AdminUsers\AdminUser@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\AdminUsers\AdminUser@delete')->name('user_delete');
                Route::delete('permandelete/{id}', 'Admin\Pages\AdminUsers\AdminUser@permandelete')->name('permandelete');
            });

            Route::prefix('qr-codes')->group(function () {
                Route::post('search', 'Admin\Pages\QrCodes\QrCode@search');
                Route::post('create', 'Admin\Pages\QrCodes\QrCode@create')->name('user_create');
                Route::put('update', 'Admin\Pages\QrCodes\QrCode@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\QrCodes\QrCode@delete')->name('user_delete');
                Route::delete('permandelete/{id}', 'Admin\Pages\QrCodes\QrCode@permandelete')->name('permandelete');
            });
            Route::prefix('qr-checks')->group(function () {
                Route::post('create', 'Admin\Pages\QrChecks\QrCheck@create');
                Route::post('search', 'Admin\Pages\QrChecks\QrCheck@search');
                Route::put('update', 'Admin\Pages\QrChecks\QrCheck@update')->name('user_update');
                Route::delete('delete/{id}', 'Admin\Pages\QrChecks\QrCheck@delete')->name('user_delete');
            });

            /*Route::prefix('users')->group(function() {
            Route::post('search', 'Admin\Pages\Users\User@search');
            Route::post('/{userId}/declaration/{decId}', 'Admin\Pages\Users\User@showDeclarationEntries');
            Route::post('create', 'Admin\Pages\Users\User@create')->name('user_create');
            Route::put('update', 'Admin\Pages\Users\User@update')->name('user_update');
            Route::delete('delete/{id}', 'Admin\Pages\Users\User@delete')->name('user_delete');
       });*/

            Route::prefix('polls')->group(function () {
                Route::get('/{id?}', 'Admin\Pages\Polls\Poll@get');
                Route::post('data', 'Admin\Pages\Polls\Poll@getData');
                Route::post('create', 'Admin\Pages\Polls\Poll@create');
                Route::put('update', 'Admin\Pages\Polls\Poll@update');
                Route::post('send', 'Admin\Pages\Polls\Poll@send');
            });

            Route::prefix('sms')->group(function () {
                Route::get('templates', 'Admin\Pages\Sms\SmsTemplate@get');
                Route::post('templates', 'Admin\Pages\Sms\SmsTemplate@create');
                Route::put('templates', 'Admin\Pages\Sms\SmsTemplate@update');
                Route::delete('templates/{id}', 'Admin\Pages\Sms\SmsTemplate@delete');

                Route::post('send', 'Admin\Pages\Sms\SmsSend@send');
                Route::post('schedule', 'Admin\Pages\Sms\SmsSend@schedule');
            });

            Route::prefix('ip-whitelist')->group(function () {
                Route::get('get', 'Admin\Pages\IPWhitelist\IPWhitelist@get');
                Route::post('create', 'Admin\Pages\IPWhitelist\IPWhitelist@create');
                Route::put('update', 'Admin\Pages\IPWhitelist\IPWhitelist@update');
                Route::delete('delete/{id}', 'Admin\Pages\IPWhitelist\IPWhitelist@delete');
            });

            Route::prefix('files')->group(function () {
                Route::get('get', 'Admin\Pages\Files\Files@get');
                Route::post('upload', 'Admin\Pages\Files\Files@create');
                Route::put('archive', 'Admin\Pages\Files\Files@update');
                Route::post('delete', 'Admin\Pages\Files\Files@delete');
                Route::post('open/{id}', 'Admin\FileController@open');
            });

            Route::post('tag-builder', 'Admin\TagBuilder@buildMessageFrontend');
        });
    });

    Route::middleware(['auth:sanctum', 'verified', 'hasPermission', 'permission'])->group(function () {
        Route::prefix('menus')->group(function () {
            Route::get('/', 'Admin\Pages\Menus\Menus@index');
        });

        /*Route::prefix('users')->group(function() {
            Route::get('/', 'Admin\Pages\Users\User@index')->name('user_list');
            Route::get('showdeclaration/{id}','Admin\Pages\Users\User@showdeclaration')->name('showdeclaration');

        });*/

        Route::prefix('employees')->group(function () {
            Route::get('/', 'Admin\Pages\Users\User@index')->name('user_list');
            Route::get('showdeclaration/{id}', 'Admin\Pages\Users\User@showdeclaration')->name('showdeclaration');
        });



        Route::prefix('admin-users')->group(function () {

            Route::get('/', 'Admin\Pages\AdminUsers\AdminUser@index');
        });

        Route::prefix('department')->group(function () {

            Route::get('/', 'Admin\Pages\Departments\Department@index');
        });

        Route::prefix('system-crons')->group(function () {

            Route::get('/', 'Admin\Pages\SystemCrons\SystemCron@index');
        });

        Route::prefix('qr-codes')->group(function () {

            Route::get('/', 'Admin\Pages\QrCodes\QrCode@index');
            Route::get('showqr/{token}', 'Admin\Pages\QrCodes\QrCode@showqr')->name('showqr');
            Route::get('contractor/{slug}', 'Admin\Pages\QrCodes\QrCode@contractor');
        });

        Route::prefix('rat-result')->group(function () {
            Route::get('/', 'Admin\Pages\QrCodes\QrCode@showRATResult')->name('showRATResult');
        });


        Route::prefix('qr-checks')->group(function () {

            Route::get('/', 'Admin\Pages\QrChecks\QrCheck@index');
        });

        Route::prefix('sms-templates')->group(function () {
            Route::get('/', 'Admin\Pages\Sms\SmsTemplate@index');
        });


        Route::prefix('settings')->group(function () {
            Route::get('/', 'Admin\Pages\Settings\Setting@index');
            Route::post('/savesetting', 'Admin\Pages\Settings\Setting@savesetting');
            Route::post('/saveimage', 'Admin\Pages\Settings\Setting@saveimage');
            Route::post('/testsmtp', 'Admin\Pages\Settings\Setting@testsmtp');
            Route::post('/testsmb', 'Admin\Pages\Settings\Setting@testsmb');
            Route::post('/reset_address', 'Admin\Pages\Settings\Setting@reset_address');
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', 'Admin\Pages\Roles\Role@index');
            Route::get('/assign/{id}', 'Admin\Pages\Roles\Role@assign')->name('assign');
            Route::post('/savepermisson', 'Admin\Pages\Roles\Role@savepermisson');
        });

        // Route::get('/roles/assign/{id}', 'Admin\Pages\Roles\Role@assign')->name('assign');
        //Route::get('/Roles', 'Admin\Pages\Roles\AssignRole@assignroles');


        Route::prefix('send-sms')->group(function () {
            Route::get('/', 'Admin\Pages\Sms\SmsSend@index');
        });

        Route::prefix('sms-status')->group(function () {
            Route::get('/', 'Admin\Pages\Sms\SmsStatus@index');
        });

        Route::prefix('declarations')->group(function () {
            Route::get('/', 'Admin\Pages\Declarations\Declaration@index');
            Route::get('declog/{id}', 'Admin\Pages\Declarations\Declaration@declog')->name('declog');
        });

        Route::prefix('polls')->group(function () {
            Route::get('/', 'Admin\Pages\Polls\Poll@index');
        });

        Route::prefix('files')->group(function () {
            Route::get('/', 'Admin\Pages\Files\Files@index');
            Route::get('filelog/{id}', 'Admin\Pages\Files\Files@filelog')->name('filelog');
        });

        Route::get('ip-whitelist', 'Admin\Pages\IPWhitelist\IPWhitelist@index');

        Route::get('system-log', 'Admin\Pages\Settings\SystemLogController@index');
    });

    Route::middleware('hasSession')->group(function () {
        Route::get('/login', 'Admin\Auth@login')->name('login');
        Route::post('/login/authenticate', 'Admin\Auth@authenticate')->name('authenticate');

        Route::get('/2fa', 'Admin\Auth@twoFA')->name('twoFa');
        Route::post('/login/finalise', 'Admin\Auth@finalise')->name('finalise');

        Route::get('/password/reset', 'Admin\Auth@passwordReset')->name('passwordReset');
        Route::post('/password/reset', 'Admin\Auth@sendResetEmail')->name('sendPasswordReset');
    });
});
Route::get('test/{id}', 'Controller@index');

Route::get('/runcomd', function () {

    Artisan::call('sms:send-daily-bulk-sms');

    \Log::info('end message');
});

Route::group(['prefix' => 'devtesting'], function () {
    Route::get('/run-commands', function () {
        //\Artisan::call('cache:clear');
        //\Artisan::call('view:clear');
        //\Artisan::call('route:clear');
        \Artisan::call('storage:link');
    });
});
