<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ApiController extends Controller
{


    function checkDot95Reachability()
    {
        $host = parse_url(env('DOT95_URL'), PHP_URL_HOST); // Extract the host from the URL

        if (empty($host)) {
            // return response()->json(['status' => 'not reachable', 'error' => 'Invalid URL']);
            return false;
        }

        // Ping command with a timeout of 5 seconds (adjust based on your OS)
        $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? "ping -n 1 -w 5000 $host" // Windows
            : "ping -c 1 -W 5 $host";   // Linux/Mac

        exec($command, $output, $result);

        if ($result === 0) {
            // return response()->json(['status' => 'reachable']);
            return true;
        } else {
            // return response()->json(['status' => 'not reachable', 'error' => 'Ping failed']);
            return false;
        }
    }







    function mobiapis($action, $arr)

    {

        // dd($arr);
        $baseUrl = config('app.MOBIAPI_URL');

        $url = "$baseUrl?rquest=$action";

        try {
            $response = Http::timeout(12000)
                ->withoutVerifying()  // Disables SSL verification
                ->asForm()
                ->post($url, $arr);

            // dd($response);

            if ($response->successful()) {
                return $response->json();
            } else {
                return ['info' => $response->body()];
            }
        } catch (\Exception $e) {
            return ['info' => $e->getMessage()];
        }
    }

    function vendors($action, $arr)
    {
        //$baseUrl = env('VENDORS_URL');
        $baseUrl = config('app.vendors_url');

        $url = "$baseUrl?rquest=$action";
        if (empty($baseUrl)) {
            return ['status' => 'error', 'message' => 'VENDORS_URL is not set'];
        }


        //dd($url);

        try {
            $response = Http::timeout(12000)
                ->withoutVerifying()  // Disables SSL verification
                ->asForm()
                ->post($url, $arr);

            if ($response->successful()) {
                return $response->json();
            } else {

                return [
                    'status' => 'error',
                    'code' => $response->status(),
                    'body' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return ['status' => 'exception', 'message' => $e->getMessage()];
        }
    }











    function datapull($action, $arr)
    {
        $baseUrl = env('DATAPULLAPI_URL');
        $url = "$baseUrl?rquest=$action";

        try {
            $response = Http::timeout(12000)
                ->withoutVerifying()  // Disables SSL verification
                ->asForm()
                ->post($url, $arr);

            if ($response->successful()) {
                return $response->json();
            } else {
                return ['info' => $response->body()];
            }
        } catch (\Exception $e) {
            return ['info' => $e->getMessage()];
        }
    }
    function uniqueStr2($length)
    {
        $character_set_array = [
            ['count' => 4, 'characters' => '123456789'],
            // Add more character sets if needed
        ];

        $temp_array = [];
        foreach ($character_set_array as $character_set) {
            for ($i = 0; $i < $character_set['count']; $i++) {
                $temp_array[] = $character_set['characters'][random_int(0, strlen($character_set['characters']) - 1)];
            }
        }

        shuffle($temp_array);
        return implode('', $temp_array);
    }












    function dotseven($action, $arr)
    {
        $baseUrl = env('DOT7API_URL');
        $url = "$baseUrl?rquest=$action";

        // dd($url);

        try {
            $response = Http::timeout(12000)
                ->withoutVerifying()  // Disables SSL verification
                ->asForm()
                ->post($url, $arr);

            if ($response->successful()) {
                return $response->json();
            } else {
                return ['info' => $response->body()];
            }
        } catch (\Exception $e) {
            return ['info' => $e->getMessage()];
        }
    }












    function hefportal($arr)
    {
        $url = env('HEF_URL');
        // return response()->json($arr);

        $data_string = json_encode($arr);

        try {
            $response = Http::timeout(12000)
                ->withoutVerifying()  // Disables SSL verification
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $arr);

            if ($response->successful()) {
                //return response()->json(['here1','response' => $response->body()]);


                return ['info' => $response->body()];
            } else {
                return ['info' => $response->body()];
                //return response()->json(['here2','response' => $response->body()]);

            }
        } catch (\Exception $e) {
            return ['info' => $e->getMessage()];
        }
    }



    function validatesafaricom($idnumber, $cellphone)
    {
        $consumerKey = env('CONSUMERKEYSAFVALIDATE');
        $consumerSecret = env('CONSUMERSECRETSAFVALIDATE');
        $ShortCode = env('SHORTCODESAF');
        // $SAFVERIFYAUTH = env('SAFVERIFYAUTHKYC');

        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $credentials,
        ])
            ->withoutVerifying()  // Disables SSL verification

            // ->post($SAFVERIFYAUTH);
            ->post("https://api.safaricom.co.ke/oauth2/v1/generate?grant_type=client_credentials");


        $accesstoken = $response->json('access_token');

        // dd($accesstoken);

        $common_data = array(
            //Fill in the request parameters with valid values
            'shortCode' => $ShortCode,
            'msisdn' => $cellphone, //254
            'idNumber' => $idnumber,
        );

        $data_string_nationalid = json_encode(array_merge($common_data, [
            'requestRefID' => time() . $idnumber,
            'idType' => '01',
        ]));

        $data_string_passport = json_encode(array_merge($common_data, [
            'requestRefID' => time() . $idnumber,
            'idType' => '02',
        ]));

        $data_string_millitary = json_encode(array_merge($common_data, [
            'requestRefID' => time() . $idnumber,
            'idType' => '05',
        ]));



        $returnedvalidationid = $this->safvalidaterequest($accesstoken, $data_string_nationalid);
        // $returnedvalidation = $this->safold($accesstoken,$data_string_nationalid);

        $status = $returnedvalidationid['status'] ?? null; // Safely access the status property

        if ($status !== null && $status === 'true') {


            //id validation success
            DB::table('safaricomkyclogs')->insert([
                'nationalidno' => $idnumber,
                'phone' => $cellphone,
                'log' => $returnedvalidationid['log'],
                'message' => $returnedvalidationid['message'],
                'responseRefID' => $returnedvalidationid['responseRefID'],
                'status' => $status,
                'responsecode' => $returnedvalidationid['responsecode'],
                'servicestatus' => $returnedvalidationid['servicestatus'],

            ]);

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' successfully verified';
            $status = 'UP';
            $verified = 'yes';

            return json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));
        } elseif ($status !== null && $status === 'false') {

            $returnedvalidationpassport = $this->safvalidaterequest($accesstoken, $data_string_passport);
            $status = $returnedvalidationpassport['status'] ?? null; // Safely access the status property



            if ($status !== null && $status === 'true') {

                //passport validation success
                DB::table('safaricomkyclogs')->insert([
                    'nationalidno' => $idnumber,
                    'phone' => $cellphone,
                    'log' => $returnedvalidationid['log'],
                    'message' => $returnedvalidationid['message'],
                    'responseRefID' => $$returnedvalidationid['responseRefID'],
                    'status' => $status,
                    'responsecode' => $returnedvalidationid['responsecode'],
                    'servicestatus' => $returnedvalidationid['servicestatus'],

                ]);

                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' successfully verified';
                $status = 'UP';
                $verified = 'yes';


                return json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));
            } else {

                $returnedvalidationmillitary = $this->safvalidaterequest($accesstoken, $data_string_millitary);
                $status = $returnedvalidationmillitary['status'] ?? null; // Safely access the status property



                if ($status !== null && $status === 'true') {
                    //id validation success
                    DB::table('safaricomkyclogs')->insert([
                        'nationalidno' => $idnumber,
                        'phone' => $cellphone,
                        'log' => $returnedvalidationid['log'],
                        'message' => $returnedvalidationid['message'],
                        'responseRefID' => $returnedvalidationid['responseRefID'],
                        'status' => $status,
                        'responsecode' => $returnedvalidationid['responsecode'],
                        'servicestatus' => $returnedvalidationid['servicestatus'],

                    ]);
                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' successfully verified';
                    $status = 'UP';
                    $verified = 'yes';


                    return json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));
                } else {

                    //false everything



                    DB::table('safaricomkyclogs')->insert([
                        'nationalidno' => $idnumber,
                        'phone' => $cellphone,
                        'log' => $returnedvalidationid['log'] ?? null,
                        'message' => $returnedvalidationid['message'] ?? null,
                        'responseRefID' => $returnedvalidationid['responseRefID'] ?? null,
                        'status' => 'false',
                        'responsecode' => $returnedvalidationid['responsecode'] ?? null,
                        'servicestatus' => $returnedvalidationid['servicestatus'] ?? null,

                    ]);


                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' NOT successfully verified';
                    $status = 'UP';
                    $verified = 'no';

                    return json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));
                }
            }
        } else {

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' CANT be verified';
            $status = 'UP';

            $verified = 'no';

            return json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));
        }
    }


    function safaricomsurepay($accesstoken, $requestData, $url)
    {

        $time = time();
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'access-token: ' . $accesstoken)); //setting custom header

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true); //
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);


        $respons = curl_exec($curl);

        curl_close($curl);
        // var_dump($respons);

        $respons = json_decode($respons);

        // print_r($respons);die();


        return $respons;
    }
    /////

    function accesstokencallgenerator()
    {


        $grant_type = env('safaricom_grant_type');
        $client_id = env('safaricom_client_id');
        $client_secret = env('safaricom_client_secret');

        $data_string = 'grant_type=' . $grant_type . '&client_id=' . $client_id . '&client_secret=' . $client_secret;
        // return  $data_string ;
        //var_dump($data_string);die();


        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('SUREPAYAUTHTOKEN'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',

                ),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            )
        );

        $response = curl_exec($curl);
        //var_dump($response);die();

        curl_close($curl);

        $response = json_decode($response);
        return $response->access_token;
    }


    function ufbaccesstokencallgenerator()
    {


        //  dd("llllllllllll");

        // Dynamic values
        $username = config('app.ufbusername');
        $password = config('app.ufbpassword');
        $url = config('app.ufburl');

        // Create the data array
        $postData = [
            "username" => $username,
            "password" => $password
        ];

        // Convert to JSON
        $jsonData = json_encode($postData);

        // cURL initialization
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url); // Replace with your API endpoint
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute and close
        $response = curl_exec($ch);
        curl_close($ch);

        $curl_response = json_decode($response);
        $status = $curl_response->token ?? null;

        // Handle response
        return $status;
    }


