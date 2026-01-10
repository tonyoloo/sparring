<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestEmailController;
use App\Http\Controllers\UssdController;
use App\Http\Controllers\AndroidController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MiniController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('ufscholarshipapplicants', [UssdController::class, 'ufscholarshitpapplicants']);
Route::middleware('auth:sanctum')->post('sendsmscrm', [UssdController::class, 'sendsmscrm']);

Route::middleware('auth:sanctum')->post('ufscholarshipapplicantsids', [UssdController::class, 'ufscholarshipapplicantsids']);

Route::post('testrainedloanai', [ChatController::class, 'testrainedloanai']);
Route::post('handlechat', [ChatController::class, 'handlechat']);
Route::post('verifyotp', [UssdController::class, 'verifyotp']);

Route::post('loadlargedata', [ApiController::class, 'loadLargeData']);



Route::middleware('throttle:100000,1')->group(function () {

    Route::post('login', function (Request $request) {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token]);
    });
    Route::post('updatepaymentdetails', [UserController::class, 'updatepaymentdetails']);



    Route::post('registerUssd', [UssdController::class, 'registerUssd']);
    Route::post('phonenumberchange', [UssdController::class, 'phonenumberchange']);



    Route::post('isServerReachable', [UssdController::class, 'isServerReachable']);
    Route::post('generatesubsequentpdfdatarealtimenew', [UssdController::class, 'generatesubsequentpdfdatarealtimenew']);

   Route::post('phonenumberchange', [UssdController::class, 'phonenumberchange']);

    //phonenumberchangeRoute::post('birthcertificatevalidation', [UssdController::class, 'birthcertificatevalidation']);

    Route::post('uploadanddetectface', [UssdController::class, 'uploadAndDetectFace']);
    Route::post('uploadanddetectfacedlib', [UssdController::class, 'uploadAndDetectFacedlip']);
    
    Route::post('idnumbervalida', [UssdController::class, 'idnumbervalidationai']);
    Route::post('birthcertificatevalidationai', [UssdController::class, 'birthcertificatevalidationai']);
    Route::post('deathcertificatevalidationai', [UssdController::class, 'deathcertificatevalidationai']);




    Route::post('birthcertificatevalidation', [ChatController::class, 'birthcertificatevalidation']);
    Route::post('deathcertificatevalidation', [ChatController::class, 'deathcertificatevalidation']);
    Route::post('idnumbervalidation', [UssdController::class, 'idnumbervalidation']);
    Route::post('idnumberocrvalidation', [ChatController::class, 'idnumberocrvalidation']);



    Route::get('phpinfo', [UssdController::class, 'phpinfo']);


    Route::post('getBilledInfo', [UserController::class, 'getBilledInfo']);
    Route::post('getMiniAccountInfo', [UserController::class, 'getMiniAccountInfo']);
    Route::post('getLoanStatementInfo', [UserController::class, 'getLoanStatementInfo']);
    Route::post('getLoanStatusInfo', [UserController::class, 'getLoanStatusInfo']);
    Route::post('getKuccpsInfo', [UserController::class, 'getKuccpsInfo']);
    Route::post('getBalanceInfo', [UserController::class, 'getBalanceInfo']);
    Route::post('iprsAX', [ApiController::class, 'iprsAX']);
    Route::post('phonevalidateAX', [ApiController::class, 'phonevalidateAX']);
    Route::post('ufbstudentdata', [ApiController::class, 'ufbstudentdata']);
    Route::post('ufbstudentdataussd', [ApiController::class, 'ufbstudentdataussd']);













    Route::get('addinstitutionbulk', [UssdController::class, 'addinstitutionbulk']);
    Route::post('registerussdnoid', [UssdController::class, 'registerussdnoid']);
    Route::post('opendynamicussdproducts', [UssdController::class, 'opendynamicussdproducts']);
    Route::post('productifqualifiedussd', [UssdController::class, 'productifqualifiedussd']);
    Route::post('fetchinstitutionussd', [UssdController::class, 'fetchinstitutionussd']);
    Route::post('productsubmit', [UssdController::class, 'productsubmit']);
    Route::post('productphonevalidate', [UssdController::class, 'productphonevalidate']);
    Route::post('productbankvalidate', [UssdController::class, 'productbankvalidate']);
    Route::post('wrongbank', [UssdController::class, 'wrongbank']);
    Route::post('wronginstitution', [UssdController::class, 'wronginstitution']);
    Route::post('oneandroidapptest', [AndroidController::class, 'oneandroidapptest']);

    Route::post('oneandroidapp', [AndroidController::class, 'oneandroidapp']);
    Route::post('twoandroidappid', [AndroidController::class, 'twoandroidappid']);
    Route::post('threeandroidverifycode', [AndroidController::class, 'threeandroidverifycode']);
    Route::post('androidpersonal', [AndroidController::class, 'androidpersonal']);
    Route::post('androidinstitution', [AndroidController::class, 'androidinstitution']);
    Route::post('androidmail', [AndroidController::class, 'androidmail']);
    Route::post('androidmailtest', [AndroidController::class, 'androidmailtest']);

    Route::post('safaricomgetallocationupkeepbalanceUssdupgrade', [AndroidController::class, 'safaricomgetallocationupkeepbalanceUssdupgrade']);
    Route::post('safaricomauthenticatewithdrawUssdupgrade', [AndroidController::class, 'safaricomauthenticatewithdrawUssdupgrade']);
    Route::post('safaricomgetransactionlistUssdUat', [AndroidController::class, 'safaricomgetransactionlistUssdUat']);
    Route::post('safaricompop', [UssdController::class, 'safaricompop']);

    Route::post('loginussd', [UssdController::class, 'loginussd']);
    Route::post('authorizesimchange', [UssdController::class, 'authorizesimchange']);




    Route::post('registerandroidnoid', [AndroidController::class, 'registerandroidnoid']);
    Route::post('paymentnumber', [AndroidController::class, 'paymentnumber']);
    Route::post('versioncheck', [AndroidController::class, 'versioncheck']);
    Route::post('hashpasswords', [ApiController::class, 'hashpasswords']);
    Route::post('serviceprovidersqueryone', [UssdController::class, 'serviceprovidersqueryone']);

    Route::post('oneminiapp', [MiniController::class, 'oneminiapp']);
    Route::post('threeminiverifycode', [MiniController::class, 'threeminiverifycode']);


    Route::post('subsequentcount', [AndroidController::class, 'subsequentcount']);
    Route::post('subsequentcountemail', [AndroidController::class, 'subsequentcountemail']);

    Route::post('addinstitution', [UserController::class, 'addinstitution']);

    Route::get('generatescholarshipnfmchunk', [UserController::class, 'generatescholarshipnfmchunk']);
    Route::get('pushloanstostagingax', [UserController::class, 'pushloanstostagingax']);
    Route::get('generatescholarshipinstchunk', [UserController::class, 'generatescholarshipinstchunk']);
    Route::get('generatescholarshipnfmportal', [UserController::class, 'generatescholarshipnfmportal']);
    Route::get('updatescholarshipnfmchunk', [UserController::class, 'updatescholarshipnfmchunk']);
    Route::get('updatescholarshipnfminst', [UserController::class, 'updatescholarshipnfminst']);
    Route::get('TestDatasqlserverHEF', [UserController::class, 'TestDatasqlserverHEF']);
    Route::get('TestDatasqlserver', [UserController::class, 'TestDatasqlserver']);

    Route::post('updatesindextoidsloansnfmchunkHEF', [UserController::class, 'updatesindextoidsloansnfmchunkHEF']);

    Route::get('generateschunked', [UserController::class, 'generateschunked']);

    Route::get('generateschunkedreg', [UserController::class, 'generateschunkedreg']);

    Route::get('updatesindextoidsloansnfmchunk', [UserController::class, 'updatesindextoidsloansnfmchunk']);
    Route::post('testhefconnection', [UserController::class, 'testhefconnection']);
    //updateindextoidumberHEF

    Route::post('updateindextoidumberHEF', [UserController::class, 'updateindextoidumberHEF']);
    Route::get('updateindextoidumberHEFGET', [UserController::class, 'updateindextoidumberHEF']);
    Route::post('updateindextoidumberBULK', [UserController::class, 'updateindextoidumberBULK']);

    
    
    
    Route::post('allocationreport', [UserController::class, 'allocationreport']);
    Route::get('allocationreportget', [UserController::class, 'allocationreport']);


    Route::post('allocationreportbulk', [UserController::class, 'allocationreportbulk']);
    Route::post('allocationreportbulkauto', [UserController::class, 'allocationreportbulkauto']);


    Route::get('updatescholsnfmchunkUPSERT', [UserController::class, 'updatescholsnfmchunkUPSERT']);

    Route::post('updatesallocation', [UserController::class, 'updatesallocation']);

    Route::post('reallocation', [UserController::class, 'reallocation']);


    Route::post('minorverify', [UserController::class, 'minorverify']);

    Route::post('clawbackdouble', [UserController::class, 'clawbackdouble']);

    // Location Routes (countries and cities are in web.php for consistency)

    // Test Email Routes
    Route::post('test-email', [TestEmailController::class, 'sendTestEmail']);
    Route::post('test-email-html', [TestEmailController::class, 'sendHtmlTestEmail']);
});

Route::get('institutions', [ApiController::class, 'getInstitutions']);
Route::get('courses', [ApiController::class, 'getCourses']);
Route::get('counties', [ApiController::class, 'getCounties']);
Route::get('towns', [ApiController::class, 'getTowns']);
Route::get('towns-by-county', [ApiController::class, 'getTownsByCounty']);
