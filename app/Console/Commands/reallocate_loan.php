<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ApiController;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Collection;
use HasRoles;
class reallocate_loan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:reallocate_loan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $this->reallocation();


    }


    public function reallocation()
    {


        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $cacheKey = "productparams";

        DB::table('allocatelocked')
            ->where('reallocated', '0') // Filter by current date
            ->orderBy('id', 'ASC')


            ->chunk(1000, function ($submittedata) use ($cacheKey) { // Pass $apiController here




                foreach ($submittedata as $record) {


                    //dd($record->allocationId);
                    $phoneNumber =  $record->phoneNumber;
                    $allocationid = $record->allocationId;
                    $idnew = $record->idno;
                    $remainingAmount = $record->remainingAmount;


                    //dd($idnew);


                    //get allocation


                    $accesstoken = $this->accesstokensurepaylive();


                    $url = env('SAFCOMALLOCATION_URL');
                    $requestData = [

                        'status' => 'ClawBack',
                        'beneficiaryIdentifier' => $phoneNumber,
                        'fundType' => 'Upkeep'


                    ];


                    // Convert the array to a JSON string
                    $requestData = json_encode($requestData);
                    $apiController = new ApiController();


                    $getallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
                    // dd($getallocation);

                    //$getbeneficiary =  $data->result[0]->accesstoken;
                    //dd($getbeneficiary);

                    $resCodes = $getallocation->resCode ?? null;;

                    if ($resCodes == '0') {
                        $resMsg = $getallocation->resMsg;

                        if ($resMsg == 'success') {

                            $result = $getallocation->result;

                            $allocation = $result->allocation;
                            $allocation = json_decode(json_encode($allocation), true);

                            $myarray = $allocation;

                            $filter =  $allocationid;

                            $Xnew_array = array_filter($myarray, function ($var) use ($filter) {
                                return isset($var['allocationId']) && $var['allocationId'] == $filter;
                            });






                            $datlogs = [
                                //Fill in the request parameters with valid values
                                'message' =>  $requestData,
                                'action' => 'clawbackgotall',
                                'phone' => $phoneNumber,



                            ];


                            DB::table('datlogs')->Insert(
                                $datlogs
                            );

                            // dd($Xnew_array);


                            if (!empty($Xnew_array)) { // Check if $Xnew_array is not empty
                                foreach ($Xnew_array as $item) {


                                    $batchno = $item['dynamicFields']['batchno'];
                                    $institutioncode = $item['dynamicFields']['institutioncode'];
                                    $loanserialno = $item['dynamicFields']['loanserialno'];




                                    $dynamicFields = array(
                                        'batchno' => $batchno,
                                        'idno' => $idnew,
                                        'institutioncode' => $institutioncode,
                                        'loanserialno' => $loanserialno,
                                        //'aaa' => $amount
                                    );


                    
                    
                                // Convert the array to a JSON string

                                    $curl_post_data = [
                                        //Fill in the request parameters with valid values

                                      //  'allocatedQuota' => $item['allocatedQuota'],
                                        'allocatedQuota' => $remainingAmount,
                                         'phoneNumber' => $phoneNumber,
                                        'currency' => 'KES',
                                        'expiredTime' => $item['expiredTime'],
                                        'fundType' => $item['fundType'],
                                        'groupCode' => "",
                                        'providerId' => env('surepayfinancierId'),
                                        'dynamicFields' => $dynamicFields,
                                    ];
                                    $curl_post_data = json_encode($curl_post_data);

                                   // dd($curl_post_data);


                                    $accesstoken = $this->accesstokensurepaylive();


                                    $url = env('SAFCOMINITIATEALLOCATION_URL');
                    


                                    // Convert the array to a JSON string

                                    $getallocation = $apiController->safaricomsurepay($accesstoken, $curl_post_data, $url);
                                    // dd($getallocation);

                                    //$getbeneficiary =  $data->result[0]->accesstoken;
                                    //dd($getbeneficiary);

                                    $resCodes = $getallocation->resCode ?? null;;

                                    if ($resCodes == '0') {
                                        $resMsg = $getallocation->resMsg;

                                        if ($resMsg == 'success') {



                                            echo 'allocation successfull 1';
                                            DB::statement('SET SQL_SAFE_UPDATES = 0');

                                            DB::update("UPDATE allocatelocked SET reallocated = ? WHERE allocationid = ?", ["1", $allocationid]);






                                        } else {

                                            echo 'allocation failed 1';
                                        }
                                    } else {

                                dd($getallocation);


                                        echo 'allocation failed 2';
                                    }
                                }
                            } else {


                                echo 'no clawedbackamount';
                            }
                        }
                    }

                    // end get allocation






                }
            });


            
    }

    public function accesstokensurepaylive()
    {



        $data = DB::select("SELECT access_token  FROM access_token");
        $accessToken = $data[0]->access_token;

        return $accessToken;
    }





}