public function loadLargeData()
{
    // Adjust the path as needed. Ensure the MySQL server allows LOCAL INFILE.
    $filePath = storage_path('app/ufbdatan.csv');



    // Escape backslashes and handle Windows paths
    $escapedPath = addslashes($filePath);

    // Prepare the LOAD DATA SQL
    $sql = "
        LOAD DATA LOCAL INFILE '{$escapedPath}'
        INTO TABLE ufbdata
        FIELDS TERMINATED BY ','
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\\n'
    ";

    try {
        DB::connection()->getPdo()->exec($sql);
        return response()->json(['message' => 'Data loaded successfully.']);
    } catch (\PDOException $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}










      function ufbstudentdataussd(Request $request)
    {


        $idno = $request->input('idno');


        $rules = [
            'idno' => 'required',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // $kuccpsid = $request->input('kuccpsid');


        try {
          $idno = (string) $idno;

            $billed = DB::connection('sqlsrv')->select(
                "
          select s.IDENTITYREFERENCENO as 'idno', j.NAME as 'student_name',concat(g.TOYEAR,g.INDEXNUMBER) as 'kuccps_id'
from LMLOANS a
join CUSTTABLE k on k.ACCOUNTNUM = a.ACCOUNTNUM
join DIRPARTYTABLE j on j.RECID = k.PARTY
join LMIDENTIFICATIONDOCUMENTS s on s.ACCOUNTNUM = a.ACCOUNTNUM
join LMSTUDENTEDUCATIONBACKGROUND g on g.ACCOUNTNUM = a.ACCOUNTNUM and g.EDUBACGINSTITUTIONTYPE = 2 and g.TOYEAR >= '2022'
where a.ACADEMICYEAR = '2025/2026' and a.APPLICANTTYPE in (1,2) and s.IDENTITYREFERENCENO = ?
order by s.IDENTITYREFERENCENO
            ",
                [$idno]
            );

           // dd($billed[0]->kuccps_id );

            $kuccps_id = $billed[0]->kuccps_id  ?? null;
           // $kuccps_id = "202237617210148";//$billed[0]->kuccps_id  ?? null;

            $accesstoken = $this->ufbaccesstokencallgenerator() ?? null;


            $url = config('app.ufburldata');

            $query = http_build_query([
                'kuccpsid' => $kuccps_id,
            ]);

            $url = $url . '?' . $query;

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accesstoken)); //setting custom header

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true); //

            $curl_response = curl_exec($curl);

           // dd($curl_response);

            $curl_response = json_encode($curl_response);
            //$status = $curl_response->status ?? null;


            return json_decode($curl_response, true); // 'true' returns associative array

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



  function aimicrosoft($microsoftendpoint,$payload){

   // dd($payload);

           $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $microsoftendpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false, // Only for testing environments
                CURLOPT_SSL_VERIFYPEER => false, // Only for testing environments
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload)
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);

            curl_close($curl);

            if ($error) {
                http_response_code(500);
                echo json_encode(['error' => 'cURL Error: ' . $error]);
                exit;
            }

            if ($httpCode >= 400) {
                http_response_code($httpCode);
                echo json_encode(['error' => 'HTTP Error', 'status' => $httpCode, 'response' => $response]);
                exit;
            }

            $data = json_decode($response, true);
            return $data;

  }






    function ufbstudentdata(Request $request)
    {


        $idno = $request->input('idno');


        $rules = [
            'idno' => 'required',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // $kuccpsid = $request->input('kuccpsid');


        try {
          $idno = (string) $idno;

            $billed = DB::connection('sqlsrv')->select(
                "
          select s.IDENTITYREFERENCENO as 'idno', j.NAME as 'student_name',concat(g.TOYEAR,g.INDEXNUMBER) as 'kuccps_id'
from LMLOANS a
join CUSTTABLE k on k.ACCOUNTNUM = a.ACCOUNTNUM
join DIRPARTYTABLE j on j.RECID = k.PARTY
join LMIDENTIFICATIONDOCUMENTS s on s.ACCOUNTNUM = a.ACCOUNTNUM
join LMSTUDENTEDUCATIONBACKGROUND g on g.ACCOUNTNUM = a.ACCOUNTNUM and g.EDUBACGINSTITUTIONTYPE = 2 and g.TOYEAR >= '2022'
where a.ACADEMICYEAR = '2025/2026' and a.APPLICANTTYPE in (1,2) and s.IDENTITYREFERENCENO = ?
order by s.IDENTITYREFERENCENO
            ",
                [$idno]
            );

           // dd($billed[0]->kuccps_id );

            $kuccps_id = $billed[0]->kuccps_id  ?? null;
           // $kuccps_id = "202237617210148";//$billed[0]->kuccps_id  ?? null;

            $accesstoken = $this->ufbaccesstokencallgenerator() ?? null;


            $url = config('app.ufburldata');

            $query = http_build_query([
                'kuccpsid' => $kuccps_id,
            ]);

            $url = $url . '?' . $query;

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accesstoken)); //setting custom header

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true); //

            $curl_response = curl_exec($curl);

           // dd($curl_response);

            $curl_response = json_encode($curl_response);
            //$status = $curl_response->status ?? null;


            return json_decode($curl_response, true); // 'true' returns associative array

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }





    function safvalidaterequest($accesstoken, $requestData)
    {

        $time = time();
        $curl = curl_init();
        $SAFVALIDATIONREQUEST = env('SAFVALIDATIONREQUEST');

        curl_setopt($curl, CURLOPT_URL, $SAFVALIDATIONREQUEST);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accesstoken)); //setting custom header

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true); //
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);

        $curl_response = curl_exec($curl);

        //dd($curl_response);

        $curl_response = json_decode($curl_response);
        $status = $curl_response->status ?? null;
        $responseRefID = $curl_response->responseRefID ?? null;
        $responseMessage = $curl_response->responseMessage ?? null;


        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($info == '200') {
            if ($status == 'true') {

                $message = 'Phone number successfully verified';
                $servicestatus = 'UP';

                $responsearray = array(

                    'log' => $responseMessage,
                    'message' => $message,
                    'responseRefID' => $responseRefID,
                    'status' => $status,
                    'responsecode' => $info,
                    'servicestatus' => $servicestatus,

                );

                return $responsearray;
            } else if ($status == 'false') {

                $message = 'Enter phone number registered on your name for validation to proceed'; //28577873

                $servicestatus = 'UP';

                $responsearray = array(

                    'log' => $responseMessage,
                    'message' => $message,
                    'responseRefID' => $responseRefID,
                    'status' => $status,
                    'responsecode' => $info,
                    'servicestatus' => $servicestatus,

                );


                return $responsearray;
            }
        }
    }
    /////
    function maskEmailAddress($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($first, $last) = explode('@', $email);
            $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first) - 3), $first);
            $last = explode('.', $last);
            $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', strlen($last['0']) - 1), $last['0']);
            $hideEmailAddress = $first . '@' . $last_domain . '.' . $last['1'];
            return $hideEmailAddress;
        }
    }

    function cross($phone)
    {

        $times = strlen(trim(substr($phone, 3, 6)));
        $cross = '';
        for ($i = 0; $i < $times; $i++) {
            $cross .= 'x';
        }
        return $cross;
    }

    public function registermobile($idno, $first_name, $email, $cellphone)
    {
        $action = 'fetchprofileax';
        $arr = array('idno' => $idno);
        $result = $this->mobiapis($action, $arr);


        if (!is_array($result)) {
            $result = [$result];
            // $result = [];

        }
        //dd(Str::lower($result['FIRSTNAME']));
        if (Arr::has($result, 'IDNO') && Str::lower($result['FIRSTNAME']) === Str::lower($first_name)) {



            $fieldsToSanitize = ['NAME', 'FIRSTNAME', 'LASTNAME', 'MIDDLENAME', 'BIRTHDAY', 'BIRTHMONTH', 'BIRTHYEAR'];
            foreach ($fieldsToSanitize as $field) {
                if (Arr::has($result, $field)) {
                    $result[$field] = str_replace("'", '', $result[$field]);
                }
            }
            $gender = $result['GENDER'];

            $fullname = $result['NAME'];
            $phone = $result['PHONE'];
            $first_Name = $result['FIRSTNAME']; // Assuming 'LASTNAME' is the correct field
            $other_Name = $result['MIDDLENAME'];
            $Surname = $result['LASTNAME'];
            $dob = $result['FULLBIRTHDATE'];
            $Place_of_Birth = 'axdata';
            $birthday = $result['BIRTHDAY'];
            $birthmonth = $result['BIRTHMONTH'];
            $birthyear = $result['BIRTHYEAR'];






            $data = [
                'id_no' => $idno,
                'full_name' => $fullname,
                'gender' => $gender,
                'dob' => $dob,
                'cell_phone' => $cellphone,
                'last_name' => $Surname,
                'mid_name' => $other_Name,
                'first_name' => $first_Name,
                'email_add' => $email,
                'cell_verified' => '1',
                'updated_by' => 'updatenfmdetails',
            ];

            $added = DB::table('tbl_users_nfm')->updateOrInsert(
                ['id_no' => $idno],
                $data
            );

            $cell_verified = '1';
            $cacheKey = "tbl_users_nfm_{$cellphone}_{$cell_verified}";
            //Cache::put($cacheKey,60);







            return response()->json([
                'result' => 'success',
                'message' => $arr
            ], 200);
        } else {

            $action = 'getIPRSDatabyID';
            $arr = array('idno' => $idno);
            $result = $this->datapull($action, $arr);
            if (!is_array($result)) {
                $result = [$result];
            }
            //dd($result);
            if (Arr::has($result, 'idnumber') && Str::lower($result['first_Name']) === Str::lower($first_name)) {

                $gender = $result['gender'];

                $fullname = $result['accountName'];
                $phone = $cellphone;
                $first_Name = $result['first_Name']; // Assuming 'LASTNAME' is the correct field
                $other_Name = $result['other_Name'];
                $Surname = $result['Surname'];
                $dob = $result['dob'];
                $Place_of_Birth = 'axdata';
                $birthday = $result['birthday'];
                $birthmonth = $result['birthmonth'];
                $birthyear = $result['birthyear'];
                $data = [
                    'id_no' => $idno,
                    'full_name' => $fullname,
                    'gender' => $gender,
                    'dob' => $dob,
                    'cell_phone' => $phone,
                    'last_name' => $Surname,
                    'mid_name' => $other_Name,
                    'first_name' => $first_Name,
                    'email_add' => $email,
                    'cell_verified' => '1',
                    'updated_by' => 'updatenfmdetails',
                ];

                $added = DB::table('tbl_users_nfm')->updateOrInsert(
                    ['id_no' => $idno],
                    $data
                );








                return response()->json([
                    'result' => 'success',
                    'message' => $arr
                ], 200);
            } else {

                return response()->json([
                    'result' => 'error',
                    'message' => 'We could not complete your registration.Please try again later'

                ], 200);
            }
        }
    }



    public function iprsAX(Request $request)

    {

        $rules = [
            'idno' => 'required|string|min:4',
            'first_name' => 'required|string|min:2',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }




        $idno = $request->input('idno', ''); // Use default value '' if not set
        $first_name = $request->input('first_name', '');

        $action = 'getIPRSDatabyID';
        $arr = array('idno' => $idno);
        $result = $this->datapull($action, $arr);
        if (!is_array($result)) {
            $result = [$result];
        }
        //dd($result);
        if (Arr::has($result, 'idnumber') && Str::lower($result['first_Name']) === Str::lower($first_name)) {


            return response()->json([
                'result' => 'success',
                'message' => $result
            ], 200);
        } else {

            return response()->json([
                'result' => 'error',
                'message' => $result

            ], 200);
        }
    }


    function phonevalidateAX(Request $request)
    {
        $rules = [
            'idno' => 'required|string|min:4',
            'phone' => 'required|string|min:10',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idnumber = $request->input('idno');
        $cellphone = $request->input('phone');




        $countryCode = '254';
        $numberLength = strlen($cellphone);

        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }


        // $apicontroller = '';
        $apiController = new ApiController();
        $userController = new UserController();


        $result = $userController->kycregister($idnumber, $cellphone, $apiController);

        $verified = json_decode($result)->verified;
        if ($verified == 'yes') {

            return response()->json([
                'result' => 'success',

                'message' => $verified
            ], 200);
        } else {

            $message = 'phone number ' . $cellphone . ' failed verification for id ' . $idnumber . ' Select another option.';

            return response()->json([
                'result' => 'fail',
                'message' => $message
            ], 200);
        }
    }

    public function hashpasswords()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Skip already hashed passwords (assuming passwords are hashed if they start with '$2y$' for bcrypt)
            if (!Hash::needsRehash($user->password)) {
                continue;
            }

            // Hash the password and save the user
            $user->password = Hash::make($user->password);
            $user->save();
        }

        echo 'All user passwords have been hashed successfully.';
    }




    function kycregisterairtelapi($idnumber, $cellphone, $firstname)
    {
        $AIRTELVERIFYAUTH = env('AIRTELVERIFYAUTH');
        $AIRTELTOKEN = env('AIRTELTOKEN');
        $cellphone = substr($cellphone, 3); // Remove the first character


        $variable = $AIRTELVERIFYAUTH . $cellphone;
        $url = $variable;

        $grant_type = env('airtel_grant_type');
        $client_id = env('airtel_client_id');
        $client_secret = env('airtel_client_secret');

        $curl_post_data = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => $grant_type,
        );

        $data_string = json_encode($curl_post_data);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $AIRTELTOKEN,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            )
        );

        $response = curl_exec($curl);
        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response);

        if ($info == '200') {
            $accesstoken = $response->access_token;

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: */*',
                        'X-Country: KE',
                        'X-Currency: KES',
                        'Authorization: Bearer ' . $accesstoken
                    ),
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                )
            );

            $response = curl_exec($curl);
            $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $response = json_decode($response);

            // $message = $response->status->message; //
            $code = $response->status->code; //



            if ($code === '500') {


                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' Not found';
                $status = 'UP';

                $verified = 'no';

                $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


                return $data;
            }


            if ($code === '200') {

                //   dd($response);


                // {#1330 // app\Http\Controllers\ApiController.php:487
                //     +"status": {#1334
                //         +"message": "SUCCESS"
                //         +"code": "200"
                //         +"result_code": "ESB000010"
                //         +"response_code": "DP02200000001"
                //         +"success": true
                //       }
                //       +"data": {#1314
                //         +"first_name": "SIMON"
                //         +"last_name": "MUNGAI"
                //         +"msisdn": "735236688"
                //         +"grade": "MCOM"
                //         +"is_pin_set": true
                //         +"is_barred": false
                //         +"registration": {#1339
                //           +"id": "27210244"
                //           +"status": "MCOM"
                //         }
                //       }
                //     }




                $is_barred = $response->data->is_barred;
                $grade = $response->data->grade;
                $last_name = $response->data->last_name;
                $msisdn = $response->data->msisdn;
                $first_name = $response->data->first_name;
                $is_pin_set = $response->data->is_pin_set;

                $registration_status = $response->data->registration->status;

                $message = $response->status->message;
                $code = $response->status->code;
                $success = $response->status->success;
                $result_code = $response->status->result_code;

                $airtelnames = $last_name . ' ' . $first_name;
                $search = $firstname;

                if (stripos($airtelnames, $search) !== false) {
                    $message = 'Phone number successfully verified';
                    DB::table('airtelkyclogs')->insert([
                        'airtelname' => $airtelnames,
                        'portalname' => $firstname,
                        'is_barred' => $is_barred,
                        'grade' => $grade,
                        'registration_status' => $registration_status,
                        'msisdn' => $msisdn,
                        'is_pin_set' => $is_pin_set,
                        'code' => $code,
                        'success' => $success,
                        'result_code' => $result_code,
                        'message' => $message,
                        'status' => 'true'
                    ]);






                    $message = 'Phone number ' . $cellphone . ' for name ' . $firstname . ' successfully verified';
                    $status = 'UP';


                    $verified = 'yes';

                    $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


                    return $data;
                } else {
                    $message = 'Phone number mismatch';
                    DB::table('airtelkyclogs')->insert([
                        'airtelname' => $airtelnames,
                        'portalname' => $firstname,
                        'is_barred' => $is_barred,
                        'grade' => $grade,
                        'registration_status' => $registration_status,
                        'msisdn' => $msisdn,
                        'is_pin_set' => $is_pin_set,
                        'code' => $code,
                        'success' => $success,
                        'result_code' => $result_code,
                        'message' => $message,
                        'status' => 'false'
                    ]);



                    $message = 'Phone number ' . $cellphone . ' for name ' . $firstname . 'NOT successfully verified';
                    $status = 'UP';


                    $verified = 'no';

                    $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


                    return $data;
                }
            }
        } else {

            $message = 'An error occured while validating Phone number ' . $cellphone . ' for name ' . $firstname;
            $status = 'UP';


            $verified = 'no';

            $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


            return $data;
        }
    }

    /**
     * Return a list of institutions for autocomplete.
     * GET /api/institutions?q=search
     */
    public function getInstitutions(Request $request)
    {
        $search = $request->query('q');
        $query = DB::table('lminstitution')->select('RECID', 'INSTITUTIONNAME');

        //dd($query);
        if ($search) {
            $query->where('INSTITUTIONNAME', 'like', '%' . $search . '%');
        }
        $institutions = $query->orderBy('INSTITUTIONNAME')->limit(20)->get();

        //dd($institutions);
        return response()->json($institutions);
    }

    /**
     * Return a list of courses for autocomplete.
     * GET /api/courses?q=search
     */
    public function getCourses(Request $request)
    {
        $search = $request->query('q');
        $query = DB::table('lmcourses')->select('RECID', 'COURSEDESCRIPTION');
        if ($search) {
            $query->where('COURSEDESCRIPTION', 'like', '%' . $search . '%');
        }
        $courses = $query->orderBy('COURSEDESCRIPTION')->limit(20)->get();
        return response()->json($courses);
    }

    /**
     * Return a list of counties for autocomplete.
     * GET /api/counties?q=search
     */
    public function getCounties(Request $request)
    {
        $search = $request->query('q');
        $query = DB::table('cre_counties')->select('county_id', 'county_name');
        if ($search) {
            $query->where('county_name', 'like', '%' . $search . '%');
        }
        $counties = $query->orderBy('county_name')->limit(20)->get();
        return response()->json($counties);
    }

    /**
     * Return a list of towns for autocomplete.
     * GET /api/towns?q=search
     */
    public function getTowns(Request $request)
    {
        $search = $request->query('q');
        $query = DB::table('cre_towns')->select('id', 'town_name');
        if ($search) {
            $query->where('town_name', 'like', '%' . $search . '%');
        }
        $towns = $query->orderBy('town_name')->limit(20)->get();
        return response()->json($towns);
    }

    /**
     * Return a list of towns for a given county_id.
     * GET /api/towns-by-county?county_id=XX
     */
    public function getTownsByCounty(Request $request)
    {
        $countyId = $request->query('county_id');
        $query = DB::table('cre_towns')->select('id', 'town_name');
        if ($countyId) {
            $query->where('county_id', $countyId);
        }
        $towns = $query->orderBy('town_name')->get();
        return response()->json($towns);
    }
}
