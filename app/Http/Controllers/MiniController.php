<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Requests\UserRequest;
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


class MiniController extends Controller
{

    public function oneminiapp(Request $request, ApiController $apiController)
    {

       
        $rules = [
            'phone' => 'required|string|min:10',
            'model' => 'required|string|min:3',

           
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

        $namba = $request->input('phone');
        $namba = str_replace(' ', '', trim(str_replace(['"', "'"], '', $namba)));

        $googletester = strtolower($namba);
		
		
        $brand = $request->input('brand');
        $model = $request->input('model');
        $platform = $request->input('platform');
        $system = $request->input('system');
        $version = $request->input('version'); 
		
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

        // if ($appversion < $latestversion) {
        //     // Handle the case where any value is null
        //     $message = 'update to version : ' . $latestversion;

        //     return response()->json([

        //         'result' => 'fail',
        //         'message' => $message

        //     ]);
        // }


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

            $insertmobilemini = array(
                'idno' => $idno,
                'smscount' => '1',
                'cell_verified' => "0",
                'phone_activation_code' => $code,
                'cell_phone' => $namba,
                 'appversion' => $version,
                'brand' => $brand,
                'platform' => $platform,
                'system' => $system,
                'deviceinfo' => $model
                
            );







            $hiddenemail = $apiController->maskEmailAddress($email_add);

            $hiddenamba = str_replace(substr($namba, 3, 6), $apiController->cross($namba), $namba);
            //dd($hiddenamba);



            $mcode = "[#] HELB:$code is your miniapp verification code. 2aUYAkIzflK";


            $action = 'sendphoneverificationCode';
            $arr = [
                'recipient' => $namba,
                'verificationcode' => $mcode,
                'msg_priority' => '235',
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
            $notifydet = (object) $notifydet;



            $query = DB::table('tbl_users_miniapp')
                ->select('smscount','deviceinfo','idno','cell_verified','time_added', DB::raw('TIMESTAMPDIFF(SECOND, time_added, NOW()) AS diff'))
                ->where('cell_phone', $namba)
                ->orderBy('time_added', 'desc')
                ->first();


          


            if ($query) {

                $previdno = $query->idno;
                $prevdeviceinfo = $query->deviceinfo;
                $allowedcount =  (int)($query->smscount);

                if ($allowedcount > 3) {

                    return json_encode([
                        'idno'   => $idno,
                        'name'   => $first_name,
                        'result' => 'fail',
                        'message' => 'You\'ve exhausted the number of allowed SMS. Kindly contact customer care'
                    ]);
                }

             
                $prevcell_verified = $query->cell_verified;
			     $prevdeviceinfo = str_replace("'", '', str_replace('"', '', $prevdeviceinfo));


                if ($prevcell_verified == '1') {

                    $comparedresult = ($previdno.$prevdeviceinfo != $idno.$prevdeviceinfo);


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
                        $added = DB::table('tbl_users_miniapp')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobilemini
                        );
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Check if the exception is due to an integrity constraint violation
                        if ($e->getCode() == '23000') {
                            // Delete the conflicting record based on the constraint
                            DB::table('tbl_users_miniapp')
                               // ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                                ->Where('cell_phone', $insertmobilemini['cell_phone']) // Another possible unique constraint
                                ->delete();

                            // Attempt to insert again
                            $added = DB::table('tbl_users_miniapp')->updateOrInsert(
                                ['idno' => $idno],
                                $insertmobilemini
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
                    $added = DB::table('tbl_users_miniapp')->updateOrInsert(
                        ['idno' => $idno],
                        $insertmobilemini
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if the exception is due to an integrity constraint violation
                    if ($e->getCode() == '23000') {
                        // Delete the conflicting record based on the constraint
                        DB::table('tbl_users_miniapp')
                           // ->where('gsf', $insertmobileandroid['gsf']) // Assuming 'gsf' is the conflicting unique column
                            ->Where('cell_phone', $insertmobilemini['cell_phone']) // Another possible unique constraint
                            ->delete();

                        // Attempt to insert again
                        $added = DB::table('tbl_users_miniapp')->updateOrInsert(
                            ['idno' => $idno],
                            $insertmobilemini
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
    public function threeminiverifycode(Request $request)
    {

        $rules = [
           'otp' => 'required|string|min:4',
            'phoneNumber' => 'required|string|min:10',

      
        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'error',
               // 'message' => $validator->errors()
               'message' => 'Kindly put the correct code'


            ]);
        }

        $code = $request->input('otp');
        $namba = $request->input('phoneNumber');

        $codeclean = str_replace(['"', "'"], '', $code);
        $stringPost = json_encode($request->all());

        DB::table('mini_log')->insert(['message' => $stringPost]);

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

      
            $data = DB::table('tbl_users_miniapp')
                ->where('cell_phone', $namba)
                ->orderByDesc('id')
                ->first(['idno', 'phone_activation_code']);

            if (!empty($data) && $data->phone_activation_code === $codeclean) {
                $idno = $data->idno;

                DB::table('tbl_users_miniapp')
                    ->where('cell_phone', $namba)
                    ->where('phone_activation_code', $codeclean)
                    ->update(['cell_verified' => '1', 'smscount' => 0]);

                    DB::statement('SET SQL_SAFE_UPDATES = 0');
                     DB::table('tbl_users_miniapp')
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
    public function miniapppersonal(Request $request, ApiController $apiController)
    {

        $rules = [
            'phone' => 'required|string|min:10',
            'model' => 'required|string|min:3',
            'brand' => 'required|string|min:3',
            'platform' => 'required|string|min:3',
            'idno' => 'required|string|min:3',


           
        ];



        $brand = $request->input('brand');
        $model = $request->input('model');
        $platform = $request->input('platform');
        $namba = $request->input('phone');
        $namba = str_replace(' ', '', trim(str_replace(['"', "'"], '', $namba)));
        $idno = $request->input('idno');



        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

      
        // dd($namba);

      
        $cell_verified = '1';
       
      

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




        $cacheKey = 'tbl_users_miniapp__' . $namba . '_' . $cell_verified . '_' . $gsf;
        $datatwo = Cache::remember($cacheKey, 60, function () use ($namba, $cell_verified, $gsf) {
            return DB::table('tbl_users_miniapp')
                ->select('id')
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


}
