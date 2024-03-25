<?php

use App\Http\Controllers\CheckController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\HistoryActionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\PostTypeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

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

// =====> be careful when change watch out # getUserRolesDetails() # to keep change Roles

Route::group(["prefix" => 'dashboard', 'middleware' => 'dashboard'], function () {

    // dashboard
    Route::get('/index', function () {

        // make it global for accessing title and other data
        generateGlobalTitle(["fake_class" => "Overview"]);

        return view("dashboard.index");
    })->name("overview");


    // post.type
    Route::group(["prefix" => 'post.type', "as" => 'post.type.'], function () {
        // create
        Route::get('{type}/create', [PostTypeController::class, "create"])->name("create.form");
        Route::post('{type}/store', [PostTypeController::class, "store"])->name("create");

        // edit
        Route::get('{type}/edit/{id}', [PostTypeController::class, "edit"])->name("edit.form");
        Route::patch('{type}/update/{id}', [PostTypeController::class, "update"])->name("edit");

        // index
        Route::get('{type}/index',  [PostTypeController::class, "index"])->name("list");
        Route::post('{type}/index',  [PostTypeController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('{type}/destroy',  [PostTypeController::class, "destroy"])->name("destroy");

        // status type
        Route::patch('{type}/set_status',  [PostTypeController::class, "statusType"])->name("status.type");
    });


    // taxonomy
    Route::group(["prefix" => 'taxonomy', "as" => 'taxonomy.'], function () {
        // create
        Route::get('{type}/create', [TaxonomyController::class, "create"])->name("create.form");
        Route::post('{type}/store', [TaxonomyController::class, "store"])->name("create");

        // edit
        Route::get('{type}/edit/{id}', [TaxonomyController::class, "edit"])->name("edit.form");
        Route::patch('{type}/update/{id}', [TaxonomyController::class, "update"])->name("edit");

        // index
        Route::get('{type}/index',  [TaxonomyController::class, "index"])->name("list");
        Route::post('{type}/index',  [TaxonomyController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('{type}/destroy',  [TaxonomyController::class, "destroy"])->name("destroy");

        // status type
        Route::patch('{type}/set_status',  [TaxonomyController::class, "statusType"])->name("status.type");
    });

    // form schema
    Route::group(["prefix" => 'forms_schema', "as" => 'forms_schema.'], function () {
        // create form
        Route::get('/create', [FormController::class, "create"])->name("create.form");
        Route::post('/store', [FormController::class, "store"])->name("create");

        // edit form
        Route::get('/edit/{id}', [FormController::class, "edit"])->name("edit.form");
        Route::patch('/update/{id}', [FormController::class, "update"])->name("edit");

        // index
        Route::get('/index',  [FormController::class, "index"])->name("list");
        Route::post('/index',  [FormController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('/destroy',  [FormController::class, "destroy"])->name("destroy");

        // status type
        Route::patch('/set_status',  [FormController::class, "statusType"])->name("status.type");
    });

    // form
    Route::group(["prefix" => 'forms', "as" => 'forms.'], function () {

        // index
        Route::get('/index',  [FormController::class, "index_form"])->name("list");
        Route::post('/index',  [FormController::class, "index_form"])->name("list.filter");

        // destroy
        Route::delete('/destroy',  [FormController::class, "destroy_form"])->name("destroy");

        // status type
        Route::patch('/set_status_form',  [FormController::class, "statusTypeForm"])->name("status.type");
    });

    // menu
    Route::group(["prefix" => 'menu', "as" => 'menu.'], function () {
        // create
        Route::get('/create', [MenuController::class, "create"])->name("create.form");
        Route::post('/store', [MenuController::class, "store"])->name("create");

        // edit
        Route::get('edit/{id}', [MenuController::class, "edit"])->name("edit.form");
        Route::patch('update/{id}', [MenuController::class, "update"])->name("edit");

        // index
        Route::get('/index',  [MenuController::class, "index"])->name("list");
        Route::post('/index',  [MenuController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('destroy',  [MenuController::class, "destroy"])->name("destroy");
    });

    // view
    Route::group(["prefix" => 'view', "as" => 'view.'], function () {
        // index
        Route::get('/index',  [ViewController::class, "index"])->name("list");
        Route::post('/index',  [ViewController::class, "index"])->name("list.filter");
    });

    // file
    Route::group(["prefix" => 'file', "as" => 'file.'], function () {
        // create
        Route::get('/create', [FileController::class, "create"])->name("create.form");
        Route::post('/store', [FileController::class, "store"])->name("create");

        // index
        Route::get('/index',  [FileController::class, "index"])->name("list");
        Route::post('/index',  [FileController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('/destroy',  [FileController::class, "destroy"])->name("destroy");
    });

    // comment
    Route::group(["prefix" => 'comments', "as" => 'comments.'], function () {
        // create
        Route::middleware('throttle:form')->group(function () {
            Route::post('/store/{type}', [CommentController::class, "store"])->name("create");
        });
        

        // edit
        Route::get('{type}/edit/{id}', [CommentController::class, "edit"])->name("edit.form");
        Route::patch('{type}/update/{id}', [CommentController::class, "update"])->name("edit")->whereNumber("id");

        // index
        Route::get('{type}/index',  [CommentController::class, "index"])->name("list");
        Route::post('{type}/index',  [CommentController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('{type}/destroy',  [CommentController::class, "destroy"])->name("destroy");

        // status type
        Route::patch('{type}/set_status',  [CommentController::class, "statusType"])->name("status.type");
    });

    // newsletter
    Route::group(["prefix" => 'newsletter', "as" => 'newsletter.'], function () {
        // create
        Route::post('/store', [NewsletterController::class, "store"])->name("create");

        // edit
        Route::get('edit/{id}', [NewsletterController::class, "edit"])->name("edit.form");

        // index
        Route::get('index',  [NewsletterController::class, "index"])->name("list");
        Route::post('index',  [NewsletterController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('destroy',  [NewsletterController::class, "destroy"])->name("destroy");
    });

    // user
    Route::group(["prefix" => 'user', "as" => 'user.'], function () {
        // create
        Route::get('/create', [UserController::class, "create"])->name("create.form");
        Route::post('/store', [UserController::class, "store"])->name("create");

        // edit
        Route::get('edit/{id}', [UserController::class, "edit"])->name("edit.form");
        Route::patch('update/{id}', [UserController::class, "update"])->name("edit");

        // index
        Route::get('index',  [UserController::class, "index"])->name("list");
        Route::post('index',  [UserController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('destroy',  [UserController::class, "destroy"])->name("destroy");

        // status type
        Route::patch('/set_status',  [UserController::class, "statusType"])->name("status.type");

        // =======================> Public User Features

        // sign up
        Route::get('sign_up',  [UserController::class, "pub_signup"])->name("signup.form");

        // sign in
        Route::get('sign_in',  [UserController::class, "pub_signin"])->name("signin.form");
        Route::post('sign_in',  [UserController::class, "pub_signin_action"])->name("signin");

        // reset password
        Route::get('reset/password',  [UserController::class, "pub_reset_password"])->name("reset.password.form");
        Route::post('reset/password',  [UserController::class, "pub_reset_password_action"])->name("reset.password");

        // verify
        Route::get('verify/{user_hash_id}/{id}/{dechex_hash?}',  [UserController::class, "pub_verify_client"])->name('verify.client.form');

        // logout
        Route::delete('logout',  [UserController::class, "logout"])->name("logout");
    });


    // redirection
    Route::group(["prefix" => 'redirect', "as" => 'redirect.'], function () {
        // create 
        Route::get('/create', [RedirectController::class, "create"])->name("create.form");
        Route::post('/store', [RedirectController::class, "store"])->name("create");

        // edit
        Route::get('edit/{id}', [RedirectController::class, "edit"])->name("edit.form");
        Route::patch('update/{id}', [RedirectController::class, "update"])->name("edit");

        // index
        Route::get('/index',  [RedirectController::class, "index"])->name("list");
        Route::post('/index',  [RedirectController::class, "index"])->name("list.filter");

        // destroy
        Route::delete('destroy',  [RedirectController::class, "destroy"])->name("destroy");
    });

    // history actions
    Route::group(["prefix" => 'history_action', "as" => 'history_action.'], function () {

        // view
        Route::get('/edit/{id}', [HistoryActionController::class, "edit"])->name("edit.form");

        // index
        Route::get('/index',  [HistoryActionController::class, "index"])->name("list");
        Route::post('/index',  [HistoryActionController::class, "index"])->name("list.filter");
    });

    // settings
    Route::group(["prefix" => 'settings', "as" => 'settings.'], function () {
        Route::get('/edit', [OptionController::class, "edit"])->name("edit.form");
        Route::patch('/update', [OptionController::class, "update"])->name("edit");
    });
});


// redirect index dashboard
Route::get('dashboard', function () {
    return redirect('/dashboard/index');
});

// check resource 
Route::post('check/{model_name}/{action}', [CheckController::class, "show"])->name("check.entity");
// get resource
Route::post('check/data/get/{callback}', [CheckController::class, "showData"])->name("check.data.get.entity");
// set resource (authorize needed)
Route::post('check/data/set/{callback}', [CheckController::class, "setData"])->name("check.data.set.entity");


#==============> ###### FRONT-END ###### <==============

Route::group(['as' => 'front_end.'], function () {

    // Root URL
    Route::get("/", [FrontEndController::class, "index_home"])->name("home");

    // migrate from web script 
    Route::get("config/db_install", [FrontEndController::class, "db_install"])->name("config.db_install");

    // post and comment
    Route::get("{type}/{id}/{slug?}", [FrontEndController::class, "single_post_type"])->name("post.type.single")->whereNumber("id")->whereIn("type", getAllType("post_type", "slug"));

    // taxonomy
    Route::get("t/{type}/{id}/{slug?}", [FrontEndController::class, "index_taxonomy"])->name("taxonomy.list")->whereNumber("id")->whereIn("type", getAllType("taxonomy", "slug"));

    // form
    Route::get("/show_form/{type}/{id?}", [FrontEndController::class, "show_form"])->name("show_form");

    Route::middleware('throttle:form')->group(function () {
        Route::post("/send_form/{type}", [FrontEndController::class, "send_form"])->name("send_form");
    });
    

    // color mode 
    Route::post("/set_color_mode/{mode}", [FrontEndController::class, "set_color_mode"])->name("set_color_mode");

    // author
    Route::get("crew/{user_id}", [FrontEndController::class, "index_author"])->name("author.list");

    // search
    Route::get("search/{term?}", [FrontEndController::class, "index_search"])->name("search.list");

    // sitemap
    Route::get("/{sitemap_name}.xml", [FrontEndController::class, "index_sitemap"])->name("sitemap.list");

    // export
    Route::get("/export/{type}", [FrontEndController::class, "export"])->name("export");
});