<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
use App\Http\Controllers\ApiController;

use Illuminate\Support\Collection;
use HasRoles;
class Updateallocationreport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:updateallocationreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'allocationreport description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
     
            //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
    
    
    
    
            // Start building the query
        DB::table('mobilepayments20240830')
        ->where('updated', 0)
       // ->where('phonenumber', '254793880364')
    
        ->orderBy('id', 'asc') // Specify the correct column for ordering if 'id' is not correct
                ->chunk(1000, function ($submittedata) {
                    foreach ($submittedata as $record) {
    
                        if ($submittedata->isEmpty()) {
                            // The collection is empty
                            echo "No records found for today.";
                        } else {
                            
                                foreach ($submittedata as $record) {
                                    
                                                                    $paynumber = $record->phonenumber;
                                                                    
                                                                     $countryCode = '254';
            $numberLength = strlen($paynumber);
    
            if ($numberLength < 11) {
                if (Str::startsWith($paynumber, '0')) {
                    $paynumber = $countryCode . substr($paynumber, 1);
                } else {
                    $paynumber = $countryCode . $paynumber;
                }
            
                                                                    
                                                                    
    
                                }
                            
                            
                            sleep(3);
                           
                                       $accesstoken = $this->accesstokensurepaylive();
                                           $url = env('SAFCOMALLOCATION_URL');
                     $paynumber = str_replace(' ', '', trim($paynumber));
    
                $requestData = [
    
                   // 'status' => 'Completed',
                    'beneficiaryIdentifier' => $paynumber
                   // 'fundType' => 'Upkeep'
    
    
                ];
    
                //dd($paynumber);
    
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
    
                        $new_array = $allocation;
    
    
      if (!empty($new_array)) {
    
    
                            foreach ($new_array as $item) {
    
    
    
    // Assigning values to variables
                                $allocatedQuota = $item["allocatedQuota"];
                                $allocationId = $item["allocationId"];
                                $status = $item["status"];
                                $batchno = $item["dynamicFields"]["batchno"];
                                $beneficiaryIdentifier = $item["beneficiaryIdentifier"];
                                $clawBackAmount = $item["clawBackAmount"];
                                $createTime = $item["createTime"];
                                $idno = $item["dynamicFields"]["idno"];
                                $institutioncode = $item["dynamicFields"]["institutioncode"];
                                $loanserialno = $item["dynamicFields"]["loanserialno"];
                                $expiredAmount = $item["expiredAmount"];
                                $expiredTime = $item["expiredTime"];
                                $fundType = $item["fundType"];
                                $remainingAmount = $item["remainingAmount"];
                                $utilizedAmount = $item["utilizedAmount"];
                                
                                // Data to insert
    $data = [
        'allocatedQuota' => $allocatedQuota,
        'allocationId' => $allocationId,
        'status' => $status,
        'batchno' => $batchno,
        'beneficiaryIdentifier' => $beneficiaryIdentifier,
        'clawBackAmount' => $clawBackAmount,
        'createTime' => $createTime,
        'idno' => $idno,
        'institutioncode' => $institutioncode,
        'loanserialno' => $loanserialno,
        'expiredAmount' => $expiredAmount,
        'expiredTime' => $expiredTime,
        'fundType' => $fundType,
        'remainingAmount' => $remainingAmount,
        'utilizedAmount' => $utilizedAmount,
    ];
    DB::statement('SET SQL_SAFE_UPDATES = 0');
    
    DB::table('mobileallocationreportnew')->updateOrInsert(
        ['allocationId' => $data['allocationId']], // Condition to check for existence
        $data // Data to insert or update
    );
    
                                 DB::table('mobilepayments20240830')
                    ->where('phonenumber', $record->phonenumber)
                    ->update(['updated' => 1]);
    
                            }
                           
                        } else {
                            //$page = $this->load->view('account_view/iprs_form', $data, TRUE);
                            echo "NO records" . $record->phonenumber;
                            DB::statement('SET SQL_SAFE_UPDATES = 0');
    
                             DB::table('mobilepayments20240830')
                    ->where('phonenumber', $record->phonenumber)
                    ->update(['updated' => 1]);
                        }
    
    
    
    
    
    
    
    
                    }else{
                        
                                            echo "err records" . $record->phonenumber;
        
                        
                    }
                        // dd($Xnew_array);
    
    
    
                    }else{
                                                echo "err2 records" . $record->phonenumber;
    
                        
                    }
                }
    
                        }
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
