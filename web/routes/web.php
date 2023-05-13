<?php

use App\Models\UserNotification;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ImageController;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Posting;
use Spatie\Honeypot\ProtectAgainstSpam;
/* Cronjob Routes */

Route::group(['prefix' => 'cronjob'], function () {
    Route::get('deposit', [\App\Http\Controllers\CronjobController::class, 'getdeposit']);
});
/* Cronjob Routes */



Route::group(['prefix' => 'posting'], function () {
    Route::get('send', [\App\Http\Controllers\User\PostinganController::class, 'getSend']);
    Route::get('read/{target:id}', [\App\Http\Controllers\User\PostinganController::class, 'reading']);
    Route::get('answer/{target:id}', [\App\Http\Controllers\User\PostinganController::class, 'anwser']);
});

Route::get('categories/{category:slug}', [\App\Http\Controllers\User\PostinganController::class, 'getBycat']);
Route::get('search', [\App\Http\Controllers\User\PostinganController::class, 'pencarian']);
Route::get('ref/{target}', [\App\Http\Controllers\User\Auth\RegisterController::class, 'regbyref'])->withoutMiddleware(['auth']);

Route::get('/sitemap', function () {
    /*$sitemap = Sitemap::create()
        ->add(Url::create('/about-us'))
        ->add(Url::create('/contact_us'));
    */
    $sitemap = Sitemap::create();
    $post = Posting::all();
    foreach ($post as $post) {
        $sitemap->add(Url::create("/posting/read/{$post->id}"));
    }
    $sitemap->writeToFile(public_path('sitemap.xml'));
});

