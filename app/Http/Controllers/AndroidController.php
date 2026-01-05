<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Mail\ApplicationCountMail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\BeautifulMail;
use App\Mail\CustoMails;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\USSDController;

class AndroidController extends Controller
{




    function safaricomgetallocationupkeepbalanceUssdupgrade(Request $request, ApiController $apiController)
    {




        $rules = [
            'phone' => 'required|string|min:5',
            'idno' => 'required',



        ];



        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correct phone number'


            ]);
        }






        $post = [
            'phone' => $request->phone,
            'idno' => $request->idno,
            'version' => $request->version,
            'gsf' => $request->gsf,


        ];




        $phoneNumber = $request->phone;
        $cellphone = $request->phone;
        $gsf = $request->gsf;
        $idno = $request->idno;
        $version = $request->version;



        // dd($request);
        if ($version == '') {
            $ussdController = new USSDController();

            $checkifauthorized = $ussdController->checkifauthorized($idno);

            // dd($checkifauthorized );

            if ($checkifauthorized == 1) {
            } else {

                return 'forbidden';
            }
        }



        $stringpost = json_encode($post);

        $logs =  DB::table('android_log')->insert(['message' => json_encode($post)]);

        $idno = str_replace("'", '', str_replace('"', '', $idno));
        $cellphone = str_replace("'", '', str_replace('"', '', $cellphone));
        $gsf = str_replace("'", '', str_replace('"', '', $gsf));
        $country_code = '254';
        $n = strlen($cellphone);
        if ($n < 11) {
            $cellphone = substr_replace($cellphone, $country_code, 0, ($cellphone[0] == '0'));
        }

        //$logs = $this->db->query("INSERT INTO mini_log(message)VALUES ('$stringpost')");


        $logs =  DB::table('mini_log')->insert(['message' => json_encode($post)]);



        if ($version > 1) {

            $data = DB::table('tbl_users_mobile')
                ->where('cell_phone', $cellphone)
                ->where('cell_verified', '1')
                ->where('gsf', $gsf)
                ->pluck('idn')
                ->toArray();

            if (empty($data)) {
                return response()->json([
                    'message' => 'fail',

                    'displaymessage' => 'Account locked. ID number: ' . $idno . ' due to a previous account details mismatch. Email contactcentre@helb.co.ke'
                ]);
            }
        }

        if (empty($phoneNumber) || $phoneNumber == '') {
            $phoneNumber = 1; //phonenumber 
        }
        $country_code = '254';
        $n = strlen($phoneNumber);
        if ($n < 11)
            $phoneNumber = substr_replace($phoneNumber, $country_code, 0, ($phoneNumber[0] == '0'));



        $requestData = [

            'status' => 'Active',
            'beneficiaryIdentifier' => $phoneNumber,
            'fundType' => 'Upkeep'

        ];

        $requestData = json_encode($requestData);

        $accessToken = DB::table('access_token')->value('access_token');

        $url = env('SAFCOMALLOCATION_URL');



        // Convert the array to a JSON string

        $getallocation = $apiController->safaricomsurepay($accessToken, $requestData, $url);
        //dd($getallocation);

        //$getbeneficiary =  $data->result[0]->accesstoken;
        //dd($getbeneficiary);

        $resCodes = $getallocation->resCode ?? null;;




        if ($resCodes == '0') {
            $resMsg = $getallocation->resMsg;


            if ($resMsg == 'success') {
                $result = $getallocation->result;
                $allocation = $result->allocation;
                $externalIdno = $idno;

                $matchFound = true;
                $batchNumbers = [];
                $remainingAmounts = [];
                $allocationIds = [];
                $loanserialnos = [];
                $combinedCounts = [];

                foreach ($allocation as $item) {
                    if (isset($item->dynamicFields)) {
                        $dynamicFields = $item->dynamicFields;
                        // Process $dynamicFields here
                    } else {

                        // dd($item->dynamicFields);
                        echo json_encode([
                            'message' => 'success',
                            'status' => '1',
                            'displaymessage' => 'Insufficient balance. Please get in touch with our customer care team',
                            'remainingAmount' => '0',
                            'serviceId' => '0'
                        ]);
                        die();
                    }



                    //$dynamicFields = $item->dynamicFields;
                    $idno = $dynamicFields->idno ?? null;

                    //  $idno = $dynamicFields['idno'] ;
                    //dd($idno);


                    if ($idno !== $externalIdno) {
                        $matchFound = false;
                    }

                    $batchNumbers[] = $dynamicFields->batchno;
                    $remainingAmounts[] = $item->remainingAmount;
                    $allocationIds[] = $item->allocationId;
                    $loanserialnos[] = $dynamicFields->loanserialno;

                    // dd($dynamicFields->loanserialno);

                    // Create a unique key using batch number, remaining amount, and loan serial number
                    $combinedKey = $dynamicFields->batchno . '|' . $item->remainingAmount . '|' . $dynamicFields->loanserialno;

                    if (!isset($combinedCounts[$combinedKey])) {
                        $combinedCounts[$combinedKey] = 0;
                    }
                    $combinedCounts[$combinedKey]++;
                }

                if ($matchFound) {
                    // Find duplicate entries based on batch number, remaining amount, and loan serial number
                    $duplicateEntries = array_filter($combinedCounts, function ($count) {
                        return $count > 1;
                    });

                    if (!empty($duplicateEntries)) {
                        foreach ($duplicateEntries as $combinedKey => $count) {
                            list($batchNo, $remainingAmount, $loanSerialNo) = explode('|', $combinedKey);

                            echo json_encode([
                                'message' => 'success',
                                'status' => '1',
                                //  'displaymessage' => 'Duplicate batch number, remaining amount, and loan serial number found. Account locked.',
                                'displaymessage' => 'Account locked. ID number: ' . $idno . ' due to a previous account details mismatch. Email contactcentre@helb.co.ke',

                                'remainingAmount' => '0',
                                'serviceId' => '0',
                                'duplicateBatchNumber' => $batchNo,
                                'duplicateRemainingAmount' => $remainingAmount,
                                'duplicateLoanSerialNo' => $loanSerialNo
                            ]);
                            die();
                        }
                    }

                    $curl_post_data = [
                        'beneficiaryIdentifier' => $phoneNumber,
                        'financierId' => 'BCH001',
                    ];

                    $data_string = json_encode($curl_post_data);
                    //$url = env('SAFARICOMAGGREGATEALLOCATION');
                    $url = config('app.aggregate_url');

                    $aggregateallocation = $apiController->safaricomsurepay($accessToken, $data_string, $url);

                    $resCodes = $aggregateallocation->resCode ?? null;

                    if ($resCodes == '0') {
                        $resMsg = $aggregateallocation->resMsg;

                        if ($resMsg == 'success') {
                            $result = $aggregateallocation->result;
                            $balanceAccountList = $result->balanceAccountList;

                            if (empty($balanceAccountList)) {
                                echo json_encode([
                                    'message' => 'success',
                                    'status' => '1',
                                    'displaymessage' => 'Your balance is insufficient for withdrawal',
                                    'remainingAmount' => '0',
                                    'serviceId' => '0'
                                ]);
                            } else {
                                $balanceAccount = $balanceAccountList[0];
                                $serviceId = $balanceAccount->serviceId;
                                $totalAllocation = $balanceAccount->totalAllocation;

                                if ($totalAllocation <= 0) {
                                    echo json_encode([
                                        'message' => 'success',
                                        'status' => '1',
                                        'displaymessage' => 'Your balance is insufficient for withdrawal',
                                        'remainingAmount' => $totalAllocation,
                                        'serviceId' => $serviceId
                                    ]);
                                } else {
                                    echo json_encode([
                                        'message' => 'success',
                                        'status' => '2',
                                        'displaymessage' => 'Your balance is ' . $totalAllocation . ', withdrawable amount ' . $totalAllocation . '/=KES, Enter the amount to withdraw',
                                        'remainingAmount' => $totalAllocation,
                                        'serviceId' => $serviceId
                                    ]);
                                }
                            }
                        }
                    } else {
                        echo json_encode([
                            'message' => 'success',
                            'status' => '1',
                            'displaymessage' => 'Insufficient balance. Please get in touch with our customer care team',
                            'remainingAmount' => '0',
                            'serviceId' => '0'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'message' => 'success',
                        'status' => '1',
                        'displaymessage' => 'Account locked. ID number: ' . $idno . ' due to a previous account details mismatch. Email contactcentre@helb.co.ke',
                        'remainingAmount' => '0',
                        'serviceId' => '0'
                    ]);
                }
            }
        } else {
            echo json_encode(array('message' => 'success', 'status' => '1', 'displaymessage' => 'Insufficient balance.Please get in touch with our customer care team', 'remainingAmount' => '0', 'serviceId' => '0'));
        }
    }


    function safaricomgetransactionlistUssdUat(Request $request, ApiController $apiController)
    {

        //dd($request);

        $rules = [
            'phone' => 'required|string|min:5',
            'idno' => 'required',



        ];



        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correct phone number'


            ]);
        }






        $post = [
            'phone' => $request->phone,
            'idno' => $request->idno,
            'version' => $request->version,
            'gsf' => $request->gsf,


        ];




        $phoneNumber = $request->phone;
        $cellphone = $request->phone;
        $gsf = $request->gsf;
        $idno = $request->idno;
        $version = $request->version;




        $stringpost = json_encode($post);

        $logs =  DB::table('android_log')->insert(['message' => json_encode($post)]);

        $idno = str_replace("'", '', str_replace('"', '', $idno));
        $cellphone = str_replace("'", '', str_replace('"', '', $cellphone));
        $gsf = str_replace("'", '', str_replace('"', '', $gsf));
        $country_code = '254';
        $n = strlen($cellphone);
        if ($n < 11) {
            $cellphone = substr_replace($cellphone, $country_code, 0, ($cellphone[0] == '0'));
        }

        //$logs = $this->db->query("INSERT INTO mini_log(message)VALUES ('$stringpost')");


        $logs =  DB::table('mini_log')->insert(['message' => json_encode($post)]);





        if (empty($phoneNumber) || $phoneNumber == '') {
            $phoneNumber = 1; //phonenumber 
        }
        $country_code = '254';
        $n = strlen($phoneNumber);
        if ($n < 11)
            $phoneNumber = substr_replace($phoneNumber, $country_code, 0, ($phoneNumber[0] == '0'));



        $requestData = [


            'status' => 'Completed',
            'beneficiaryIdentifier' => $phoneNumber,

        ];

        $requestData = json_encode($requestData);

        $accessToken = DB::table('access_token')->value('access_token');

        $url = env('SAFCOMWITHDRAW_URL');



        // Convert the array to a JSON string

        $getransactionlist = $apiController->safaricomsurepay($accessToken, $requestData, $url);
        //dd($getallocation);

        //$getbeneficiary =  $data->result[0]->accesstoken;
        //dd($getbeneficiary);

        $resCodes = $getransactionlist->resCode ?? null;;




        if ($resCodes == '0') {
            $resMsg = $getransactionlist->resMsg;

            if ($resMsg == 'success') {

                $result = $getransactionlist->result;
                $transactionList = $result->transactionList;
                echo json_encode(array('message' => 'success', 'transactionList' => $transactionList));
            }
        } else {
            echo json_encode(array('message' => 'fail', 'error' => 'an error occured`1', 'code' => $resCodes));
        }
    }






    public function safaricomauthenticatewithdrawUssdupgrade(Request $request, ApiController $apiController)
    {


        $rules = [
            'serviceId' => 'required|string|min:1',
            'phone' => 'required|string|min:5',
            'amount' => 'required',
            'idno' => 'required',



        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correct phone number'


            ]);
        }

        $serviceId = $request->serviceId;
        $phoneNumber = $request->phone;
        $externalOrderId = $request->idno;
        $amount = $request->amount;
        $receiverIdentifier = $request->phone;
        $version = $request->version;


        $post = [
            'serviceId' => $request->serviceId,
            'phone' => $request->phone,
            'idno' => $request->idno,
            'amount' => $request->amount,
            'receiverIdentifier' => $request->phone,
            'version' => $request->version,
        ];

        DB::table('mini_log')->insert(['message' => json_encode($post)]);


        $externalOrderId = str_replace(' ', '', trim(str_replace(['"', "'"], '', $externalOrderId)));
        $phoneNumber = str_replace(' ', '', trim(str_replace(['"', "'"], '', $phoneNumber)));


        // Format phone number
        if (strlen($phoneNumber) < 11) {
            $phoneNumber = '254' . ltrim($phoneNumber, '0');
        }

        $idnumber = $externalOrderId;

        if ($version < 1) {
            $blocked = DB::table('tbl_blocked_nfm')
                ->where('idno', $externalOrderId)
                ->where('reason', 'double allocation')

                ->where('status', 'blocked')
                ->exists();

            if ($blocked) {
                DB::table('mini_log')->insert(['message' => json_encode($post) . 'notallowed']);



                // $mcode = "[#] HELB:$code is your verification code. 2aUYAkIzflK";
                $mcode = 'Your transfer to MPESA has failed due to a previous allocation account details mismatch. Email contactcentre@helb.co.ke';


                $action = 'sendphoneverificationCode';
                $arr = [
                    'recipient' => $phoneNumber,
                    'verificationcode' => $mcode,
                    'msg_priority' => '204',
                    'category' => '397'
                ];

                $result = $apiController->datapull($action, $arr);

                //dd($arr);







                return response()->json([
                    'message' => 'fail',
                    'displaymessage' => 'Your transfer to MPESA has failed due to a previous allocation account details mismatch. Email contactcentre@helb.co.ke'
                ]);
            }

            $walletReferenceData = ['source' => 'ussd'];
            $accessToken = DB::table('access_token')->value('access_token');

            $payload = [
                'serviceId' => $serviceId,
                'financierId' => 'BCH001',
                'amount' => $amount,
                'commandId' => 'Upkeep',
                'beneficiaryIdentifier' => $phoneNumber,
                'requester' => $phoneNumber,
                'walletReferenceData' => $walletReferenceData,
                'externalOrderId' => $externalOrderId,
                'callbackUrl' =>  env('latestversion')
            ];
            $SAFARICOMINITIATEAMOUNT = config('app.safaricominitiateamount_url');

            $response = Http::withHeaders([
                'access-token' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post($SAFARICOMINITIATEAMOUNT, $payload);

            $responseBody = $response->json();

            if ($responseBody['resCode'] == '0' && $responseBody['result']['responseCode'] == '0') {
                return response()->json([
                    'message' => 'success',
                    'displaymessage' => 'Your transfer to MPESA is being processed. Enter MPESA PIN to withdraw.'
                ]);
            }

            DB::table('mini_log')->insert(['message' => json_encode($post) . json_encode($responseBody)]);

            return response()->json([
                'message' => 'fail',
                'displaymessage' => 'Your transfer to MPESA has failed.',
                'error' => 'An error occurred',
                'code' => $responseBody
            ]);
        } else {

            $apiController = new ApiController();
            $userController = new   UserController();

            $result = $userController->kycregister($idnumber, $phoneNumber, $apiController);

            $verified = json_decode($result)->verified;
            if ($verified == 'yes') {
                return $this->safaricomauthenticatewithdrawmobileapp($phoneNumber, $serviceId, $externalOrderId, $amount, $receiverIdentifier, $version);
            }

            DB::table('mini_log')->insert(['message' => json_encode($post)]);

            return response()->json([
                'message' => 'fail',
                'displaymessage' => 'Kindly dial *642# to withdraw.',
                'error' => 'An error occurred'
            ]);
        }
    }




    public function  safaricomauthenticatewithdrawmobileapp($phoneNumber, $serviceId, $externalOrderId, $amount, $receiverIdentifier, $version)
    {




        $post = [
            $phoneNumber,
            $serviceId,
            $externalOrderId,
            $amount,
            $receiverIdentifier,
            $version,
        ];

        DB::table('mini_log')->insert(['message' => json_encode($post)]);


        $externalOrderId = str_replace(' ', '', trim(str_replace(['"', "'"], '', $externalOrderId)));
        $phoneNumber = str_replace(' ', '', trim(str_replace(['"', "'"], '', $phoneNumber)));


        // Format phone number
        if (strlen($phoneNumber) < 11) {
            $phoneNumber = '254' . ltrim($phoneNumber, '0');
        }


        $blocked = DB::table('tbl_blocked_nfm')
            ->where('idno', $externalOrderId)
            ->where('status', 'blocked')
            ->exists();

        if ($blocked) {
            DB::table('mini_log')->insert(['message' => json_encode($post) . 'notallowed']);
            return response()->json([
                'message' => 'fail',
                'displaymessage' => 'Your transfer to MPESA has failed due to a previous allocation account details mismatch. Email contactcentre@helb.co.ke'
            ]);
        }

        $walletReferenceData = ['source' => 'mobileapp'];
        $accessToken = DB::table('access_token')->value('access_token');

        $payload = [
            'serviceId' => $serviceId,
            'financierId' => 'BCH001',
            'amount' => $amount,
            'commandId' => 'Upkeep',
            'beneficiaryIdentifier' => $phoneNumber,
            'requester' => $phoneNumber,
            'walletReferenceData' => $walletReferenceData,
            'externalOrderId' => $externalOrderId,
            'callbackUrl' =>  env('latestversion')
        ];

        $SAFARICOMINITIATEAMOUNT = config('app.safaricominitiateamount_url');

        $response = Http::withHeaders([
            'access-token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post($SAFARICOMINITIATEAMOUNT, $payload);

        $responseBody = $response->json();

        if ($responseBody['resCode'] == '0' && $responseBody['result']['responseCode'] == '0') {
            return response()->json([
                'message' => 'success',
                'displaymessage' => 'Your transfer to MPESA is being processed. Enter MPESA PIN to withdraw.'
            ]);
        }

        DB::table('mini_log')->insert(['message' => json_encode($post) . json_encode($responseBody)]);

        return response()->json([
            'message' => 'fail',
            'displaymessage' => 'Your transfer to MPESA has failed.',
            'error' => 'An error occurred',
            'code' => $responseBody
        ]);
    }










    public function oneandroidapptest(Request $request, ApiController $apiController)
    {





        $rules = [
            'phonenumber' => 'required|string|min:10',
            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correct phone number'


            ]);
        }

        $namba = $request->input('phonenumber');
        $googletester = strtolower($namba);

        $gsf = $request->input('gsf');
        $serial = $request->input('serial');
        $networkused = $request->input('networkused');
        $simoperatorname = $request->input('simoperatorname');
        $androidid = $request->input('androidid');
        $imei = $request->input('imei');
        $uuid = $request->input('uuid');
        $deviceinfo = $request->input('deviceinfo');
        $appversion = $request->input('appversion');
        $telco = $request->input('telco');
        $maxi = 4;
        $latestversion = env('latestversion');
        $cell_verified = '1';


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'fail',
                'message' => $message

            ]);
        }


        if ($googletester == 'googletester') {
            // Handle the case where any value is null

            return response()->json([
                'idno' => '66666666',
                'name' => 'googletester',
                'result' => 'success',
                'message' => 'Kindly proceed verification code : 1111'

            ]);
        }

        $cacheKey = "tbl_users_nfm_{$namba}_{$cell_verified}";

        $user = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified) {
            return DB::table('tbl_users_nfm')
                ->where('cell_phone', $namba)
                ->where('cell_verified', $cell_verified)

                ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
        });




        //dd($datatwo);

        if (!empty($user)) {
            $email_add = $user->email_add;
            $first_name = $user->first_name;
            $idno = $user->id_no;
            $code = $apiController->uniqueStr2(4);

            $insertmobileandroid = array(
                'idno' => $idno,
                'smscount' => '1',
                'appversion' => $appversion,
                'simoperatorname' => $simoperatorname,
                'networkused' => $networkused,
                'cell_verified' => "0",
                'phone_activation_code' => $code,
                'cell_phone' => $namba,
                'gsf' => $gsf,
                'serial' => $serial,
                'android_id' => $androidid,
                'imei' => $imei,
                'uuid' => $uuid,
                'deviceinfo' => $deviceinfo
            );







            $hiddenemail = $apiController->maskEmailAddress($email_add);

            $hiddenamba = str_replace(substr($namba, 3, 6), $apiController->cross($namba), $namba);
            //dd($hiddenamba);



            $mcode = "[#] HELB:$code is your verification code. 2aUYAkIzflK";


            $action = 'sendphoneverificationCode';
            $arr = [
                'recipient' => $namba,
                'verificationcode' => $mcode,
                'msg_priority' => '204',
                'category' => '391'
            ];



            $salutation = 'Dear ' . $first_name . ',';

            $message = $salutation . $mcode;
            $msg = $salutation . $mcode;
            $sbject = 'OTP VERIFICATION';


            $notifydet = array(
                'subject' => 'OTP VERIFICATION',
                'salutation' => $salutation,
                'emailmessage' => $mcode,
                'name' => $first_name,
                'demail' => $email_add,
                'message' => $message
            );
            $notifydet = (object) $notifydet;



            $query = DB::table('tbl_users_mobile')
                ->select('idno', 'cell_verified', 'gsf', 'time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))
                ->where('cell_phone', $namba)
                ->orderBy('time_added', 'desc')
                ->first();






            if ($query) {

                $previdno = $query->idno;

                //dd($previdno);
                $prevgsf = $query->gsf;
                $prevcell_verified = $query->cell_verified;

                if ($prevcell_verified == '1') {

                    $comparedresult = ($previdno . $prevgsf != $idno . $gsf);


                    // dd($comparedresult);
                    if ($comparedresult) {
                        return json_encode(
                            array(
                                'idno' => $idno,
                                'name' => $first_name,
                                'result' => 'fail',
                                'message' => 'The national ID is activated on another Phone device . Kindly contact customer care'
                            )
                        );
                    }
                }






                try {
                    // Attempt to insert or update
                    $added = DB::table('tbl_users_mobile')->updateOrInsert(
                        ['idno' => $idno],
                        $insertmobileandroid
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if the exception is due to an integrity constraint violation
                    if ($e->getCode() == '23000') {
                        // Delete the conflicting record based on the constraint
                        DB::table('tbl_users_mobile')
                            ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                            ->orWhere('cell_phone', $insertmobileandroid['cell_phone']) // Another possible unique constraint
                            ->delete();

                        // Attempt to insert again
                        $added = DB::table('tbl_users_mobile')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobileandroid
                        );
                    } else {
                        // Rethrow the exception if it's a different error
                        throw $e;
                    }
                }


                if ($query->diff > 120) {

                    // Assuming callapion96 is a method in AuthModel and it returns a response
                    $result = $apiController->datapull($action, $arr);
                    try {
                        $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
                    } catch (\Exception $e) {
                        // Log the exception for debugging purposes
                        Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                        // Send a fallback plain text email
                        $subject = $notifydet->subject;
                        try {

                            Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                                $message->to($email_add)
                                    ->subject($subject);
                            });
                        } catch (\Exception $e) {
                            // Log the exception for debugging purposes
                            Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                        }
                    }

                    return json_encode(
                        array(
                            'idno' => $idno,
                            'name' => $first_name,
                            'result' => 'success',
                            'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
                        )
                    );
                }

                $subject = $notifydet->subject;

                Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                    $message->to($email_add)
                        ->subject($subject);
                });




                return json_encode(
                    array(
                        'idno' => $idno,
                        'name' => $first_name,
                        'result' => 'success',
                        'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
                    )
                );
            } else {


                try {
                    // Attempt to insert or update
                    $added = DB::table('tbl_users_mobile')->updateOrInsert(
                        ['idno' => $idno],
                        $insertmobileandroid
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if the exception is due to an integrity constraint violation
                    if ($e->getCode() == '23000') {
                        // Delete the conflicting record based on the constraint
                        DB::table('tbl_users_mobile')
                            ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                            ->orWhere('cell_phone', $insertmobileandroid['cell_phone']) // Another possible unique constraint
                            ->delete();

                        // Attempt to insert again
                        $added = DB::table('tbl_users_mobile')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobileandroid
                        );
                    } else {
                        // Rethrow the exception if it's a different error
                        throw $e;
                    }
                }

                $result = $apiController->datapull($action, $arr);
                try {
                    $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
                } catch (\Exception $e) {
                    // Log the exception for debugging purposes
                    Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                    // Send a fallback plain text email
                    $subject = $notifydet->subject;
                    try {

                        Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                            $message->to($email_add)
                                ->subject($subject);
                        });
                    } catch (\Exception $e) {
                        // Log the exception for debugging purposes
                        Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                    }
                }


                //
                return json_encode(
                    array(
                        'idno' => $idno,
                        'name' => $first_name,
                        'result' => 'success',
                        'message' => 'Kindly proceed verification code sent to :' . $hiddenemail
                    )
                );


                ///
            }
        } else {

            return json_encode(
                array(

                    'result' => 'error',
                    'message' => 'c We could not find your details. Please continue to register.'
                )
            );
        }
    }


    public function oneandroidapp(Request $request, ApiController $apiController)
    {


        // return response()->json([

        //     'result' => 'fail',
        //     'message' => 'system under maintenance retry after 24hrs'

        // ]);


        $rules = [
            'phonenumber' => 'required|string|min:10',
            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correct phone number'


            ]);
        }

        $namba = $request->input('phonenumber');
        $namba = str_replace(' ', '', trim(str_replace(['"', "'"], '', $namba)));

        $googletester = strtolower($namba);

        $gsf = $request->input('gsf');
        $serial = $request->input('serial');
        $networkused = $request->input('networkused');
        $simoperatorname = $request->input('simoperatorname');
        $androidid = $request->input('androidid');
        $imei = $request->input('imei');
        $uuid = $request->input('uuid');
        $deviceinfo = $request->input('deviceinfo');
        $appversion = $request->input('appversion');
        $telco = $request->input('telco');
        $maxi = 4;
        $latestversion = env('latestversion');
        $cell_verified = '1';


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'fail',
                'message' => $message

            ]);
        }


        if ($googletester == 'googletester') {
            // Handle the case where any value is null

            return response()->json([
                'idno' => '66666666',
                'name' => 'googletester',
                'result' => 'success',
                'message' => 'Kindly proceed verification code : 1111'

            ]);
        }

        $namba = preg_replace('/\D/', '', $namba);

        $cacheKey = "tbl_users_nfm_{$namba}_{$cell_verified}";

        $user = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified) {
            return DB::table('tbl_users_nfm')
                ->where('cell_phone', $namba)
                ->where('cell_verified', $cell_verified)

                ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
        });




        //dd($datatwo);

        if (!empty($user)) {
            $email_add = $user->email_add;
            $first_name = $user->first_name;
            $idno = $user->id_no;
            $code = $apiController->uniqueStr2(4);

            $insertmobileandroid = array(
                'idno' => $idno,
                'smscount' => '1',
                'appversion' => $appversion,
                'simoperatorname' => $simoperatorname,
                'networkused' => $networkused,
                'cell_verified' => "0",
                'phone_activation_code' => $code,
                'cell_phone' => $namba,
                'gsf' => $gsf,
                'serial' => $serial,
                'android_id' => $androidid,
                'imei' => $imei,
                'uuid' => $uuid,
                'deviceinfo' => $deviceinfo
            );







            $hiddenemail = $apiController->maskEmailAddress($email_add);

            $hiddenamba = str_replace(substr($namba, 3, 6), $apiController->cross($namba), $namba);
            //dd($hiddenamba);



            $mcode = "[#] HELB:$code is your verification code. 2aUYAkIzflK";


            $action = 'sendphoneverificationCode';
            $arr = [
                'recipient' => $namba,
                'verificationcode' => $mcode,
                'msg_priority' => '204',
                'category' => '391'
            ];



            $salutation = 'Dear ' . $first_name . ',';

            $message = $salutation . $mcode;
            $notifydet = array(
                'subject' => 'OTP VERIFICATION',
                'salutation' => $salutation,
                'emailmessage' => $mcode,
                'name' => $first_name,
                'demail' => $email_add,
                'message' => $message
            );
            // $result = $apiController->datapull($action, $arr);

            $notifydet = (object) $notifydet;



            $query = DB::table('tbl_users_mobile')
                ->select('smscount', 'idno', 'cell_verified', 'gsf', 'time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))
                ->where('cell_phone', $namba)
                ->orderBy('time_added', 'desc')
                ->first();


            // if ($query->count() <= 10) {
            //     $query = $query->first();
            // } else {
            //     $result = null;
            // }



            if ($query) {

                $previdno = $query->idno;
                $allowedcount =  (int)($query->smscount);

                if ($allowedcount > 3) {

                    return json_encode([
                        'idno'   => $idno,
                        'name'   => $first_name,
                        'result' => 'fail',
                        'message' => 'You\'ve exhausted the number of allowed SMS. Kindly contact customer care'
                    ]);
                }

                //dd($previdno);
                $prevgsf = $query->gsf;
                $prevcell_verified = $query->cell_verified;

                if ($prevcell_verified == '1') {

                    $comparedresult = ($previdno . $prevgsf != $idno . $gsf);


                    // dd($comparedresult);
                    if ($comparedresult) {
                        return json_encode(
                            array(
                                'idno' => $idno,
                                'name' => $first_name,
                                'result' => 'fail',
                                'message' => 'The national ID is activated on another Phone device . Kindly contact customer care'
                            )
                        );
                    }
                }





                //  dd($query->diff );
                if ($query->diff > 120) {
                    $count = $allowedcount + 1;

                    // Assuming callapion96 is a method in AuthModel and it returns a response
                    $result = $apiController->datapull($action, $arr);


                    try {
                        $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
                    } catch (\Exception $e) {
                        // Log the exception for debugging purposes
                        Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                        // Send a fallback plain text email
                        $subject = $notifydet->subject;
                        try {

                            Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                                $message->to($email_add)
                                    ->subject($subject);
                            });
                        } catch (\Exception $e) {
                            // Log the exception for debugging purposes
                            Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                        }
                    }
                    try {
                        // Attempt to insert or update
                        $added = DB::table('tbl_users_mobile')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobileandroid
                        );
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Check if the exception is due to an integrity constraint violation
                        if ($e->getCode() == '23000') {
                            // Delete the conflicting record based on the constraint
                            DB::table('tbl_users_mobile')
                                ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                                ->orWhere('cell_phone', $insertmobileandroid['cell_phone']) // Another possible unique constraint
                                ->delete();

                            // Attempt to insert again
                            $added = DB::table('tbl_users_mobile')->updateOrInsert(
                                ['idno' => $idno],
                                $insertmobileandroid
                            );
                        } else {
                            // Rethrow the exception if it's a different error
                            throw $e;
                        }
                    }
                    return json_encode(
                        array(
                            'idno' => $idno,
                            'name' => $first_name,
                            'result' => 'success',
                            'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
                        )
                    );
                }
                try {
                    $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
                } catch (\Exception $e) {
                    // Log the exception for debugging purposes
                    Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                    // Send a fallback plain text email
                    $subject = $notifydet->subject;
                    try {

                        Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                            $message->to($email_add)
                                ->subject($subject);
                        });
                    } catch (\Exception $e) {
                        // Log the exception for debugging purposes
                        Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                    }
                }
                return json_encode(
                    array(
                        'idno' => $idno,
                        'name' => $first_name,
                        'result' => 'success',
                        'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
                    )
                );
            } else {
                try {
                    // Attempt to insert or update
                    $added = DB::table('tbl_users_mobile')->updateOrInsert(
                        ['idno' => $idno],
                        $insertmobileandroid
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if the exception is due to an integrity constraint violation
                    if ($e->getCode() == '23000') {
                        // Delete the conflicting record based on the constraint
                        DB::table('tbl_users_mobile')
                            ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                            ->orWhere('cell_phone', $insertmobileandroid['cell_phone']) // Another possible unique constraint
                            ->delete();

                        // Attempt to insert again
                        $added = DB::table('tbl_users_mobile')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobileandroid
                        );
                    } else {
                        // Rethrow the exception if it's a different error
                        throw $e;
                    }
                }

                $result = $apiController->datapull($action, $arr);

                try {
                    $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
                } catch (\Exception $e) {
                    // Log the exception for debugging purposes
                    Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                    // Send a fallback plain text email
                    $subject = $notifydet->subject;
                    try {

                        Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                            $message->to($email_add)
                                ->subject($subject);
                        });
                    } catch (\Exception $e) {
                        // Log the exception for debugging purposes
                        Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                    }
                }
                return json_encode(
                    array(
                        'idno' => $idno,
                        'name' => $first_name,
                        'result' => 'success',
                        'message' => 'Kindly proceed verification code sent to :' . $hiddenemail
                    )
                );
                // }
            }
        } else {

            return json_encode(
                array(

                    'result' => 'error',
                    'message' => 'We could not find your details. Please continue to register.'
                )
            );
        }
    }
    // public function oneandroidapp(Request $request, ApiController $apiController)
    // {

    //     // $cacheKey = "tbl_users_nfm_all_verified";

    //     // $cachedUsers = Cache::get($cacheKey);

    //     // dd($cachedUsers);


    //     // return response()->json([

    //     //     'result' => 'fail',
    //     //     'message' => 'system under maintenance retry after 24hrs'

    //     // ]);


    //     $rules = [
    //         'phonenumber' => 'required|string|min:10',
    //         'gsf' => 'required|string|min:5',
    //         'appversion' => 'required',


    //     ];


    //     // Validate the request
    //     $validator = Validator::make($request->all(), $rules);

    //     // Check if validation fails
    //     if ($validator->fails()) {

    //         return response()->json([

    //             'result' => 'fail',
    //             // 'message' => $validator->errors()
    //             'message' => 'Kindly put the correct phone number'


    //         ]);
    //     }

    //     $namba = $request->input('phonenumber');
    //     $googletester = strtolower($namba);

    //     $gsf = $request->input('gsf');
    //     $serial = $request->input('serial');
    //     $networkused = $request->input('networkused');
    //     $simoperatorname = $request->input('simoperatorname');
    //     $androidid = $request->input('androidid');
    //     $imei = $request->input('imei');
    //     $uuid = $request->input('uuid');
    //     $deviceinfo = $request->input('deviceinfo');
    //     $appversion = $request->input('appversion');
    //     $telco = $request->input('telco');
    //     $maxi = 4;
    //     $latestversion = env('latestversion');
    //     $cell_verified = '1';


    //     // Add your logic here for sending phone verification

    //     $countryCode = '254';
    //     $numberLength = strlen($namba);

    //     if ($numberLength < 11) {
    //         if (Str::startsWith($namba, '0')) {
    //             $namba = $countryCode . substr($namba, 1);
    //         } else {
    //             $namba = $countryCode . $namba;
    //         }
    //     }

    //     if ($appversion < $latestversion) {
    //         // Handle the case where any value is null
    //         $message = 'update to version : ' . $latestversion;

    //         return response()->json([

    //             'result' => 'fail',
    //             'message' => $message

    //         ]);
    //     }


    //     if ($googletester == 'googletester') {
    //         // Handle the case where any value is null

    //         return response()->json([
    //             'idno' => '66666666',
    //             'name' => 'googletester',
    //             'result' => 'success',
    //             'message' => 'Kindly proceed verification code : 1111'

    //         ]);
    //     }

    //     try {
    //         // Set session timeout for this query
    //        // DB::statement("SET SESSION MAX_EXECUTION_TIME=30000"); // Timeout in milliseconds (30 seconds)

    //         $pdo = DB::connection()->getPdo();
    //         $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 1); // Timeout in seconds

    //         $cacheKey = "tbl_users_nfm_{$namba}_{$cell_verified}";

    //    // dd($user);
    //    $startTime = microtime(true);

    //    // Execute the query with MAX_EXECUTION_TIME and parameters
    //    $user = DB::select("
    //        SELECT /*+ MAX_EXECUTION_TIME(1000) */ *
    //        FROM tbl_users_nfm
    //        WHERE cell_phone = ? AND cell_verified = ?
    //        LIMIT 1
    //    ", [$namba, $cell_verified]);

    //    // End time
    //    $endTime = microtime(true);
    //  dd($endTime - $startTime);

    //         $user = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified) {
    //             return DB::select("
    //             SELECT /*+ MAX_EXECUTION_TIME(1000) */ *
    //             FROM tbl_users_nfm
    //             WHERE cell_phone = ? AND cell_verified = ?
    //             LIMIT 1
    //         ", [$namba, $cell_verified]); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
    //         });
    //         dd($user);

    //     } catch (\Exception $e) {
    //         // If the query takes longer than the specified time or any other error occurs, return null
    //         ///return null;


    //         try {
    //             // Set session timeout for this query
    //             DB::statement("SET SESSION MAX_EXECUTION_TIME=5000"); // Timeout in milliseconds (30 seconds)

    //             // Get the PDO instance and set the timeout attribute
    //             $pdo = DB::connection()->getPdo();
    //             $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 10); // Timeout in seconds

    //             $nfmportaluserfetch = DB::table('tbl_users_SQ_tony')
    //                 ->where('cell_phone', $namba)
    //                 ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility

    //             if (!empty($nfmportaluserfetch)) {
    //                 $email_add = $user->email_add;
    //                 $first_name = $user->first_name;
    //                 $id_no = $user->id_no;
    //                 $gender = substr($user->gender, 0, 10);;
    //                 $fullname = $user->full_name;
    //                 $mid_name = $user->mid_name;
    //                 $last_name = $user->last_name;
    //                 $dob = $user->dob;

    //                 $data = [
    //                     'id_no' => $id_no,
    //                     'full_name' => $fullname,
    //                     'gender' => $gender,
    //                     'dob' => $dob,
    //                     'cell_phone' => $namba,
    //                     'last_name' => $last_name,
    //                     'mid_name' => $mid_name,
    //                     'first_name' => $first_name,
    //                     'email_add' => $email_add,
    //                     'cell_verified' => $cell_verified,
    //                     'updated_by' => 'updatenfmptdetails',
    //                 ];

    //                 $added = DB::table('tbl_users_nfm')->updateOrInsert(
    //                     ['id_no' => $id_no],
    //                     $data
    //                 );
    //                 $cell_not_verified = '0';
    //                 $cacheKeyOld = "tbl_users_nfm_{$namba}_{$cell_not_verified}";
    //                 $cacheKeyNew = "tbl_users_nfm_{$namba}_{$cell_verified}";


    //                 // Invalidate the existing cache with cell_verified = '0'
    //                 Cache::forget($cacheKeyOld);
    //                 Cache::put($cacheKeyNew, now()->addHours(1)); // Cache for 1 hour

    //                 return json_encode(
    //                     array(

    //                         'result' => 'fail',
    //                         'message' => 'Details updated. Please try again.'
    //                     )
    //                 );
    //             } else {

    //                 return json_encode(
    //                     array(

    //                         'result' => 'error',
    //                         'message' => 'a We could not find your details. Please continue to register.'
    //                     )
    //                 );
    //             }
    //         } catch (\Exception $e) {
    //             // If the query takes longer than the specified time or any other error occurs, return null
    //             return json_encode(
    //                 array(

    //                     'result' => 'error',
    //                     'message' => 'b We could not find your details. Please continue to register.'
    //                 )
    //             );
    //         }
    //     }











    //     //dd($datatwo);

    //     if (!empty($user)) {
    //         $email_add = $user->email_add;
    //         $first_name = $user->first_name;
    //         $idno = $user->id_no;
    //         $code = $apiController->uniqueStr2(4);

    //         $insertmobileandroid = array(
    //             'idno' => $idno,
    //             'smscount' => '1',
    //             'appversion' => $appversion,
    //             'simoperatorname' => $simoperatorname,
    //             'networkused' => $networkused,
    //             'cell_verified' => "0",
    //             'phone_activation_code' => $code,
    //             'cell_phone' => $namba,
    //             'gsf' => $gsf,
    //             'serial' => $serial,
    //             'android_id' => $androidid,
    //             'imei' => $imei,
    //             'uuid' => $uuid,
    //             'deviceinfo' => $deviceinfo
    //         );







    //         $hiddenemail = $apiController->maskEmailAddress($email_add);

    //         $hiddenamba = str_replace(substr($namba, 3, 6), $apiController->cross($namba), $namba);
    //         //dd($hiddenamba);



    //         $mcode = "[#] HELB:$code is your verification code. 2aUYAkIzflK";


    //         $action = 'sendphoneverificationCode';
    //         $arr = [
    //             'recipient' => $namba,
    //             'verificationcode' => $mcode,
    //             'msg_priority' => '235',
    //             'category' => '108'
    //         ];



    //         $salutation = 'Dear ' . $first_name . ',';

    //         $message = $salutation . $mcode;
    //         $notifydet = array(
    //             'subject' => 'OTP VERIFICATION',
    //             'salutation' => $salutation,
    //             'emailmessage' => $mcode,
    //             'name' => $first_name,
    //             'demail' => $email_add,
    //             'message' => $message
    //         );
    //         $notifydet = (object) $notifydet;



    //         $query = DB::table('tbl_users_mobile')
    //             ->select('smscount', 'idno', 'cell_verified', 'gsf', 'time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))
    //             ->where('cell_phone', $namba)
    //             ->orderBy('time_added', 'desc')
    //             ->first();


    //         // if ($query->count() <= 10) {
    //         //     $query = $query->first();
    //         // } else {
    //         //     $result = null;
    //         // }



    //         if ($query) {

    //             $previdno = $query->idno;
    //             $allowedcount =  (int)($query->smscount);

    //             if ($allowedcount > 3) {

    //                 return json_encode([
    //                     'idno'   => $idno,
    //                     'name'   => $first_name,
    //                     'result' => 'fail',
    //                     'message' => 'You\'ve exhausted the number of allowed SMS. Kindly contact customer care'
    //                 ]);
    //             }
    //             $count = $allowedcount + 1;

    //             //dd($previdno);
    //             $prevgsf = $query->gsf;
    //             $prevcell_verified = $query->cell_verified;

    //             if ($prevcell_verified == '1') {

    //                 $comparedresult = ($previdno . $prevgsf != $idno . $gsf);


    //                 // dd($comparedresult);
    //                 if ($comparedresult) {
    //                     return json_encode(
    //                         array(
    //                             'idno' => $idno,
    //                             'name' => $first_name,
    //                             'result' => 'fail',
    //                             'message' => 'The national ID is activated on another Phone device . Kindly contact customer care'
    //                         )
    //                     );
    //                 }
    //             }



    //             $added = DB::table('tbl_users_mobile')->updateOrInsert(
    //                 ['idno' => $idno],
    //                 array_merge($insertmobileandroid, ['smscount' => $count])
    //             );


    //             if ($query->diff > 120) {

    //                 // Assuming callapion96 is a method in AuthModel and it returns a response
    //                 $result = $apiController->datapull($action, $arr);


    //                 try {
    //                     $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
    //                 } catch (\Exception $e) {
    //                     // Log the exception for debugging purposes
    //                     Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

    //                     // Send a fallback plain text email
    //                     $subject = $notifydet->subject;
    //                     try {

    //                         Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
    //                             $message->to($email_add)
    //                                 ->subject($subject);
    //                         });
    //                     } catch (\Exception $e) {
    //                         // Log the exception for debugging purposes
    //                         Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
    //                     }
    //                 }


    //                 return json_encode(
    //                     array(
    //                         'idno' => $idno,
    //                         'name' => $first_name,
    //                         'result' => 'success',
    //                         'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
    //                     )
    //                 );
    //             }
    //             try {
    //                 $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
    //             } catch (\Exception $e) {
    //                 // Log the exception for debugging purposes
    //                 Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

    //                 // Send a fallback plain text email
    //                 $subject = $notifydet->subject;
    //                 try {

    //                     Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
    //                         $message->to($email_add)
    //                             ->subject($subject);
    //                     });
    //                 } catch (\Exception $e) {
    //                     // Log the exception for debugging purposes
    //                     Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
    //                 }
    //             }
    //             return json_encode(
    //                 array(
    //                     'idno' => $idno,
    //                     'name' => $first_name,
    //                     'result' => 'success',
    //                     'message' => 'Kindly proceed verification code sent to :' . $hiddenamba . ' and :' . $hiddenemail
    //                 )
    //             );
    //         } else {
    //             $added = DB::table('tbl_users_mobile')->updateOrInsert(
    //                 ['idno' => $idno],
    //                 $insertmobileandroid
    //             );

    //             $result = $apiController->datapull($action, $arr);

    //             try {
    //                 $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
    //             } catch (\Exception $e) {
    //                 // Log the exception for debugging purposes
    //                 Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

    //                 // Send a fallback plain text email
    //                 $subject = $notifydet->subject;
    //                 try {

    //                     Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
    //                         $message->to($email_add)
    //                             ->subject($subject);
    //                     });
    //                 } catch (\Exception $e) {
    //                     // Log the exception for debugging purposes
    //                     Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
    //                 }
    //             }
    //             return json_encode(
    //                 array(
    //                     'idno' => $idno,
    //                     'name' => $first_name,
    //                     'result' => 'success',
    //                     'message' => 'Kindly proceed verification code sent to :' . $hiddenemail
    //                 )
    //             );
    //             // }
    //         }
    //     } else {

    //         return json_encode(
    //             array(

    //                 'result' => 'error',
    //                 'message' => 'd We could not find your details. Please continue to register.'
    //             )
    //         );
    //     }
    // }

    function registerandroidnoid(Request $request, ApiController $apiController)
    {
        $request->validate([
            'cellphone' => 'required|string|min:10',
            'appversion' => 'required',
            'first_name' => 'required',



        ]);

        $namba = $request->input('cellphone');
        $google = strtolower($namba);
        $fastname = $request->input('first_name');

        $gsf = $request->input('gsf');
        $serial = $request->input('serial');
        $networkused = $request->input('networkused');
        $simoperatorname = $request->input('simoperatorname');
        $androidid = $request->input('androidid');
        $imei = $request->input('imei');
        $uuid = $request->input('uuid');
        $deviceinfo = $request->input('deviceinfo');
        $appversion = $request->input('appversion');
        $telco = $request->input('telco');
        $maxi = 4;
        $latestversion = env('latestversion');
        $cell_verified = '1';
        $emailaddress = '';

        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        $namba = preg_replace('/\D/', '', $namba);


        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }


        $phone = str_replace(['"', "'"], '', $namba);
        $data = Cache::remember('cached_loans_' . $phone, 60, function () use ($phone) {
            return DB::table('tbl_whitelisted')
                //->select('phone')
                ->where('phone', $phone)
                ->first();
        });
        $failmessage = 'Kindly visit HELB offices for activation of this service or call 0711052000';

        if ($data) {
            $phone = $data->phone;
            $idno = $data->studentid;
            $query = DB::table('registration_mobile')
                ->select('time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))

                ->where('id_no', $phone)
                ->orderBy('time_added', 'desc')
                ->first();

            //dd($query);

            if ($query) {

                //dd($query->diff);
                if ($query->diff > 600) {
                    $insert = DB::table('registration_mobile')->updateOrInsert(
                        ['id_no' => $phone],
                        ['id_no' => $phone],
                        ['updated_at' => DB::raw('CURRENT_TIMESTAMP')]
                    );

                    return $apiController->registermobile($idno, $fastname, $emailaddress, $phone);
                }

                return json_encode(
                    array(

                        'result' => 'error',
                        'message' => 'Wait 10 minutes before retry'
                    )
                );
            } else {
                $insert = DB::table('registration_mobile')->updateOrInsert(
                    ['id_no' => $phone],
                    ['id_no' => $phone],
                    ['updated_at' => DB::raw('CURRENT_TIMESTAMP')]
                );

                return $apiController->registermobile($idno, $fastname, $emailaddress, $phone);
            }
        } else {
            return response()->json([
                'result' => 'error',
                'message' => $failmessage
            ], 200);
        }
    }








    public function twoandroidappid(Request $request, ApiController $apiController)
    {
        $request->validate([
            'cellphone' => 'required|string|min:10',
            'id_no' => 'required|string|min:4',
            'first_name' => 'required',
            'appversion' => 'required',
            'email_add' => 'required',




        ]);

        $telco = $request->input('telco');
        $appversion = $request->input('appversion');
        $idnumber = $request->input('id_no');
        $cellphone = $request->input('cellphone');
        $fastname = $request->input('first_name');
        $emailaddress = $request->input('email_add');
        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($cellphone);

        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }

        $cellphone = preg_replace('/\D/', '', $cellphone);

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }

        $query = DB::table('registration_mobile')
            ->select('time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))

            ->where('id_no', $idnumber)
            ->orderBy('time_added', 'desc')
            ->first();

        //dd($query);

        if ($query) {


            if ($query->diff > 600) {
                $insert = DB::table('registration_mobile')->updateOrInsert(
                    ['id_no' => $idnumber],
                    ['id_no' => $idnumber],
                    ['updated_at' => DB::raw('CURRENT_TIMESTAMP')]

                );
                return $apiController->registermobile($idnumber, $fastname, $emailaddress, $cellphone);
            }

            return json_encode(
                array(

                    'result' => 'error',
                    'message' => 'Wait 10 minutes before retry'
                )
            );
        } else {
            $insert = DB::table('registration_mobile')->updateOrInsert(
                ['id_no' => $idnumber],
                ['id_no' => $idnumber],
                ['updated_at' => DB::raw('CURRENT_TIMESTAMP')]

            );

            return $apiController->registermobile($idnumber, $fastname, $emailaddress, $cellphone);
        }
    }


    public function threeandroidverifycode(Request $request)
    {




        $request->validate([

            'code' => 'required|string|min:4',
            'mobile' => 'required|string|min:10',


        ]);


        // Validate the request
        // $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }



        $code = $request->input('code');
        $namba = $request->input('mobile');
        $google = strtolower($namba);

        $codeclean = str_replace(['"', "'"], '', $code);
        $stringPost = json_encode($request->all());

        DB::table('android_log')->insert(['message' => $stringPost]);

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($google === 'googletester' && $codeclean === '1111') {
            return response()->json([
                'result' => 'success',
                'message' => 'verified',
                'idno' => '66666'

            ]);
        } else {
            $data = DB::table('tbl_users_mobile')
                ->where('cell_phone', $namba)
                ->orderByDesc('idn')
                ->first(['idno', 'phone_activation_code']);

            if (!empty($data) && $data->phone_activation_code === $codeclean) {
                $idno = $data->idno;

                DB::table('tbl_users_mobile')
                    ->where('cell_phone', $namba)
                    ->where('phone_activation_code', $codeclean)

                    ->update(['cell_verified' => '1', 'smscount' => 0]);

                DB::table('tbl_users_mobile')
                    ->where('idno', $idno)
                    ->where('phone_activation_code', '!=', $codeclean)
                    ->delete();

                return response()->json([
                    'result' => 'success',
                    'message' => 'verified',
                    'idno' => $idno
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'not verified'
                ]);
            }
        }
    }


    public function androidinstitution(Request $request, ApiController $apiController)
    {

        $rules = [
            'cell_phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',

            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $namba = $request->input('cell_phone');

        // dd($namba);

        $gsf = $request->input('gsf');
        $cell_verified = '1';
        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }




        $cacheKey = 'tbl_users_mobile__' . $namba . '_' . $cell_verified . '_' . $gsf;
        $datatwo = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified, $gsf) {
            return DB::table('tbl_users_mobile')
                ->select('idn')
                ->where('cell_phone', $namba)
                ->where('cell_verified', $cell_verified)
                ->where('gsf', $gsf)

                ->first();
        });



        if (!empty($datatwo)) {
            $cacheKey = "dminstitututions_2024_{$idno}";

            $datam = Cache::remember($cacheKey, 120, function () use ($idno) {
                return DB::table('dminstitututions_2024')
                    ->where('IDNO', $idno)
                    //->orderBy('ADMISSIONYEAR', 'desc')

                    ->first();
            });


            if (!is_array($datam)) {
                $datam = [$datam];
            }
            if ($datam) {
                return response()->json(['message' => 'success', 'details' => $datam]);
            } else {
                return response()->json(['message' => 'fail', 'details' => 'two']);
            }
        } else {

            return response()->json([

                'result' => 'error',
                'message' => 'fail'

            ]);
        }
    }



    public function versioncheck(Request $request, ApiController $apiController)
    {

        $rules = [
            'cell_phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',

            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $namba = $request->input('cell_phone');

        // dd($namba);

        $gsf = $request->input('gsf');
        $cell_verified = '1';
        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');
        $maintenance = false;



        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }




        if ($maintenance) {
            // Handle the case where any value is null
            $message = 'system under maintenance';

            return response()->json([

                'result' => 'fail',
                'message' => $message

            ]);
        }
        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }

        return response()->json([

            'result' => 'success',
            'message' => 'success'

        ]);
    }



    public function androidpersonal(Request $request, ApiController $apiController)
    {

        $rules = [
            'cell_phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',

            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $namba = $request->input('cell_phone');

        // dd($namba);

        $gsf = $request->input('gsf');
        $cell_verified = '1';
        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }




        $cacheKey = 'tbl_users_mobile__' . $namba . '_' . $cell_verified . '_' . $gsf;
        $datatwo = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified, $gsf) {
            return DB::table('tbl_users_mobile')
                ->select('idn')
                ->where('cell_phone', $namba)
                ->where('cell_verified', $cell_verified)
                ->where('gsf', $gsf)

                ->first();
        });



        if (!empty($datatwo)) {
            $cacheKey = "tbl_users_nfm_{$idno}";

            $datam = Cache::remember($cacheKey, 120, function () use ($idno) {
                return DB::table('tbl_users_nfm')
                    ->select('*', DB::raw("concat(first_name, ' ', mid_name, ' ', last_name) as full_name"))
                    ->where('id_no', $idno)
                    ->get();
            });

            if ($datam) {
                return response()->json(['message' => 'success', 'details' => $datam]);
            } else {
                return response()->json(['message' => 'fail', 'details' => 'two']);
            }
        } else {

            return response()->json([

                'result' => 'error',
                'message' => 'fail'

            ]);
        }
    }

    function productphonevalidate(Request $request, UserController $userController)
    {
        $rules = [
            'idno' => 'required|string|min:4',
            'phone' => 'required|string|min:10',
            'academicyear' => 'required',
            'productid' => 'required',
            'productcode' => 'required',
            'studentgrouping' => 'required',
            'name' => 'required',
            'type' => 'required',
            'idcre' => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idnumber = $request->input('idno');
        $cellphone = $request->input('phone');
        $academicyear = $request->input('academicyear');
        $productid = $request->input('productid');
        $productcode = $request->input('productcode');
        $studentgrouping = $request->input('studentgrouping');
        $name = $request->input('name');
        $type = $request->input('type');
        $idcre = $request->input('idcre');
        $gsf = $request->input('gsf');
        $brand = $request->input('brand');
        $source = 'ussd';
        $failmessage = 'Kindly visit HELB offices for update of institution details or call 0711052000';


        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }





       
        // $apicontroller = '';
        $apiController = new ApiController();

        $result = $userController->kycregister($idnumber, $cellphone, $apiController);

        $verified = json_decode($result)->verified;
        if ($verified == 'yes') {

            $message = 'Phone number ' . $cellphone . ' Are the details correct?';
            return response()->json([
                'result' => 'success',
                'disbursementoption' => 'mobile',
                'disbursementoptionvalue' => $cellphone,
                'message' => $message
            ], 200);
        } else {

            $message = 'phone number ' . $cellphone . ' failed verification for id ' . $idnumber . ' Select another option.';

            return response()->json([
                'result' => 'fail',
                'message' => $message
            ], 200);
        }
    }


    function paymentnumber(Request $request, ApiController $apiController)
    {
        $rules = [
            'idno' => 'required|string|min:4',
            'phoneNumber' => 'required|string|min:10',
            'gsf' => 'required',

        ];
        $cell_verified = '1';

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idno = $request->input('idno');
        $cellphone = $request->input('phoneNumber');

        $gsf = $request->input('gsf');



        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($cellphone);

        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }



        // dd($cellphone);

        $cacheKey = 'tbl_users_mobile__' . $cellphone . '_' . $cell_verified . '_' . $gsf;
        $datatwo = Cache::remember($cacheKey, 60, function () use ($cellphone, $cell_verified, $gsf) {
            return DB::table('tbl_users_mobile')
                ->select('idn')
                ->where('cell_phone', $cellphone)
                ->where('cell_verified', $cell_verified)
                ->where('gsf', $gsf)

                ->first();
        });


        if (!empty($datatwo)) {
            $action = 'fetchminiaccount';
            $arr = array('idno' => $idno);
            $result = $apiController->mobiapis($action, $arr);
            //dd($result);


            if (!is_array($result)) {
                $result = [$result];
            }
            return  $result;
            //return response()->json([ $result   ]);


        } else {

            return response()->json(['error']);
        }
    }

    public function androidmail(Request $request, ApiController $apiController)
    {

        $rules = [
            'cell_phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',
            'email' => 'required',
            'content' => 'required',

            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $namba = $request->input('cell_phone');
        $content = $request->input('content');
        $emailmobi = $request->input('email');


        // dd($namba);

        $gsf = $request->input('gsf');
        $cell_verified = '1';
        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }




        $cacheKey = 'tbl_users_mobile__' . $namba . '_' . $cell_verified . '_' . $gsf;
        $datatwo = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified, $gsf) {
            return DB::table('tbl_users_mobile')
                ->select('idn')
                ->where('cell_phone', $namba)
                ->where('cell_verified', $cell_verified)
                ->where('gsf', $gsf)

                ->first();
        });



        if (!empty($datatwo)) {
            $email_add = 'contactcentre@helb.co.ke';

            $notifydet = array(
                'subject' => 'EMAIL FROM ANDROID APP IDNO: ' . $idno,
                'salutation' => 'Greetings',
                'emailmessage' => 'Phone number: ' . $namba . ' message ' . $content . ' Student Email ' . $emailmobi . ' Student IDNO ' . $idno,
                'name' => 'HELB STAFF',
                'demail' => $email_add,
                'message' => $content
            );
            $notifydet = (object) $notifydet;
            try {
                $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
            } catch (\Exception $e) {
                // Log the exception for debugging purposes
                Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

                // Send a fallback plain text email
                $subject = $notifydet->subject;
                try {

                    Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                        $message->to($email_add)
                            ->subject($subject);
                    });
                } catch (\Exception $e) {
                    // Log the exception for debugging purposes
                    Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                }
            }
            return response()->json([

                'info' => 'email sent',

            ]);
        } else {

            return response()->json([

                'info' => 'email not sent try later',

            ]);
        }
    }



    public function androidmailtest(Request $request, ApiController $apiController)
    {

        $rules = [
            'cell_phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',
            'email' => 'required',
            'content' => 'required',

            'gsf' => 'required|string|min:5',
            'appversion' => 'required',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $namba = $request->input('cell_phone');
        $content = $request->input('content');
        $emailmobi = $request->input('email');


        // dd($namba);

        $gsf = $request->input('gsf');
        $cell_verified = '1';
        $appversion = $request->input('appversion');
        $idno = $request->input('idno');


        $latestversion = env('latestversion');


        // Add your logic here for sending phone verification

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        if ($appversion < $latestversion) {
            // Handle the case where any value is null
            $message = 'update to version : ' . $latestversion;

            return response()->json([

                'result' => 'error',
                'message' => $message

            ]);
        }





        // $email_add = 'toloo@helb.co.ke';
        $email_add = 'tonyoloo@ymail.com';
        //$email_add = 'contactcentre@helb.co.ke';


        $notifydet = array(
            'subject' => 'EMAIL FROM ANDROID APP IDNO: ' . $idno,
            'salutation' => 'Greetings',
            'emailmessage' => 'Student of Phone number: ' . $namba . ' Details to be modified/updated ' . $content . ' Student Email ' . $emailmobi . ' Student IDNO ' . $idno,
            'name' => 'HELB STAFF',
            'demail' => $email_add,
            'message' => $content,
            'mailer' => 'smtp'

        );
        $notifydet = (object) $notifydet;


        try {
            $var = Mail::to($email_add)->send(new BeautifulMail($notifydet));
            return response()->json([

                'info' => 'beautiful  email  sent',

            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Failed to send BeautifulMail: ' . $e->getMessage());

            // Send a fallback plain text email
            $subject = $notifydet->subject;
            try {

                Mail::mailer('smtp_sendmail')->raw($notifydet->message, function ($message) use ($email_add, $subject) {

                    // Mail::raw($notifydet->message, function ($message) use ($email_add, $subject) {
                    $message->to($email_add)
                        ->subject($subject)
                        ->from('no-reply@helb.co.ke', 'HELB'); // Specify the "From" address

                });
                return response()->json([

                    'info' => 'raw email sent',

                ]);
            } catch (\Exception $e) {
                return response()->json([

                    'info' => 'raw email not sent' . $e->getMessage(),

                ]);
                // Log the exception for debugging purposes
                //  echo  $e->getMessage();
                Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
            }
        }
    }

    public function subsequentcount()
    {

        $results = DB::table('tbl_products_submit_new as a')
            ->leftJoin('cre_pastapplicationstwo as b', 'a.idno', '=', 'b.IDNO')
            ->select('b.productcode', 'b.ACADEMIC_YEAR', DB::raw('COUNT(*) as count'))
            ->where('a.submittedloan', '1')
            ->where('b.qualifiedloanmodel', '1')
            ->where('b.qualifiedscholarship', '0')
            ->groupBy('b.productcode', 'b.ACADEMIC_YEAR')
            ->get();

        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACADEMIC_YEAR;

            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'loan-OFM')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'OFM' => $count
                ]);

            echo 'updated OFM loan ' . $productcode . ' count ' . $count;
        }


        $results = DB::table('tbl_products_submit_new as a')
            ->leftJoin('cre_pastapplicationstwo as b', 'a.idno', '=', 'b.IDNO')
            ->select('b.productcode', 'b.ACADEMIC_YEAR', DB::raw('COUNT(*) as count'))
            ->where('a.submittedloan', '1')
            ->where('b.qualifiedloanmodel', '=', '2') // Use 'greater than or equal to' here
            ->groupBy('b.productcode', 'b.ACADEMIC_YEAR')
            ->get();

        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACADEMIC_YEAR;

            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'loan-NFM')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'NFM' => $count
                ]);

            echo 'updated NFM loan ' . $productcode . ' count ' . $count;
        }

        $results = DB::table('tbl_products_submit_new as a')
            ->leftJoin('cre_pastapplicationstwo as b', 'a.idno', '=', 'b.IDNO')
            ->select('b.productcode', 'b.ACADEMIC_YEAR', DB::raw('COUNT(*) as count'))
            ->where('a.submittedscholarship', '1')
            ->where('b.qualifiedscholarship', '1')
            ->groupBy('b.productcode', 'b.ACADEMIC_YEAR')
            ->get();

        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACADEMIC_YEAR;

            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'scholarship')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'NFM' => $count
                ]);

            echo 'updated NFM scholarship ' . $productcode . ' count ' . $count;
        }
    }


    public function subsequentcountemail()
    {




        $data = DB::table('ussd_products_count_email as a')
            ->select(
                DB::raw('a.id AS ID'),
                DB::raw('a.name AS NAME'),
                DB::raw('a.type AS TYPE'),
                DB::raw('a.OFM AS OFM'),
                DB::raw('a.NFM AS NFM'),
                DB::raw('a.academicyear AS ACADEMICYEAR')
            )->where('academicyear', '=', '2025/2026')

            ->orderBy('a.academicyear', 'DESC')
            ->get();



        //  $tableHtml = $this->renderTableHtml($cached_loans);
        // $cached_loans = array();
        $tableHtml = $this->renderTableHtml($data);
        // dd($tableHtml);











        $email_add = 'toloo@helb.co.ke';
        $ccemail_add = ['toloo@helb.co.ke', 'bkiprono@helb.co.ke', 'pwambugu@helb.co.ke'];
        $ccAddresses = ['tonyoloo15@gmail.com', 'tonyoloo@ymail.com']; // Add your CC addresses here

        // $email_add = 'jnzuki@helb.co.ke';
        // $ccAddresses = ['bkiprono@helb.co.ke', 'pwambugu@helb.co.ke', 'emacharia@helb.co.ke', 'toloo@helb.co.ke', 'wwanjohi@helb.co.ke'];


        // $email_add = 'cmringera@helb.co.ke';
        // $ccAddresses = ['sgichimu@helb.co.ke', 'geoffrey.monari@ufb.go.ke',
        //  'immaculate.njoroge@ufb.go.ke', 'Leah.miano@ufb.go.ke', 'emmanuel.abook@ufb.go.ke',
        //   'mercy.gikonyo@ufb.go.ke', 'samuel.nandasaba@ufb.go.ke', 'jkiplagatuwei@gmail.com',
        //    'bmasinde@helb.co.ke', 'wwanjohi@helb.co.ke', 'jnzuki@helb.co.ke',
        //     'nkingori@helb.co.ke', 'jaloo@helb.co.ke', 'mplalampaa@helb.co.ke',
        //      'cmwaikwasi@helb.co.ke', 'jgachari@helb.co.ke', 'jswanya@helb.co.ke', 
        //      'mwanyingi@helb.co.ke', 'mboke@helb.co.ke', 'bnzioka@helb.co.ke', 
        //      'fndege@helb.co.ke', 'jmungai@helb.co.ke', 'fokoth@helb.co.ke', 
        //      'bkiprono@helb.co.ke', 'wmbala@helb.co.ke', 'pwambugu@helb.co.ke', 
        //      'emacharia@helb.co.ke', 'toloo@helb.co.ke', 'swanyama@helb.co.ke',
        //       'dpepela@helb.co.ke', 'enafula@helb.co.ke', 'rngeno@helb.co.ke', 
        //       'aomondi@helb.co.ke', 'akibugu@helb.co.ke', 'cwenje@helb.co.ke', 
        //       'batandi@helb.co.ke', 'jkoech@helb.co.ke', 'jalex@helb.co.ke',
        //        'mmbiti@helb.co.ke','toloo@helb.co.ke'];




        $idno = '28613556';
        $namba = '0727045828';
        $content = 'test message';
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d H:i:s'); // Get the current date and time in 'Y-m-d H:i:s' format
        //  $date_now = "";



        $notifydet = array(
            'subject' => 'SUBSEQUENT APPLICATION REPORT',
            'salutation' => 'Greetings,',
            'emailmessage' => 'Subsequent application count as at : '  . $date_now,
            'name' => 'HELB',
            'demail' => $email_add,
            'message' => $content,
            'mailer' => 'smtp'

        );
        $notifydet = (object) $notifydet;
        // dd('here', $notifydet->emailmessage);
        // $fullBodyContent = '<p>' . $notifydet->emailmessage . '</p>' . $tableHtml;

        // dd($fullBodyContent); die();


        try {
            $var = Mail::to($email_add)->send(new ApplicationCountMail($notifydet, $tableHtml, $ccAddresses));








            return response()->json([

                'info' => 'formatted email sent',

            ]);
        } catch (\Exception $e) {

            // dd($e);
            // Log the exception for debugging purposes
            Log::error('Failed to send BeautifulMail: ' . $e->getMessage());
            //dd($e);

            // Send a fallback plain text email
            try {


                $subject = $notifydet->subject;

                $fullBodyContent = '<p>' . $notifydet->emailmessage . '</p>' . $tableHtml;

                Mail::send([], [], function ($message) use ($email_add, $subject, $fullBodyContent) {
                    $message->to($email_add)
                        ->subject($subject)
                        ->html($fullBodyContent); // Use the 'html' method instead of 'setBody'
                });



                return response()->json([

                    'info' => 'raw email sent',

                ]);
            } catch (\Exception $e) {
                //dd($e);
                // Log the exception for debugging purposes
                Log::error('Failed to send fallback plain text email: ' . $e->getMessage());
                $this->subsequentcountemail();
            }
        }
    }


    private function renderTableHtml($data)
    {
        // Initialize the table HTML
        $html = '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
        $html .= '<thead><tr>';

        // Add table headers
        if (!empty($data)) {
            // Get the keys from the first item as the headers
            $headers = array_keys((array)$data[0]); // Convert object to array to get keys
            foreach ($headers as $header) {
                $html .= "<th>{$header}</th>";
            }
            $html .= '</tr></thead><tbody>';

            // Add table rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    // Ensure cells are properly escaped for HTML
                    $html .= "<td>" . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . "</td>";
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $headers = array();
            $html .= '<tr><td colspan="' . count($headers) . '">No data available</td></tr></table>';
        }

        return $html;
    }
}
