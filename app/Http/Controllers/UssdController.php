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
// use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class UssdController extends Controller
{





    public function phpinfo()
    {
        echo '<pre>';
        var_dump(exec("gs -v"));
        echo '</pre>';
        phpinfo();
    }



    public function phonenumberchange(Request $request, ApiController $apiController, UserController $userController)
    {

        // $logs =  DB::table('android_log')->insert(['message' => json_encode($request)]);


        //eref
        //  dd($request);

        // idno:
        // oldphone:
        // newphone:
        // name:
        // telcoprovider:

        //$user = auth()->user()->name;
        $phonenumberdropdown = $request->telcoprovider;
        $firstname = $request->name;

        $cellphone = $request->newphone;
        $oldphone = $request->oldphone;

        $idnumber = $request->idno;


        // $phonenumberdropdown = 'safaricom';
        // $firstname = 'tony';

        // $cellphone = '254711506060';
        // $oldphone = '254711506060';

        // $idnumber = '27557021';






        //$phonenumberdropdown = 'safaricom'; // Example value

        $countryCode = '254';
        $numberLength = strlen($cellphone);

        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }



        $code = $apiController->uniqueStr2(4);

        $mcode = "[#] HELB:$code is your code to verify phone number change. 2aUYAkIzflK";


        $action = 'sendphoneverificationCode';
        $arr = [
            'recipient' => $cellphone,
            'verificationcode' => $mcode,
            'msg_priority' => '204',
            'category' => '395'
        ];



        switch ($phonenumberdropdown) {
            case 'safaricom':
                // dd("saf");

                // Code for safaricom
                $data = $userController->kycregister($idnumber, $cellphone, $apiController);
                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];

                if ($verified === 'yes') {
                    $result = $apiController->datapull($action, $arr);

                    //dd($result);
                    $insert = DB::table('ussd_phone_update')->insert([
                        'otp' => $code,
                        'idno' => $idnumber,
                        'phone' => $oldphone,
                    ]);


                    // // dd($verified);
                    // $this->verifyotp($otp,$phone);




                    // $data = $this->replaceussdline($firstname, $idnumber, $cellphone, $apiController);

                    // return $data;

                    return json_encode(array('result' => 'success', 'message' => 'successfully verified safaricom'));
                } else {


                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 1cant be verified';



                    return json_encode(array('result' => 'fail', 'message' => $message));
                }


            case 'airtel':

                $data = $userController->kycregisterairtel($idnumber, $cellphone, $firstname, $apiController);

                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];
                if ($verified === 'yes') {
                    // $data = $this->replaceussdline($firstname, $idnumber, $cellphone, $apiController);

                    // return $data;
                    $result = $apiController->datapull($action, $arr);
                    $insert = DB::table('ussd_phone_update')->insert([
                        'otp' => $code,
                        'idno' => $idnumber,
                        'phone' => $oldphone,
                    ]);


                    return json_encode(array('result' => 'success', 'message' => 'airtel number cant be verified'));
                } else {

                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 2cant be verified';



                    return json_encode(array('result' => 'fail', 'message' => $message));
                }
                break;
            default:
                //dd("non");

                // Code for other cases
                $data = $userController->kycregister($idnumber, $cellphone, $apiController);
                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];
                if ($verified === 'yes') {
                    // $data = $this->replaceussdline($firstname, $idnumber, $cellphone, $apiController);

                    // return $data;

                    $result = $apiController->datapull($action, $arr);
                    $insert = DB::table('ussd_phone_update')->insert([
                        'otp' => $code,
                        'idno' => $idnumber,
                        'phone' => $oldphone,
                    ]);
                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' verified';



                    return json_encode(array('result' => 'success', 'message' => $message));
                } else {

                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 3cant be verified';



                    return json_encode(array('result' => 'fail', 'message' => $message));
                }
        }





        //return response()->json($result);


    }

    public function verifyotp(Request $request, ApiController $apiController)


    {

        $otp = $request->otp;

        $firstname = $request->name;

        $cellphone = $request->newphone;
        $phone = $request->oldphone;

        $idnumber = $request->idno;

        $codeclean = str_replace(['"', "'"], '', $otp);

        $countryCode = '254';
        $numberLength = strlen($phone);



        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $namba = $countryCode . substr($phone, 1);
            } else {
                $namba = $countryCode . $phone;
            }
        } else {

            $namba =  $phone;
        }


        // dd($namba);



        $data = DB::table('ussd_phone_update')
            ->where('phone', $namba)
            ->orderByDesc('id')
            ->first(['otp']); //0758177224


        if (!empty($data) && $data->otp == $codeclean) {
            //  dd($data->otp.'----'.$codeclean);

            // $idno = $data->idno;
            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_phone_update')
                ->where('phone', $namba)
                ->where('otp', $codeclean)

                ->update(['verified' => '1']);

            // DB::table('ussd_phone_update')
            //   //s  ->where('idno', $idno)
            //     ->where('otp', '!=', $codeclean)
            //     ->delete();

            $data = $this->replaceussdline($firstname, $idnumber, $cellphone, $apiController);

            return $data;
        } else {
            return response()->json([
                'result' => 'error',
                'message' => 'otp not verified'
            ]);
        }
    }


    function replaceussdline($firstname, $idnumber, $cellphone, ApiController $apiController)
    {

        // $user = auth()->user()->name;

        $cellphone = str_replace(['"', "'"], '', $cellphone);

        $countryCode = '254';
        $numberLength = strlen($cellphone);



        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }







        $action = 'updatetelcotestenvironment';
        $arr = array('phone' => $cellphone, 'idno' => $idnumber);


        $result96 = $apiController->mobiapis($action, $arr);

        //dd($result96);


        if (!is_array($result96)) {
            $result96 = [$result96];
        }

        $idno = $result96['idnumber'] ?? null;
        $code = $result96['code'] ?? null;
        $missing = $result96['missing'] ?? null;
        $result = $result96['result'] ?? null;



        if (!empty($missing)) {



            // $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' cant be found';

            return json_encode(array('result' => 'error', 'message' => $missing));
        }

        if (!empty($code)) {

            if ($code == -14) {

                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' updated';

                return json_encode(array('result' => 'success', 'message' => $message));
            } else {

                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . 'not updated';

                return json_encode(array('result' => 'failed', 'message' => $message));
            }
        }

        if (!empty($result) && ($result === 'success')) {


            $insert = DB::table('tbl_ussd_updates')->insert([
                'student_name' => $cellphone,
                'student_id' => $idnumber,
                'updated_by' => $firstname,
            ]);

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' updated';

            //dd($message);


            return json_encode(array('result' => 'success', 'message' => $message));
        }
        return json_encode(array('result' => 'error', 'message' => (string) $result96));
    }


    // public function idnumberocrvalidation(Request $request)
    // {
    //     // Path to uploaded image

    //    // dd("ffffff");


    //     $rules = [
    //         'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //         // 'image' => 'required|image|mimes:jpeg,png,jpg',

    //     ];

    //     // Validate the request
    //     $validator = Validator::make($request->all(), $rules);

    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $imagePath = $request->file('image')->getPathname();

    //     // Perform OCR
    //     $text = (new TesseractOCR($imagePath))->lang('eng')->run();

    //    // dd($text);

    //     // Expected fields for Birth Certificate
    //     $expectedPatterns = [
    //         // "Birth Place" => "/Birth/i", // Just check if 'Birth' exists
    //         "Entry Number" => "/Entry/i", // Just check if 'Entry' exists
    //         // "Sex" => "/Sex/i", // Just check if 'Sex' exists
    //         // "Father's Name" => "/Father/i", // Just check if 'Father' exists

    //     ];

    //     // Validate extracted text
    //     $failedPatterns = [];
    //     $successfulPatterns = [];

    //     foreach ($expectedPatterns as $field => $pattern) {
    //         if (preg_match($pattern, $text, $matches)) {
    //             $successfulPatterns[$field] = $matches[0]; // Extracted text
    //         } else {
    //             $failedPatterns[$field] = "Expected pattern: " . $pattern;
    //         }
    //     }

    //     // Return JSON response
    //     $successfulPatternsString = implode(', ', $successfulPatterns);

    //     return response()->json([
    //         'message' => empty($failedPatterns) ? 'Document format is valid ' . $successfulPatternsString : 'Invalid document format',
    //         'successful_patterns' => $successfulPatterns,
    //         'failed_patterns' => $failedPatterns,
    //         'extracted_text' => $text // Optional: Helps with debugging
    //     ], empty($failedPatterns) ? 200 : 400);
    // }







    public function birthcertificatevalidationx(Request $request)
    {
        // Path to uploaded image


        $rules = [
            'birthno' => 'required',
            'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Allow images and PDFs up to 5MB
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->file('image')->getPathname();

        // Perform OCR
        $text = (new TesseractOCR($imagePath))->lang('eng')->run();

        // Expected fields for Birth Certificate
        $expectedPatterns = [
            // "Birth Place" => "/Birth/i", // Just check if 'Birth' exists
            "Entry Number" => "/Entry/i", // Just check if 'Entry' exists
            // "Sex" => "/Sex/i", // Just check if 'Sex' exists
            // "Father's Name" => "/Father/i", // Just check if 'Father' exists

        ];

        // Validate extracted text
        $failedPatterns = [];
        $successfulPatterns = [];

        foreach ($expectedPatterns as $field => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $successfulPatterns[$field] = $matches[0]; // Extracted text
            } else {
                $failedPatterns[$field] = "Expected pattern: " . $pattern;
            }
        }

        // Return JSON response
        $successfulPatternsString = implode(', ', $successfulPatterns);

        return response()->json([
            'message' => empty($failedPatterns) ? 'Document format is valid ' . $successfulPatternsString : 'Invalid document format',
            'successful_patterns' => $successfulPatterns,
            'failed_patterns' => $failedPatterns,
            'extracted_text' => $text // Optional: Helps with debugging
        ], empty($failedPatterns) ? 200 : 400);
    }


    public function idnumbervalidationai(Request $request, ApiController $apiController)
    {

        dd("jjj");

        try {
            // Validation rules
            $rules = [
                'idno' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //$microsoftendpoint = 'https://prod-208.westeurope.logic.azure.com:443/workflows/1d01e9b7af8d4622929cba32e55dd404/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=BC1ig_CJJh1vEI7he2IWatylIhms4WAlYeE4T7xmk78';

            $microsoftendpoint = config('app.microsoftendpoint');


            $idno = $request->idno;
            $file = $request->file('image');

            // Get original file name
            $filename = $file->getClientOriginalName();
            $filetype = $file->getMimeType(); // e.g. image/png

            // Get file contents and base64 encode
            $filecontent = base64_encode(file_get_contents($file->getRealPath()));

            // Example structure to pass to OCR system
            // $payload = [
            //     'idno' => $idno,
            //     'filename' => $filename,
            //     'filecontent' => $filecontent,
            // ];

            // Now you can pass this to your OCR service
            // For example: Http::post('http://ocr-service/parse', $payload);

            // return response()->json(['message' => 'File ready for OCR', 'payload' => $payload]);


            // JSON payload
            $payload = json_encode([
                'idno' => $idno,
                'filename' => $filename,
                'filetype'   => $filetype,

                'filecontent' => $filecontent,
            ]);


            $data = $apiController->aimicrosoft($microsoftendpoint, $payload);

            // dd($data);


            $ocrText = $data['response'] ?? '';

            $found = str_contains($ocrText, $idno);
            return response()->json([
                'idno' => $idno,
                'found' => $found,
                'data' => $ocrText // true or false
                // true or false
            ]);




            // $accessToken = $data['access_token'];
            return $data ?? null;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
        }
    }




    public function birthcertificatevalidationai(Request $request, ApiController $apiController)
    {

        try {
            // Validation rules
            $rules = [
                'birthno' => 'required',
                'idno' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //$microsoftendpoint = 'https://prod-208.westeurope.logic.azure.com:443/workflows/1d01e9b7af8d4622929cba32e55dd404/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=BC1ig_CJJh1vEI7he2IWatylIhms4WAlYeE4T7xmk78';

            $microsoftendpoint = config('app.microsoftendpoint');


            $idno = $request->idno;
            $birthno = $request->birthno;

            $file = $request->file('image');

            // Get original file name
            $filename = $file->getClientOriginalName();

            // Get file contents and base64 encode
            $filecontent = base64_encode(file_get_contents($file->getRealPath()));

            // Example structure to pass to OCR system
            // $payload = [
            //     'idno' => $idno,
            //     'filename' => $filename,
            //     'filecontent' => $filecontent,
            // ];

            // Now you can pass this to your OCR service
            // For example: Http::post('http://ocr-service/parse', $payload);

            // return response()->json(['message' => 'File ready for OCR', 'payload' => $payload]);


            // JSON payload
            $payload = json_encode([
                'idno' => $idno,
                'filename' => $filename,
                'filecontent' => $filecontent,
            ]);


            $data = $apiController->aimicrosoft($microsoftendpoint, $payload);

            // dd($data);


            $ocrText = $data['response'] ?? '';


            // Expected fields for Birth Certificate
            $expectedPatterns = [
                // "Name" => "/Name/i", // Just check if 'Birth' exists
                // "Birth Certificate" => "/CERTIFICATE OF BIRTH/i", // Just check if 'Entry' exists
                "Birth Certificate" => "/C\s*E\s*R\s*T\s*I\s*F\s*I\s*C\s*A\s*T\s*E\s*\s*O\s*F\s*\s*B\s*I\s*R\s*T\s*H/i",

                // "Sex" => "/Sex/i", // Just check if 'Sex' exists
                // "Father's Name" => "/Father/i", // Just check if 'Father' exists

            ];

            // // Validate extracted text
            $failedPatterns = [];
            $successfulPatterns = [];

            foreach ($expectedPatterns as $field => $pattern) {
                if (preg_match($pattern, $ocrText, $matches)) {
                    $successfulPatterns[$field] = $matches[0]; // Extracted text

                } else {
                    $failedPatterns[$field] = "Expected pattern: " . $pattern;
                }
            }

            // Return JSON response
            $successfulPatternsString = implode(', ', $successfulPatterns);

            if (!empty($failedPatterns)) {
                return response()->json([
                    'message' => 'Invalid document format',
                    'found' => 0,
                    'successful_patterns' => $successfulPatterns,
                    'failed_patterns' => $failedPatterns,
                    'extracted_text' => $ocrText // Optional: Helps with debugging
                ], 400);
            }


            $found = str_contains($ocrText, $birthno);
            $found = preg_match('/\b' . preg_quote($birthno, '/') . '\b/', $ocrText);
            return response()->json([
                'birthno' => $birthno,
                'found' => $found,
                'data' => $ocrText // true or false
                // true or false
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
        }
    }

    public function deathcertificatevalidationai(Request $request, ApiController $apiController)
    {

        try {
            // Validation rules
            $rules = [
                'deathno' => 'required',
                'idno' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //$microsoftendpoint = 'https://prod-208.westeurope.logic.azure.com:443/workflows/1d01e9b7af8d4622929cba32e55dd404/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=BC1ig_CJJh1vEI7he2IWatylIhms4WAlYeE4T7xmk78';

            $microsoftendpoint = config('app.microsoftendpoint');


            $idno = $request->idno;
            $deathno = $request->deathno;

            $file = $request->file('image');

            // Get original file name
            $filename = $file->getClientOriginalName();

            // Get file contents and base64 encode
            $filecontent = base64_encode(file_get_contents($file->getRealPath()));

            // Example structure to pass to OCR system
            // $payload = [
            //     'idno' => $idno,
            //     'filename' => $filename,
            //     'filecontent' => $filecontent,
            // ];

            // Now you can pass this to your OCR service
            // For example: Http::post('http://ocr-service/parse', $payload);

            // return response()->json(['message' => 'File ready for OCR', 'payload' => $payload]);


            // JSON payload
            $payload = json_encode([
                'idno' => $idno,
                'filename' => $filename,
                'filecontent' => $filecontent,
            ]);


            $data = $apiController->aimicrosoft($microsoftendpoint, $payload);

            // dd($data);


            $ocrText = $data['response'] ?? '';


            // Expected fields for Birth Certificate
            $expectedPatterns = [
                // "Name" => "/Name/i", // Just check if 'Birth' exists
                // "Birth Certificate" => "/CERTIFICATE OF BIRTH/i", // Just check if 'Entry' exists
                "Death Certificate" => "/C\s*E\s*R\s*T\s*I\s*F\s*I\s*C\s*A\s*T\s*E\s*\s*O\s*F\s*\s*D\s*E\s*A\s*T\s*H/i",
                // "Sex" => "/Sex/i", // Just check if 'Sex' exists
                // "Father's Name" => "/Father/i", // Just check if 'Father' exists

            ];

            // // Validate extracted text
            $failedPatterns = [];
            $successfulPatterns = [];

            foreach ($expectedPatterns as $field => $pattern) {
                if (preg_match($pattern, $ocrText, $matches)) {
                    $successfulPatterns[$field] = $matches[0]; // Extracted text

                } else {
                    $failedPatterns[$field] = "Expected pattern: " . $pattern;
                }
            }

            // Return JSON response
            $successfulPatternsString = implode(', ', $successfulPatterns);

            if (!empty($failedPatterns)) {
                return response()->json([
                    'message' => 'Invalid document format',
                    'found' => 0,
                    'successful_patterns' => $successfulPatterns,
                    'failed_patterns' => $failedPatterns,
                    'extracted_text' => $ocrText // Optional: Helps with debugging
                ], 400);
            }


            $found = str_contains($ocrText, $deathno);
            $found = preg_match('/\b' . preg_quote($deathno, '/') . '\b/', $ocrText);
            return response()->json([
                'deathno' => $deathno,
                'found' => $found,
                'data' => $ocrText // true or false
                // true or false
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
        }
    }






    public function idnumbervalidation(Request $request)
    {
        // Path to uploaded image


        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'image' => 'required|image|mimes:jpeg,png,jpg',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->file('image')->getPathname();

        // Perform OCR
        $text = (new TesseractOCR($imagePath))->lang('eng')->run();

        // Expected fields for Birth Certificate
        $expectedPatterns = [
            // "Birth Place" => "/Birth/i", // Just check if 'Birth' exists
            "Entry Number" => "/Entry/i", // Just check if 'Entry' exists
            // "Sex" => "/Sex/i", // Just check if 'Sex' exists
            // "Father's Name" => "/Father/i", // Just check if 'Father' exists

        ];

        // Validate extracted text
        $failedPatterns = [];
        $successfulPatterns = [];

        foreach ($expectedPatterns as $field => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $successfulPatterns[$field] = $matches[0]; // Extracted text
            } else {
                $failedPatterns[$field] = "Expected pattern: " . $pattern;
            }
        }

        // Return JSON response
        $successfulPatternsString = implode(', ', $successfulPatterns);

        return response()->json([
            'message' => empty($failedPatterns) ? 'Document format is valid ' . $successfulPatternsString : 'Invalid document format',
            'successful_patterns' => $successfulPatterns,
            'failed_patterns' => $failedPatterns,
            'extracted_text' => $text // Optional: Helps with debugging
        ], empty($failedPatterns) ? 200 : 400);
    }





    public function uploadAndDetectFace(Request $request)
    {

        //dd($request);

        // $request->validate([
        //      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        //     //'image' => 'required|image|mimes:jpeg,png,jpg',

        // ]);

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'image' => 'required|image|mimes:jpeg,png,jpg',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // Store image in Laravel's storage directory
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = storage_path('app/public/' . $imageName);

        $image->move(storage_path('app/public/'), $imageName);


        // Call Python script for face detection
        ///local query////
        $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_face.py')) . " " . escapeshellarg($imagePath));
        // $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_dlib_cnn.py.py')) . " " . escapeshellarg($imagePath));


        //dd($output);
        $scriptPath = storage_path('app/python/detect_face.py'); // Ensure this is correct
        //  $scriptPath = storage_path('app/python/detect_dlib_cnn.py'); // Ensure this is correct



        // Fix the command
        $command = 'python ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath);

        // Run the command and capture errors


        $output = shell_exec($command . ' 2>&1');
        // Decode the JSON output
        //$result = json_decode($output, true);

        //dd($result);
        // Trim and try decoding the output
        $stringOutput = trim($output);
        $result = json_decode($stringOutput, true);

        if ($result !== null && array_key_exists('success', $result)) {
            if ($result['success'] === true) {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '✅ Image contains a human face.',
                    'status' => true,
                    'image' => asset('storage/' . $imageName)
                ]);
            } else {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '❌ No human face detected. Please upload a valid image.',
                    'status' => false
                ]);
            }
        } else {
            // In case of JSON decoding error or malformed Python response
            return response()->json([
                'stringout' => $stringOutput,
                'message' => '⚠️ Error processing image. Try again.',
                'status' => false
            ]);
        }
    }

    public function uploadanddetectfaceai(Request $request)
    {

        //dd($request);

        // $request->validate([
        //      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        //     //'image' => 'required|image|mimes:jpeg,png,jpg',

        // ]);

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'image' => 'required|image|mimes:jpeg,png,jpg',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // Store image in Laravel's storage directory
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = storage_path('app/public/' . $imageName);

        $image->move(storage_path('app/public/'), $imageName);


        // Call Python script for face detection
        ///local query////
        $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_face.py')) . " " . escapeshellarg($imagePath));
        // $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_dlib_cnn.py.py')) . " " . escapeshellarg($imagePath));


        //dd($output);
        $scriptPath = storage_path('app/python/detect_face.py'); // Ensure this is correct
        //  $scriptPath = storage_path('app/python/detect_dlib_cnn.py'); // Ensure this is correct



        // Fix the command
        $command = 'python ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath);

        // Run the command and capture errors


        $output = shell_exec($command . ' 2>&1');
        // Decode the JSON output
        //$result = json_decode($output, true);

        //dd($result);
        // Trim and try decoding the output
        $stringOutput = trim($output);
        $result = json_decode($stringOutput, true);

        if ($result !== null && array_key_exists('success', $result)) {
            if ($result['success'] === true) {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '✅ Image contains a human face.',
                    'status' => true,
                    'image' => asset('storage/' . $imageName)
                ]);
            } else {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '❌ No human face detected. Please upload a valid image.',
                    'status' => false
                ]);
            }
        } else {
            // In case of JSON decoding error or malformed Python response
            return response()->json([
                'stringout' => $stringOutput,
                'message' => '⚠️ Error processing image. Try again.',
                'status' => false
            ]);
        }
    }








    public function uploadAndDetectFacedlip(Request $request)
    {

        //  dd($request);

        // $request->validate([
        //      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        //     //'image' => 'required|image|mimes:jpeg,png,jpg',

        // ]);

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'image' => 'required|image|mimes:jpeg,png,jpg',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // Store image in Laravel's storage directory
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = storage_path('app/public/' . $imageName);

        $image->move(storage_path('app/public/'), $imageName);


        // Call Python script for face detection
        ///local query////
        // $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_face.py')) . " " . escapeshellarg($imagePath));
        $output = shell_exec("python " . escapeshellarg(storage_path('app/python/detect_dlib_cnn.py')) . " " . escapeshellarg($imagePath));


        //dd($output);
        //  $scriptPath = storage_path('app/python/detect_face.py'); // Ensure this is correct
        $scriptPath = storage_path('app/python/detect_dlib_cnn.py'); // Ensure this is correct



        // Fix the command
        $command = 'python ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath);

        // Run the command and capture errors


        $output = shell_exec($command . ' 2>&1');
        // Decode the JSON output
        //$result = json_decode($output, true);

        //dd($result);
        // Trim and try decoding the output
        $stringOutput = trim($output);
        $result = json_decode($stringOutput, true);

        if ($result !== null && array_key_exists('success', $result)) {
            if ($result['success'] === true) {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '✅ Image contains a human face.',
                    'status' => true,
                    'image' => asset('storage/' . $imageName)
                ]);
            } else {
                return response()->json([
                    'stringout' => $stringOutput,
                    'message' => '❌ No human face detected. Please upload a valid image.',
                    'status' => false
                ]);
            }
        } else {
            // In case of JSON decoding error or malformed Python response
            return response()->json([
                'stringout' => $stringOutput,
                'message' => '⚠️ Error processing image. Try again.',
                'status' => false
            ]);
        }
    }



    public function registerUssd(Request $request, ApiController $apiController)
    {
        //dd($idno);


        // Validate the request inputs
        $validatedData = $request->validate([
            'idno' => 'required|string|max_digits:9', // Ensures idno is less than 9 characters
            'phone' => 'required|string|min:10',
            'serial_number' => 'required|string',
        ]);

        // Sanitize the inputs by removing single and double quotes
        $idno = str_replace(['"', "'"], '', $validatedData['idno']);
        $phone = str_replace(['"', "'"], '', $validatedData['phone']);
        $serial_number = str_replace(['"', "'"], '', $validatedData['serial_number']);

        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }


        $action = 'fetchprofileax';
        $arr = array('idno' => $idno);
        $result = $apiController->mobiapis($action, $arr);


        if (!is_array($result)) {
            $result = [$result];
            // $result = [];

        }

        if (Arr::has($result, 'IDNO') && Str::lower($result['PHONE']) === Str::lower($phone)) {
            $axphone = $result['PHONE'];



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
            $idno = $result['IDNO'];



            $arr = array(
                'idno' => $idno,
                'accountName' => $fullname,
                'Surname' => $Surname,
                'first_Name' => $first_Name,
                'other_Name' => $other_Name,
                'dob' => $dob, //substr($retval['Date_of_Birth'],0,-12),
                'gender' => $gender,
                'idnumber' => $idno,
                'Place_of_Birth' => $Place_of_Birth,
                'birthmonth' => $birthmonth,
                'birthday' => $birthday,
                'birthyear' => $birthyear
            );

            //dd($arr);


            return response()->json([
                'result' => 'success',
                'message' => $arr
            ], 200);
        } else {
            // dd($idno);

            $action = 'getIPRSDatabyID';
            $arr = array('serial_number' => $serial_number);
            $result = $apiController->datapull($action, $arr);
            if (!is_array($result)) {
                $result = [$result];
            }
            if (array_key_exists('idnumber', $result) && !empty($result['idnumber']) && $result['idnumber'] == $idno) {
                $dta = array(
                    'idno' => $result['idnumber'],
                    'accountName' => $result['accountName'],
                    'Surname' => $result['Surname'],
                    'first_Name' => $result['first_Name'],
                    'other_Name' => $result['other_Name'],
                    'dob' => $result['dob'], //substr($retval['Date_of_Birth'],0,-12),
                    'gender' => $result['gender'],
                    'idnumber' => $result['idnumber'],
                    'Place_of_Birth' => $result['Place_of_Birth'],
                    'birthmonth' => $result['birthmonth'],
                    'birthday' => $result['birthday'],
                    'birthyear' => $result['birthyear']

                );
                return response()->json([
                    'result' => 'success',
                    'message' => $dta
                ], 200);
                //$updateapplicants = $this->auth_model->AXInstitutionData($idno, $prod);
            } else {


                return response()->json([
                    'result' => 'fail',
                    'message' =>  'no registration records '
                ], 200);
            }
        }
    }
    public function serviceprovidersqueryone(Request $request, ApiController $apiController)
    {







        // Retrieve input data from POST request
        $action = $request->input('action');
        $idno = $request->input('idno');
        $username = $request->input('username');
        $password = $request->input('password');
        $phonesaf = $request->input('phonesaf');
        $amountsent = $request->input('amountsent');
        $academicyear = $request->input('academicyear');
        $certno = $request->input('certno');


        $checkifauthorized = $this->checkifauthorized($idno);

        if ($checkifauthorized == 1) {
        } else {

            return 'forbidden';
        }








        // Generate a cache key
        $cacheKey = "service_providers_{$action}_{$idno}_{$phonesaf}_{$amountsent}_{$academicyear}_{$certno}";

        // Prepare data array
        $arr = [
            'idno' => $idno,
            'action' => $action,
            'phonesaf' => $phonesaf,
            'amountsent' => $amountsent,
            'academicyear' => $academicyear,
            'certno' => $certno,
        ];

        // Get response from cache or fetch new data
        $result = Cache::remember($cacheKey, 60 * 60, function () use ($action, $arr, $apiController) { // Cache for 1 hour
            $result = $apiController->vendors($action, $arr);

            if (is_array($result) && !empty($result)) {
                return $result; // Store in cache if valid
            }

            // Handle case when API response is invalid
            return ($action == 'getussdLogin') ? ['loanee' => 'YES'] : ['error' => 'Invalid response from vendor'];
        });
        //$result = $apiController->vendors($action, $arr);

        return response()->json($result);
    }

    function registerussdnoid(Request $request, ApiController $apiController)
    {
        // Validate the request inputs
        // dd($request);
        $validatedData = $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $phone = str_replace(['"', "'"], '', $validatedData['phone']);

        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }
        //  dd($phone);

        $data = Cache::remember('cached_loans_' . $phone, 60, function () use ($phone) {
            return DB::table('tbl_whitelisted')
                //->select('phone')
                ->where('phone', $phone)
                ->first();
        });

        //  dd($data);
        $failmessage = 'Kindly visit HELB offices for activation of this service or call 0711052000';

        if ($data) {
            $phone = $data->phone;
            $idno = $data->studentid;
            $action = 'fetchprofileax';
            $arr = array('idno' => $idno);
            $result = $apiController->mobiapis($action, $arr);


            if (!is_array($result)) {
                $result = [$result];
                // $result = [];

            }
            if (Arr::has($result, 'IDNO')) {

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


                $arr = array(
                    'idno' => $idno,
                    'accountName' => $fullname,
                    'Surname' => $Surname,
                    'first_Name' => $first_Name,
                    'other_Name' => $other_Name,
                    'dob' => $dob, //substr($retval['Date_of_Birth'],0,-12),
                    'gender' => $gender,
                    'idnumber' => $idno,
                    'Place_of_Birth' => $Place_of_Birth,
                    'birthmonth' => $birthmonth,
                    'birthday' => $birthday,
                    'birthyear' => $birthyear
                );

                //dd($arr);


                return response()->json([
                    'result' => 'success',
                    'message' => $arr
                ], 200);
            } else {

                return response()->json([
                    'result' => 'success',
                    'message' => $arr
                ], 200);


                // return response()->json([
                //     'result' => 'fail',
                //     'message' => $failmessage
                // ], 200);
            }
        } else {
            return response()->json([
                'result' => 'fail',
                'message' => $failmessage
            ], 200);
        }
    }



    function loginussd(Request $request)
    {

        //254790175434


        // $user = DB::connection('sqlsrvsms')
        // ->table('dbo.mobile_users')

        // ->where('user_name', '254727045828')
        // ->select('user_name', 'date_modified','id_no') // Ensure date_modified is selected
        // ->first();

        // dd($user->id_no);





        // Validate the request inputs
        $validatedData = $request->validate([
            'phone' => 'required|string|min:10',
            'pin'   => 'required|string|min:4',
        ]);

        $phone = str_replace(['"', "'"], '', $validatedData['phone']);
        $pin   = str_replace(['"', "'"], '', $validatedData['pin']);



        $authorizationkey = Carbon::now('Africa/Nairobi')->format('Y-m-d') . $phone;





        // Format phone number
        $countryCode  = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }

        // // Encrypt PIN
        // $Stringsalt      = '2AFIoT1wFTNrj3g7ZVSpft5juiRSyGwZ2vD107U6SUqlN9Mqhye09wxwJWPwwEfkPfkxQOpRDT64HzYu';
        // $Stringcleartext = $phone . $Stringsalt . $pin;
        // $encryptedPin    = hash('sha256', $Stringcleartext);

        // Generate cache key
        $cacheKey = "user_login:$phone";

        // Check Redis Cache
        $cachedUser = Cache::get($cacheKey);

        if ($cachedUser && isset($cachedUser['date_modified'])) {
            $latestDateModified = DB::connection('sqlsrvsms')
                ->table('dbo.mobile_users')
                ->where('user_name', $phone)
                ->where('user_pwd', $pin)

                ->value('date_modified');

            if ($cachedUser['date_modified'] == $latestDateModified) {
                return response()->json(['status' => 'success', 'data' => $cachedUser]);
            }
        }

        // Fetch from DB if cache is not available or date_modified has changed
        $user = DB::connection('sqlsrvsms')
            ->table('dbo.mobile_users')
            ->where('user_pwd', $pin)
            ->where('user_name', $phone)
            ->select('user_name', 'date_modified', 'id_no') // Ensure date_modified is selected
            ->first();

        if ($user) {
            // Store in cache with a 30-minute expiration
            //  Cache::put($cacheKey, (array) $user, now()->addMinutes(30));
            Cache::put($cacheKey, (array) $user, now()->addHours(48));

            ///////////////////////////////////////////
            $datakey = [

                'authkey'  => $authorizationkey,
                'phone'  => $phone,
                'id_no'  => $user->id_no




            ];

            $id_no = $user->id_no;

            $cacheauthKey = 'authkey_exists_' . $id_no;


            $exists = Cache::remember($cacheauthKey, now()->addMinutes(1), function () use ($phone, $id_no) {
                return DB::table('ussdauthorizationkey')
                    ->where('id_no', $id_no)
                    ->exists();
            });

            if (!$exists) {
                DB::table('ussdauthorizationkey')->insert($datakey);

                // Update cache to reflect new state
                Cache::put($cacheauthKey, true, now()->addMinutes(1));
            }



            /////////////////////////////////////////////////////

            return response()->json(['status' => 'success', 'data' => $user]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }




    public function checkifauthorized($idno)
    {

        $cacheKey = 'authkey_exists_' . $idno;

        // Check cache only
        $cached = Cache::get($cacheKey);

        if ($cached) {
            // It exists, no need to query DB
            return 1;
        }

        // If not cached, you may choose to query DB or handle accordingly
        $exists = DB::table('ussdauthorizationkey')
            ->where('id_no', $idno)
            ->exists();

        if ($exists) {
            return  1;
        } else {

            return 2;
        }

        // Optional: Handle case where it still doesn’t exist







    }



    function authorizesimchange(Request $request)
    {





        // Validate the request inputs
        $validatedData = $request->validate([
            'phone' => 'required|string|min:10',
            'idno'   => 'required|string|min:4',
        ]);

        $phone = str_replace(['"', "'"], '', $validatedData['phone']);
        $idno   = str_replace(['"', "'"], '', $validatedData['idno']);

        // Format phone number
        $countryCode  = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }



        $checkifauthorized = $this->checkifauthorized($idno);

        if ($checkifauthorized == 1) {
        } else {

            return 'forbidden';
        }


        // Generate Redis cache key
        $cacheKey = "simchange:$phone:$idno";

        // Check if the data is already cached
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);

            // If date_modified hasn't changed, return cached data
            if (isset($cachedData['date_modified'])) {
                $user = DB::connection('sqlsrvsms')
                    ->table('dbo.mobile_users')
                    ->where('id_no', $idno)
                    ->where('user_name', $phone)
                    ->select('date_modified')
                    ->first();

                if ($user && $user->date_modified == $cachedData['date_modified']) {
                    return response()->json(['status' => 'success', 'data' => $cachedData]);
                }
            }
        }

        // If not cached or date_modified has changed, query the database
        $user = DB::connection('sqlsrvsms')
            ->table('dbo.mobile_users')
            ->where('id_no', $idno)
            ->where('user_name', $phone)
            ->select('user_name', 'date_modified') // Ensure date_modified is selected
            ->first();

        if ($user) {
            // Store the result in Redis for 48 hours
            Cache::put($cacheKey, (array) $user, now()->addHours(48));

            return response()->json(['status' => 'success', 'data' => $user]);
        }

        return response()->json(['status' => 'error', 'message' => 'Kindly contact HELB Customer care or visit Huduma Centre for assistance'], 401);
    }





    //     function loginussd(Request $request, ApiController $apiController)
    //     {
    //         // Validate the request inputs
    //        // dd($request);
    //         $validatedData = $request->validate([
    //             'phone' => 'required|string|min:10',
    //             'pin' => 'required|string|min:4',

    //         ]);

    //         $phone = str_replace(['"', "'"], '', $validatedData['phone']);
    //         $pin  = str_replace(['"', "'"], '', $validatedData['pin']);


    //         $countryCode = '254';
    //         $numberLength = strlen($phone);

    //         if ($numberLength < 11) {
    //             if (Str::startsWith($phone, '0')) {
    //                 $phone = $countryCode . substr($phone, 1);
    //             } else {
    //                 $phone = $countryCode . $phone;
    //             }
    //         }

    //         $Stringsalt = '2AFIoT1wFTNrj3g7ZVSpft5juiRSyGwZ2vD107U6SUqlN9Mqhye09wxwJWPwwEfkPfkxQOpRDT64HzYu';

    //         $Stringcleartext = $phone . $Stringsalt . $pin;
    //        $encryptedtext = '41aa9ce742b2b53de072c500be7df8e0f32ab0ea45660806a3c88bab1bc53e62';

    //         $pin = hash('sha256', $Stringcleartext);


    //         $generated = 'this is the generated pin :'. $pin ;
    //         $dbpin = 'this is the DB pin :'.$encryptedtext ;
    //         //dd(DB::connection('sqlsrvsms')->getDatabaseName());

    //        // dd(DB::connection('sqlsrvsms')->getDatabaseName());
    //     //    $tables = DB::connection('sqlsrvsms')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
    //     //    dd($tables);

    // //     $tables = DB::connection('sqlsrvsms')->select("SELECT TABLE_SCHEMA, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'MOBILE_USERS'");
    // // dd($tables);

    //     $usercredentials = DB::connection('sqlsrvsms')->table('dbo.mobile_users')
    //      ->where('user_pwd', $pin)
    //     ->where('user_name', $phone)
    //    // ->select('user_name', 'user_pwd')
    //     ->get();

    // dd($usercredentials);


    //         // $usercredentials = DB::connection('sqlsrvsms')->table('dbo.mobile_users')
    //         // ->where('user_pwd', $pin)
    //         // ->where('user_name', $phone)
    //         // ->select('user_name', 'user_pwd')
    //         // ->get();

    //         // dd($usercredentials);





    //         echo $generated;
    //         echo  $dbpin;




    //     }






    public function opendynamicussdproducts(Request $request)
    {
        $type = $request->input('type', ''); // Use default value '' if not set
        $category = $request->input('category', ''); // Use default value '' if not set
        $idno = $request->input('idno', ''); // Use default value '' if not set
        $version = $request->input('version', '');


        //dd($version);

        if ($version == '') {
            $checkifauthorized = $this->checkifauthorized($idno);

            if ($checkifauthorized == 1) {
            } else {

                return 'forbidden';
            }
        }






        $closedate = Carbon::now('Africa/Nairobi')->format('Y-m-d');
        // $closedate= '2024-07-03';

        //dd($closedate.'-');
        $cacheKey = 'ussd_products_test' . $idno . '_' . $type . '_' . $category . '_' . $closedate;

        // Check if data exists in cache first
        $data = Cache::remember($cacheKey, 60, function () use ($idno, $type, $category, $closedate) {



            $query1 = DB::table('ussd_products_test as a')
                ->select(
                    'a.id',
                    'a.name',
                    'a.type',
                    'a.value',
                    'a.idcre',
                    'a.productid',
                    'a.productcode',
                    'a.studentgrouping',
                    'a.academicyear',
                    'a.closedate',
                    'a.category',
                    'a.partnership',
                    'a.status'


                )

                ->where('a.closedate', '>=', $closedate);

            $query2 = DB::table('tbl_late_applicants as b')
                ->leftJoin('ussd_products_test as a', 'b.product_id', '=', 'a.productid')
                ->select(
                    'a.id',
                    'a.name',
                    'a.type',
                    'a.value',
                    'a.idcre',
                    'a.productid',
                    'a.productcode',
                    'a.studentgrouping',
                    'b.acad_year as academicyear',
                    'b.close_date as closedate',
                    'a.category',
                    'a.partnership',
                    'a.status'


                )

                ->where('b.close_date', '>=', $closedate)
                ->where('b.id_no', $idno);

            if (request()->has('type')) {
                $query1->where('a.type', $type);
                $query2->where('a.type', $type);
            }
            if (request()->has('category')) {
                $query1->where('a.category', $category);
                $query2->where('a.category', $category);
            }
            // return $query1->get();
            // return $query2->get();

            return $query1->union($query2)->get();
        });

        return response()->json($data);
    }


    public function addinstitutionbulk(Request $request, ApiController $apiController)
    {

        DB::table('cancelledloans')

            ->where('updated', '0') // Filter by current date
            ->limit(10)
            ->orderBy('cancelledloans.id', 'DESC')

            ->chunk(1000, function ($cancelledloans) {



                foreach ($cancelledloans as $record) {

                    $idno = $record->idno;

                    $typesgrp = [
                        'UG' => 'ussd_products_test_UG',
                        'TVET' => 'ussd_products_test_TVET',
                    ];
                    $GRPresults = [];

                    foreach ($typesgrp as $typexgrp => $cacheKey) {
                        $GRPresults[$typexgrp] = Cache::remember($cacheKey, 3600, function () use ($typexgrp) {
                            return DB::table('ussd_products_test')
                                ->where('studentgrouping', $typexgrp)
                                ->pluck('productid') // Retrieve only the product_id column
                                ->toArray(); // Convert the collection to an array
                        });
                    }
                    $TVET = $GRPresults['TVET']; //42531148
                    $UG = $GRPresults['UG'];


                    $product_id = '2';
                    $STUDGROUPING2 = 'UG';
                    $academicyear = '2025/2026';
                    $productcode = '5637144616';
                    $arr = array(
                        'idno' => $idno,
                        'prod' => $productcode,
                        'productcode' => $productcode,
                        'group' => $STUDGROUPING2,
                        'id' => $product_id,
                        'academicyear' => $academicyear,
                    );












                    $action = 'fetchSubsequentRecordscurrent';
                    $apiController = new ApiController();
                    $result = $apiController->mobiapis($action, $arr);
                    // dd($result);
                    if (empty($result)) {
                        echo ' failed';
                        die();
                        // echo 'Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
                    }

                    if (!empty($result) && ($result['missing'] ?? false)) {


                        // return response()->json($result['missing']);
                        echo ' failed';
                    }


                    $fields_to_clean = [
                        'LOANSERIALNO',
                        'ACCOUNTNUM',
                        'IDNO',

                        'INSTITUTIONCODE',
                        'ADMISSIONNUMBER',
                        'COURSECODE',
                        'COURSEDESCRIPTION',


                        'ADMISSIONCATEGORY',
                        'ADMISSIONYEAR',
                        'YEAROFSTUDY',

                        'ACADEMIC_YEAR',
                        'PRODUCTCODE',
                        'INSTITUTIONNAME',

                        'indexno',
                        'examsityr'
                    ];

                    foreach ($fields_to_clean as $field) {
                        $result[$field] = str_replace(["'", '"'], '', $result[$field]);
                    }
                    $result = [
                        'LOANSERIALNO' => $result['LOANSERIALNO'],
                        'ACCOUNTNUM' => $result['ACCOUNTNUM'],
                        'IDNO' => $result['IDNO'],

                        'INSTITUTIONCODE' => $result['INSTITUTIONCODE'],
                        'ADMISSIONNUMBER' => $result['ADMISSIONNUMBER'],
                        'ADMISSIONCATEGORY' => $result['ADMISSIONCATEGORY'],

                        'COURSECODE' => $result['COURSECODE'],
                        'COURSEDESCRIPTION' => $result['COURSEDESCRIPTION'],


                        'ADMISSIONYEAR' => $result['ADMISSIONYEAR'],
                        'YEAROFSTUDY' => $result['YEAROFSTUDY'],

                        'ACADEMIC_YEAR' => $result['ACADEMIC_YEAR'],
                        'PRODUCTCODE' => $result['PRODUCTCODE'],
                        'INSTITUTIONNAME' => $result['INSTITUTIONNAME'],

                        'indexno' => $result['indexno'],
                        'examsityr' => $result['examsityr']




                    ];




                    DB::table('dminstitututions_2024')->updateOrInsert(
                        ['ACCOUNTNUM' => $result['ACCOUNTNUM']], // The conditions to check if the record exists
                        [
                            'ACADEMICYEAR' => $result['ACADEMIC_YEAR'] ?? null,
                            'ADMISSIONCATEGORY' => $result['ADMISSIONCATEGORY'] ?? null,
                            'ADMISSIONNUMBER' => $result['ADMISSIONNUMBER'] ?? null,
                            'ADMISSIONYEAR' => $result['ADMISSIONYEAR'] ?? null,
                            'COURSECODE' => $result['COURSECODE'] ?? null,
                            'INSTITUTIONBRANCHCODE' => $result['INSTITUTIONBRANCHCODE'] ?? null,
                            'INSTITUTIONCODE' => $result['INSTITUTIONCODE'] ?? null,
                            'ACCOUNTNUM' => $result['ACCOUNTNUM'] ?? null,
                            'Productcode' => $result['PRODUCTCODE'] ?? null,
                            'IDNO' => $result['IDNO'] ?? null,
                            'InstitutionName' => $result['INSTITUTIONNAME'] ?? null,
                            'CourseDescription' => $result['COURSEDESCRIPTION'] ?? null,
                            'LOANSERIALNO' => $result['LOANSERIALNO'] ?? null

                        ]
                    );









                    $examsityr = $result['examsityr'] ?? null;

                    $updateData = [
                        'IDNO' => $idno,
                        'STUDGROUPING2' => $STUDGROUPING2,
                        'ADMISSIONO' => $result['ADMISSIONNUMBER'] ?? null,
                        'EXAMYR' => $examsityr,
                        'ACADEMIC_YEAR' => $academicyear,
                        'productcode' => $productcode



                    ];

                    $data = [
                        'id_no' => $idno,
                        'product_id' => $product_id,
                        'productname' => $productcode,
                        'added_by' => 'admin',
                        'acad_year' => $academicyear,
                    ];

                    $message = 'admin' . ' has SSQ enabled  ' . $idno . ' - ' . ' for product ' . $STUDGROUPING2 . '-' . $academicyear;
                    $applicationrecord  = DB::table('cre_pastapplicationstwo')
                        ->where('IDNO', $idno)
                        ->orwhere('ADMISSIONO', $result['ADMISSIONNUMBER'])
                        ->get();

                    if (in_array($product_id, $TVET)) {
                        if ($examsityr >= '2022') {
                            $updateData['qualifiedloanmodel'] = '2';
                            $updateData['qualifiedscholarship'] = '1';
                            $updateData['qualifiedboth'] = '1';

                            $qualifiedloanmodel = 'NFM';
                            $qualifiedscholarship = 'YES';
                            $qualifiedboth  = 'YES';

                            if (!$applicationrecord->isEmpty()) {
                                foreach ($applicationrecord as $bkrecord) {



                                    if ($bkrecord->IDNO == $result['IDNO']) {

                                        DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                            [
                                                'IDNO' => $bkrecord->IDNO,
                                            ],
                                            $updateData
                                        );
                                    }
                                    if ($bkrecord->ADMISSIONO == $result['ADMISSIONNUMBER']) {

                                        $admissiono = $bkrecord->ADMISSIONO;

                                        DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                            [
                                                'ADMISSIONO' => $admissiono,
                                            ],
                                            $updateData
                                        );
                                    }
                                }
                                DB::table('tbl_nfm_enabled')->insert($data);

                                echo ' success';

                                // return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            } else {

                                DB::table('cre_pastapplicationstwo')->insert($updateData);

                                DB::table('tbl_nfm_enabled')->insert($data);
                                echo ' success';

                                // return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            }
                        } else {

                            $updateData['qualifiedloanmodel'] = '1';
                            //  $updateData['qualifiedscholarship'] = '0';


                            $qualifiedloanmodel = 'OFM';
                            $qualifiedscholarship = 'NO';


                            if (!$applicationrecord->isEmpty()) {
                                foreach ($applicationrecord as $bkrecord) {



                                    if ($bkrecord->IDNO == $result['IDNO']) {

                                        DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                            [
                                                'IDNO' => $bkrecord->IDNO,
                                            ],
                                            $updateData
                                        );
                                    }
                                    if ($bkrecord->ADMISSIONO == $result['ADMISSIONNUMBER']) {

                                        $admissiono = $bkrecord->ADMISSIONO;

                                        DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                            [
                                                'ADMISSIONO' => $admissiono,
                                            ],
                                            $updateData
                                        );
                                    }
                                }
                                DB::table('tbl_nfm_enabled')->insert($data);
                                echo ' success';

                                //  return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            } else {

                                DB::table('cre_pastapplicationstwo')->insert($updateData);

                                DB::table('tbl_nfm_enabled')->insert($data);
                                echo ' success';

                                //  return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            }
                        }
                    }

                    if (in_array($product_id, $UG)) {

                        if ($examsityr >= '2022') {
                            $updateData['qualifiedloanmodel'] = '2';
                            $qualifiedloanmodel = 'NFM';
                        } else {

                            $updateData['qualifiedloanmodel'] = '1';
                            $qualifiedloanmodel = 'OFM';
                        }


                        if (!$applicationrecord->isEmpty()) {
                            foreach ($applicationrecord as $bkrecord) {



                                if ($bkrecord->IDNO == $result['IDNO']) {

                                    DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                        [
                                            'IDNO' => $bkrecord->IDNO,
                                        ],
                                        $updateData
                                    );
                                }
                                if ($bkrecord->ADMISSIONO == $result['ADMISSIONNUMBER']) {

                                    $admissiono = $bkrecord->ADMISSIONO;

                                    DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                        [
                                            'ADMISSIONO' => $admissiono,
                                        ],
                                        $updateData
                                    );
                                }
                            }
                            DB::table('tbl_nfm_enabled')->insert($data);
                            echo ' success';

                            //   return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
                        } else {

                            DB::table('cre_pastapplicationstwo')->insert($updateData);

                            DB::table('tbl_nfm_enabled')->insert($data);
                            echo ' success';

                            //  return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
                        }
                    }


                    $qualifiedloanmodel = 'OFM';
                    $qualifiedscholarship = 'NO';
                    $qualifiedboth  = 'NO';
                    if (!$applicationrecord->isEmpty()) {
                        foreach ($applicationrecord as $bkrecord) {



                            if ($bkrecord->IDNO == $result['IDNO']) {

                                DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                    [
                                        'IDNO' => $bkrecord->IDNO,
                                    ],
                                    $updateData
                                );
                            }
                            if ($bkrecord->ADMISSIONO == $result['ADMISSIONNUMBER']) {

                                $admissiono = $bkrecord->ADMISSIONO;

                                DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                    [
                                        'ADMISSIONO' => $admissiono,
                                    ],
                                    $updateData
                                );
                            }
                        }
                        DB::table('tbl_nfm_enabled')->insert($data);
                        echo ' success';

                        // return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                    } else {

                        DB::table('cre_pastapplicationstwo')->insert($updateData);

                        DB::table('tbl_nfm_enabled')->insert($data);
                        echo ' success';

                        // return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                    }
                }
            });
    }


    public function productifqualifiedussd(Request $request)
    {



        // $cacheKey = 'tbl_blocked_nfm_27815531';
        // if (Cache::has($cacheKey)) {
        //     Cache::forget($cacheKey);
        //     $this->info("Cache key $cacheKey has been cleared.");
        // } else {
        //     $this->info("Cache key $cacheKey does not exist.");
        // }
        $rules = [
            'idno' => 'required|string|min:4', // Ensures idno is less than 9 characters
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



        $idno = $request->input('idno');
        $phone = $request->input('phone');
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
        $submittedloan = '1';
        $submittedscholarship = '1';
        $submittedboth = '1';



        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }


        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }

        $cacheKey = 'blocked_nfm_' . $idno;
        $cacheDuration = 60; // Cache duration in minutes

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($idno) {
            return DB::table('tbl_blocked_nfm')
                ->where('idno', $idno)
                ->where('status', 'blocked')

                ->first();
        });
        // if (!empty($data) && $data->idno === $idno && in_array($data->productcode, ['5637170076', '5637167826'])) {
        //     $message = "ID number {$idno}  cannot access this service. Please contact our customer experience team for assistance.";
        //     return response()->json([
        //         'result' => 'fail',
        //         'message' => $message
        //     ]);
        // }
        if (!empty($data) && $data->idno === $idno) {
            $message = "ID number {$idno}  cannot access this service. Please contact our customer experience team for assistance.";
            return response()->json([
                'result' => 'fail',
                'message' => $message
            ]);
        }





        $types = [
            'loan' => 'ussd_products_test_loan',
            'scholarship' => 'ussd_products_test_scholarship',
            'loan & Scholarship' => 'ussd_products_test_loanscholarship'
        ];

        $results = [];

        foreach ($types as $typex => $cacheKey) {
            $results[$typex] = Cache::remember($cacheKey, 3600, function () use ($typex) {
                return DB::table('ussd_products_test')
                    ->where('type', $typex)
                    ->pluck('productid') // Retrieve only the product_id column
                    ->toArray(); // Convert the collection to an array
            });
        }

        // Access the cached results
        $loans = $results['loan'];
        $scholarship = $results['scholarship'];
        $loanscholarship = $results['loan & Scholarship'];

        //dd($scholarship);




        $cacheKey = "product_submit_new_loans_{$idno}_{$academicyear}_{$productcode}";
        $datatwo = Cache::remember($cacheKey, 60, function () use ($idno, $academicyear, $productcode) {
            return DB::table('tbl_products_submit_new')
                //  ->select('submittedloan', 'serial_number', 'date_created')
                ->where('idno', $idno)
                ->where('acad_year', $academicyear)
                ->where('productcode', $productcode)


                ->first();
        });

        //dd($datatwo);

        return $this->productserialreturn($source, $idno, $productid, $studentgrouping, $datatwo, $academicyear, $name, $type, $idcre, $productcode, $loanscholarship, $scholarship, $loans);
    }

    public function safaricompop(Request $request, ApiController $apiController)
    {


        $rules = [
            'amount' => 'required|string|min:1', // Ensures idno is less than 9 characters
            'phone' => 'required|string|min:10',
            'idno' => 'required|string|min:4',
            'action' => 'required',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $phone =  $request->phone;

        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }
        $action =  'ecitizenlivepush';
        $arr = array(
            'amount' => $request->amount,
            'name' => 'mobileapp',
            'phone' => $phone,
            'idnumber' => $request->idno
        );
        // $checkifauthorized = $this->checkifauthorized($request->idno);

        // if ($checkifauthorized == 1) {
        // } else {

        //     return 'forbidden';
        // }


        //dd($arr);

        $result = $apiController->dotseven($action, $arr);
        //dd($result);


        return response()->json($result);
    }


    public function generatesubsequentpdfdatarealtimenew(Request $request, ApiController $apiController)
    {

        ini_set('max_execution_time', 0); // Removes the time limit (infinite execution time)




        try {



            DB::connection('mysql')
                ->table('tbl_products_submit_new as b')
                ->leftJoin('tbl_users_applicants as a', 'b.idno', '=', 'a.id_no')

                ->leftJoin('ussd_products_test as c', 'b.productcode', '=', 'c.productcode')
                ->leftJoin('dminstitututions_2024 as d', function ($join) {
                    $join->on('b.idno', '=', 'd.IDNO')

                        ->on('b.productcode', '=', 'd.productcode');
                })
                ->leftJoin('tbl_users_mobile as f', 'a.id_no', '=', 'f.idno')

                ->leftJoin('tbl_users_miniapp as g', 'a.cell_phone', '=', 'g.cell_phone')
                ->leftJoin('ussdetails as h', 'a.id_no', '=', 'h.id_no')

                ->where('b.submittedloan', '1')
                ->where('b.EdmsUpload', '0')
                //->where('b.source', '!=', 'portal')
                // ->where('a.id_no', '=', '28613556')
                // ->where('d.IDNO', '=', '28613556')
                // ->where('h.id_no', '=', '28613556')
                // ->where('f.idno', '=', '28613556')


                ->select([
                    'b.id',

                    'b.acad_year',
                    'a.first_name',
                    'a.mid_name',
                    'a.last_name',
                    'a.id_no',
                    'a.email_add',
                    'a.cell_phone',
                    'b.serial_number',
                    'c.name',
                    'b.date_loan_submit',
                    'b.date_sch_submit',

                    'b.date_created',
                    'b.submittedloan',
                    'b.submittedscholarship',
                    'b.disbursementoption',
                    'b.disbursementoptionvalue',

                    'd.InstitutionName',
                    'd.ADMISSIONNUMBER',
                    'd.ADMISSIONYEAR',
                    'd.INSTITUTIONCODE',
                    'f.cell_phone as androidcellphone',
                    'f.gsf as androidgsf',
                    'f.networkused as androidnetworkused',
                    'f.simoperatorname as androidsimoperatorname',
                    'f.serial as oserial',
                    'f.time_added as androidtime',
                    'f.deviceinfo as androideviceinfo',
                    'f.imei as androidimei',
                    'g.cell_phone as miniphone',
                    'g.platform as miniphoneplatform',
                    'g.brand as miniphonebrand',
                    'g.appversion as miniphoneappversion',
                    'g.system as miniphonesystem',
                    'g.time_added as minitime_added',
                    'g.deviceinfo as minideviceinfo',
                    'h.user_name as ussdphone',
                    'h.date_created as ussdatecreated',
                    'h.user_status as ussdstatus',
                    'h.sim_id as ussdsimid',
                    DB::raw("CASE WHEN b.submittedscholarship = '1' THEN 'Yes' ELSE 'No' END as submittedscholarship_status"),
                    DB::raw("CASE WHEN b.submittedloan = '1' THEN 'Yes' ELSE 'No' END as submittedloan_status"),

                ])
                ->orderBy('b.acad_year')
                //->chunkById(1000, function ($records) use ($apiController) {
                ->chunk(1000, function ($records) use ($apiController) {


                    $recordCount = 0;


                    foreach ($records as $val) {
                        $filename = $val->id_no . '_' . $val->serial_number . '.pdf';

                        $data = (array) $val;

                        $isServerReachable = $apiController->checkDot95Reachability();


                        if ($isServerReachable) {
                            //dd($data);
                            $pdf = PDF::loadView('pages.pdfsubseequent', $data);
                            //echo $pdf;
                            //die();
                            // dd($pdf);
                            $pdfContentBase64 = base64_encode($pdf->output());

                            // $arr = ['filename' => $filename, 'file' => $pdfContentBase64];
                            // $action = 'echopath';
                            // // $this->auth_model->mobiaps($action, $arr);
                            $location = $this->echopath($filename, $pdfContentBase64);
                            //s  dd($location);
                            //return $location;
                            // Insert or update record in edms_subsequent_pdf
                            // DB::connection('sqlsrv')
                            //     ->table('edms_subsequent_pdf')
                            //     ->updateOrInsert(
                            //         ['serialnumber' => $val->serial_number], // Condition to check if record exists
                            //         ['idnumber' => $val->id_no] // Data to insert or update
                            //     );

                            // Update EdmsUpload in tbl_products_users
                            DB::connection('mysql')
                                ->table('tbl_products_submit_new')
                                ->where('serial_number', $val->serial_number)
                                ->update(['EdmsUpload' => '1']);


                            // Update if record exists

                            echo 'Update successful. ' . $val->id_no . ' ' . $val->serial_number . ' ' . $recordCount++ . '<br/>';
                        } else {
                            echo 'Transfer failed. ' . $val->id_no . ' ' . $recordCount++ . '<br/>';
                        }
                    }

                    echo 'Processing completed! Total records processed: ' . $recordCount . '<br>';
                    //}, ['b.id', 'b.date_created']);
                });
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function echopath($filename, $pdfContentBase64)

    {


        $fileContent = base64_decode($pdfContentBase64);

        // Save the file using Laravel's Storage
        if (Storage::disk('network95')->put($filename, $fileContent)) {
            return response()->json(['success' => 'File ' . $filename . ' saved successfully.']);
        } else {
            return response()->json(['error' => 'Failed to save the file ' . $filename], 500);
        }

        // // Define the save path
        // // $save_path = '//192.168.1.95/Kapture/eedd/Test/' . $filename;
        // $save_path = '\\\\192.168.1.95\\Kapture\\eedd\\Test\\' . $filename;









        // if (!is_dir(dirname($save_path))) {
        //     return response()->json(['error' => 'Directory does not exist: ' . dirname($save_path)], 500);
        // }

        // if (!is_writable(dirname($save_path))) {
        //     return response()->json(['error' => 'Directory is not writable: ' . dirname($save_path)], 500);
        // }
        // // Save the file
        // if (file_put_contents($save_path, $fileContent) !== false) {
        //     return response()->json(['success' => 'File ' . $filename . ' saved successfully.']);
        // } else {
        //     return response()->json(['error' => 'Failed to save the file ' . $filename], 500);
        // }
    }







    function isServerReachable(ApiController $apiController)
    {





        $action =  'filereachable';
        $arr = array();

        //dd($arr);

        $location = $apiController->mobiapis($action, $arr);
        //dd($result);


        return $location;
    }



    function ismobiappServerReachable(ApiController $apiController)
    {


        try {
            $response = Http::timeout(5)->get(config('app.testmobileserver'));

            if ($response->ok()) {
                return [
                    'status' => true,
                    'message' => 'Server is reachable',
                    'code' => $response->status()
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Server responded with error',
                    'code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Server is not reachable',
                'error' => $e->getMessage()
            ];
        }
    }







    public function ufscholarshipapplicants(Request $request)
    {


        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        //dd($request);
        $rules = [
            'idno' => 'required|string|min:4', // Ensures idno is less than 9 characters
            // 'productcode' => 'required|string|min:4', // Ensures idno is less than 9 characters

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $idno = $request->input('idno');
        $productcode = '5637144616';
        $cacheKey = "product_submit_new_loans_{$idno}";

        $datatwo = Cache::remember($cacheKey, 1440, function () use ($idno, $productcode) {
            return DB::table('tbl_products_submit_new')
                ->leftJoin('dminstitututions_2024', function ($join) use ($productcode) {
                    $join->on('dminstitututions_2024.IDNO', '=', 'tbl_products_submit_new.idno')
                        ->where('dminstitututions_2024.Productcode', '=', $productcode); // Explicitly use $productcode
                })
                ->select(
                    'tbl_products_submit_new.idno',
                    'tbl_products_submit_new.productcode',
                    'tbl_products_submit_new.submittedscholarship',
                    'tbl_products_submit_new.serial_number',
                    'tbl_products_submit_new.date_created',
                    'tbl_products_submit_new.date_sch_submit',
                    'tbl_products_submit_new.source',
                    'tbl_products_submit_new.acad_year',
                    'dminstitututions_2024.ADMISSIONNUMBER',
                    'dminstitututions_2024.COURSECODE',
                    'dminstitututions_2024.INSTITUTIONCODE',
                    'dminstitututions_2024.InstitutionName'
                )
                ->where('tbl_products_submit_new.idno', $idno)
                ->where('tbl_products_submit_new.productcode', $productcode)
                ->first();
        });

        return $datatwo;
    }

    public function sendsmscrm(Request $request, ApiController $apiController)
    {


        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        //dd($request);
        $rules = [
            'phone' => 'required|string|min:10', // Ensures idno is less than 9 characters
            'message' => 'required|string|min:4', // Ensures idno is less than 9 characters
            'idno' => 'required|string|min:4', // Ensures idno is less than 9 characters

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = $request->message;
        $phone = $request->phone;


        $post = [
            'phone' => $request->phone,
            'idno' => $request->idno,
            'message' => $request->message,



        ];

        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }

        $action = 'sendphoneverificationCode';
        $arr = [
            'recipient' => $phone,
            'verificationcode' => $message,
            'msg_priority' => '236',
            'category' => '342'
        ];

        $logs =  DB::table('android_log')->insert(['message' => json_encode($post)]);

        // Assuming callapion96 is a method in AuthModel and it returns a response
        $result = $apiController->datapull($action, $arr);

        return response()->json([
            'result' => 'success',
            'message' => $message
        ]);
    }



    public function ufscholarshipapplicantsids(Request $request)
    {


        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        //dd($request);
        $rules = [
            //'idno' => 'required|string|min:4', // Ensures idno is less than 9 characters
            'productcode' => 'required|string|min:4', // Ensures idno is less than 9 characters

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $productcode = $request->input('productcode');
        $cacheKey = "product_submit_new_loans_{$productcode}";

        $datatwo = Cache::remember($cacheKey, 1440, function () use ($productcode) {
            return DB::table('tbl_products_submit_new')

                ->select(
                    'tbl_products_submit_new.idno',

                )
                ->where('tbl_products_submit_new.submittedscholarship', '1')
                ->where('tbl_products_submit_new.productcode', $productcode)
                ->where('tbl_products_submit_new.acad_year', '2025/2026')

                ->get();
        });

        return $datatwo;
    }









    public function productserialreturn($source, $idno, $productid, $studentgrouping, $datatwo, $academicyear, $name, $type, $idcre, $productcode, $loanscholarship, $scholarship, $loans)

    {

        $idno = htmlspecialchars(trim($idno));
        $studentgrouping = htmlspecialchars(trim($studentgrouping));
        $academicyear = htmlspecialchars(trim($academicyear));
        $productcode = htmlspecialchars(trim($productcode));

        $cacheKey = "cre_pastapplicationstwo__{$idno}_{$academicyear}_{$productcode}_{$studentgrouping}";


        // dd($cacheKey);
        // Attempt to retrieve data from cache
        $datathree = Cache::remember($cacheKey, 300, function () use ($idno, $studentgrouping, $academicyear, $productcode) {
            $result = DB::table('cre_pastapplicationstwo')
                //  ->select('IDNO')
                ->where('IDNO', $idno)
                ->where('STUDGROUPING2', $studentgrouping)
                ->where('ACADEMIC_YEAR', $academicyear)
                ->where('productcode', $productcode)
                ->orderBy('cre_pastapplicationstwo.id_pri', 'DESC')

                ->get();

            // If no results in cre_pastapplicationstwo, try cre_pastapplicationsthree
            if ($result->isEmpty()) {



                if ($studentgrouping == 'TVET') {

                    $studentgrouping == 'VD';
                }


                $result = DB::connection('sqlsrv')->table('LMSSQview')
                    ->where('IDNO', $idno)
                    ->where('LOANSPRODUCTCODE', $studentgrouping)
                    // ->where('ACADEMIC_YEAR', $academicyear)
                    ->where('LOANPRODUCTCODE', $productcode)
                    ->whereColumn('COURSEDURATION', '>', 'LoansCount')
                    ->first();
                // dd($result);

                // dd($result->AdmisionCategory ?? null);


                $Institutiondata = [
                    'ACADEMICYEAR' => $academicyear,
                    'ADMISSIONCATEGORY' =>  null,
                    'ADMISSIONNUMBER' => $result->ADMISSIONNUMBER ?? null,
                    'ADMISSIONYEAR' => $result->ExamYear ?? null,
                    'COURSECODE' => $result->CourseCode ?? null,
                    'INSTITUTIONBRANCHCODE' => $result->INSTITUTIONCODE ?? null,
                    'INSTITUTIONCODE' => $result->INSTITUTIONCODE ?? null,
                    'ACCOUNTNUM' => null,
                    'Productcode' => $productcode ?? null,
                    'IDNO' =>  $idno ?? null,
                    'InstitutionName' => $result->InstitutionName ?? null,
                    'CourseDescription' => null,
                    // 'LOANSERIALNO' => '', // still commented out
                ];

                if ($result->ExamYear >= '2022' || $studentgrouping == 'VD') {
                    $qualifiedloanmodel = '2';
                    $qualifiedscholarship = '1';
                    $qualifiedboth = '1';
                } else {

                    $qualifiedloanmodel = '1';
                    $qualifiedscholarship = '0';
                    $qualifiedboth = '0';
                }




                DB::table('dminstitututions_2024')->updateOrInsert(
                    ['IDNO' => $idno],  // The condition for checking existence
                    $Institutiondata    // The data to update or insert
                );

                DB::table('cre_pastapplicationstwo')->insert([
                    'IDNO' => $idno,
                    'ADMISSIONO' => $result->ADMISSIONNUMBER ?? null,
                    'EXAMYR'  => $result->ExamYear ?? null,
                    'STUDGROUPING2' => $studentgrouping,
                    'ACADEMIC_YEAR' => $academicyear,
                    'qualifiedscholarship' => $qualifiedscholarship,
                    'qualifiedloanmodel' => $qualifiedloanmodel,
                    'qualifiedboth' => $qualifiedboth,
                    'productcode' => $productcode,
                    'product_id' => '99'
                ]);

                $result = DB::table('cre_pastapplicationstwo')
                    //  ->select('IDNO')
                    ->where('IDNO', $idno)
                    ->where('STUDGROUPING2', $studentgrouping)
                    ->where('ACADEMIC_YEAR', $academicyear)
                    ->where('productcode', $productcode)
                    ->orderBy('cre_pastapplicationstwo.id_pri', 'DESC')

                    ->get();
            }

            return $result;
        });

        // dd($cacheKey);
        // $qualifiedloanmodel = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedloanmodel : null;
        // $qualifiedscholarship = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedscholarship : null;
        // $qualifiedboth = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedboth : null;

        //dd($qualifiedscholarship);


        // // Initialize variables
        $qualifiedloanmodel = null;
        $qualifiedscholarship = null;
        $qualifiedboth = null;


        //dd($datathree);

        // // Loop through each record
        foreach ($datathree as $record) {
            // Collect or process data from each record
            if ($record->qualifiedloanmodel) {
                $qualifiedloanmodel = $record->qualifiedloanmodel;
            }
            if ($record->qualifiedscholarship) {
                $qualifiedscholarship = $record->qualifiedscholarship;
            }
            if ($record->qualifiedboth) {
                $qualifiedboth = $record->qualifiedboth;
            }
        }
        if (!empty($datatwo)) {
            $Bdat = $datatwo->date_updated;
            $submittedloan = $datatwo->submittedloan;
            $submittedscholarship = $datatwo->submittedscholarship;
            $submittedboth = $datatwo->submittedboth;
            /// dd($submittedloan);

            $Bser = $datatwo->serial_number;
            $message = "Your {$academicyear} {$name} {$type} application of serial {$Bser} had been received. Application date {$Bdat}";

            //dd(Str::is('1', $submittedboth) && (in_array($productid, $loanscholarship)));

            if (Str::is('1', $submittedboth) && (in_array($productid, $loanscholarship))) {

                return response()->json([
                    'serialnumber' => $Bser,
                    'result' => 'fail',
                    'message' => $message
                ]);
            }




            // dd($submittedloan);

            if ((in_array($productid, $loanscholarship)) && $submittedloan == '1' || in_array($productid, $loanscholarship) && $submittedscholarship == '1') {

                if (Str::is('11', $submittedloan . $submittedscholarship)) {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'fail',
                        'message' => $message
                    ]);
                }





                if (Str::is('1', $submittedloan)) {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'fail',
                        'message' => "Kindly select scholarship option to submit your scholarship application"
                    ]);
                }

                if (Str::is('1', $submittedscholarship)) {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'fail',
                        'message' => "Kindly select loan option to submit your loan application"
                    ]);
                }
            }


            if (Str::is('1', $submittedloan) && (in_array($productid, $loans))) {
                // dd($submitted);


                return response()->json([
                    'serialnumber' => $Bser,
                    'result' => 'fail',
                    'message' => $message
                ]);
            }



            if (Str::is('1', $submittedscholarship) && (in_array($productid, $scholarship))) {
                // dd($submitted);


                return response()->json([
                    'serialnumber' => $Bser,
                    'result' => 'fail',
                    'message' => $message
                ]);
            }

            if (Str::is('1', $submittedloan) && (in_array($productid, $scholarship))) {
                // dd($submitted);
                //check if the user qualifies for scholarship
                //if qualifies allow to proceed else error meessage

                if (Str::is('1', $qualifiedscholarship)) {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'success',
                        'message' => 'success'
                    ]);
                }
                if (Str::is('2', $qualifiedloanmodel)) {
                    $message = "Kindly update details from the HEF Portal.To apply for {$academicyear} undergraduate subsequent scholarship.";

                    return response()->json([
                        'result' => 'fail',
                        'message' => $message
                    ]);
                }

                return response()->json([
                    'result' => 'fail',
                    //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                    'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                ]);
            }

            if (Str::is('1', $submittedscholarship) && (in_array($productid, $loans))) {
                // dd($submitted);
                //check if the user qualifies for scholarship
                //if qualifies allow to proceed else error meessage

                if ($qualifiedloanmodel >= '1') {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'success',
                        'message' => 'success'
                    ]);
                }
                $message = "Kindly update your guarantor details from the HEF Portal.to apply for {$academicyear} undergraduate subsequent loan.";


                return response()->json([
                    'serialnumber' => $Bser,
                    'result' => 'fail',
                    'message' => $message
                ]);
            }

            if (in_array($productid, $loans)) {
                // dd('hhhhhhh');

                //QUALIFIES FOR LOAN IF HE HAS SERIAL RETURN IF NOT GENERATE
                if ($qualifiedloanmodel >= 1) {

                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'success',
                        'message' => 'success'
                    ]);
                } else {
                    //if qualifies for NFM GIVE DIFFERENT ERROR
                    $message = "Kindly update your guarantor details from the HEF Portal.to apply for {$academicyear} undergraduate subsequent loan.";

                    if (Str::is('1', $qualifiedscholarship)) {
                        return response()->json([
                            'result' => 'fail',
                            'message' => $message
                        ]);
                    }
                    //NOT QUALIFIES NFM

                    return response()->json([
                        'result' => 'fail',
                        // 'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                        'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                    ]);
                }
            }
            if (in_array($productid, $scholarship)) {


                if (Str::is('1', $qualifiedscholarship)) {
                    return response()->json([
                        'serialnumber' => $Bser,
                        'result' => 'success',
                        'message' => 'success'
                    ]);
                }

                if (Str::is('2', $qualifiedloanmodel)) {
                    $message = "Kindly update details from the HEF Portal.To apply for {$academicyear} undergraduate subsequent scholarship.";

                    return response()->json([
                        'result' => 'fail',
                        'message' => $message
                    ]);
                }

                return response()->json([
                    'result' => 'fail',
                    //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                    'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                ]);
            }
            if (in_array($productid, $loanscholarship)) {

                if (!Str::is('1', $qualifiedboth)) {
                    return response()->json([
                        'result' => 'fail',

                        //   'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                        'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                    ]);
                }

                return response()->json([
                    'serialnumber' => $Bser,
                    'result' => 'success',
                    'message' => 'success'
                ]);
            }
        } else {

            if (in_array($productid, $loans)) {
                // dd('hhhhhhh');

                //QUALIFIES FOR LOAN IF HE HAS SERIAL RETURN IF NOT GENERATE
                if ($qualifiedloanmodel >= 1) {

                    //   dd($this->serialgenerator($productcode, $idno, $source, $academicyear));

                    return  $this->serialgenerator($productcode, $idno, $source, $academicyear);
                } else {
                    //if qualifies for NFM GIVE DIFFERENT ERROR
                    $message = "Kindly update your guarantor details from the HEF Portal.to apply for {$academicyear} undergraduate subsequent loan.";

                    if (Str::is('1', $qualifiedscholarship)) {
                        return response()->json([
                            'result' => 'fail',
                            'message' => $message
                        ]);
                    }
                    //NOT QUALIFIES NFM

                    return response()->json([
                        'result' => 'fail',
                        // 'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                        'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                    ]);
                }
            }
            if (in_array($productid, $scholarship)) {


                if (Str::is('1', $qualifiedscholarship)) {
                    return  $this->serialgenerator($productcode, $idno, $source, $academicyear);
                }

                if (Str::is('2', $qualifiedloanmodel)) {
                    $message = "Kindly update details from the HEF Portal.To apply for {$academicyear} undergraduate subsequent scholarship.";

                    return response()->json([
                        'result' => 'fail',
                        'message' => $message
                    ]);
                }

                return response()->json([
                    'result' => 'fail',
                    //   'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                    'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                ]);
            }
            if (in_array($productid, $loanscholarship)) {

                if (!Str::is('1', $qualifiedboth)) {
                    return response()->json([
                        'result' => 'fail',
                        //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                        'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                    ]);
                }

                return  $this->serialgenerator($productcode, $idno, $source, $academicyear);
            }
        }
        return response()->json([
            'result' => 'fail',
            //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
            'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

        ]);
    }


    public function serialgenerator($productcode, $idno, $source, $academicyear)
    {
        // dd($productcode, $idno, $source,$academicyear);
        // dd("SELECT start_serial2 FROM tbl_fserial_setup WHERE acad_year = ".$academicyear);

        $query = DB::select("SELECT start_serial2 FROM tbl_fserial_setup WHERE acad_year = ?", [$academicyear]);
        $sno = $query[0]->start_serial2;

        $query = DB::select("SELECT serial_number FROM tbl_products_submit_new WHERE serial_number = ?", [$sno]);

        $serial = $sno;
        $nwserial = $sno + 1;

        if (empty($query)) {


            DB::beginTransaction();

            try {
                DB::insert('INSERT INTO tbl_products_submit_new (serial_number, productcode, idno, acad_year, source) VALUES (?, ?, ?, ?, ?)', [$serial, $productcode, $idno, $academicyear, $source]);
                DB::update("UPDATE tbl_fserial_setup SET start_serial2 = ? WHERE acad_year = ?", [$nwserial, $academicyear]);

                DB::commit();

                return response()->json([
                    'serialnumber' => $serial,
                    'result' => 'success',
                    'message' => 'success'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'result' => 'fail',
                    'message' => 'try later ' . $e
                ]);
            }
        }
        DB::update("UPDATE tbl_fserial_setup SET start_serial2 = ? WHERE acad_year = ?", [$nwserial, $academicyear]);
    }





    public function fetchinstitutionussd(Request $request)
    {
        $rules = [
            'idno' => 'required|string|min:4',
            'phone' => 'required|string|min:10',
            //'academicyear' => 'required',
            // 'productid' => 'required',
            'productcode' => 'required',
            //'studentgrouping' => 'required',
            // 'name' => 'required',
            //'type' => 'required',
            //'idcre' => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idno = $request->input('idno');
        $phone = $request->input('phone');
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
        $failmessage = 'Kindly retry after two minutes institution details updating..';


        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }
        $cacheKey = "dminstitututions_2024_{$idno}_{$productcode}";

        $dataone = Cache::remember($cacheKey, 300, function () use ($idno, $productcode) {
            return DB::table('dminstitututions_2024')
                ->where('IDNO', $idno)
                ->where('Productcode', $productcode)
                ->orderBy('dminstitututions_2024.id', 'ASC')

                ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
        });

        // Check if data was found in cache and is not null
        if (!empty($dataone)) {
            // Define the properties you want to check for null values
            $properties = [
                //'InstitutionName',
                //  'CourseDescription',
                //  'COURSECODE',
                // 'INSTITUTIONCODE',
                'ADMISSIONNUMBER'

            ];

            // Loop through each property to check if it's null
            foreach ($properties as $property) {
                if ($dataone->$property === null || $dataone->$property === '') {
                    // Handle the case where any value is null
                    return response()->json([
                        'result' => 'fail',
                        'message' => $failmessage,
                        'details' => [$dataone]

                    ]);
                }
            }

            $applicationMessage = $name . ' Application for ' . $idno . '.' . $dataone->InstitutionName . '.' . $dataone->CourseDescription . '.ADMISSION NO: ' . $dataone->ADMISSIONNUMBER . ' Are the details correct?';
            return response()->json([
                'result' => 'success',
                'message' => $applicationMessage,
                'details' => [$dataone]

            ]);
        } else {

            $request = [
                'idno' => $idno,
                'productcode' => $productcode,
                'STUDGROUPING2' => $studentgrouping,
                'product_id' => $productid,
                'academicyear' => $academicyear,
            ];
            $apiController = new ApiController();
            $userController = new UserController();

            $results = $userController->pushinstitution($request, $apiController);





            // Handle case where no data was found
            return response()->json([
                'result' => 'fail',
                'message' => $failmessage,
                'details' => [$dataone]

            ]);
        }
    }


    public function productsubmit(Request $request, ApiController $apiController)
    {
        $rules = [
            'idno' => 'required|string|min:4', // Ensures idno is less than 9 characters
            'phone' => 'required|string|min:10',
            'academicyear' => 'required',
            'productid' => 'required',
            'productcode' => 'required',
            'studentgrouping' => 'required',
            'name' => 'required',
            'type' => 'required',
            'idcre' => 'required',
            'serialnumber' => 'required',


        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idno = $request->input('idno');
        $phone = $request->input('phone');
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
        $serialnumber = $request->input('serialnumber');
        $disbursementoption = $request->input('disbursementoption');
        $disbursementoptionvalue = $request->input('disbursementoptionvalue');
        $submitted = '1';
        $cell_verified = '1';
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }

        if (empty($disbursementoption)) {
            $disbursementoption = $type;
        }

        if (empty($disbursementoptionvalue)) {
            $disbursementoptionvalue = $type;
        }



        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }



        $types = [
            'loan' => 'ussd_products_test_loan',
            'scholarship' => 'ussd_products_test_scholarship',
            'loan & Scholarship' => 'ussd_products_test_loanscholarship'
        ];

        $results = [];

        foreach ($types as $typex => $cacheKey) {
            $results[$typex] = Cache::remember($cacheKey, 3600, function () use ($typex) {
                return DB::table('ussd_products_test')
                    ->where('type', $typex)
                    ->pluck('productid') // Retrieve only the product_id column
                    ->toArray(); // Convert the collection to an array
            });
        }

        // Access the cached results
        $loans = $results['loan'];
        $scholarship = $results['scholarship'];
        $loanscholarship = $results['loan & Scholarship'];


        $cacheKey = "cre_pastapplicationstwo__{$idno}_{$academicyear}_{$productcode}_{$studentgrouping}";

        // Attempt to retrieve data from cache
        $datathree = Cache::remember($cacheKey, 300, function () use ($idno, $studentgrouping, $academicyear, $productcode) {
            return DB::table('cre_pastapplicationstwo')
                //  ->select('IDNO')
                ->where('IDNO', $idno)
                ->where('STUDGROUPING2', $studentgrouping)
                ->where('ACADEMIC_YEAR', $academicyear)
                ->where('productcode', $productcode)
                ->orderBy('cre_pastapplicationstwo.id_pri', 'DESC')

                ->get();
        });

        //dd($datathree);
        // $qualifiedloanmodel = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedloanmodel : null;
        // $qualifiedscholarship = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedscholarship : null;
        // $qualifiedboth = !empty($datathree) && $datathree->first() ? $datathree->first()->qualifiedboth : null;

        $qualifiedloanmodel = null;
        $qualifiedscholarship = null;
        $qualifiedboth = null;

        // // Loop through each record
        foreach ($datathree as $record) {
            // Collect or process data from each record
            if ($record->qualifiedloanmodel) {
                $qualifiedloanmodel = $record->qualifiedloanmodel;
            }
            if ($record->qualifiedscholarship) {
                $qualifiedscholarship = $record->qualifiedscholarship;
            }
            if ($record->qualifiedboth) {
                $qualifiedboth = $record->qualifiedboth;
            }
        }




        $updateData = [
            'source' => $source,

        ];

        if (in_array($productid, $loanscholarship)) {
            $incrementValue = 2;

            if (!Str::is('1', $qualifiedboth)) {
                return response()->json([
                    'result' => 'fail',
                    //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                    'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

                ]);
            }
            $updateData['submittedloan'] = '1';
            $updateData['submittedscholarship'] = '1';
            $updateData['submittedboth'] = '1';
            $updateData['date_loan_submit'] = $date_now;
            $updateData['date_sch_submit'] = $date_now;
            $updateData['disbursementoptionvalue'] = $disbursementoptionvalue;
            $updateData['disbursementoption'] =  $disbursementoption;
            return  $this->productfinalsubmit($type, $productid, $serialnumber, $cell_verified, $name, $incrementValue, $phone, $updateData, $productcode, $idno, $source, $academicyear, $apiController);
        }
        if (in_array($productid, $loans)) {
            // dd('hhhhhhh');
            $incrementValue = 1;

            //QUALIFIES FOR LOAN I
            if ($qualifiedloanmodel >= 1) {
                $updateData['submittedloan'] = '1';
                $updateData['date_loan_submit'] = $date_now;
                $updateData['disbursementoptionvalue'] = $disbursementoptionvalue;
                $updateData['disbursementoption'] =  $disbursementoption;

                return  $this->productfinalsubmit($type, $productid, $serialnumber, $cell_verified, $name, $incrementValue, $phone, $updateData, $productcode, $idno, $source, $academicyear, $apiController);
            } else {
                //if qualifies for NFM GIVE DIFFERENT ERROR
                $message = "Kindly update your guarantor details from the HEF Portal.to apply for {$academicyear} undergraduate subsequent loan.";

                if (Str::is('1', $qualifiedscholarship)) {
                    return response()->json([
                        'result' => 'fail',
                        'message' => $message
                    ]);
                }
                //NOT QUALIFIES NFM

                return response()->json([
                    'result' => 'fail',
                    //  'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                    'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."


                ]);
            }
        }
        if (in_array($productid, $scholarship)) {
            $incrementValue = 1;

            if (Str::is('1', $qualifiedscholarship)) {
                $updateData['submittedscholarship'] = '1';

                $updateData['date_sch_submit'] = $date_now;


                return  $this->productfinalsubmit($type, $productid, $serialnumber, $cell_verified, $name, $incrementValue, $phone, $updateData, $productcode, $idno, $source, $academicyear, $apiController);
            }

            if (Str::is('2', $qualifiedloanmodel)) {
                $message = "Kindly update details from the HEF Portal.To apply for {$academicyear} undergraduate subsequent scholarship.";

                return response()->json([
                    'result' => 'fail',
                    'message' => $message
                ]);
            }

            return response()->json([
                'result' => 'fail',
                // 'message' => "This {$type} is only for {$name} students. ID number: {$idno}. Please ensure you made a previous application and that you have not exhausted the number of allowable {$type} for your course."
                'message' => "This {$type} is only for {$name} students. ID number: {$idno}.Ensure you made a previous application.If 18 years & had applied with Index No, update it to  National ID on the HEF portal."

            ]);
        }
    }


    public function productfinalsubmit($type, $productid, $serialnumber, $cell_verified, $name, $incrementValue, $phone, $updateData, $productcode, $idno, $source, $academicyear, $apiController)
    {


        $cacheKeyNFM = "tbl_users_nfm_{$idno}_{$cell_verified}";

        $user = Cache::remember($cacheKeyNFM, 300, function () use ($idno, $cell_verified) {
            return DB::table('tbl_users_nfm')
                ->where('id_no', $idno)
                ->where('cell_verified', $cell_verified)

                ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
        });

        // Check if data was found in cache and is not null
        if (!empty($user)) {
            $userfirstname = $user->first_name;
            $useremail = $user->email_add;

            $affectedRows = DB::table('tbl_products_submit_new')
                ->where('productcode', $productcode)
                ->where('serial_number', $serialnumber)
                ->where('idno', $idno)
                ->where('acad_year', $academicyear)
                ->update($updateData);





            //dd($affectedRows);


            if ($affectedRows > 0) {



                $countrows = DB::table('ussd_products_count')
                    ->where('productid', $productid)
                    ->where('academicyear', $academicyear);

                if ($source == 'ussd') {
                    $countrows->update([
                        'count' => DB::raw('count + ' . $incrementValue),
                        'ussd' => DB::raw('ussd + ' . $incrementValue)
                    ]);
                }
                if ($source == 'mobile') {
                    $countrows->update([
                        'count' => DB::raw('count + ' . $incrementValue),
                        'mobile' => DB::raw('mobile + ' . $incrementValue)
                    ]);
                }
                if ($source == 'miniapp') {
                    $countrows->update([
                        'count' => DB::raw('count + ' . $incrementValue),
                        'miniapp' => DB::raw('miniapp + ' . $incrementValue)
                    ]);
                }

                if ($source == 'ios') {
                    $countrows->update([
                        'count' => DB::raw('count + ' . $incrementValue),
                        'ios' => DB::raw('ios + ' . $incrementValue)
                    ]);
                }











                //  $mcode = 'Congratulations ' . $userfirstname . ', Your ' . $academicyear . ' ' . $name . ' ' . $type . ' application of serial ' . $serialnumber . ' has been received.';
                $mcode = "Bravo!{$userfirstname}. Your {$academicyear} {$name} {$type} application Serial no. {$serialnumber} has been received. Track progress on www.hef.co.ke";


                $action = 'sendphoneverificationCode';
                $arr = [
                    'recipient' => $phone,
                    'verificationcode' => $mcode,
                    'msg_priority' => '205',
                    'category' => '400'
                ];

                // Assuming callapion96 is a method in AuthModel and it returns a response
                $result = $apiController->datapull($action, $arr);

                return response()->json([
                    'result' => 'success',
                    'message' => $mcode
                ]);
            } else {
                return response()->json([
                    'result' => 'fail',
                    'message' => 'Loan not submitted for ID number ' . $idno . '. Please retry later.'
                ]);
            }
        } else {

            return  $this->updatenfmdetails($idno, $phone, $apiController);
        }
    }
    public function updatenfmdetails($idno, $phone, ApiController $apiController)
    { {

            $action = 'fetchprofileax';
            $arr = array('idno' => $idno);
            $result = $apiController->mobiapis($action, $arr);


            if (!is_array($result)) {
                $result = [$result];
                // $result = [];

            }

            if (Arr::has($result, 'IDNO')) {
                $axphone = $result['PHONE'];



                $fieldsToSanitize = ['NAME', 'FIRSTNAME', 'LASTNAME', 'MIDDLENAME', 'FULLBIRTHDATE', 'GENDER', 'BIRTHYEAR'];
                foreach ($fieldsToSanitize as $field) {
                    if (Arr::has($result, $field)) {
                        $result[$field] = str_replace("'", '', $result[$field]);
                    }
                }
                $gender = $result['GENDER'];

                $fullname = $result['NAME'];
                $firstname = $result['FIRSTNAME']; // Assuming 'LASTNAME' is the correct field
                $middlename = $result['MIDDLENAME'];
                $lastname = $result['LASTNAME'];
                $dob = $result['FULLBIRTHDATE'];
                $email = $result['EMAIL'];




                $data = [
                    'id_no' => $idno,
                    'full_name' => $fullname,
                    'gender' => $gender,
                    'dob' => $dob,
                    'cell_phone' => $phone,
                    'last_name' => $lastname,
                    'mid_name' => $middlename,
                    'first_name' => $firstname,
                    'email_add' => $email,
                    'cell_verified' => '1',
                    'updated_by' => 'updatenfmdetails',
                ];

                $added = DB::table('tbl_users_nfm')->updateOrInsert(
                    ['id_no' => $idno],
                    $data
                );


                if ($added) {
                    return response()->json([
                        'result' => 'success',
                        'message' => 'Your account details have been updated.Please retry applying.'
                    ], 200);
                } else {

                    return response()->json([
                        'result' => 'success',
                        'message' => 'Account details update failed.Please contact HELB on 0711052000, email ContactCentre@helb.co.ke or visit select Huduma centres or Anniversary towers Nairobi for assistance.'
                    ], 200);
                }
            } else {

                $data = Cache::remember('cached_tbl_users_SQ_tony_' . $idno, 60, function () use ($idno) {
                    return DB::table('tbl_users_SQ_tony')
                        //->select('phone')
                        ->where('id_no', $idno)
                        ->first();
                });
                if (Arr::has((array)$data, 'id_no')) {

                    $gender = $data->gender;
                    $fullname = $data->full_name;

                    $email_add = $data->email_add;
                    $phone = $phone;
                    $first_Name = $data->first_name; // Assuming 'LASTNAME' is the correct field
                    $other_Name = $data->mid_name;
                    $Surname = $data->last_name;
                    $dob = $data->dob;
                    $data = [
                        'id_no' => $idno,
                        'full_name' => $fullname,
                        'gender' => $gender,
                        'dob' => $dob,
                        'cell_phone' => $phone,
                        'last_name' => $Surname,
                        'mid_name' => $other_Name,
                        'first_name' => $first_Name,
                        'email_add' => $email_add,
                        'cell_verified' => '1',
                        'updated_by' => 'updatenfmdetails',
                    ];

                    $added = DB::table('tbl_users_nfm')->updateOrInsert(
                        ['id_no' => $idno],
                        $data
                    );








                    return response()->json([
                        'result' => 'success',
                        'message' => 'Your account details have been updated.Please retry applying.'
                    ], 200);
                } else {

                    return response()->json([
                        'result' => 'success',
                        'message' => 'Account details update failed.Please contact HELB on 0711052000, email ContactCentre@helb.co.ke or visit select Huduma centres or Anniversary towers Nairobi for assistance.'
                    ], 200);
                }
            }
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



        $countryCode = '254';
        $numberLength = strlen($cellphone);

        if ($numberLength < 11) {
            if (Str::startsWith($cellphone, '0')) {
                $cellphone = $countryCode . substr($cellphone, 1);
            } else {
                $cellphone = $countryCode . $cellphone;
            }
        }


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


    function productbankvalidate(Request $request, ApiController $apiController)
    {
        $rules = [
            'idno' => 'required|string|min:4',
            // 'phone' => 'required|string|min:10',
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

        $idno = $request->input('idno');
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
        $action = 'fetchbankax';
        $arr = array('idno' => $idno);
        $result = $apiController->mobiapis($action, $arr);


        if (!is_array($result)) {
            $result = [$result];
            // $result = [];

        }

        if (Arr::has($result, 'bankaccountnumber')) {

            $bankaccountnumber = $result['bankaccountnumber'];

            $message = 'Bank account number ' . $bankaccountnumber . ' Are the details correct?';

            return response()->json([
                'result' => 'success',
                'disbursementoption' => 'bank',
                'disbursementoptionvalue' => $bankaccountnumber,
                'message' => $message
            ], 200);
        } else {

            $message = 'Bank account number failed verification for id ' . $idno . ' select another option.';
            return response()->json([
                'result' => 'fail',
                'message' => $message
            ], 200);
        }
    }
    public function wrongbank(Request $request)
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

        $idno = $request->input('idno');
        $phone = $request->input('phone');
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


        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }

        $insert = array(
            'phone' => $phone,
            'productid' => $productid,
            'idno' => $idno,

            'academicyear' => $academicyear,
            'source' => $source,
        );



        $affectedRows = DB::table('tbl_incorrectbank')->Insert(
            $insert
        );
        if ($affectedRows > 0) {
            return response()->json([
                'result' => 'success',
                'message' =>  'success'
            ], 200);
        } else {

            return response()->json([
                'result' => 'fail',
                'message' => 'fail'
            ], 200);
        }
    }
    public function wronginstitution(Request $request)
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
            'institution' => 'required'

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idno = $request->input('idno');
        $phone = $request->input('phone');
        $academicyear = $request->input('academicyear');
        $productid = $request->input('productid');
        $productcode = $request->input('productcode');
        $studentgrouping = $request->input('studentgrouping');
        $name = $request->input('name');
        $type = $request->input('type');
        $idcre = $request->input('idcre');
        $institution = $request->input('institution');

        $gsf = $request->input('gsf');
        $brand = $request->input('brand');
        $source = 'ussd';


        if (!empty($gsf)) {
            $source = 'mobile';
        }

        if (!empty($brand)) {
            $source = 'miniapp';
        }

        $insert = array(
            'phone' => $phone,
            'productid' => $productid,
            'idno' => $idno,
            'institution' => $institution,

            'academicyear' => $academicyear,
            'source' => $source,
        );



        $affectedRows = DB::table('tbl_incorrectinstitution')->Insert(
            $insert
        );
        if ($affectedRows > 0) {
            return response()->json([
                'result' => 'success',
                'message' =>  'success'
            ], 200);
        } else {

            return response()->json([
                'result' => 'fail',
                'message' => 'fail'
            ], 200);
        }
    }
}
