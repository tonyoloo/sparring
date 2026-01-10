<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::any('/birthcertificatevalidation', function () {
    return view('birthcertificatevalidation');
});

Route::post('/uploadanddetectface', [
    'uses' => 'App\Http\Controllers\UssdController@uploadAndDetectFace',
    'as' => 'uploadanddetectface',

]);

Route::post('/uploadanddetectidno', [
    'uses' => 'App\Http\Controllers\ChatController@idnumberocrvalidation',
    'as' => 'uploadanddetectidno',

]);

Route::any('/facedetection', function () {
    return view('facedetection');
});

Route::any('/facedetectiondlib', function () {
    return view('facedetectiondlib');
});

Route::any('/idnodetection', function () {
    return view('idnodetection');
});



Route::post('/uploadanddetectfacedlib', [
    'uses' => 'App\Http\Controllers\UssdController@uploadAndDetectFacedlip',
    'as' => 'uploadanddetectfacedlib',

]);

Route::post('/birthcertificatevalidation', [
    'uses' => 'App\Http\Controllers\UssdController@birthcertificatevalidation',
    'as' => 'birthcertificatevalidation',

]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/home', function () {
//     return view('login');
// });
Auth::routes(['verify' => true]);

// Temporary routes for testing registration without email verification
Route::get('register-test', function () {
    return view('auth.register-test');
})->name('register.test.form');
Route::post('register-test', [App\Http\Controllers\Auth\RegisterController::class, 'registerTest'])->name('register.test');

// Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');





Route::group(['middleware' => ['auth']], function () {
    Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.general', 'uses' => 'App\Http\Controllers\ProfileController@general']);
    Route::get('test-email', function () {
        return view('test-email');
    });
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
    Route::get('fighter-profile', ['as' => 'fighter.edit', 'uses' => 'App\Http\Controllers\ProfileController@editFighter']);
    Route::put('profile/fighter', ['as' => 'fighter.update', 'uses' => 'App\Http\Controllers\ProfileController@updateFighter']);
    Route::patch('fighter/photo/{photoId}/primary', ['as' => 'fighter.photo.make-primary', 'uses' => 'App\Http\Controllers\ProfileController@makePhotoPrimary']);
    Route::delete('fighter/photo/{photoId}', ['as' => 'fighter.photo.delete', 'uses' => 'App\Http\Controllers\ProfileController@deletePhoto']);

    // Spar Request Routes
    Route::get('spar-requests', ['as' => 'spar-requests.index', 'uses' => 'App\Http\Controllers\SparRequestController@index']);
    Route::get('spar-request/{fighterId}/create', ['as' => 'spar-request.create', 'uses' => 'App\Http\Controllers\SparRequestController@create']);
    Route::post('spar-request/{fighterId}', ['as' => 'spar-request.store', 'uses' => 'App\Http\Controllers\SparRequestController@store']);
    Route::post('spar-request/{id}/accept', ['as' => 'spar-request.accept', 'uses' => 'App\Http\Controllers\SparRequestController@accept']);
    Route::post('spar-request/{id}/reject', ['as' => 'spar-request.reject', 'uses' => 'App\Http\Controllers\SparRequestController@reject']);
    Route::post('spar-request/{id}/cancel', ['as' => 'spar-request.cancel', 'uses' => 'App\Http\Controllers\SparRequestController@cancel']);
    Route::post('spar-request/{id}/complete', ['as' => 'spar-request.complete', 'uses' => 'App\Http\Controllers\SparRequestController@complete']);
    Route::get('/loanstatus', [
        'uses' => 'App\Http\Controllers\UserController@loanstatus',
        'as' => 'loanstatus',

    ]);
    //CREATES DATA FROM URL


    Route::get('/nfmsupport', [
        'uses' => 'App\Http\Controllers\UserController@nfmsupport',
        'as' => 'nfmsupport',

    ]);
    Route::get('/mobilesupport', [
        'uses' => 'App\Http\Controllers\UserController@mobilesupport',
        'as' => 'mobilesupport',

    ]);
    Route::get('/ussdsupport', [
        'uses' => 'App\Http\Controllers\UserController@ussdsupport',
        'as' => 'ussdsupport',

    ]);


    Route::get('/downloadblocktemplate', [
        'uses' => 'App\Http\Controllers\UserController@downloadblocktemplate',
        'as' => 'downloadblocktemplate',

    ]);

   
  Route::post('/import-block', [
    'uses' => 'App\Http\Controllers\UserController@importblock',
        'as' => 'import-block',

    ]);


    Route::get('/users-list', [
        'uses' => 'App\Http\Controllers\UserController@indexusers',
        'as' => 'users-list',

    ]);
    Route::get('/users-list/{id}/useroles', [
        'uses' => 'App\Http\Controllers\UserController@useroles',
        'as' => 'users-list.useroles',
    ]);
    Route::get('/users-list/{id}/userpermissions', [
        'uses' => 'App\Http\Controllers\UserController@userpermissions',
        'as' => 'users-list.userpermissions',
    ]);
    Route::get('/users-list/{id}/allroles', [
        'uses' => 'App\Http\Controllers\UserController@allroles',
        'as' => 'users-list.allroles',
    ]);
    Route::get('/unblockrecord/{id}', [
        'uses' => 'App\Http\Controllers\UserController@unblockrecords',
        'as' => 'unblockrecord.unblockrecords',
    ]);

    Route::get('/users-list/{id}/allpermission', [
        'uses' => 'App\Http\Controllers\UserController@allpermission',
        'as' => 'users-list.allpermission',
    ]);

    Route::post('/updatedpermission-store', [
        'uses' => 'App\Http\Controllers\UserController@storepermission',
        'as' => 'updatedpermission-store',

    ]);

    Route::post('/updatedrole-store', [
        'uses' => 'App\Http\Controllers\UserController@storerole',
        'as' => 'updatedrole-store',

    ]);


    Route::get('/surepaysupport', [
        'uses' => 'App\Http\Controllers\UserController@surepaysupport',
        'as' => 'surepaysupport',

    ]);






    Route::get('/adminsupport', [
        'uses' => 'App\Http\Controllers\UserController@adminsupport',
        'as' => 'adminsupport',

    ]);
    Route::post('/axidform', [
        'uses' => 'App\Http\Controllers\UserController@fetchaxuser',
        'as' => 'axidform',

    ]);

    Route::post('/refreshuser', [
        'uses' => 'App\Http\Controllers\UserController@refreshuser',
        'as' => 'refreshuser',

    ]);

    Route::post('/idnumberiprs', [
        'uses' => 'App\Http\Controllers\UserController@idnumberiprs',
        'as' => 'idnumberiprs',

    ]);
    Route::post('/idnumberiprsmaisha', [
        'uses' => 'App\Http\Controllers\UserController@idnumberiprsmaisha',
        'as' => 'idnumberiprsmaisha',

    ]);
    Route::post('/minorform', [
        'uses' => 'App\Http\Controllers\UserController@minorform',
        'as' => 'minorform',

    ]);

    Route::post('/mpesaidpost', [
        'uses' => 'App\Http\Controllers\UserController@mpesaidform',
        'as' => 'mpesaidpost',

    ]);


    Route::post('/ifqualifiedform', [
        'uses' => 'App\Http\Controllers\UserController@ifqualifiedform',
        'as' => 'ifqualifiedform',

    ]);
    Route::post('/addinstitution', [
        'uses' => 'App\Http\Controllers\UserController@addinstitution',
        'as' => 'addinstitution',

    ]);

    Route::post('/addlateapplicant', [
        'uses' => 'App\Http\Controllers\UserController@addlateapplicant',
        'as' => 'addlateapplicant',

    ]);

    Route::post('/addinstitutionextra', [
        'uses' => 'App\Http\Controllers\UserController@addinstitutionextra',
        'as' => 'addinstitutionextra',

    ]);

    Route::post('/minorverify', [
        'uses' => 'App\Http\Controllers\UserController@minorverify',
        'as' => 'minorverify',

    ]);

    Route::post('/schidformpost', [
        'uses' => 'App\Http\Controllers\UserController@schidformpost',
        'as' => 'schidformpost',

    ]);




    // Route::post('/deleteandroiduser', [
    //     'uses' => 'App\Http\Controllers\UserController@deleteandroiduser',
    //     'as' => 'deleteandroiduser',

    // ]);

    Route::get('/deleteandroiduser/{id}', [
        'uses' => 'App\Http\Controllers\UserController@deleteandroiduser',
        'as' => 'deleteandroiduser.deleteandroiduser',
    ]);

    Route::post('/addplatform', [
        'uses' => 'App\Http\Controllers\UserController@addplatform',
        'as' => 'addplatform',

    ]);


    Route::post('/loanblocked', [
        'uses' => 'App\Http\Controllers\UserController@loanblocked',
        'as' => 'loanblocked',

    ]);



    Route::post('/datatablepaymentrealocation', [
        'uses' => 'App\Http\Controllers\UserController@datatablepaymentrealocation',
        'as' => 'datatablepaymentrealocation',

    ]);


    Route::post('/phonenumberverify', [
        'uses' => 'App\Http\Controllers\UserController@phonenumberverify',
        'as' => 'phonenumberverify',

    ]);

    Route::post('/ussdverify', [
        'uses' => 'App\Http\Controllers\UserController@ussdverify',
        'as' => 'ussdverify',

    ]);

    Route::post('/updateussd', [
        'uses' => 'App\Http\Controllers\UserController@updateussd',
        'as' => 'updateussd',

    ]);

    Route::post('/updateussdnumber', [
        'uses' => 'App\Http\Controllers\UserController@updateussdnumber',
        'as' => 'updateussdnumber',

    ]);

    Route::post('/searchpaymobi', [
        'uses' => 'App\Http\Controllers\UserController@searchpaymobi',
        'as' => 'searchpaymobi',

    ]);
    //addplatform

    Route::post('/idnumbersearchpaymobi', [
        'uses' => 'App\Http\Controllers\UserController@idnumbersearchpaymobi',
        'as' => 'idnumbersearchpaymobi',

    ]);

    Route::post('/withdrawstatement', [
        'uses' => 'App\Http\Controllers\UserController@withdrawstatement',
        'as' => 'withdrawstatement',

    ]);



    Route::post('/updatesallocation', [
        'uses' => 'App\Http\Controllers\UserController@updatesallocation',
        'as' => 'updatesallocation',

    ]);


    Route::post('/bulkallocationreport', [
        'uses' => 'App\Http\Controllers\UserController@bulkallocationreport',
        'as' => 'bulkallocationreport',

    ]);

    Route::get('/admins-list', [
        'uses' => 'App\Http\Controllers\UserController@admins-list',
        'as' => 'admins-list',

    ]);

    //addplatform


});

// Directory Routes (Public Access - No Authentication Required)
Route::get('/directory', [
    'uses' => 'App\Http\Controllers\DirectoryController@index',
    'as' => 'directory',
]);

Route::get('/professionals', [
    'uses' => 'App\Http\Controllers\DirectoryController@professionals',
    'as' => 'professionals',
]);

Route::get('/gyms', [
    'uses' => 'App\Http\Controllers\DirectoryController@gyms',
    'as' => 'gyms',
]);

Route::get('/fighter/{id}', [
    'uses' => 'App\Http\Controllers\DirectoryController@show',
    'as' => 'fighter.show',
]);

// Location Routes (for countries and cities)
Route::get('/api/countries', [
    'uses' => 'App\Http\Controllers\LocationController@getCountries',
    'as' => 'api.countries',
]);

Route::get('/api/cities', [
    'uses' => 'App\Http\Controllers\LocationController@getCities',
    'as' => 'api.cities',
]);

Route::get('/api/regions', [
    'uses' => 'App\Http\Controllers\LocationController@getRegions',
    'as' => 'api.regions',
]);

Route::post('/api/seed-locations', [
    'uses' => 'App\Http\Controllers\LocationController@seedLocations',
    'as' => 'api.seed-locations',
]);

Route::get('/api/disciplines', [
    'uses' => 'App\Http\Controllers\LocationController@getDisciplines',
    'as' => 'api.disciplines',
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('{page}', ['as' => 'page.index', 'uses' => 'App\Http\Controllers\PageController@index']);
});

Route::post('/submit-student', [StudentController::class, 'store']);
Route::post('/studentdata', [StudentController::class, 'index']);