/* User Menu Routes */
Route::group(['namespace' => 'User', 'middleware' => ['auth', 'cookie', 'maintenance']], function () {
    Route::get('/', [\App\Http\Controllers\User\Auth\LoginController::class, 'index'])->withoutMiddleware(['auth']);
    Route::get('/home', [\App\Http\Controllers\User\Auth\LoginController::class, 'index'])->withoutMiddleware(['auth', 'cookie']);;
    Route::group(['prefix' => 'auth'], function () {
        Route::get('activate/{target:token}', [\App\Http\Controllers\User\Auth\ActivateAccountController::class, 'index'])->withoutMiddleware(['auth', 'cookie']);
        Route::get('login', [\App\Http\Controllers\User\Auth\LoginController::class, 'getLogin'])->withoutMiddleware(['auth'])->name('user.login');
        Route::post('login', [\App\Http\Controllers\User\Auth\LoginController::class, 'postLogin'])->withoutMiddleware(['auth'])->name('user.login');
        Route::get('logout', [\App\Http\Controllers\User\Auth\LoginController::class, 'logout'])->withoutMiddleware(['auth']);


        // if (website_config('main')->is_register_enabled) {
        Route::get('register', [\App\Http\Controllers\User\Auth\RegisterController::class, 'getRegister'])->withoutMiddleware(['auth'])->name('user.register');
        Route::post('register', [\App\Http\Controllers\User\Auth\RegisterController::class, 'postRegister'])->withoutMiddleware(['auth'])->name('user.register');

        Route::get('otp', [\App\Http\Controllers\User\Auth\RegisterController::class, 'getOtp'])->withoutMiddleware(['auth'])->name('user.register');
        Route::post('otp', [\App\Http\Controllers\User\Auth\RegisterController::class, 'activeotp'])->withoutMiddleware(['auth'])->name('user.register');
        //}
        if (website_config('main')->is_reset_password_enabled) {
            Route::get('reset/{token:hash?}', [\App\Http\Controllers\User\Auth\ResetPasswordController::class, 'getReset'])->withoutMiddleware(['auth'])->name('user.reset');
            Route::post('reset/{token:hash?}', [\App\Http\Controllers\User\Auth\ResetPasswordController::class, 'postReset'])->withoutMiddleware(['auth'])->name('user.reset');
        }

        //Route::get('{provider}', [\App\Http\Controllers\User\Auth\LoginController::class, 'redirectToProvider'])->withoutMiddleware(['auth'])->name('user.login');
        //Route::get('{provider}/callback', [\App\Http\Controllers\User\Auth\LoginController::class, 'handleProviderCallback'])->withoutMiddleware(['auth'])->name('user.login');
    });

    Route::group(['prefix' => 'posting'], function () {
        Route::post('send', [\App\Http\Controllers\User\PostinganController::class, 'postSend']);
        Route::post('read/{target:id}', [\App\Http\Controllers\User\PostinganController::class, 'postTerbaik']);
        Route::post('laporkan', [\App\Http\Controllers\User\PostinganController::class, 'postlapor']);
        Route::post('answer/{target:id}', [\App\Http\Controllers\User\PostinganController::class, 'postanwser']);
        Route::post('replay', [\App\Http\Controllers\User\PostinganController::class, 'postReplay'])->middleware(ProtectAgainstSpam::class);;
    });

    Route::group(['prefix' => 'project'], function () {
        Route::get('list', [\App\Http\Controllers\User\ProjectController::class, 'list']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'detail']);
        Route::get('create', [\App\Http\Controllers\User\ProjectController::class, 'createproject']);
        Route::post('create', [\App\Http\Controllers\User\ProjectController::class, 'postcreate']);
        Route::get('browse', [\App\Http\Controllers\User\ProjectController::class, 'browseproject']);
        Route::get('bid/placed', [\App\Http\Controllers\User\ProjectController::class, 'bidplaced']);
        Route::get('bid/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'projectbid']);
        Route::post('bid/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'postbid']);
        Route::get('bid-history', [\App\Http\Controllers\User\ProjectController::class, 'bidhistory']);
        Route::post('bid-history', [\App\Http\Controllers\User\ProjectController::class, 'cancelbid']);
        Route::get('show_conversation/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'show_conversation']);
        Route::post('show_conversation/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'chat']);
        Route::post('gugatan', [\App\Http\Controllers\User\ProjectController::class, 'postgugatan']);
        Route::get('show_bid/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'show_bid']);
        Route::post('approve', [\App\Http\Controllers\User\ProjectController::class, 'approvebid']);
        Route::post('cancel', [\App\Http\Controllers\User\ProjectController::class, 'cancelproject']);
        Route::post('finish', [\App\Http\Controllers\User\ProjectController::class, 'finishproject']);
        Route::get('edit/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'editproject']);
        Route::post('edit/{target:id}', [\App\Http\Controllers\User\ProjectController::class, 'postedit']);
        Route::post('ufinish', [\App\Http\Controllers\User\ProjectController::class, 'userfinish']);

        //Route::post('answer/{target:id}', [\App\Http\Controllers\User\PostinganController::class, 'postanwser']);
    });


    Route::group(['prefix' => 'downline'], function () {
        Route::get('summary', [\App\Http\Controllers\User\DownlineController::class, 'summary']);
        Route::post('summary', [\App\Http\Controllers\User\DownlineController::class, 'wdbonus']);
    });
    Route::group(['prefix' => 'account'], function () {
        Route::get('create_api_key', [\App\Http\Controllers\User\AccountController::class, 'create_api_key']);
        Route::get('profile', [\App\Http\Controllers\User\AccountController::class, 'profile']);
        Route::get('log/login', [\App\Http\Controllers\User\AccountController::class, 'login_log']);
        Route::get('log/balance', [\App\Http\Controllers\User\AccountController::class, 'balance_log']);
        Route::get('settings', [\App\Http\Controllers\User\AccountController::class, 'getSettings']);
        Route::patch('settings', [\App\Http\Controllers\User\AccountController::class, 'postSettings']);
        Route::get('settings/notification/{type}/{value}', [\App\Http\Controllers\User\AccountController::class, 'setNotification']);
        Route::get('withdraw', [\App\Http\Controllers\User\AccountController::class, 'withdraw']);
        Route::post('withdraw', [\App\Http\Controllers\User\AccountController::class, 'withdrawpost']);
        Route::post('settings/updateprofile', [\App\Http\Controllers\User\AccountController::class, 'postUpdateProfile']);
    });
    Route::group(['prefix' => 'deposit'], function () {
        Route::get('new', [\App\Http\Controllers\User\DepositController::class, 'getNew']);
        Route::post('new', [\App\Http\Controllers\User\DepositController::class, 'postNew']);
        Route::get('history', [\App\Http\Controllers\User\DepositController::class, 'history']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\User\DepositController::class, 'detail']);
    });


    /*
    Route::group(['prefix' => 'order'], function () {
        Route::get('category', function () {
            $components['breadcrumb'] = (object) [
                'first' => 'Pesan Baru',
                'second' => 'Pemesanan'
            ];
            return view('user.order.category', $components);
        });
        Route::get('product/{target:id}', [\App\Http\Controllers\User\OrderController::class, 'postNew']);
        Route::get('history', [\App\Http\Controllers\User\OrderController::class, 'history']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\User\OrderController::class, 'detail']);
        Route::get('{category:slug}', [\App\Http\Controllers\User\OrderController::class, 'getNew']);
        Route::post('{category:slug}', function () {
            return redirect()->back();
        });
    });
    */
    Route::group(['prefix' => 'point'], function () {
        Route::get('exchange', [\App\Http\Controllers\User\PointController::class, 'getExchange']);
        Route::post('exchange', [\App\Http\Controllers\User\PointController::class, 'postExchange']);
        Route::get('exchange/history', [\App\Http\Controllers\User\PointController::class, 'history']);
    });
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('list', [\App\Http\Controllers\User\TicketController::class, 'list']);
        Route::get('send', [\App\Http\Controllers\User\TicketController::class, 'getSend']);
        Route::post('send', [\App\Http\Controllers\User\TicketController::class, 'postSend']);
        Route::get('reply/{target:id}', [\App\Http\Controllers\User\TicketController::class, 'getReply']);
        Route::patch('reply/{target:id}', [\App\Http\Controllers\User\TicketController::class, 'postReply']);
    });


    Route::group(['prefix' => 'page'], function () {
        Route::group(['prefix' => 'service'], function () {
            Route::get('list', [\App\Http\Controllers\User\PageController::class, 'service_list'])->withoutMiddleware(['auth']);
            Route::get('monitoring', [\App\Http\Controllers\User\PageController::class, 'monitoring_service']);
            Route::get('detail/{target:id}', [\App\Http\Controllers\User\PageController::class, 'service_detail'])->withoutMiddleware(['auth']);
        });
        /*
        Route::group(['prefix' => 'product'], function () {
            Route::get('list', [\App\Http\Controllers\User\PageController::class, 'product_list'])->withoutMiddleware(['auth']);
            Route::get('detail/{target:id}', [\App\Http\Controllers\User\PageController::class, 'product_detail'])->withoutMiddleware(['auth']);
        });
        */
        Route::get('site/{target:slug}', [\App\Http\Controllers\User\PageController::class, 'site'])->withoutMiddleware(['auth']);
        Route::get('notification', [\App\Http\Controllers\User\PageController::class, 'notification']);
        Route::get('information', [\App\Http\Controllers\User\PageController::class, 'information']);
        Route::get('information/{target:id}', [\App\Http\Controllers\User\PageController::class, 'detailinformation']);
    });
});
/* User Menu Routes */


/* Admin Menu Routes */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth:admin']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\AuthController::class, 'index']);
    Route::group(['prefix' => 'auth'], function () {
        Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'getLogin'])->withoutMiddleware(['auth:admin', 'status'])->name('admin.login');
        Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'postLogin'])->withoutMiddleware(['auth:admin', 'status'])->name('admin.login');
        Route::get('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->withoutMiddleware(['auth:admin', 'status']);
    });
    Route::group(['prefix' => 'admin'], function () {
        Route::get('list', [\App\Http\Controllers\Admin\AdminController::class, 'list']);
        Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\AdminController::class, 'getForm']);
        Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\AdminController::class, 'postForm']);
        Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\AdminController::class, 'delete']);
        Route::get('status/{target:id}/{status:status}', [\App\Http\Controllers\Admin\AdminController::class, 'status']);
    });
    Route::group(['prefix' => 'posting'], function () {
        Route::get('list', [\App\Http\Controllers\Admin\PostingController::class, 'list']);
        Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\PostingController::class, 'delete']);
        Route::get('bermasalah', [\App\Http\Controllers\Admin\PostingController::class, 'bermasalah']);
        Route::get('kategori', [\App\Http\Controllers\Admin\PostingController::class, 'kategori']);
        Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\PostingController::class, 'getForm']);
        Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\PostingController::class, 'postForm']);
    });

    Route::group(['prefix' => 'project'], function () {
        Route::get('list', [\App\Http\Controllers\Admin\ProjectController::class, 'list']);
        //Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\ProjectController::class, 'delete']);
        Route::get('pending', [\App\Http\Controllers\Admin\ProjectController::class, 'pendingproject']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\Admin\ProjectController::class, 'detail']);
        Route::post('reject', [\App\Http\Controllers\Admin\ProjectController::class, 'reject']);
        Route::post('approve', [\App\Http\Controllers\Admin\ProjectController::class, 'approveproject']);
        Route::get('gugatan', [\App\Http\Controllers\Admin\ProjectController::class, 'gugatan']);
        Route::get('gugatan/{target:id}', [\App\Http\Controllers\Admin\ProjectController::class, 'gugatandetail']);
        Route::post('cancel/{target:id}', [\App\Http\Controllers\Admin\ProjectController::class, 'cancelproject']);
        Route::post('finish/{target:id}', [\App\Http\Controllers\Admin\ProjectController::class, 'finishproject']);
        Route::get('kategori', [\App\Http\Controllers\Admin\ProjectController::class, 'kategori']);
        Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\ProjectController::class, 'getForm']);
        Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\ProjectController::class, 'postForm']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('list', [\App\Http\Controllers\Admin\UserController::class, 'list']);
        Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\UserController::class, 'getForm']);
        Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\UserController::class, 'postForm']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\Admin\UserController::class, 'detail']);
        Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\UserController::class, 'delete']);
        Route::get('status/{target:id}/{status:status}', [\App\Http\Controllers\Admin\UserController::class, 'status']);
        Route::get('withdraw/pending', [\App\Http\Controllers\Admin\UserController::class, 'pendingwd']);
        Route::get('withdraw/history', [\App\Http\Controllers\Admin\UserController::class, 'historywd']);
        Route::post('withdraw/approve', [\App\Http\Controllers\Admin\UserController::class, 'approvewd']);
    });
    Route::group(['prefix' => 'page'], function () {
        Route::get('notification', [\App\Http\Controllers\Admin\Page\PageController::class, 'notification']);
        Route::get('hof', [\App\Http\Controllers\Admin\Page\PageController::class, 'hof']);
        Route::group(['prefix' => 'ovo'], function () {
            Route::get('login', [\App\Http\Controllers\Admin\Page\OVOController::class, 'getLogin']);
            Route::post('login', [\App\Http\Controllers\Admin\Page\OVOController::class, 'postLogin']);
            Route::get('confirm', [\App\Http\Controllers\Admin\Page\OVOController::class, 'getConfirm']);
            Route::post('confirm', [\App\Http\Controllers\Admin\Page\OVOController::class, 'postConfirm']);
            Route::get('pin', [\App\Http\Controllers\Admin\Page\OVOController::class, 'getPin']);
            Route::post('pin', [\App\Http\Controllers\Admin\Page\OVOController::class, 'postPin']);
        });
    });

    Route::group(['prefix' => 'deposit'], function () {
        Route::get('list', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'list']);
        Route::get('confirm/{target:id}', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'confirm']);
        Route::get('cancel/{target:id}', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'cancel']);
        Route::get('detail/{target:id}', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'detail']);
        Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'delete']);
        Route::get('report', [\App\Http\Controllers\Admin\Deposit\DepositController::class, 'report']);
        Route::group(['prefix' => 'send'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\Deposit\SendController::class, 'getForm']);
            Route::post('/', [\App\Http\Controllers\Admin\Deposit\SendController::class, 'postForm']);
        });
        Route::group(['prefix' => 'method'], function () {
            Route::get('/list', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'list']);
            Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'getForm']);
            Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'postForm']);
            Route::get('detail/{target:id}', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'detail']);
            Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'delete']);
            Route::get('status/{target:id}/{status:status}', [\App\Http\Controllers\Admin\Deposit\MethodController::class, 'status']);
        });
    });
    /*
    Route::group(['prefix' => 'point'], function () {
        Route::get('exchange/list', [\App\Http\Controllers\Admin\PointController::class, 'list']);
    });*/
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('send', [\App\Http\Controllers\Admin\TicketController::class, 'getSend']);
        Route::post('send', [\App\Http\Controllers\Admin\TicketController::class, 'postSend']);
        Route::get('reply/{target:id}', [\App\Http\Controllers\Admin\TicketController::class, 'getReply']);
        Route::patch('reply/{target:id}', [\App\Http\Controllers\Admin\TicketController::class, 'postReply']);
        Route::get('list', [\App\Http\Controllers\Admin\TicketController::class, 'list']);
        Route::get('close/{target:id}', [\App\Http\Controllers\Admin\TicketController::class, 'close']);
        Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\TicketController::class, 'delete']);
    });
    Route::group(['prefix' => 'log'], function () {
        Route::get('user_register', [\App\Http\Controllers\Admin\LogController::class, 'user_register']);
        Route::get('user_login', [\App\Http\Controllers\Admin\LogController::class, 'user_login']);
        Route::get('admin_login', [\App\Http\Controllers\Admin\LogController::class, 'admin_login']);
        Route::get('user_balance', [\App\Http\Controllers\Admin\LogController::class, 'user_balance']);
        Route::get('bank_mutation', [\App\Http\Controllers\Admin\LogController::class, 'bank_mutation']);
    });
    Route::group(['prefix' => 'settings'], function () {

        Route::group(['prefix' => 'bank_account'], function () {
            Route::get('list', [\App\Http\Controllers\Admin\Settings\BankAccountController::class, 'list']);
            Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\BankAccountController::class, 'getForm']);
            Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\BankAccountController::class, 'postForm']);
            Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\Settings\BankAccountController::class, 'delete']);
        });
        Route::group(['prefix' => 'website_information'], function () {
            Route::get('list', [\App\Http\Controllers\Admin\Settings\WebsiteInformationController::class, 'list']);
            Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\WebsiteInformationController::class, 'getForm']);
            Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\WebsiteInformationController::class, 'postForm']);
            Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\Settings\WebsiteInformationController::class, 'delete']);
        });
        Route::group(['prefix' => 'website_page'], function () {
            Route::get('list', [\App\Http\Controllers\Admin\Settings\WebsitePageController::class, 'list']);
            Route::get('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\WebsitePageController::class, 'getForm']);
            Route::post('form/{target:id?}', [\App\Http\Controllers\Admin\Settings\WebsitePageController::class, 'postForm']);
            Route::get('delete/{target:id}', [\App\Http\Controllers\Admin\Settings\WebsitePageController::class, 'delete']);
        });
        Route::group(['prefix' => 'website_configs'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'getIndex']);
            Route::patch('/', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'postIndex']);
            Route::get('delete_logo', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'delete_logo']);
            Route::get('delete_favicon', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'delete_favicon']);
            Route::get('delete_banner', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'delete_banner']);
            Route::get('test_email', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'test_email']);
        });
        Route::get('point', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'poinwd']);
        Route::post('point', [\App\Http\Controllers\Admin\Settings\WebsiteConfigController::class, 'postpoinwd']);
    });
});
/* Admin Menu Routes */
