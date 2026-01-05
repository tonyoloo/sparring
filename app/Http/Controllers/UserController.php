<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BlockpaysImport;

use DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use HasRoles;



class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        return view('users.index', ['users' => $model->paginate(15)]);
    }

    // Md5 password hash

    function hash($data)
    {
        $salt = substr(md5(uniqid(rand(), true)), 0, 10);
        $salt_length = 10; // Adjust this if necessary
        $data = $salt . substr(sha1($salt . $data), 0, -$salt_length);
        return $data;
    }

    /**
     * Password unhash
     * */
    function unhash($userdata, $data)
    {
        $salt = substr($userdata, 0, 10);
        $salt_length = 10; // Adjust this if necessary
        $data = $salt . substr(sha1($salt . $data), 0, -$salt_length);
        return $data;
    }



    public function idnumberiprs(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;


        $idnumberiprs = $request->idnumberiprs;
        $action = 'iprs_mitigation_push_staff_helb';
        $arr = array('idno' => $idnumberiprs);
        $result = $apiController->mobiapis($action, $arr);
        // dd($result);


        return response()->json($result);
    }







    public function idnumberiprsmaisha(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;


        $idnumberiprs = $request->idnumberiprs;
        $action = 'iprs_mitigation_push_staff_helb_maisha';
        $arr = array('idno' => $idnumberiprs);
        $result = $apiController->mobiapis($action, $arr);
        // dd($result);


        return response()->json($result);
    }

    public function mpesaidform(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;


        $documentno = $request->documentno;
        $action = 'mpesamissingupdatesinglenew';
        $arr = array('documentno' => $documentno);
        $result = $apiController->dotseven($action, $arr);
        // dd($result);


        return response()->json($result);
    }



    public function minorform(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;


        $qualification = $request->academiclevelnfm;
        $qualification = str_replace("'", '', str_replace('"', '', $qualification));

        $examyrnfm = $request->examyrnfm;
        $examyrnfm = str_replace("'", '', str_replace('"', '', $examyrnfm));

        $kcseindexnumber = $request->kcseindexnumber;
        $kcseindexnumber = str_replace("'", '', str_replace('"', '', $kcseindexnumber));

        $birthdate = Carbon::parse($request->birthdatenfm);

        $student_id = $examyrnfm . $kcseindexnumber;
        $age = $birthdate->age;



        $class = 'minor_portal';


        $data = [
            'student_id' => $student_id,
            'updated_by' => $user,

        ];

        DB::table('tbl_iprs_updates_admins')->Insert(
            $data
        );



        $idnumberiprs = $request->idnumberiprs;

        $arr = array(
            'qualification' => $qualification,
            'student_id' => $student_id,
            'age' => $age,
            'class' => $class
        );


        // dd($arr);


        $result = $apiController->hefportal($arr);


        return strip_tags($result['info']);
    }



    public function generatescholarshipnfmchunk()
    {
        // Retrieve records from nfm_applications_2023 in chunks to save memory
        DB::table('nfm_applications_2023')->orderBy('id', 'DESC')->chunk(10000, function ($records) {
            $updateData = [];
            $insertData = [];

            foreach ($records as $record) {
                // Prepare data for update or insertion
                $data = [
                    'IDNO' => $record->id_no,
                    'qualifiedscholarship' => '1',
                    'product_id' => '183',
                ];

                // Check if REGNO exists in cre_pastapplicationstwo_kiprono
                $exists = DB::table('cre_pastapplicationstwo')
                    ->where('IDNO', $record->id_no)
                    ->exists();

                if ($exists) {
                    // Prepare update data
                    $updateData[] = $data;
                } else {
                    // Prepare insert data with additional fields
                    $data['ADMISSIONO'] = $record->AdmissionNumber;
                    $data['EXAMYR'] = $record->AdmiYear;
                    $data['STUDGROUPING2'] = 'UG';
                    $data['ACADEMIC_YEAR'] = '2025/2026';
                    $data['productcode'] = '5637144616';
                    $data['product_id'] = '193';

                    $insertData[] = $data;
                }
            }

            // Bulk update records
            foreach ($updateData as $data) {
                DB::table('cre_pastapplicationstwo')
                    ->where('IDNO', $data['IDNO'])
                    ->update($data);
            }

            // Bulk insert new records
            if (!empty($insertData)) {
                DB::table('cre_pastapplicationstwo')->insert($insertData);
            }

            echo 'processed ' . count($records) . ' records';
        });
    }

    public function TestDatasqlserver()
    {
        //  DB::setDefaultConnection('sqlsrv');

        // Use the test SQL Server connection with the instance name
        $data = DB::connection('sqlsrv')
            ->table('LMIDENTIFICATIONDOCUMENTS')
            ->take(5) // This corresponds to "TOP 5"
            ->get();
        return $data;
    }


    public function TestDatasqlserverHEF()
    {
        //  DB::setDefaultConnection('sqlsrv');

        // Use the test SQL Server connection with the instance name
        $data = DB::connection('mysqlhef')
            ->table('LMIDENTIFICATIONDOCUMENTS')
            ->take(5) // This corresponds to "TOP 5"
            ->get();
        return $data;
    }


    public function reallocation(Request $request, ApiController $apiController)
    {


        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $cacheKey = "productparams";

        DB::table('allocatelocked')
            ->where('reallocated', '0') // Filter by current date
            ->orderBy('id', 'ASC')


            ->chunk(1000, function ($submittedata) use ($cacheKey, $apiController) { // Pass $apiController here




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










    // public function updatesallocation(Request $request, ApiController $apiController)
    // {
    //     //$user = auth()->user()->name;

    //     $id = auth()->user()->id;
    //    // $id = 331;



    //     $userr = User::find($id);
    //     $user = auth()->user()->name;

    //     $userpermissions = $userr->getPermissionNames();


    //     $approvedpermission = collect($userpermissions);

    //     //dd($approvedpermission);


    //     if (
    //          $approvedpermission->contains('reallocate') ||
    //         $approvedpermission->contains('administrator')
    //     ) {









    //         $rules = [
    //             'academicyear' => 'required|string|min:4|max:4',
    //             'indexnumber' => 'required|string|min:5',
    //             'idnumber' => 'required|string|min:5',



    //         ];


    //         // Validate the request
    //         $validator = Validator::make($request->all(), $rules);

    //         // Check if validation fails
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => $validator->errors()], 422);
    //         }

    //         $idnonew = $request->idnumber;
    //         $idnumber_wrong = $request->academicyear . $request->indexnumber;

    //         $firstPartindex = $request->academicyear; //substr($idnumberax, 0, 4); // "2022"
    //         $secondPartindex =  $request->indexnumber; //substr($idnumberax, 4);   // "08202001269"


    //         //check if index belongs to idno
    //         $checkax = DB::connection('sqlsrv')->table('LMIDENTIFICATIONDOCUMENTS as b')
    //             ->leftJoin('LMSTUDENTEDUCATIONBACKGROUND as y', 'b.ACCOUNTNUM', '=', 'y.ACCOUNTNUM')
    //             ->where('b.IDENTITYREFERENCENO', $idnonew)
    //             ->where('y.TOYEAR', $firstPartindex)
    //             ->where('y.INDEXNUMBER', $secondPartindex)
    //             ->where('y.EDUBACGINSTITUTIONTYPE', '2')
    //             ->select('b.IDENTITYREFERENCENO', 'y.INDEXNUMBER', 'y.TOYEAR')
    //             ->get();

    //         $recordexist = $checkax[0]->IDENTITYREFERENCENO ?? null;

    //         if (empty($recordexist)) {

    //             return response()->json(['errors' => 'ID NOT FOUND ON AX WITH THE SPECIFIED INDEX'], 422);
    //         }
    //         //search on surepay portal record with the index



    //         $accesstoken = $this->accesstokensurepaylive();

    //         $url = env('SAFCOMGETBENEFICIARY_URL');
    //         $requestData = [

    //             'idNumber' => $idnumber_wrong,
    //             // 'idNumber' => '28613556'


    //         ];


    //         // Convert the array to a JSON string
    //         $requestData = json_encode($requestData);

    //         $getbeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
    //         //$getbeneficiary =  $data->result[0]->accesstoken;
    //         //dd($getbeneficiary);

    //         $resCodes = $getbeneficiary->resCode ?? null;;

    //         if ($resCodes == '0') {



    //             $resMsg = $getbeneficiary->resMsg;

    //             if ($resMsg == 'success') {

    //                 $result = $getbeneficiary->result;

    //                 $beneficiary = $result->beneficiary;

    //                 $data = $beneficiary[0] ?? null;

    //                 if (empty($data)) {

    //                     return response()->json(['errors' => 'ID NOT FOUND ON SUREPAY WITH THE SPECIFIED INDEX'], 422);
    //                 }




    //                 $email = $data->email;
    //                 $address = $data->address;
    //                 // $createdBy = $data->{'createdBy.name'};
    //                 // $createdDate = $data->createdDate;
    //                 // $idno = $data->dynamicFields->idno;
    //                 $institutioncode = $data->dynamicFields->institutioncode;
    //                 $firstName = $data->firstName;
    //                 $fullName = $data->fullName;
    //                 $id = $data->id;
    //                 // $idNumber = $data->idNumber;
    //                 $idType = $data->idType;
    //                 // $identityValue = $data->identityValue;
    //                 // $kycFlag = $data->kycFlag;
    //                 $lastName = $data->lastName;
    //                 $phoneNumber = $data->phoneNumber;
    //                 // $status = $data->status;
    //                 // $userStatus = $data->userStatus;
    //                 // $userType = $data->userType;



    //                 //do kyc check safaricom

    //                 $kycresult = $this->kycregister($idnonew, $phoneNumber, $apiController);
    //                 // return $data;
    //                 if (json_decode($kycresult)->verified == 'yes') {
    //                     //do beneficiary update

    //                     $dynamicFields = array(
    //                         'idno' =>  $idnonew,
    //                         'institutioncode' => $institutioncode


    //                     );

    //                     $requestData = [
    //                         //Fill in the request parameters with valid values
    //                         'id' => $id,
    //                         'identityValue' =>  $idnonew,
    //                         'firstName' => $firstName,
    //                         'lastName' => $lastName,
    //                         'address' => $address,
    //                         'idType' => $idType,
    //                         'email' => $email,
    //                         'dynamicFields' => $dynamicFields,
    //                         'fullName' => $fullName,
    //                         'idNumber' =>  $idnonew,
    //                         'phoneNumber' => $phoneNumber

    //                     ];


    //                     $accesstoken = $this->accesstokensurepaylive();

    //                     $url = env('SAFCOMUPDATEBENEFICIARY_URL');






    //                     // Convert the array to a JSON string
    //                     $requestData = json_encode($requestData);

    //                     $updatebeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
    //                     //$getbeneficiary =  $data->result[0]->accesstoken;
    //                     //dd($getbeneficiary);

    //                     $resCodes = $updatebeneficiary->resCode ?? null;;

    //                     if ($resCodes == '0') {
    //                         $resMsg = $updatebeneficiary->resMsg;

    //                         if ($resMsg == 'success') {

    //                             // dd("update success");
    //                             //get funds allocated
    //                             $datlogs = [
    //                                 //Fill in the request parameters with valid values
    //                                 'message' =>  $requestData,
    //                                 'action' => 'updatebeneficiary',
    //                                 'phone' => $phoneNumber,


    //                             ];


    //                             DB::table('datlogs')->Insert(
    //                                 $datlogs
    //                             );




    //                             $accesstoken = $this->accesstokensurepaylive();


    //                             $url = env('SAFCOMALLOCATION_URL');
    //                             $requestData = [

    //                                 'status' => 'Active',
    //                                 'beneficiaryIdentifier' => $phoneNumber,
    //                                 'fundType' => 'Upkeep'


    //                             ];


    //                             // Convert the array to a JSON string
    //                             $requestData = json_encode($requestData);

    //                             $getallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
    //                             // dd($getallocation);

    //                             //$getbeneficiary =  $data->result[0]->accesstoken;
    //                             //dd($getbeneficiary);

    //                             $resCodes = $getallocation->resCode ?? null;;

    //                             if ($resCodes == '0') {
    //                                 $resMsg = $getallocation->resMsg;

    //                                 if ($resMsg == 'success') {

    //                                     $result = $getallocation->result;

    //                                     $allocation = $result->allocation;
    //                                     $allocation = json_decode(json_encode($allocation), true);

    //                                     $myarray = $allocation;

    //                                     $filter =  $idnumber_wrong;

    //                                     $Xnew_array = array_filter($myarray, function ($var) use ($filter) {
    //                                         return isset($var['dynamicFields']['idno']) && $var['dynamicFields']['idno'] == $filter;
    //                                     });






    //                                     $datlogs = [
    //                                         //Fill in the request parameters with valid values
    //                                         'message' =>  $requestData,
    //                                         'action' => 'allocationgot',
    //                                         'phone' => $phoneNumber,



    //                                     ];


    //                                     DB::table('datlogs')->Insert(
    //                                         $datlogs
    //                                     );
    //                                     // dd($Xnew_array);

    //                                     foreach ($Xnew_array as $item) {

    //                                         $allocationId = $item['allocationId'];


    //                                         $values = [
    //                                             $item['allocatedQuota'], // allocatedQuota
    //                                             $item['beneficiaryIdentifier'], // phoneNumber
    //                                             $item['currency'], // currency

    //                                             $item['expiredTime'], // expiredTime
    //                                             $item['fundType'], // fundType
    //                                             // $item['groupCode'], // groupCode
    //                                             $item['providerId'], // providerId

    //                                             $item['dynamicFields']['batchno'], // batchno
    //                                             //$item['dynamicFields']['idno'], // idno
    //                                             $idnonew,
    //                                             $item['dynamicFields']['institutioncode'], // institutioncode
    //                                             $item['dynamicFields']['loanserialno'], // loanserialno
    //                                             $item['allocationId'],
    //                                          $item['remainingAmount']


    //                                         ];







    //                                         //clawbackallocation
    //                                         $accesstoken = $this->accesstokensurepaylive();

    //                                         $url = env('SAFCOMCLAWBACK_URL');
    //                                         $requestData = [

    //                                             'status' => 'Clawback',
    //                                             'allocationId' => $allocationId,


    //                                         ];


    //                                         // Convert the array to a JSON string
    //                                         $requestData = json_encode($requestData);

    //                                         $clawbackallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
    //                                         // dd($getallocation);

    //                                         //$getbeneficiary =  $data->result[0]->accesstoken;
    //                                         //dd($getbeneficiary);

    //                                         $resCodes = $clawbackallocation->resCode ?? null;;

    //                                         if ($resCodes == '0') {
    //                                             $resMsg = $clawbackallocation->resMsg;

    //                                             if ($resMsg == 'success') {

    //                                                 $result = $clawbackallocation->result;

    //                                                 $clawbackID = $result->id;
    //                                                 array_unshift($values, $clawbackID);



    //                                                 $datlogs = [
    //                                                     //Fill in the request parameters with valid values
    //                                                     'message' =>  $requestData,
    //                                                     'action' => 'allocationclawed',


    //                                                 ];


    //                                                 DB::table('datlogs')->Insert(
    //                                                     $datlogs
    //                                                 );



    //                                                 DB::insert("INSERT IGNORE INTO allocatelocked (
    //                                             clawbackid,allocatedQuota, phoneNumber, 
    //                                             currency, expiredTime, fundType,
    //                                             providerId, batchno,idno, 
    //                                               institutioncode, loanserialno,allocationId,remainingAmount
    //                                         ) VALUES (?, ?, ?, 
    //                                                  ?, ?, ?,
    //                                                  ?, ?, ?,
    //                                                   ?, ?,?,?)", $values);

    //                                                 // dd($Xnew_array);


    //                                                 $data = [
    //                                                     'student_id' =>  $item['beneficiaryIdentifier'],
    //                                                     'updated_by' => $user,

    //                                                 ];

    //                                                 DB::table('tbl_surepay_updates_admins')->Insert(
    //                                                     $data
    //                                                 );

    //                                                 return response()->json(['success' => 'STAGED FOR REALLOCATION'], 200);




    //                                                 // dd($Xnew_array);
    //                                                 //clawbackallocation

    //                                                 ////////////////
    //                                             } else {

    //                                                 return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
    //                                             }
    //                                         } else {

    //                                             return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
    //                                         }
    //                                     }
    //                                     ////////////////endclawback
    //                                 } else {

    //                                     return response()->json(['errors' => 'NO ALLOCATION'], 422);
    //                                 }
    //                             } else {

    //                                 return response()->json(['errors' => 'Allocation not found'], 422);
    //                                 // return response()->json(['errors' => $updatebeneficiary], 422);
    //                             }
    //                         } else {

    //                             return response()->json(['errors' => 'beneficiary update failed'], 422);
    //                             // return response()->json(['errors' => $updatebeneficiary], 422);
    //                         }
    //                     } else {
    //                         return response()->json(['errors' => 'beneficiary update failed'], 422);

    //                         return response()->json(['errors' => json_decode($kycresult)->message], 422);
    //                     }
    //                 } else {

    //                     return response()->json(['errors' => 'KYC FAILED'], 422);
    //                 }
    //             } else {

    //                 return response()->json(['errors' => 'CANT GET BENEFICIARY'], 422);
    //             }
    //         }
    //     } else {

    //         return response()->json(['errors' => 'INSUFFICIENT RIGHTS'], 422);
    //     }
    // }


    public function  clawbackdouble(Request $request, ApiController $apiController)
    {

        DB::statement('SET SQL_SAFE_UPDATES = 0');

        $results = DB::table('mobile_to_clawback')

            ->where('status', '=', '0')
            // ->where('allocationId', '=', '1353591691801601')


            ->get();



        // dd( $results );
        foreach ($results as $data) {
            $allocationId = $data->allocationId;




            //clawbackallocation
            $accesstoken = $this->accesstokensurepaylive();

            $url = env('SAFCOMCLAWBACK_URL');
            $requestData = [

                'status' => 'Clawback',
                'allocationId' => $allocationId,


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

            $clawbackallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
            // dd($getallocation);

            //$getbeneficiary =  $data->result[0]->accesstoken;
            //dd($getbeneficiary);

            $resCodes = $clawbackallocation->resCode ?? null;;

            if ($resCodes == '0') {
                $resMsg = $clawbackallocation->resMsg;

                if ($resMsg == 'success') {

                    $result = $clawbackallocation->result;

                    $clawbackID = $result->id;
                    //  array_unshift($values, $clawbackID);



                    $datlogs = [
                        //Fill in the request parameters with valid values
                        'message' =>  $requestData,
                        'action' => 'allocationclawed',


                    ];


                    DB::table('datlogs')->Insert(
                        $datlogs
                    );


                    DB::table('mobile_to_clawback')

                        ->where('allocationId', $allocationId)
                        ->update([

                            'status' => '1',

                        ]);




                    //  return response()->json(['success' => 'STAGED FOR REALLOCATION'], 200);

                    echo 'CLAWED ' . $allocationId . '\n';


                    // dd($Xnew_array);
                    //clawbackallocation

                    ////////////////
                } else {

                    return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
                }
            } else {

                return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
            }
        }
    }





    public function updatesallocation(Request $request, ApiController $apiController)
    {
        //$user = auth()->user()->name;

        // $id = auth()->user()->id;
        $id = 331;



        $userr = User::find($id);
        // $user = auth()->user()->name;
        $user = 'tony';
        $userpermissions = $userr->getPermissionNames();


        $approvedpermission = collect($userpermissions);

        //dd($approvedpermission);


        if (
            $approvedpermission->contains('reallocate') ||
            $approvedpermission->contains('administrator')
        ) {









            $rules = [
                'academicyear' => 'required|string|min:4|max:4',
                'indexnumber' => 'required|string|min:5',
                'idnumber' => 'required|string|min:5',



            ];


            // Validate the request
            $validator = Validator::make($request->all(), $rules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $idnonew = $request->idnumber;
            $idnumber_wrong = $request->academicyear . $request->indexnumber;

            $firstPartindex = $request->academicyear; //substr($idnumberax, 0, 4); // "2022"
            $secondPartindex =  $request->indexnumber; //substr($idnumberax, 4);   // "08202001269"


            //check if index belongs to idno
            $checkax = DB::connection('sqlsrv')->table('LMIDENTIFICATIONDOCUMENTS as b')
                ->leftJoin('LMSTUDENTEDUCATIONBACKGROUND as y', 'b.ACCOUNTNUM', '=', 'y.ACCOUNTNUM')
                ->where('b.IDENTITYREFERENCENO', $idnonew)
                ->where('y.TOYEAR', $firstPartindex)
                ->where('y.INDEXNUMBER', $secondPartindex)
                ->where('y.EDUBACGINSTITUTIONTYPE', '2')
                ->select('b.IDENTITYREFERENCENO', 'y.INDEXNUMBER', 'y.TOYEAR')
                ->get();

            $recordexist = $checkax[0]->IDENTITYREFERENCENO ?? null;

            if (empty($recordexist)) {

                return response()->json(['errors' => 'ID NOT FOUND ON AX WITH THE SPECIFIED INDEX'], 422);
            }
            //search on surepay portal record with the index



            $accesstoken = $this->accesstokensurepaylive();

            $url = env('SAFCOMGETBENEFICIARY_URL');
            $requestData = [

                'idNumber' => $idnumber_wrong,
                // 'idNumber' => '28613556'


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

            $getbeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
            //$getbeneficiary =  $data->result[0]->accesstoken;
            //dd($getbeneficiary);

            $resCodes = $getbeneficiary->resCode ?? null;;

            if ($resCodes == '0') {



                $resMsg = $getbeneficiary->resMsg;

                if ($resMsg == 'success') {
                    //begins here

                    $result = $getbeneficiary->result;

                    $beneficiary = $result->beneficiary;

                    if (empty($beneficiary) || !is_array($beneficiary)) {
                        return response()->json(['errors' => 'NO RECORDS FOUND IN SUREPAY FOR THE GIVEN INDEX'], 422);
                    }

                    foreach ($beneficiary as $data) {



                        $email = $data->email;
                        $address = $data->address;
                        // $createdBy = $data->{'createdBy.name'};
                        // $createdDate = $data->createdDate;
                        // $idno = $data->dynamicFields->idno;
                        $institutioncode = $data->dynamicFields->institutioncode;
                        $firstName = $data->firstName;
                        $fullName = $data->fullName;
                        $id = $data->id;
                        // $idNumber = $data->idNumber;
                        $idType = $data->idType;
                        // $identityValue = $data->identityValue;
                        // $kycFlag = $data->kycFlag;
                        $lastName = $data->lastName;
                        $phoneNumber = $data->phoneNumber;
                        // $status = $data->status;
                        // $userStatus = $data->userStatus;
                        // $userType = $data->userType;



                        //do kyc check safaricom

                        $kycresult = $this->kycregister($idnonew, $phoneNumber, $apiController);
                        // return $data;
                        if (json_decode($kycresult)->verified == 'yes') {
                            //do beneficiary update

                            $dynamicFields = array(
                                'idno' =>  $idnonew,
                                'institutioncode' => $institutioncode


                            );

                            $requestData = [
                                //Fill in the request parameters with valid values
                                'id' => $id,
                                'identityValue' =>  $idnonew,
                                'firstName' => $firstName,
                                'lastName' => $lastName,
                                'address' => $address,
                                'idType' => $idType,
                                'email' => $email,
                                'dynamicFields' => $dynamicFields,
                                'fullName' => $fullName,
                                'idNumber' =>  $idnonew,
                                'phoneNumber' => $phoneNumber

                            ];


                            $accesstoken = $this->accesstokensurepaylive();

                            $url = env('SAFCOMUPDATEBENEFICIARY_URL');






                            // Convert the array to a JSON string
                            $requestData = json_encode($requestData);

                            $updatebeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
                            //$getbeneficiary =  $data->result[0]->accesstoken;
                            //dd($getbeneficiary);

                            $resCodes = $updatebeneficiary->resCode ?? null;;

                            if ($resCodes == '0') {
                                $resMsg = $updatebeneficiary->resMsg;

                                if ($resMsg == 'success') {

                                    // dd("update success");
                                    //get funds allocated
                                    $datlogs = [
                                        //Fill in the request parameters with valid values
                                        'message' =>  $requestData,
                                        'action' => 'updatebeneficiary',
                                        'phone' => $phoneNumber,


                                    ];


                                    DB::table('datlogs')->Insert(
                                        $datlogs
                                    );




                                    $accesstoken = $this->accesstokensurepaylive();


                                    $url = env('SAFCOMALLOCATION_URL');
                                    $requestData = [

                                        'status' => 'Active',
                                        'beneficiaryIdentifier' => $phoneNumber,
                                        'fundType' => 'Upkeep'


                                    ];


                                    // Convert the array to a JSON string
                                    $requestData = json_encode($requestData);

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

                                            $filter =  $idnumber_wrong;

                                            $Xnew_array = array_filter($myarray, function ($var) use ($filter) {
                                                return isset($var['dynamicFields']['idno']) && $var['dynamicFields']['idno'] == $filter;
                                            });






                                            $datlogs = [
                                                //Fill in the request parameters with valid values
                                                'message' =>  $requestData,
                                                'action' => 'allocationgot',
                                                'phone' => $phoneNumber,



                                            ];


                                            DB::table('datlogs')->Insert(
                                                $datlogs
                                            );
                                            // dd($Xnew_array);

                                            foreach ($Xnew_array as $item) {

                                                $allocationId = $item['allocationId'];


                                                $values = [
                                                    $item['allocatedQuota'], // allocatedQuota
                                                    $item['beneficiaryIdentifier'], // phoneNumber
                                                    $item['currency'], // currency

                                                    $item['expiredTime'], // expiredTime
                                                    $item['fundType'], // fundType
                                                    // $item['groupCode'], // groupCode
                                                    $item['providerId'], // providerId

                                                    $item['dynamicFields']['batchno'], // batchno
                                                    //$item['dynamicFields']['idno'], // idno
                                                    $idnonew,
                                                    $item['dynamicFields']['institutioncode'], // institutioncode
                                                    $item['dynamicFields']['loanserialno'], // loanserialno
                                                    $item['allocationId'],
                                                    $item['remainingAmount']


                                                ];







                                                //clawbackallocation
                                                $accesstoken = $this->accesstokensurepaylive();

                                                $url = env('SAFCOMCLAWBACK_URL');
                                                $requestData = [

                                                    'status' => 'Clawback',
                                                    'allocationId' => $allocationId,


                                                ];


                                                // Convert the array to a JSON string
                                                $requestData = json_encode($requestData);

                                                $clawbackallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
                                                // dd($getallocation);

                                                //$getbeneficiary =  $data->result[0]->accesstoken;
                                                //dd($getbeneficiary);

                                                $resCodes = $clawbackallocation->resCode ?? null;;

                                                if ($resCodes == '0') {
                                                    $resMsg = $clawbackallocation->resMsg;

                                                    if ($resMsg == 'success') {

                                                        $result = $clawbackallocation->result;

                                                        $clawbackID = $result->id;
                                                        array_unshift($values, $clawbackID);



                                                        $datlogs = [
                                                            //Fill in the request parameters with valid values
                                                            'message' =>  $requestData,
                                                            'action' => 'allocationclawed',


                                                        ];


                                                        DB::table('datlogs')->Insert(
                                                            $datlogs
                                                        );



                                                        DB::insert("INSERT IGNORE INTO allocatelocked (
                                                clawbackid,allocatedQuota, phoneNumber, 
                                                currency, expiredTime, fundType,
                                                providerId, batchno,idno, 
                                                  institutioncode, loanserialno,allocationId,remainingAmount
                                            ) VALUES (?, ?, ?, 
                                                     ?, ?, ?,
                                                     ?, ?, ?,
                                                      ?, ?,?,?)", $values);

                                                        // dd($Xnew_array);


                                                        $data = [
                                                            'student_id' =>  $item['beneficiaryIdentifier'],
                                                            'updated_by' => $user,

                                                        ];

                                                        DB::table('tbl_surepay_updates_admins')->Insert(
                                                            $data
                                                        );

                                                        //  return response()->json(['success' => 'STAGED FOR REALLOCATION'], 200);

                                                        echo 'STAGED FOR REALLOCATION';


                                                        // dd($Xnew_array);
                                                        //clawbackallocation

                                                        ////////////////
                                                    } else {

                                                        return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
                                                    }
                                                } else {

                                                    return response()->json(['errors' => 'CLAWBACK FAILED'], 422);
                                                }
                                            }
                                            ////////////////endclawback
                                        } else {

                                            return response()->json(['errors' => 'NO ALLOCATION'], 422);
                                        }
                                    } else {

                                        return response()->json(['errors' => 'Allocation not found'], 422);
                                        // return response()->json(['errors' => $updatebeneficiary], 422);
                                    }
                                } else {

                                    return response()->json(['errors' => 'beneficiary update failed'], 422);
                                    // return response()->json(['errors' => $updatebeneficiary], 422);
                                }
                            } else {
                                return response()->json(['errors' => 'beneficiary update failed'], 422);

                                return response()->json(['errors' => json_decode($kycresult)->message], 422);
                            }
                        } else {
                            // dd("jjjjjjjjjjjjj");
                            echo 'KYC FAILED';

                            // return response()->json(['errors' => 'KYC FAILED'], 422);
                        }
                    }
                    //ends here
                } else {

                    return response()->json(['errors' => 'CANT GET BENEFICIARY'], 422);
                }
            }
        } else {

            return response()->json(['errors' => 'INSUFFICIENT RIGHTS'], 422);
        }
    }
    public function deleteDuplicatesInChunks()
    {
        $chunkSize = 1000; // Define the chunk size

        // Retrieve duplicates with counts greater than 1
        DB::table('tbl_users_mobile')
            ->select('idno', DB::raw('MAX(id) as max_id'))
            ->where('cell_verified', '1')
            ->groupBy('idno')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('idno') // Add orderBy clause
            ->chunk($chunkSize, function ($chunks) {
                foreach ($chunks as $chunk) {
                    // Initialize the array to keep IDs
                    $idsToKeep = [];

                    // Process each record in the chunk
                    foreach ($chunk as $record) {
                        // Collect max IDs from each record
                        if (isset($record->max_id) && $record->max_id > 0) {
                            $idsToKeep[] = $record->max_id;
                        }
                    }

                    // Ensure there are IDs to keep before performing deletion
                    if (!empty($idsToKeep)) {
                        DB::table('tbl_users_mobile')
                            ->where('cell_verified', '1')
                            ->whereNotIn('id', $idsToKeep)
                            ->delete();
                    }
                }
            });
    }



    public function testhefconnection()

    {

        $response = Http::post('https://portal.hef.co.ke/auth/mobiportal');

        if ($response->successful()) {
            echo "Connection successful!";
        } elseif ($response->failed()) {
            echo "Connection failed!";
        } elseif ($response->clientError()) {
            echo "Client error!";
        } elseif ($response->serverError()) {
            echo "Server error!";
        } else {
            echo "Unknown error!";
        }
    }

    public function updateindextoidumberHEF()
    {
        //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
        ini_set('max_execution_time',  10800); // 3600 seconds = 1 hour

        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $years = ['2018', '2019', '2020', '2021', '2022', '2023'];



        // Start building the query
        $query = DB::table('ufbdata');

        // Add each year as an orWhere condition
        // foreach ($years as $index => $year) {
        //     if ($index === 0) {
        //         $query->where('IDNO', 'like', $year . '%');
        //     } else {
        //         $query->orWhere('IDNO', 'like', $year . '%');
        //     }
        // }
        //dd("fff");
        // Execute the query with ordering and chunking
        $query
            ->whereNull('idno')
            ->orderBy('id', 'asc') // Specify the correct column for ordering if 'id' is not correct


            ->chunk(1000, function ($submittedata) {

                if ($submittedata->isEmpty()) {


                    //dd("gggg");
                    echo "No records found for today.";
                    return; // stop processing
                }





                foreach ($submittedata as $record) {

                    if ($submittedata->isEmpty()) {
                        // The collection is empty
                        echo "No records found for today.";
                    } else {


                        // dd($submittedata);
                        // The collection is not empty

                        $response = Http::timeout(30)
                            ->retry(3, 500) // 3 attempts, 500ms delay between
                            ->post('https://portal.hef.co.ke/auth/mobiportal');
                        // dd($response);

                        if ($response->successful()) {
                            foreach ($submittedata as $record) {

                                $IDNO = $record->kuccps_id;
                                $productcode = '5637144616'; //$record->productcode;
                                $admissiono = $record->admission_no;


                                $indexax = substr($IDNO, 4);
                                $examyear = substr($IDNO, 0, 4);







                                $curl = curl_init();

                                curl_setopt($curl, CURLOPT_URL, 'https://portal.hef.co.ke/auth/mobiportal');
                                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

                                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Setting custom header

                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($curl, CURLOPT_POST, true);
                                curl_setopt(
                                    $curl,
                                    CURLOPT_POSTFIELDS,
                                    '{ "index": "' . $indexax . '", "year": "' . $examyear . '"}'


                                );

                                $respons = curl_exec($curl);


                                // dd($respons);

                                if (curl_errno($curl)) {
                                    //dd($curl);

                                    // If an error occurs, display the error message
                                    $error_msg = curl_error($curl);
                                    curl_close($curl);
                                    //dd('cURL Error: ' . $error_msg);
                                } else {
                                    // Close the cURL session and display the response

                                    $respons = json_decode($respons);
                                    curl_close($curl);
                                    // dd($respons);

                                    // dd($respons->Valid);//
                                    // dd($respons->data[0]->idnumber);

                                    if (isset($respons->Valid) && $respons->Valid === true) {


                                        //dd($respons->data[0]);

                                        $idnumber =  $respons->data[0]->idnumber;

                                        //  if ((preg_match('/^(07|254|2022)/', $idnumber)) &&  (strlen($idnumber) >= 8)) {
                                        // $idnumber does not begin with 2022

                                        // dd($respons->data[0]);
                                        echo "$idnumber does not begin with 2022";

                                        $updateData = [
                                            'IDNO' => $idnumber,




                                        ];

                                        $Institutiondata = [
                                            'ACADEMICYEAR' => '2025/2026',
                                            'ADMISSIONCATEGORY' => $respons->data[0]->AdmissionCategory ?? null,
                                            'ADMISSIONNUMBER' => $respons->data[0]->AdmissionNumber ?? null,
                                            'ADMISSIONYEAR' => $respons->data[0]->AdmiYear ?? null,
                                            'COURSECODE' => $respons->data[0]->CourseCode ?? null,
                                            'INSTITUTIONBRANCHCODE' => $respons->data[0]->InstitutionBranchCode ?? null,
                                            'INSTITUTIONCODE' => $respons->data[0]->InstitutionCode ?? null,
                                            'ACCOUNTNUM' => $respons->data[0]->accountnum ?? null,
                                            'Productcode' => $productcode ?? null,
                                            'IDNO' => $respons->data[0]->idnumber ?? null,
                                            'InstitutionName' => $respons->data[0]->INSTITUTIONNAME ?? null,
                                            'CourseDescription' => $respons->data[0]->CourseName ?? null,
                                            // 'LOANSERIALNO' => '', // still commented out
                                        ];



                                        // {
                                        //     "Valid": true,
                                        //     "data": [
                                        //         {
                                        //             "idnumber": "202201100003120",
                                        //             "accountnum": "STUD35827929",
                                        //             "AdmissionCategory": "1",
                                        //             "AdmissionNumber": "I73/5399/2023",
                                        //             "AdmiYear": "2023",
                                        //             "AnnualFees": "0",
                                        //             "InstitutionCode": "KU",
                                        //             "INSTITUTIONNAME": "Kenyatta University",
                                        //             "InstitutionBranchCode": "",
                                        //             "YearOfStudy": "1",
                                        //             "CourseCode": "I73",
                                        //             "CourseName": "BSC ANALYTICAL CHEMISTRY WITH MANAGEMENT",
                                        //             "CountryCode": "KEN",
                                        //             "LevelOfStudy": "3",
                                        //             "InstitutionType": "7",
                                        //             "StudyMode": "1"
                                        //         }
                                        //     ]
                                        // }

                                        $ADMISSIONO  = $respons->data[0]->AdmissionNumber;


                                        $ufbdata = [
                                            'updated' => '2',
                                            'idno' => $idnumber,

                                        ];





                                        DB::table('ufbdata')
                                            ->where('kuccps_id', $IDNO)
                                            ->update($ufbdata);


                                        DB::table('dminstitututions_2024')->updateOrInsert(
                                            ['IDNO' => $idnumber],  // The condition for checking existence
                                            $Institutiondata    // The data to update or insert
                                        );
                                        // }

                                        //   dd($respons->data[0]);


                                        // $exists = DB::table('cre_pastapplicationstwo')
                                        //     ->where('IDNO', $idnumber)
                                        //     ->exists();

                                        // if (!$exists) {
                                        // DB::table('cre_pastapplicationstwo')->insert([
                                        //     'IDNO' => $idnumber,
                                        //     'ADMISSIONO' => $ADMISSIONO,
                                        //     'EXAMYR'  => $productcode,
                                        //     'STUDGROUPING2' => 'UG',
                                        //     'ACADEMIC_YEAR' => '2025/2026',
                                        //     'qualifiedscholarship' => '1',
                                        //     'productcode' => $productcode,
                                        //     'product_id' => '2'
                                        // ]);

                                        //  }


                                        //  }







                                        // Second update


                                        // Second update
                                        // DB::table('dminstitututions_2024_original')
                                        //     ->where('IDNO', $IDNO)
                                        //     ->update($Institutiondata);


                                    } else {
                                        // $idnumber begins with 2022
                                        echo $IDNO . " does not begins with 2022";
                                    }
                                }
                            }
                        }
                        //  echo "Connection successful!";
                    }

                    echo "no Connection successful!";
                }
                // }
            });
    }


    public function updateindextoidumberBULK()
    {
        //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
        ini_set('max_execution_time',  259200); // 3600 seconds = 1 hour

        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');





        // Start building the query
        $query = DB::table('mobile_portal_data');


        $query
            ->where('updated', 1)
            ->orderBy('id', 'asc') // Specify the correct column for ordering if 'id' is not correct


            ->chunk(1000, function ($submittedata) {

                if ($submittedata->isEmpty()) {


                    //dd("gggg");
                    echo "No records found for today.";
                    return; // stop processing
                }





                foreach ($submittedata as $record) {

                    if ($submittedata->isEmpty()) {
                        // The collection is empty
                        echo "No records found for today.";
                    } else {



                        foreach ($submittedata as $record) {

                            $productcode = '5637144616'; //$record->productcode;






                            $Institutiondata = [
                                'ACADEMICYEAR' => '2025/2026',
                                'ADMISSIONCATEGORY' => $record->AdmissionCategory ?? null,
                                'ADMISSIONNUMBER' => $record->AdmissionNumber ?? null,
                                'ADMISSIONYEAR' => $record->AdmiYear ?? null,
                                'COURSECODE' => $record->CourseCode ?? null,
                                'INSTITUTIONBRANCHCODE' => $record->InstitutionBranchCode ?? null,
                                'INSTITUTIONCODE' => $record->InstitutionCode ?? null,
                                'ACCOUNTNUM' => $record->accountnum ?? null,
                                'Productcode' => $productcode ?? null,
                                'IDNO' => $record->idnumber ?? null,
                                'InstitutionName' => $record->INSTITUTIONNAME ?? null,
                                'CourseDescription' => $record->CourseName ?? null,
                                // 'LOANSERIALNO' => '', // still commented out
                            ];





                            $ufbdata = [
                                'updated' => '2',

                            ];








                            $exists = DB::table('cre_pastapplicationstwo')
                                ->where('IDNO', $record->idnumber)
                                ->exists();

                            if (!$exists) {
                                DB::statement('SET SQL_SAFE_UPDATES = 0');

                                DB::table('cre_pastapplicationstwo')->insert([
                                    'IDNO' => $record->idnumber,
                                    'ADMISSIONO' => $record->AdmissionNumber,
                                    'EXAMYR'  => $record->AdmiYear,
                                    'STUDGROUPING2' => 'UG',
                                    'ACADEMIC_YEAR' => '2025/2026',
                                    'qualifiedscholarship' => '1',
                                    'qualifiedloanmodel' => '0',
                                    'qualifiedboth' => '0',

                                    'productcode' => $productcode,
                                    'product_id' => '2'
                                ]);

                                DB::table('mobile_portal_data')
                                    ->where('idnumber', $record->idnumber)
                                    ->update($ufbdata);


                                DB::table('dminstitututions_2024')->updateOrInsert(
                                    ['IDNO' => $record->idnumber],  // The condition for checking existence
                                    $Institutiondata    // The data to update or insert
                                );
                            }
                        }
                    }

                    //  echo "Connection successful!";
                }

                echo "no Connection successful!";

                // }
            });
    }












    function updatetelco()
    {
        error_reporting(E_ALL & ~E_WARNING);

        $idnumber = $_REQUEST['idno'];
        $phone = $_REQUEST['phone'];

        $conn3 = $this->dbConnect7();

        if (empty($idnumber)) {
            $idnumber = '1';
        }

        if ($conn3 === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $sql = "select * from mobile_users where user_name = ? or id_no = ?";


        $paramsone = array($phone, $idnumber);
        $stm = sqlsrv_query($conn3, $sql, $paramsone);



        if ($stm === false) {
            die(print_r(sqlsrv_errors(), true));
        }




        $json = array();

        do {
            while ($row = sqlsrv_fetch_array($stm, SQLSRV_FETCH_ASSOC)) {
                $json[] = $row;
            }
        } while (sqlsrv_next_result($stm));

        if (empty($json)) {

            echo json_encode(array('missing' =>  'Phone number ' . $phone . ' cant be found'));
        } else {


            $sqltt = "select * from mobile_users where user_name = ? and id_no != ?";


            $paramstt = array($phone, $idnumber);


            $stmtt = sqlsrv_query($conn3, $sqltt, $paramstt);



            if ($stmtt === false) {
                die(print_r(sqlsrv_errors(), true));
            }




            $jsontt = array();

            do {
                while ($rowtt = sqlsrv_fetch_array($stmtt, SQLSRV_FETCH_ASSOC)) {
                    $jsontt[] = $rowtt;
                }
            } while (sqlsrv_next_result($stmtt));


            if (empty($jsontt[0]['user_id'])) {




                $mobile_id =  $json[0]['mobile_id'];
                $sim_id =  $json[0]['sim_id'];
                $date_created =  $json[0]['date_created'];
                $date_modified =  $json[0]['date_modified'];

                $date_of_birth =  $json[0]['date_of_birth'];
                $first_name =  $json[0]['first_name'];
                $id_no =  $json[0]['id_no'];
                $last_login_date =  $json[0]['last_login_date'];

                $last_name =  $json[0]['last_name'];
                $loanee =  $json[0]['loanee'];
                $login_attempts =  $json[0]['login_attempts'];
                $user_name =  $json[0]['user_name'];

                $user_pwd =  $json[0]['user_pwd'];
                $user_status =  $json[0]['user_status'];
                $user_status_date =  $json[0]['user_status_date'];
                $user_status_description =  $json[0]['user_status_description'];

                $disbursement_report_last_sent =  $json[0]['disbursement_report_last_sent'];
                $loan_mini_statement_last_sent =  $json[0]['loan_mini_statement_last_sent'];
                $login_flag =  $json[0]['login_flag'];
                $auth_action =  $json[0]['auth_action'];

                $auth_action_valid_date =  $json[0]['auth_action_valid_date'];

                $sqltwo = "INSERT INTO mobile_users_history (
mobile_id,sim_id,date_created, date_modified,

date_of_birth, first_name,id_no, last_login_date,

last_name, loanee,login_attempts, user_name,

user_pwd,user_status,user_status_date,user_status_description,

disbursement_report_last_sent,loan_mini_statement_last_sent,login_flag, auth_action,

auth_action_valid_date)

VALUES (?,?,?,?,?,?,
?,?,?,?,
?,?,?,?,
?,?,?,?,
?,?,?);";

                $paramstwo = array(
                    $mobile_id,
                    $sim_id,
                    $date_created,
                    $date_modified,
                    $date_of_birth,
                    $first_name,
                    $id_no,
                    $last_login_date,
                    $last_name,
                    $loanee,
                    $login_attempts,
                    $user_name,
                    $user_pwd,
                    $user_status,
                    $user_status_date,
                    $user_status_description,
                    $disbursement_report_last_sent,
                    $loan_mini_statement_last_sent,
                    $login_flag,
                    $auth_action,
                    $auth_action_valid_date
                );

                $stmtwo = sqlsrv_query($conn3, $sqltwo, $paramstwo);






                $sql = "update mobile_users set  user_name = ? , sim_id  = ? where id_no = ?";
                $params = array($phone, '', $idnumber);
                $stmthree = sqlsrv_query($conn3, $sql, $params);

                $rows_affected = sqlsrv_rows_affected($stmthree);
                if ($rows_affected === false) {
                    // die( print_r( sqlsrv_errors(), true));
                    echo json_encode(sqlsrv_errors());
                } elseif ($rows_affected == -1) {
                    // echo "No information available.<br />";
                    echo json_encode(array('result' => 'error'));
                } else {
                    echo json_encode(array('result' => 'success'));
                }


                sqlsrv_free_stmt($stmtwo);
                sqlsrv_close($conn3);












                sqlsrv_free_stmt($stmtwo);
                sqlsrv_close($conn3);
            } else {
                $user_name =  $jsontt[0]['id_no'];
                $first_name =  $jsontt[0]['first_name'];
                $last_name =  $jsontt[0]['last_name'];
                $fullname = $first_name . '' . $last_name;



                $mobile_id =  $jsontt[0]['mobile_id'];
                $sim_id =  $jsontt[0]['sim_id'];
                $date_created =  $jsontt[0]['date_created'];
                $date_modified =  $jsontt[0]['date_modified'];

                $date_of_birth =  $jsontt[0]['date_of_birth'];
                $first_name =  $jsontt[0]['first_name'];
                $id_no =  $jsontt[0]['id_no'];
                $last_login_date =  $jsontt[0]['last_login_date'];

                $last_name =  $jsontt[0]['last_name'];
                $loanee =  $jsontt[0]['loanee'];
                $login_attempts =  $jsontt[0]['login_attempts'];
                $user_name =  $jsontt[0]['user_name'];

                $user_pwd =  $jsontt[0]['user_pwd'];
                $user_status =  $jsontt[0]['user_status'];
                $user_status_date =  $jsontt[0]['user_status_date'];
                $user_status_description =  $jsontt[0]['user_status_description'];

                $disbursement_report_last_sent =  $jsontt[0]['disbursement_report_last_sent'];
                $loan_mini_statement_last_sent =  $jsontt[0]['loan_mini_statement_last_sent'];
                $login_flag =  $jsontt[0]['login_flag'];
                $auth_action =  $jsontt[0]['auth_action'];

                $auth_action_valid_date =  $jsontt[0]['auth_action_valid_date'];

                $sqltwop = "INSERT INTO mobile_users_history (
mobile_id,sim_id,date_created, date_modified,

date_of_birth, first_name,id_no, last_login_date,

last_name, loanee,login_attempts, user_name,

user_pwd,user_status,user_status_date,user_status_description,

disbursement_report_last_sent,loan_mini_statement_last_sent,login_flag, auth_action,

auth_action_valid_date)

VALUES (?,?,?,?,?,?,
?,?,?,?,
?,?,?,?,
?,?,?,?,
?,?,?);";

                $paramstwop = array(
                    $mobile_id,
                    $sim_id,
                    $date_created,
                    $date_modified,
                    $date_of_birth,
                    $first_name,
                    $id_no,
                    $last_login_date,
                    $last_name,
                    $loanee,
                    $login_attempts,
                    $user_name,
                    $user_pwd,
                    $user_status,
                    $user_status_date,
                    $user_status_description,
                    $disbursement_report_last_sent,
                    $loan_mini_statement_last_sent,
                    $login_flag,
                    $auth_action,
                    $auth_action_valid_date
                );

                $stmtwo = sqlsrv_query($conn3, $sqltwop, $paramstwop);














                $sql = "delete from mobile_users where user_name = ? or id_no = ? ";
                $params = array($phone, $user_name);
                $stmt = sqlsrv_query($conn3, $sql, $params);
                $rows_affected = sqlsrv_rows_affected($stmt);
                if ($rows_affected === false) {
                    echo "no update.phone numbers dont exist in  sms database one";
                } elseif ($rows_affected == -1) {
                    // echo "No information available.<br />";
                    echo "an error occured";
                } elseif ($rows_affected >= 1) {


                    echo json_encode(array('missing' => 'phone number ' . $phone . ' being used by another student id: ' . $user_name . ' Name: ' . $fullname .
                        ' Student to register again,record removed after KYC VALIDATION: ' . $phone));
                }
            }
        }
    }


    public function updatesindextoidsloansnfmchunk()

    {



        $chunkSize = 1000; // Define the chunk size

        // Retrieve records from tvetprogress in chunks to save memory
        $examyear = '2022';
        DB::table('cre_pastapplicationstwo')
            ->where('IDNO', 'like',  $examyear . '%')
            ->limit(5)

            ->orderBy('id_pri', 'DESC')->chunk(10000, function ($records) use ($chunkSize) {
                $Institutiondata = [];
                $credata = [];
                $examyear = '2022';
                foreach ($records as $record) {
                    // Prepare data for update or insertion
                    $IDNO = $record->IDNO;
                    $indexax = substr($IDNO, 4);


                    //dd("hhhhhhhhhh");





                    $checkax = DB::connection('sqlsrv')->table('lmloans as a')
                        ->select([
                            DB::raw('MAX(e.ADMISSIONCATEGORY) as ADMISSIONCATEGORY'),
                            DB::raw('MAX(a.LOANPRODUCTCODE) as PRODUCTCODE'),
                            DB::raw('MAX(lc.COURSEDESCRIPTION) as COURSEDESCRIPTION'),
                            DB::raw('MAX(e.ADMISSIONYEAR + lc.COURSEDURATION) as COMPLETIONYEAR'),
                            DB::raw('MAX(e.LEVELOFSTUDY) as LEVELOFSTUDY'),
                            DB::raw('MAX(e.YEAROFSTUDY) + 1 as YEAROFSTUDY'),
                            DB::raw('MAX(e.ADMISSIONYEAR) as ADMISSIONYEAR'),
                            DB::raw('MAX(e.COURSECODE) as COURSECODE'),
                            DB::raw('MAX(b.ACCOUNTNUM) as ACCOUNTNUM'),
                            DB::raw("CONCAT(MAX(a.LOANSERIALNO), 'S24') as LOANSERIALNO"),
                            DB::raw('MAX(le.INSTITUTIONNAME) as INSTITUTIONNAME'),
                            DB::raw('MAX(y.INDEXNUMBER) as indexno'),
                            DB::raw('MAX(y.TOYEAR) as examsityr'),
                            'b.IDENTITYREFERENCENO as IDNO',
                            DB::raw('MAX(d.LASTNAME) as LASTNAME'),
                            DB::raw('MAX(d.FIRSTNAME) as FIRSTNAME'),
                            DB::raw('MAX(d.MIDDLENAME) as MIDDLENAME'),
                            DB::raw('MAX(e.INSTITUTIONCODE) as INSTITUTIONCODE'),
                            DB::raw('MAX(e.ADMISSIONNUMBER) as ADMISSIONNUMBER'),
                            DB::raw("'UG' as STUDENTGROUPING1"),
                            DB::raw("'UG' as STUDGROUPING2"),
                            DB::raw('MAX(nx.applications) as applications'),
                            DB::raw('MAX(lc.COURSEDURATION) as Courseduration'),
                            DB::raw('MAX(a.LOANPRODUCTCODE) as LOANPRODUCTCODE')
                        ])
                        ->leftJoin('LMIDENTIFICATIONDOCUMENTS as b', 'a.ACCOUNTNUM', '=', 'b.ACCOUNTNUM')
                        ->leftJoin('LMSTUDENTEDUCATIONBACKGROUND as y', 'b.ACCOUNTNUM', '=', 'y.ACCOUNTNUM')
                        ->leftJoin('CUSTTABLE as c', 'b.ACCOUNTNUM', '=', 'c.ACCOUNTNUM')
                        ->leftJoin('DIRPERSONNAME as d', 'c.PARTY', '=', 'd.PERSON')
                        ->leftJoin('LMSTUDENTINSTITUTION as e', 'a.LOANSERIALNO', '=', 'e.LOANSERIALNO')
                        ->leftJoin('LMINSTITUTION as le', 'e.INSTITUTIONCODE', '=', 'le.INSTITUTIONCODE')
                        ->leftJoin('LMCOURSES as lc', function ($join) {
                            $join->on('e.INSTITUTIONCODE', '=', 'lc.INSTITUTIONCODE')
                                ->on('e.COURSECODE', '=', 'lc.COURSECODE');
                        })
                        ->leftJoin('DIRPARTYTABLE as f', 'c.PARTY', '=', 'f.RECID')
                        ->leftJoin(DB::connection('sqlsrv')->raw('(select max(lm.RECID) as RECID, lm.ACCOUNTNUM as ACCOUNTNUM from lmloans lm where lm.ACADEMICYEAR in (\'2017/2016\',\'2018/2019\',\'2019/2020\',\'2020/2021\',\'2021/2022\',\'2022/2023\',\'2023/2024\') and lm.LOANREGISTERED != 0 and lm.LOANSTATUS in (\'4\',\'5\',\'6\',\'8\',\'9\',\'11\') group by lm.ACCOUNTNUM) lm'), function ($join) {
                            $join->on('lm.ACCOUNTNUM', '=', 'a.ACCOUNTNUM')
                                ->on('lm.RECID', '=', 'a.RECID');
                        })
                        ->leftJoin(DB::connection('sqlsrv')->raw('(select a.ACCOUNTNUM as ACCOUNTNUM, sum(count(*)) over (partition by a.accountnum order by a.accountnum) as applications from lmloans a where a.LOANSTATUS in (\'4\',\'5\',\'6\',\'8\',\'9\',\'11\') group by a.ACCOUNTNUM) nx'), 'lm.ACCOUNTNUM', '=', 'nx.ACCOUNTNUM')
                        ->where('lm.RECID', '!=', '')
                        ->where('y.EDUBACGINSTITUTIONTYPE', '2')
                        ->where('y.INDEXNUMBER', '=', $indexax)
                        ->where('y.TOYEAR', '=', $examyear)
                        ->groupBy('b.IDENTITYREFERENCENO')
                        ->get();


                    $checkaxdd = $checkax[0]->IDNO ?? null;

                    if (empty($checkaxdd)) {
                        //$source = 'mobile';
                        echo 'empty';
                        // dd($checkax);

                    } else {



                        $Institutiondata =   [
                            'ACADEMICYEAR' => '2025/2026' ?? null,
                            'ADMISSIONCATEGORY' => $checkax[0]->ADMISSIONCATEGORY ?? null,
                            'ADMISSIONNUMBER' => $checkax[0]->ADMISSIONNUMBER ?? null,
                            'ADMISSIONYEAR' => $checkax[0]->ADMISSIONYEAR ?? null,
                            'COURSECODE' => $checkax[0]->COURSECODE ?? null,
                            'INSTITUTIONBRANCHCODE' =>  null,
                            'INSTITUTIONCODE' => $checkax[0]->INSTITUTIONCODE ?? null,
                            'ACCOUNTNUM' => $checkax[0]->ACCOUNTNUM ?? null,
                            'Productcode' => $checkax[0]->LOANPRODUCTCODE ?? null,
                            'IDNO' => $checkax[0]->IDNO ?? null,
                            'InstitutionName' => $checkax[0]->INSTITUTIONNAME ?? null,
                            'CourseDescription' => $checkax[0]->COURSEDESCRIPTION ?? null,
                            'LOANSERIALNO' => $checkax[0]->LOANSERIALNO ?? null

                        ];

                        $credata =   [
                            'IDNO' => $checkax[0]->IDNO ?? null
                        ];
                    }
                }

                // Function to process updates in chunks
                $processUpdates = function ($Institutiondata) use ($chunkSize) {
                    foreach (array_chunk($Institutiondata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {
                            //dd($data);

                            DB::table('dminstitututions_2024')
                                ->where('ACCOUNTNUM', $data['ACCOUNTNUM'])
                                ->update($data);
                        }
                    }
                };

                $processUpdatestwo = function ($credata) use ($chunkSize) {
                    foreach (array_chunk($credata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {

                            DB::table('cre_pastapplicationstwo')
                                ->where('ADMISSIONO', $data['ADMISSIONNUMBER'])
                                ->where('STUDGROUPING2', $data['UG'])

                                ->update($data);
                        }
                    }
                };

                // Bulk update records in chunks
                if (!empty($Institutiondata)) {
                    $processUpdates($Institutiondata);
                }

                // Bulk update records in chunks
                if (!empty($credata)) {
                    $processUpdatestwo($credata);
                }


                echo 'Processed ' . count($records) . ' records';
            });
    }

    public function updatesindextoidsloansnfmchunkHEF()

    {


        // var_dump("jjjj");
        // die();

        // $requestData = [

        //     "index" => "02100005095",
        //     "year" => "2022",


        // ];


        // dd("jjjj");
        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        // ])
        //     ->timeout(30)  // Timeout in seconds
        //     ->post('https://portal.hef.co.ke/auth/mobiportal', $requestData);

        // dd($response);








        $chunkSize = 1000; // Define the chunk size

        // Retrieve records from tvetprogress in chunks to save memory
        $examyear = '2022';
        DB::table('ufbdata')
            ->where('IDNO', 'like',  $examyear . '%')
            //->limit(5)

            ->orderBy('id', 'DESC')->chunk(10000, function ($records) use ($chunkSize) {
                $Institutiondata = [];
                $credata = [];
                $examyear = '2022';
                foreach ($records as $record) {
                    // Prepare data for update or insertion
                    $IDNO = $record->IDNO;
                    $indexax = substr($IDNO, 4);


                    //dd("hhhhhhhhhh");





                    $checkax = DB::connection('sqlsrv')->table('lmloans as a')
                        ->select([
                            DB::raw('MAX(e.ADMISSIONCATEGORY) as ADMISSIONCATEGORY'),
                            DB::raw('MAX(a.LOANPRODUCTCODE) as PRODUCTCODE'),
                            DB::raw('MAX(lc.COURSEDESCRIPTION) as COURSEDESCRIPTION'),
                            DB::raw('MAX(e.ADMISSIONYEAR + lc.COURSEDURATION) as COMPLETIONYEAR'),
                            DB::raw('MAX(e.LEVELOFSTUDY) as LEVELOFSTUDY'),
                            DB::raw('MAX(e.YEAROFSTUDY) + 1 as YEAROFSTUDY'),
                            DB::raw('MAX(e.ADMISSIONYEAR) as ADMISSIONYEAR'),
                            DB::raw('MAX(e.COURSECODE) as COURSECODE'),
                            DB::raw('MAX(b.ACCOUNTNUM) as ACCOUNTNUM'),
                            DB::raw("CONCAT(MAX(a.LOANSERIALNO), 'S24') as LOANSERIALNO"),
                            DB::raw('MAX(le.INSTITUTIONNAME) as INSTITUTIONNAME'),
                            DB::raw('MAX(y.INDEXNUMBER) as indexno'),
                            DB::raw('MAX(y.TOYEAR) as examsityr'),
                            'b.IDENTITYREFERENCENO as IDNO',
                            DB::raw('MAX(d.LASTNAME) as LASTNAME'),
                            DB::raw('MAX(d.FIRSTNAME) as FIRSTNAME'),
                            DB::raw('MAX(d.MIDDLENAME) as MIDDLENAME'),
                            DB::raw('MAX(e.INSTITUTIONCODE) as INSTITUTIONCODE'),
                            DB::raw('MAX(e.ADMISSIONNUMBER) as ADMISSIONNUMBER'),
                            DB::raw("'UG' as STUDENTGROUPING1"),
                            DB::raw("'UG' as STUDGROUPING2"),
                            DB::raw('MAX(nx.applications) as applications'),
                            DB::raw('MAX(lc.COURSEDURATION) as Courseduration'),
                            DB::raw('MAX(a.LOANPRODUCTCODE) as LOANPRODUCTCODE')
                        ])
                        ->leftJoin('LMIDENTIFICATIONDOCUMENTS as b', 'a.ACCOUNTNUM', '=', 'b.ACCOUNTNUM')
                        ->leftJoin('LMSTUDENTEDUCATIONBACKGROUND as y', 'b.ACCOUNTNUM', '=', 'y.ACCOUNTNUM')
                        ->leftJoin('CUSTTABLE as c', 'b.ACCOUNTNUM', '=', 'c.ACCOUNTNUM')
                        ->leftJoin('DIRPERSONNAME as d', 'c.PARTY', '=', 'd.PERSON')
                        ->leftJoin('LMSTUDENTINSTITUTION as e', 'a.LOANSERIALNO', '=', 'e.LOANSERIALNO')
                        ->leftJoin('LMINSTITUTION as le', 'e.INSTITUTIONCODE', '=', 'le.INSTITUTIONCODE')
                        ->leftJoin('LMCOURSES as lc', function ($join) {
                            $join->on('e.INSTITUTIONCODE', '=', 'lc.INSTITUTIONCODE')
                                ->on('e.COURSECODE', '=', 'lc.COURSECODE');
                        })
                        ->leftJoin('DIRPARTYTABLE as f', 'c.PARTY', '=', 'f.RECID')
                        ->leftJoin(DB::connection('sqlsrv')->raw('(select max(lm.RECID) as RECID, lm.ACCOUNTNUM as ACCOUNTNUM from lmloans lm where lm.ACADEMICYEAR in (\'2017/2016\',\'2018/2019\',\'2019/2020\',\'2020/2021\',\'2021/2022\',\'2022/2023\',\'2023/2024\') and lm.LOANREGISTERED != 0 and lm.LOANSTATUS in (\'4\',\'5\',\'6\',\'8\',\'9\',\'11\') group by lm.ACCOUNTNUM) lm'), function ($join) {
                            $join->on('lm.ACCOUNTNUM', '=', 'a.ACCOUNTNUM')
                                ->on('lm.RECID', '=', 'a.RECID');
                        })
                        ->leftJoin(DB::connection('sqlsrv')->raw('(select a.ACCOUNTNUM as ACCOUNTNUM, sum(count(*)) over (partition by a.accountnum order by a.accountnum) as applications from lmloans a where a.LOANSTATUS in (\'4\',\'5\',\'6\',\'8\',\'9\',\'11\') group by a.ACCOUNTNUM) nx'), 'lm.ACCOUNTNUM', '=', 'nx.ACCOUNTNUM')
                        ->where('lm.RECID', '!=', '')
                        ->where('y.INDEXNUMBER', '=', $indexax)
                        ->where('y.EDUBACGINSTITUTIONTYPE', '2')
                        ->where('y.TOYEAR', '=', $examyear)
                        ->groupBy('b.IDENTITYREFERENCENO')
                        ->get();


                    $checkaxdd = $checkax[0]->IDNO ?? null;

                    if (empty($checkaxdd)) {
                        //$source = 'mobile';
                        echo 'empty';
                        // dd($checkax);

                    } else {



                        $Institutiondata =   [
                            'ACADEMICYEAR' => '2025/2026' ?? null,
                            'ADMISSIONCATEGORY' => $checkax[0]->ADMISSIONCATEGORY ?? null,
                            'ADMISSIONNUMBER' => $checkax[0]->ADMISSIONNUMBER ?? null,
                            'ADMISSIONYEAR' => $checkax[0]->ADMISSIONYEAR ?? null,
                            'COURSECODE' => $checkax[0]->COURSECODE ?? null,
                            'INSTITUTIONBRANCHCODE' =>  null,
                            'INSTITUTIONCODE' => $checkax[0]->INSTITUTIONCODE ?? null,
                            'ACCOUNTNUM' => $checkax[0]->ACCOUNTNUM ?? null,
                            'Productcode' => $checkax[0]->LOANPRODUCTCODE ?? null,
                            'IDNO' => $checkax[0]->IDNO ?? null,
                            'InstitutionName' => $checkax[0]->INSTITUTIONNAME ?? null,
                            'CourseDescription' => $checkax[0]->COURSEDESCRIPTION ?? null,
                            'LOANSERIALNO' => $checkax[0]->LOANSERIALNO ?? null

                        ];

                        $credata =   [
                            'IDNO' => $checkax[0]->IDNO ?? null
                        ];
                    }
                }

                // Function to process updates in chunks
                $processUpdates = function ($Institutiondata) use ($chunkSize) {
                    foreach (array_chunk($Institutiondata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {
                            //dd($data);

                            DB::table('dminstitututions_2024')
                                ->where('ACCOUNTNUM', $data['ACCOUNTNUM'])
                                ->update($data);
                        }
                    }
                };

                $processUpdatestwo = function ($credata) use ($chunkSize) {
                    foreach (array_chunk($credata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {

                            DB::table('cre_pastapplicationstwo')
                                ->where('ADMISSIONO', $data['ADMISSIONNUMBER'])
                                ->where('STUDGROUPING2', $data['UG'])

                                ->update($data);
                        }
                    }
                };

                // Bulk update records in chunks
                if (!empty($Institutiondata)) {
                    $processUpdates($Institutiondata);
                }

                // Bulk update records in chunks
                if (!empty($credata)) {
                    $processUpdatestwo($credata);
                }


                echo 'Processed ' . count($records) . ' records';
            });
    }

    public function updatescholsnfmchunk()
    {
        $chunkSize = 1000; // Define the chunk size

        // Retrieve records from schorlldata in chunks to save memory
        DB::table('schorlldata')
            ->limit(5)
            ->orderBy('id', 'DESC')
            ->chunk(1000, function ($records) use ($chunkSize) {
                $Institutiondata = [];
                $credata = [];
                foreach ($records as $record) {
                    // Prepare data for update or insertion
                    $IDNO = $record->IDNO;

                    $Institutiondata = [
                        'ACADEMICYEAR' => '2025/2026' ?? null,
                        'ADMISSIONCATEGORY' => $record->ADMISSIONCATEGORY ?? null,
                        'ADMISSIONNUMBER' => $record->ADMISSIONNUMBER ?? null,
                        'ADMISSIONYEAR' => $record->ADMISSIONYEAR ?? null,
                        'COURSECODE' => $record->COURSECODE ?? null,
                        'INSTITUTIONBRANCHCODE' => null,
                        'INSTITUTIONCODE' => $record->INSTITUTIONCODE ?? null,
                        'ACCOUNTNUM' => $record->ACCOUNTNUM ?? null,
                        'Productcode' => $record->LOANPRODUCTCODE ?? null,
                        'IDNO' => $record->IDNO ?? null,
                        'InstitutionName' => $record->INSTITUTIONNAME ?? null,
                        'CourseDescription' => $record->COURSEDESCRIPTION ?? null,
                        'LOANSERIALNO' => $record->LOANSERIALNO ?? null
                    ];


                    if ($record->LOANPRODUCTCODE == '5637144616') {
                        $STUDGROUPING2 = 'UG';
                    } elseif ($record->LOANPRODUCTCODE == '5637144605') {
                        $STUDGROUPING2 = 'TVET';
                    } else {
                        $STUDGROUPING2 = 'UG'; // Default value if no condition matches
                    }



                    $credata = [
                        'IDNO' => $record->IDNO ?? null,
                        'ADMISSIONO' => $record->ADMISSIONNUMBER,
                        'EXAMYR'  => $record->examsityr,
                        'STUDGROUPING2' => $STUDGROUPING2,
                        'ACADEMIC_YEAR' => '2025/2026',
                        'productcode' => $record->LOANPRODUCTCODE ?? null,
                        'product_id' => '293'
                    ];
                }

                // Function to process updates in chunks
                $processUpdates = function ($Institutiondata) use ($chunkSize) {
                    foreach (array_chunk($Institutiondata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {
                            DB::table('dminstitututions_2024')->updateOrInsert(
                                ['ACCOUNTNUM' => $data['ACCOUNTNUM']], // Match the record based on ACCOUNTNUM
                                $data // Update or insert the data
                            );
                        }
                    }
                };

                $processUpdatestwo = function ($credata) use ($chunkSize) {
                    foreach (array_chunk($credata, $chunkSize) as $chunk) {
                        foreach ($chunk as $data) {
                            DB::table('cre_pastapplicationstwo')->updateOrInsert(
                                ['ADMISSIONO' => $data['ADMISSIONNUMBER']], // Match the record based on ACCOUNTNUM
                                $data // Update or insert the data
                            );
                        }
                    }
                };

                // Bulk update records in chunks
                if (!empty($Institutiondata)) {
                    $processUpdates($Institutiondata);
                }

                // Bulk update records in chunks
                if (!empty($credata)) {
                    $processUpdatestwo($credata);
                }

                echo 'Processed ' . count($records) . ' records';
            }); // Closing the chunk function
    } // Closing the updatescholsnfmchunk function


    public function updatescholsnfmchunkUPSERT()
    {
        $chunkSize = 5; // Define the chunk size

        DB::table('schorlldata')
            // ->limit(5)
            ->orderBy('id', 'DESC')
            ->chunk($chunkSize, function ($records) {
                $institutionData = [];
                $creData = [];

                foreach ($records as $record) {
                    // foreach ($records->take(5) as $record) {

                    // Prepare data for update or insertion
                    $institutionData[] = [
                        'ACADEMICYEAR' => '2025/2026',
                        'ADMISSIONCATEGORY' => $record->ADMISSIONCATEGORY ?? 0,
                        'ADMISSIONNUMBER' => $record->ADMISSIONNUMBER ?? null,
                        'ADMISSIONYEAR' => $record->ADMISSIONYEAR ?? 0,
                        'COURSECODE' => $record->COURSECODE ?? null,
                        'INSTITUTIONBRANCHCODE' => 'null',
                        'INSTITUTIONCODE' => $record->INSTITUTIONCODE ?? null,
                        'ACCOUNTNUM' => $record->ACCOUNTNUM ?? null,
                        'Productcode' => $record->LOANPRODUCTCODE ?? 0,
                        'IDNO' => $record->IDNO ?? null,
                        'InstitutionName' => $record->INSTITUTIONNAME ?? null,
                        'CourseDescription' => $record->COURSEDESCRIPTION ?? null,
                        'LOANSERIALNO' => $record->LOANSERIALNO ?? null
                    ];

                    $STUDGROUPING2 = ($record->LOANPRODUCTCODE == '5637144616') ? 'UG' : (($record->LOANPRODUCTCODE == '5637144605') ? 'TVET' : 'UG');

                    $creData[] = [
                        'IDNO' => $record->IDNO ?? 0,
                        'ADMISSIONO' => $record->ADMISSIONNUMBER,
                        'EXAMYR'  => $record->examsityr,
                        'STUDGROUPING2' => $STUDGROUPING2,
                        'ACADEMIC_YEAR' => '2025/2026',
                        'qualifiedscholarship' => '1',
                        'productcode' => $record->LOANPRODUCTCODE ?? null,
                        'product_id' => '293'
                    ];
                }

                // Bulk upsert for institutionData
                DB::table('dminstitututions_2024')->upsert($institutionData, ['ACCOUNTNUM'], [
                    'ACADEMICYEAR',
                    'ADMISSIONCATEGORY',
                    'ADMISSIONNUMBER',
                    'ADMISSIONYEAR',
                    'COURSECODE',
                    'INSTITUTIONBRANCHCODE',
                    'INSTITUTIONCODE',
                    'Productcode',
                    'IDNO',
                    'InstitutionName',
                    'CourseDescription',
                    'LOANSERIALNO'
                ]);

                // Bulk upsert for creData
                DB::table('cre_pastapplicationstwo')->upsert($creData, ['ADMISSIONO'], [
                    'IDNO',
                    'EXAMYR',
                    'STUDGROUPING2',
                    'ACADEMIC_YEAR',
                    'productcode',
                    'product_id'
                ]);
                echo "Inserted/Updated 5 records into dminstitututions_2024 and cre_pastapplicationstwo.\n";
            });
    }








    public function updatescholarshipnfmchunk()
    {
        $chunkSize = 1000; // Define the chunk size

        // Retrieve records from tvetprogress in chunks to save memory
        DB::table('tvetprogress')->orderBy('id_pri', 'DESC')->chunk(10000, function ($records) use ($chunkSize) {
            $updateData = [];
            $insertData = [];

            foreach ($records as $record) {
                // Prepare data for update or insertion
                $data = [
                    'IDNO' => $record->IDNO,
                    'ADMISSIONO' => $record->ADMISSIONO,
                    'EXAMYR' => $record->EXAMYR,
                    'qualifiedloanmodel' => strval($record->qualifiedloanmodel),
                    'qualifiedscholarship' => strval($record->qualifiedscholarship),
                    'qualifiedboth' => strval($record->qualifiedboth),
                    'STUDGROUPING2' => $record->STUDGROUPING2,
                    'product_id' => $record->product_id,
                    'productcode' => $record->productcode,
                    'ACADEMIC_YEAR' => '2025/2026'
                ];

                // Check if IDNO exists in cre_pastapplicationstwo
                $exists = DB::table('cre_pastapplicationstwo')
                    ->where('IDNO', $record->IDNO)
                    ->exists();

                if ($exists) {
                    $updateData[] = $data;
                } else {
                    // Prepare insert data with additional fields
                    $insertData[] = $data;
                }
            }

            // Function to process updates in chunks
            $processUpdates = function ($updateData) use ($chunkSize) {
                foreach (array_chunk($updateData, $chunkSize) as $chunk) {
                    foreach ($chunk as $data) {
                        DB::table('cre_pastapplicationstwo')
                            ->where('IDNO', $data['IDNO'])
                            ->update($data);
                    }
                }
            };

            // Function to process inserts in chunks
            $processInserts = function ($insertData) use ($chunkSize) {
                foreach (array_chunk($insertData, $chunkSize) as $chunk) {
                    DB::table('cre_pastapplicationstwo')->insert($chunk);
                }
            };

            // Bulk update records in chunks
            if (!empty($updateData)) {
                $processUpdates($updateData);
            }

            // Bulk insert new records in chunks
            if (!empty($insertData)) {
                $processInserts($insertData);
            }

            echo 'Processed ' . count($records) . ' records';
        });
    }






    public function generatescholarshipinstchunk()
    {
        // Retrieve records from nfm_applications_2023 in chunks to save memory
        DB::table('nfm_applications_2023')

            //  ->where('id_no', '209828062')
            ->orderBy('id', 'DESC')

            ->chunk(10000, function ($records) {
                $updateData = [];
                $insertData = [];

                foreach ($records as $record) {
                    // Prepare data for update or insertion


                    $data = [
                        'ACADEMICYEAR' =>  '2025/2026',
                        'ADMISSIONCATEGORY' =>  $record->AdmissionCategory ?? null,
                        'ADMISSIONNUMBER' =>  $record->AdmissionNumber ?? null,
                        'ADMISSIONYEAR' =>  $record->AdmiYear ?? null,
                        'COURSECODE' =>  $record->CourseCode ?? null,
                        'INSTITUTIONBRANCHCODE' =>  $record->InstitutionBranchCode ?? null,
                        'INSTITUTIONCODE' =>  $record->InstitutionCode ?? null,
                        'ACCOUNTNUM' =>  $record->accountnum ?? null,
                        'Productcode' => '5637144616',
                        'IDNO' =>  $record->id_no ?? null,
                        'InstitutionName' =>  '' ?? null,
                        'CourseDescription' =>  '' ?? null,
                        'LOANSERIALNO' => '' ?? null

                    ];


                    // Check if REGNO exists in cre_pastapplicationstwo_kiprono
                    $exists = DB::table('dminstitututions_2024')
                        ->where('IDNO', $record->id_no)
                        ->exists();

                    if ($exists) {
                        // Prepare update data
                        // $data['productcode'] = '5637144616';

                        //$updateData[] = $data;
                    } else {


                        $insertData[] = $data;
                    }
                }

                // Bulk update records
                // foreach ($updateData as $data) {
                //     DB::table('cre_pastapplicationstwo_kiprono')
                //         ->where('IDNO', $data['IDNO'])
                //         ->update($data);
                // }

                // Bulk insert new records
                if (!empty($insertData)) {
                    DB::table('dminstitututions_2024')->insert($insertData);
                }

                echo 'processed ' . count($records) . ' records';
            });
    }



    public function updatescholarshipnfminst()
    {
        // Retrieve records from nfm_applications_2023 in chunks to save memory
        $records = DB::table('nfm_applications_2023')

            // ->where('id_no', '209828062')
            ->orderBy('id', 'DESC')
            ->get();


        foreach ($records as $record) {
            // Prepare data for update or insertion


            $data = [
                'ACADEMICYEAR' =>  '2025/2026',
                'ADMISSIONCATEGORY' =>  $record->AdmissionCategory ?? null,
                'ADMISSIONNUMBER' =>  $record->AdmissionNumber ?? null,
                'ADMISSIONYEAR' =>  $record->AdmiYear ?? null,
                'COURSECODE' =>  $record->CourseCode ?? null,
                'INSTITUTIONBRANCHCODE' =>  $record->InstitutionBranchCode ?? null,
                'INSTITUTIONCODE' =>  $record->InstitutionCode ?? null,
                'ACCOUNTNUM' =>  $record->accountnum ?? null,
                'Productcode' => '5637144616',
                'IDNO' =>  $record->id_no ?? null,
                'InstitutionName' =>  '' ?? null,
                'CourseDescription' =>  '' ?? null,
                'LOANSERIALNO' => '' ?? null

            ];


            // Check if REGNO exists in cre_pastapplicationstwo_kiprono
            $exists = DB::table('dminstitututions_2024')
                ->where('IDNO', $record->id_no)
                ->exists();

            if ($exists) {
                // Prepare update data
                // $data['productcode'] = '5637144616';

                //$updateData[] = $data;
            } else {


                $insertData[] = $data;
            }
        }

        // Bulk update records
        // foreach ($updateData as $data) {
        //     DB::table('cre_pastapplicationstwo_kiprono')
        //         ->where('IDNO', $data['IDNO'])
        //         ->update($data);
        // }

        // Bulk insert new records
        if (!empty($insertData)) {
            DB::table('dminstitututions_2024')->insert($insertData);
        }

        echo 'processed ' . count($records) . ' records';
    }
    public function generatescholarshipnfmportal()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        $records = DB::table('scholarshipdata2024')
            ->whereNotNull('idone')
            ->where('idtwo', 'not like', '2022%')
            ->whereRaw('LENGTH(idtwo) >= 8')


            ->get();

        // Loop through each record
        foreach ($records as $record) {
            // Prepare data for insertion or update
            $data = [


                'qualifiedscholarship' => '1',
                'product_id' => '163',


            ];

            // Check if REGNO exists in cre_pastapplicationstwo_bck
            $exists = DB::table('cre_pastapplicationstwo')
                ->where('IDNO', $record->idtwo)
                ->exists();

            if ($exists) {

                // $data['qualifiedboth'] = '1';
                // $data['product_id'] ='73';


                // Update record if exists
                DB::table('cre_pastapplicationstwo')
                    ->where('IDNO', $record->idtwo)
                    ->update($data);

                echo 'updated';
            } else {






                $data['IDNO'] = $record->idtwo;
                $data['ADMISSIONO'] = $record->REGNO;
                $data['EXAMYR'] = '2022';
                $data['STUDGROUPING2'] = 'UG';
                $data['ACADEMIC_YEAR'] = '2025/2026';
                $data['productcode'] = '5637144616';
                $data['product_id'] = '163';




                // Insert new record if not exists
                DB::table('cre_pastapplicationstwo')->insert($data);
                echo 'inserted';
            }
        }
    }



    public function generatescholarshipnfm()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        $records = DB::table('nfm_applications_2023')->get();

        // Loop through each record
        foreach ($records as $record) {
            // Prepare data for insertion or update
            $data = [


                'qualifiedscholarship' => '1',
                'product_id' => '63',


            ];

            // Check if REGNO exists in cre_pastapplicationstwo_bck
            $exists = DB::table('cre_pastapplicationstwo_kiprono')
                ->where('IDNO', $record->id_no)
                ->exists();

            if ($exists) {

                // $data['qualifiedboth'] = '1';
                // $data['product_id'] ='73';


                // Update record if exists
                DB::table('cre_pastapplicationstwo_kiprono')
                    ->where('IDNO', $record->id_no)
                    ->update($data);

                echo 'updated';
            } else {






                $data['IDNO'] = $record->id_no;
                $data['ADMISSIONO'] = $record->AdmissionNumber;
                $data['EXAMYR'] = $record->AdmiYear;
                $data['STUDGROUPING2'] = 'UG';
                $data['ACADEMIC_YEAR'] = '2025/2026';
                $data['productcode'] = '5637144616';
                $data['product_id'] = '93';




                // Insert new record if not exists
                DB::table('cre_pastapplicationstwo_kiprono')->insert($data);
                echo 'inserted';
            }
        }
    }





    public function generateschbatch()
    { {

            $records = DB::table('scholarshipdata2024')
                ->where('idone', 'like', '2022%')


                ->get();

            $dataToUpdate = [];
            $dataToInsert = [];

            foreach ($records as $record) {
                $exists = DB::table('cre_pastapplicationstwo')
                    ->where('ADMISSIONO', $record->REGNO)


                    ->exists();
                $data = [
                    'qualifiedscholarship' => '1',

                ];
                if ($exists) {


                    $dataToUpdate[] = $data;
                } else {

                    $dataToInsert[] = $data;
                }
            }

            // Perform batch update and insert
            if (!empty($dataToUpdate)) {
                foreach ($dataToUpdate as $data) {
                    DB::table('cre_pastapplicationstwo')
                        ->where('ADMISSIONO', $data['ADMISSIONO'])
                        ->update($data);
                }
                echo 'updated';
            }

            if (!empty($dataToInsert)) {
                DB::table('cre_pastapplicationstwo')->insert($dataToInsert);
                echo 'inserted';
            }
        }
    }


    public function generatesch()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        $records = DB::table('scholarshipdata2024')->get();

        // Loop through each record
        foreach ($records as $record) {
            // Prepare data for insertion or update
            $data = [


                'qualifiedscholarship' => '1',
                'product_id' => '73',


            ];

            // Check if REGNO exists in cre_pastapplicationstwo_bck
            $exists = DB::table('cre_pastapplicationstwo_bck')
                ->where('ADMISSIONO', $record->REGNO)
                ->exists();

            if ($exists) {

                // $data['qualifiedboth'] = '1';
                // $data['product_id'] ='73';


                // Update record if exists
                DB::table('cre_pastapplicationstwo_bck')
                    ->where('ADMISSIONO', $record->REGNO)
                    ->update($data);

                echo 'updated';
            } else {






                $data['IDNO'] = $record->INDEXNO;
                $data['ADMISSIONO'] = $record->REGNO;
                $data['EXAMYR'] = '2022';
                $data['STUDGROUPING2'] = 'UG';
                $data['ACADEMIC_YEAR'] = '2025/2026';
                $data['productcode'] = '5637144616';
                $data['product_id'] = '73';




                // Insert new record if not exists
                DB::table('cre_pastapplicationstwo_bck')->insert($data);
                echo 'inserted';
            }
        }
    }


    public function generateschunked()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        DB::table('scholarshipdata2024')
            //->whereNotNull('idone')
            //->where('idone', 'not like', '2022%')
            ->whereRaw('LENGTH(idone) >= 8 AND LENGTH(idone) <= 9')
            ->orderBy('idone', 'DESC')
            ->chunk(1000, function ($records) {
                // Loop through each chunk of records
                foreach ($records as $record) {
                    // Prepare data for insertion or update
                    $data = [
                        'qualifiedscholarship' => '1',
                        'product_id' => '123',
                    ];

                    // Check if REGNO exists in cre_pastapplicationstwo
                    $exists = DB::table('cre_pastapplicationstwo')
                        ->where('IDNO', $record->idone)
                        ->exists();

                    if ($exists) {
                        // Update record if exists
                        DB::table('cre_pastapplicationstwo')
                            ->where('IDNO', $record->idone)
                            ->update($data);

                        echo 'updated';
                    } else {
                        // Prepare additional data for insertion
                        $data['IDNO'] = $record->idone;
                        $data['ADMISSIONO'] = $record->REGNO;
                        $data['EXAMYR'] = '2022';
                        $data['STUDGROUPING2'] = 'UG';
                        $data['ACADEMIC_YEAR'] = '2025/2026';
                        $data['productcode'] = '5637144616';
                        $data['product_id'] = '123';

                        // Insert new record if not exists
                        DB::table('cre_pastapplicationstwo')->insert($data);
                        echo 'inserted';
                    }
                }
            });
    }


    public function generateschunkedregids()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        DB::table('scholarshipdata2024')
            ->whereNotNull('REGNO')
            //->where('idone', 'not like', '2022%')
            //->whereRaw('LENGTH(idone) >= 8 AND LENGTH(idone) <= 9')
            ->orderBy('IDP', 'DESC')
            ->chunk(1000, function ($records) {
                // Loop through each chunk of records
                foreach ($records as $record) {
                    // Prepare data for insertion or update
                    $data = [
                        'qualifiedscholarship' => '1',
                        'product_id' => '113',
                    ];

                    // Check if REGNO exists in cre_pastapplicationstwo
                    $exists = DB::table('cre_pastapplicationstwo')
                        ->where('ADMISSIONO', $record->REGNO)
                        ->exists();

                    if ($exists) {
                        // Update record if exists
                        DB::table('cre_pastapplicationstwo')
                            ->where('ADMISSIONO', $record->REGNO)
                            ->update($data);

                        echo 'updated';

                        // $existsinst = DB::table('dminstitututions_2024')
                        //     ->where('ADMISSIONNUMBER', $record->REGNO)
                        //     ->whereRaw('LENGTH(IDNO) < 9')

                        //     ->exists();
                        // if ($existsinst) {

                        //     $updated = DB::table('dminstitututions_2024')
                        //         ->where('ADMISSIONNUMBER', $record->REGNO)
                        //         ->whereRaw('LENGTH(IDNO) < 9')
                        //         ->update(['IDNO' => $record->idtwo]);

                        //     if ($updated) {
                        //         echo 'IDNO updated';
                        //     } else {
                        //         echo 'No matching record found or update not needed';
                        //     }
                        // }
                    }
                }
            });
    }

    public function generateschunkedreg()
    {

        // Retrieve records from scholarshipdata2024 ordered by idone DESC
        DB::table('scholarshipdata2024')
            ->whereNotNull('REGNO')
            //->where('idone', 'not like', '2022%')
            //->whereRaw('LENGTH(idone) >= 8 AND LENGTH(idone) <= 9')
            ->orderBy('IDP', 'DESC')
            ->chunk(1000, function ($records) {
                // Loop through each chunk of records
                foreach ($records as $record) {
                    // Prepare data for insertion or update
                    $data = [
                        'qualifiedscholarship' => '1',
                        'product_id' => '113',
                    ];

                    // Check if REGNO exists in cre_pastapplicationstwo
                    $exists = DB::table('cre_pastapplicationstwo')
                        ->where('ADMISSIONO', $record->REGNO)
                        ->exists();

                    if ($exists) {
                        // Update record if exists
                        DB::table('cre_pastapplicationstwo')
                            ->where('ADMISSIONO', $record->REGNO)
                            ->update($data);

                        echo 'updated';

                        // $existsinst = DB::table('dminstitututions_2024')
                        //     ->where('ADMISSIONNUMBER', $record->REGNO)
                        //     ->whereRaw('LENGTH(IDNO) < 9')

                        //     ->exists();
                        // if ($existsinst) {

                        //     $updated = DB::table('dminstitututions_2024')
                        //         ->where('ADMISSIONNUMBER', $record->REGNO)
                        //         ->whereRaw('LENGTH(IDNO) < 9')
                        //         ->update(['IDNO' => $record->idtwo]);

                        //     if ($updated) {
                        //         echo 'IDNO updated';
                        //     } else {
                        //         echo 'No matching record found or update not needed';
                        //     }
                        // }
                    }
                }
            });
    }


    public function fetchaxuser(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;


        //dd($request);
        $idnumberax = $request->idnumberax;
        $action = 'fetchprofileax';
        $arr = array('idno' => $idnumberax);
        $result = $apiController->mobiapis($action, $arr);

        // dd($result);

        if (!is_array($result)) {
            $result = [$result];
        }
        //dd($result);
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $idnumber = $result['IDNO'];
        $gender = $result['GENDER'];


        $accountnum = $result['ACCOUNTNUM'];
        $passharsh = $this->hash($idnumber); // Assuming you are hashing the password
        $fullname = str_replace("'", '', $result['NAME']);
        $phone = $result['PHONE'];
        $lastname = str_replace("'", '', $result['LASTNAME']);
        $middlename = str_replace("'", '', $result['MIDDLENAME']);
        $firstname = str_replace("'", '', $result['LASTNAME']);
        $email = $result['EMAIL'];
        $dob = $result['FULLBIRTHDATE'];

        $regdate = $date_now; // Assuming the registration date is now


        $data = [
            'id_no' => $idnumber,
            'full_name' => $fullname,
            'gender' => $gender,
            'dob' => $dob,
            'cell_phone' => $phone,
            'last_name' => $lastname,
            'mid_name' => $middlename,
            'first_name' => $firstname,
            'email_add' => $email,
            'cell_verified' => '1',
            'updated_by' => 'updateaxuser',
        ];

        // $added = DB::table('tbl_users_nfm')->updateOrInsert(
        //     ['id_no' => $idnumber],
        //     $data
        // );

        $existingUser = DB::table('tbl_users_nfm')
            ->where('id_no', $idnumber)
            ->orWhere('cell_phone', $data['cell_phone'])
            ->first();

        if ($existingUser) {
            // Update the existing record
            $added = DB::table('tbl_users_nfm')
                ->where('id_no', $idnumber)
                ->orWhere('cell_phone', $data['cell_phone'])
                ->update($data);
        } else {
            // Insert new record
            $added = DB::table('tbl_users_nfm')->insert($data);
        }



        return response()->json($result);
    }








    public function loanstatus()
    {


        // Cache::forget('cached_loans');

        $cached_loans = Cache::remember('cached_loans', 60, function () {
            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');


            $data = DB::select("
            select
                a.id,
                a.name,
                a.type,
                a.value,
                a.idcre,
                a.productid,
                a.productcode,
                a.studentgrouping,
                a.academicyear,
                b.count,
                b.mobile,
                b.ussd,
                b.miniapp,
                b.ios,
                a.closedate,
                a.category
            from ussd_products_test a
             left join ussd_products_count b  on a.productid = b.productid

            where a.status = '1' and a.closedate >= ?
             ", [$date_now]);


            // Convert the result to an array
            //  $data = json_decode(json_encode($data), true);
            return collect($data)->toArray();
        });



        if (request()->ajax()) {
            return datatables()->of($cached_loans)

                ->make(true);
        }




        //  return view('pages.mobilesupport');
    }






    public function dashboard()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.dashboard', compact('cached_closed_loans'));
    }


    public function nfmsupport()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.nfmsupport', compact('cached_closed_loans'));
    }
    public function adminsupport()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.adminsupport', compact('cached_closed_loans'));
    }
    public function storerole(Request $request)
    {
        $id = auth()->user()->id;

        $user = User::find($id);
        $userpermissions = $user->getPermissionNames();


        $approvedpermission = collect($userpermissions);

        if ($approvedpermission->contains('roleassigner') || $approvedpermission->contains('administrator')) {

            $data  = $request->id[0];
            $id = preg_replace("/[^0-9]/", "", $data);
            $words = preg_replace('/\d/', '', json_encode($request->all()));
            $updatedrole = json_decode($words);

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Disable SQL safe updates
                DB::statement('SET SQL_SAFE_UPDATES = 0');

                // Sync the roles for the user
                $user = User::whereId($id)->firstOrFail();
                $user->syncRoles($updatedrole->id);

                // Prepare the menuroles value
                $trimmedstring = preg_replace('/[^a-zA-Z,"{}:]/', '', json_encode($updatedrole->id));

                // Update the menuroles for the user
                $user->update(['menuroles' => $trimmedstring]);

                // Enable SQL safe updates back if needed
                DB::statement('SET SQL_SAFE_UPDATES = 1');

                // Commit the transaction
                DB::commit();

                return  response()->json($approvedpermission);
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();

                return response()->json('Failed to update user roles and menuroles.');
            }
        } else {
            return  response()->json("insufficient rights");
        }
    }
    public function storepermission(Request $request)
    {

        $id = auth()->user()->id;

        $user = User::find($id);
        $userpermissions = $user->getPermissionNames();


        $approvedpermission = collect($userpermissions);

        //dd($approvedpermission);

        if ($approvedpermission->contains('roleassigner') || $approvedpermission->contains('administrator')) {

            $data  = $request->id[0];
            $id = preg_replace("/[^0-9]/", "", $data);
            $words = preg_replace('/\d/', '', json_encode($request->all()));
            $updatedpermission = json_decode($words);

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Disable SQL safe updates
                DB::statement('SET SQL_SAFE_UPDATES = 0');

                // Sync the roles for the user
                $user = User::whereId($id)->firstOrFail();
                $user->syncPermissions($updatedpermission->id);

                User::whereId($id)->firstOrFail()->syncPermissions($updatedpermission->id);

                // Enable SQL safe updates back if needed
                DB::statement('SET SQL_SAFE_UPDATES = 1');

                // Commit the transaction
                DB::commit();
                $text = '';
                foreach ($updatedpermission as $value) {
                    if (is_array($value)) {
                        // Handle nested arrays if needed
                        $text .= implode(', ', $value) . ' ';
                    } else {
                        $text .= $value . ' ';
                    }
                }
                // dd ($text)   ;      
                return  response()->json($text);
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();

                return response()->json($e . 'Failed to update user permission.');
            }
        } else {
            return  response()->json("insufficient rights");
        }
    }
    public function ussdsupport()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.ussdsupport', compact('cached_closed_loans'));
    }




    public function surepaysupport()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.surepaysupport', compact('cached_closed_loans'));
    }
    public function mobilesupport()
    {


        $cached_closed_loans = Cache::remember('cached_closed_loans', 1, function () {

            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
            $date_now = now(); // Assuming $date_now is set to the current date and time

            // Fetch data from the database
            $academic_year = '2025/2026';

            $data = DB::select("SELECT * FROM ussd_products_test WHERE closedate < ? AND academicyear = ?", [$date_now, $academic_year]);
            $access_level = DB::select("SELECT * FROM studylevel");
            $access_level = DB::select("SELECT * FROM studylevel");
            $exam_yr = DB::select("SELECT * FROM exam_yr");
            $subsequentloansall = DB::select("SELECT * FROM ussd_products_test WHERE  academicyear = ?", [$academic_year]);


            // Create collections from the fetched data
            $dataCollection = collect($data);
            $accessLevelCollection = collect($access_level);
            $examYrCollection = collect($exam_yr);
            $subsequentloansallCollection = collect($subsequentloansall);


            // Merge the additional data into the main collection
            $mergedCollection = $dataCollection->merge(['subsequentloansall' => $subsequentloansallCollection, 'openloans' => $dataCollection, 'access_level' => $accessLevelCollection, 'exam_yr' => $examYrCollection]);

            // Convert the merged collection to an array and return it
            return $mergedCollection->toArray();
        });
        //return view('pages.dashboard');



        return view('pages.mobilesupport', compact('cached_closed_loans'));
    }

    public function ifqualifiedform(Request $request)
    {

        $applicationtype = $request->applicantsubmittedetails;
        $idno = $request->nationalidnumber;


        // dd($applicationtype);

        list($name, $product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $applicationtype);



        $dataqualified = DB::select("
        SELECT 
            CONCAT(a.first_name, ' ', a.mid_name, ' ', a.last_name) AS names,
            CONCAT(c.name, ' ', c.type, ' ', c.academicyear) AS productname,
            b.serial_number,
            b.idno,
            b.submittedloan,
            b.submittedscholarship,
            b.source,
            b.date_loan_submit,
            b.date_sch_submit,
            b.disbursementoption,
            b.disbursementoptionvalue
        FROM tbl_users_nfm a
        LEFT JOIN tbl_products_submit_new b ON a.id_no = b.idno
        LEFT JOIN ussd_products_test c ON b.productcode = c.productcode
        WHERE a.id_no = ? AND b.acad_year = ? AND b.productcode = ? limit 1
    ", [$idno, $academicyear, $productcode]);

        $checkdataqualified = DB::table('cre_pastapplicationstwo')
            //  ->select('IDNO')
            ->where('IDNO', $idno)
            ->where('STUDGROUPING2', $STUDGROUPING2)
            ->where('ACADEMIC_YEAR', $academicyear)
            ->where('productcode', $productcode)
            ->first();


        $dataqualified = collect($dataqualified)->toArray();

        //dd($dataqualified);

        if (is_array($dataqualified) && !empty($dataqualified)) {
            if (request()->ajax()) {
                return datatables()->of($dataqualified)

                    ->addIndexColumn()
                    ->make(true);
            }
        }
        $checkdataqualified = DB::table('cre_pastapplicationstwo')
            //  ->select('IDNO')
            ->where('IDNO', $idno)
            ->where('STUDGROUPING2', $STUDGROUPING2)
            ->where('ACADEMIC_YEAR', $academicyear)
            ->where('productcode', $productcode)
            ->get();
        // dd($checkdataqualified->IDNO);die();

        if (!is_null($checkdataqualified)) {


            $qualifiedloanmodel = null;
            $qualifiedscholarship = null;
            $qualifiedboth = null;

            // // Loop through each record
            foreach ($checkdataqualified as $record) {
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


            // $qualifiedloanmodel = $checkdataqualified->qualifiedloanmodel;
            // $qualifiedscholarship = $checkdataqualified->qualifiedscholarship;
            // $qualifiedboth = $checkdataqualified->qualifiedboth;

            $loanModels = [
                '1' => 'QUALIFIED OFM',
                '2' => 'QUALIFIED NFM',
                '0' => 'NOT QUALIFIED LOAN'
            ];



            $scholarshipModels = [
                '1' => 'QUALIFIED SCHOLARSHIP',

                '0' => 'NOT QUALIFIED SCHOLARSHIP'
            ];



            $bothModels = [
                '1' => 'QUALIFIED LOAN &SCHOLARSHIP',

                '0' => 'NOT QUALIFIED LOAN & SCHOLARSHIP'
            ];

            $qualifiedloanmodel = $this->mapModel($qualifiedloanmodel, $loanModels);
            $qualifiedscholarship = $this->mapModel($qualifiedscholarship, $scholarshipModels);
            $qualifiedboth = $this->mapModel($qualifiedboth, $bothModels);




            $message = $name . ' ' . $academicyear . ' ' . 'enabled parameters for ' . $idno . ' - ' . ' ' . $qualifiedloanmodel . '-' . ' ' .  $qualifiedscholarship . '-' . ' ' .  $qualifiedboth;
            return response()->json(['error' => $message], 400);
        }

        $message = $idno . ' Not enabled for ' . $name . ' ' . $academicyear;
        return response()->json(['error' => $message], 400);
    }
    public function mapModel($value, $mappings)
    {
        return isset($mappings[$value]) ? $mappings[$value] : $value;
    }
    public function addplatform(Request $request)
    {

        $phone = $request->platformnumber;
        $type = $request->type;

        // dd($type);
        $countryCode = '254';
        $numberLength = strlen($phone);

        if ($numberLength < 11) {
            if (Str::startsWith($phone, '0')) {
                $phone = $countryCode . substr($phone, 1);
            } else {
                $phone = $countryCode . $phone;
            }
        }

        $dataminiapp = DB::select("select
              id, 
    idno, 
    platform, 
    brand, 
    system, 
    smscount, 
    appversion, 
    deviceinfo, 
    cell_verified, 
    cell_phone, 
   
    time_added 
            from tbl_users_miniapp a
            where   a.cell_phone = ?", [$phone]);
        $datamobileapp = DB::select("select
                
    id, 
    appversion, 
    idno, 
    cell_verified, 
    cell_phone, 
   
    gsf, 
    time_added, 
    networkused, 
    simoperatorname, 
    serial, 
    android_id, 
    imei, 
    uuid, 
    deviceinfo, 
    idn, 
    smscount  


            from tbl_users_mobile 
            where  cell_phone = ?", [$phone]);


        // Create collections from the fetched data
        $dataCollection = collect($dataminiapp);
        $accessLevelCollection = collect($datamobileapp);

        // dd($accessLevelCollection);

        //$mergedCollection = $dataCollection->merge($accessLevelCollection);
        if (request()->ajax()) {


            $type = request()->get('type'); // Check for a query parameter to distinguish between requests

            if ($type == 'miniapp') {
                if (request()->ajax()) {
                    return datatables()->of($dataCollection)

                        ->addIndexColumn()
                        ->make(true);
                }
            }

            if ($type == 'androidapp') {
                return datatables()->of($accessLevelCollection)

                    ->addColumn('action', function ($row) {

                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-gsf="' . $row->gsf . '"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-danger btn-sm deleteandroid">Delete</a>';


                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
    }

    public function deleteandroiduser($id)
    {

        $user = auth()->user()->name;
        $gsf = $id;







        $instExists = DB::table('tbl_users_mobile as a')
            ->leftJoin('tbl_users_nfm as b', 'a.idno', '=', 'b.id_no')
            ->leftJoin('tbl_products_submit_new as c', 'b.id_no', '=', 'c.idno')
            ->where('a.gsf', $gsf)
            ->where('c.source', 'mobile')
            ->exists();

        if ($instExists) {



            $errorMessage = 'mobile phone mapped to a loan';


            return response()->json(['error' => $errorMessage], 400);
        } else {

            DB::table('tbl_users_mobile')->where('gsf', $gsf)->delete();


            return response()->json([
                'result' => 'success',
                'message' => 'mobile phone deleted',
            ]);
        }
    }



    public function accesstokensurepaylive()
    {



        $data = DB::select("SELECT access_token  FROM access_token");
        $accessToken = $data[0]->access_token;

        return $accessToken;
    }

    function accesstokencallsurepay(Request $request, ApiController $apiController)
    {
        $accesstoken = $apiController->accesstokencallgenerator();
        $url = env('SUREPAYLOGIN');
        $requestData = [
            'password' => env('surepaypassword'),
            'financierId' => env('surepayfinancierId'),
            'username' => env('surepayusername')

        ];


        // Convert the array to a JSON string
        $requestData = json_encode($requestData);



        $data = $apiController->safaricomsurepay($accesstoken, $requestData, $url);

        //$resp = json_decode($data);
        $dat = $data->result[0]->accesstoken;
        if (is_string($dat) && strlen($dat) > 5) {
            // dd($dat);

            DB::table('access_token')
                ->where('id', 1)
                ->update(['access_token' => $dat]);
        }
    }

    public function allocationreportbulkauto(Request $request, ApiController $apiController)
    {
        ini_set('max_execution_time', 86400); // 3600 seconds = 1 hour

        $rules = [
            'startTime' => 'required|date',
            'endTime' => 'required|date|after:startTime',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => 'Kindly put the correct date',
            ]);
        }

        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
        $startTime = $request->input('startTime', '');
        $endTime = $request->input('endTime', '');

        $accesstoken = $this->accesstokensurepaylive();
        $url = env('SAFCOMALLOCATION_URL');

        $pageIndex = 1;
        $pageSize = 100; // Fixed page size of 100

        do {
            $requestData = [
                'startTime' => $startTime,
                'endTime'  => $endTime,
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
            ];

            $requestData = json_encode($requestData);
            $apiController = new ApiController();

            // Fetch data from API
            $getallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
            $resCodes = $getallocation->resCode ?? null;

            if ($resCodes == '0') {
                $resMsg = $getallocation->resMsg;

                if ($resMsg == 'success') {
                    $result = $getallocation->result;
                    $allocation = $result->allocation ?? [];

                    if (!empty($allocation)) {
                        $allocation = json_decode(json_encode($allocation), true);

                        foreach ($allocation as $item) {
                            $data = [
                                'allocatedQuota' => $item["allocatedQuota"] ?? null,
                                'allocationId' => $item["allocationId"] ?? null,
                                'status' => $item["status"] ?? null,
                                'batchno' => isset($item["dynamicFields"]["batchno"]) ? $item["dynamicFields"]["batchno"] : null,
                                'beneficiaryIdentifier' => $item["beneficiaryIdentifier"] ?? null,
                                'clawBackAmount' => $item["clawBackAmount"] ?? null,
                                'createTime' => $item["createTime"] ?? null,
                                'idno' => isset($item["dynamicFields"]["idno"]) ? $item["dynamicFields"]["idno"] : null,
                                'institutioncode' => isset($item["dynamicFields"]["institutioncode"]) ? $item["dynamicFields"]["institutioncode"] : null,
                                'loanserialno' => isset($item["dynamicFields"]["loanserialno"]) ? $item["dynamicFields"]["loanserialno"] : null,
                                'expiredAmount' => $item["expiredAmount"] ?? null,
                                'expiredTime' => $item["expiredTime"] ?? null,
                                'fundType' => $item["fundType"] ?? null,
                                'remainingAmount' => $item["remainingAmount"] ?? null,
                                'utilizedAmount' => $item["utilizedAmount"] ?? null,
                            ];


                            DB::statement('SET SQL_SAFE_UPDATES = 0');
                            DB::table('mobileallocationreportbulk')->updateOrInsert(
                                ['allocationId' => $data['allocationId']],
                                $data
                            );
                        }
                    }
                } else {
                    echo $getallocation;
                }
            }

            // Increment page index, but stop after reaching page 100
            $pageIndex++;
        } while (!empty($allocation) && $pageIndex <= 1000000);
    }


    public function allocationreportbulk(Request $request, ApiController $apiController)
    {
        ini_set('max_execution_time', 3600); // 3600 seconds = 1 hour

        $rules = [
            'startTime' => 'required|date',
            'endTime' => 'required|date|after:startTime',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {

            return response()->json([

                'result' => 'fail',
                // 'message' => $validator->errors()
                'message' => 'Kindly put the correctdate'


            ]);
        }







        //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');
        $startTime = $request->input('startTime', ''); // Use default value '' if not set
        $endTime = $request->input('endTime', ''); // Use default value '' if not set












        $accesstoken = $this->accesstokensurepaylive();
        $url = env('SAFCOMALLOCATION_URL');
        // $paynumber = str_replace(' ', '', trim($paynumber));

        $requestData = [

            // 'status' => 'Completed',
            // 'beneficiaryIdentifier' => $paynumber,
            // 'fundType' => 'Upkeep'
            'startTime' => $startTime,
            'endTime'  => $endTime,
            'pageIndex' => 1,
            'pageSize' => 100




        ];

        //dd($paynumber);

        // Convert the array to a JSON string
        $requestData = json_encode($requestData);
        $apiController = new ApiController();

        $getallocation = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
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

                        DB::table('mobileallocationreportbulk')->updateOrInsert(
                            ['allocationId' => $data['allocationId']], // Condition to check for existence
                            $data // Data to insert or update
                        );
                    }
                }
            }
        } else {

            dd($getallocation);
        }
    }

    public function bulkallocationreport(Request $request, ApiController $apiController)
    {

        $cached_loans = Cache::remember('cached_loans', 60, function () {
            $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');


            $data = DB::select("
            select
                * 
            from mobileallocationreportbulk
             ");


            // Convert the result to an array
            //  $data = json_decode(json_encode($data), true);
            return collect($data)->toArray();
        });

        //$cachedData = Cache::get('cached_loans');

        // dd($cached_loans);









        if (request()->ajax()) {
            return datatables()->of($cached_loans)

                //->addIndexColumn()
                ->make(true);
        }
    }







    public function allocationreport(Request $request, ApiController $apiController)
    {
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
                                } else {

                                    echo "err records" . $record->phonenumber;
                                }
                                // dd($Xnew_array);



                            } else {
                                echo "err2 records" . $record->phonenumber;
                            }
                        }
                    }
                }
            });
    }






    public function searchpaymobi(Request $request, ApiController $apiController)
    {



        $paynumber = $request->paynumber;
        $type = $request->type;


        $countryCode = '254';
        $numberLength = strlen($paynumber);

        if ($numberLength < 11) {
            if (Str::startsWith($paynumber, '0')) {
                $paynumber = $countryCode . substr($paynumber, 1);
            } else {
                $paynumber = $countryCode . $paynumber;
            }
        }

        if (request()->ajax()) {





            $action = 'mobilepaymentchecker';
            $arr = array('paymentno' => $paynumber);
            $result = $apiController->mobiapis($action, $arr);

            $resultdetails = collect([(object) $result]);

            $accesstoken = $this->accesstokensurepaylive();

            $url = env('SAFCOMGETBENEFICIARY_URL');
            $requestData = [

                'phoneNumber' => $paynumber,


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

            $getbeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
            //$getbeneficiary =  $data->result[0]->accesstoken;
            //dd($getbeneficiary);

            $resCodes = $getbeneficiary->resCode ?? null;;

            if ($resCodes == '0') {
                $resMsg = $getbeneficiary->resMsg;

                if ($resMsg == 'success') {

                    $result = $getbeneficiary->result;

                    $beneficiary = $result->beneficiary;
                    $beneficiary = json_decode(json_encode($beneficiary), true);

                    $myarray = $beneficiary;

                    $filter = $paynumber;

                    $new_array = array_filter($myarray, function ($var) use ($filter) {
                        return ($var['phoneNumber'] == $filter);
                    });


                    // dd($new_array);



                }
            }


            $url = env('SAFCOMALLOCATION_URL');
            $requestData = [

                // 'status' => 'Active',
                'beneficiaryIdentifier' => $paynumber,
                'fundType' => 'Upkeep'


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

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

                    $filter = $paynumber;

                    $Xnew_array = array_filter($myarray, function ($var) use ($filter) {
                        return ($var['beneficiaryIdentifier'] == $filter);
                    });


                    // dd($Xnew_array);



                }
            }


            //dd($result);



            $type = request()->get('type'); // Check for a query parameter to distinguish between requests

            // dd($type);
            if ($type == 'searchpay') {

                return datatables()->of($resultdetails)
                    ->make(true);       // Finalizes the response and returns it in JSON format

            }


            //s dd($new_array);

            if ($type == 'searchstats') {
                return datatables()->of($new_array)
                    ->addColumn('action', function ($row) {
                        // dd($row['userStatus']);
                        // Only show refresh button if userStatus is Active
                        if ($row['userStatus'] == 'Active') {
                            // Encode the entire row as JSON for the button
                            $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            return '<button class="btn btn-sm btn-primary refresh-btn" 
                                    data-row=\'' . $rowData . '\'
                                    data-id="' . $row['id'] . '">Refresh</button>';
                        }
                        return ''; // Return empty string if not Active
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            if ($type == 'searchallocationstats') {

                return datatables()->of($Xnew_array)
                    ->make(true);       // Finalizes the response and returns it in JSON format

            }
        }
    }



    public function refreshuser(Request $request, ApiController $apiController)
    {

        // Check if user_data exists and has an id
        if (empty($request->input('user_data')) || empty($request->input('user_data.id'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: user data or ID is missing'
            ], 400);
        }

        $dynamicFields = array(
            'idno' =>  $request['user_data']['dynamicFields']['idno'],
            'institutioncode' =>  $request['user_data']['dynamicFields']['institutioncode']


        );

        $requestData = [
            //Fill in the request parameters with valid values
            'id' => $request['user_data']['id'],
            'identityValue' =>  $request['user_data']['idNumber'],
            'firstName' => $request['user_data']['firstName'],
            'lastName' => $request['user_data']['lastName'],
            'address' => $request['user_data']['address'],
            'idType' => $request['user_data']['idType'],
            'email' => $request['user_data']['email'],
            'dynamicFields' => $dynamicFields,
            'fullName' => $request['user_data']['fullName'],
            'idNumber' => $request['user_data']['idNumber'],
            'phoneNumber' => $request['user_data']['phoneNumber'],

        ];


        $accesstoken = $this->accesstokensurepaylive();

        $url = env('SAFCOMUPDATEBENEFICIARY_URL');
        $requestData = json_encode($requestData);

        $updatebeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
        //$getbeneficiary =  $data->result[0]->accesstoken;
        //dd($getbeneficiary);

        $resCodes = $updatebeneficiary->resCode ?? null;;

        if ($resCodes == '0') {
            $resMsg = $updatebeneficiary->resMsg;

            if ($resMsg == 'success') {

                // dd("update success");
                //get funds allocated
                $datlogs = [
                    //Fill in the request parameters with valid values
                    'message' =>  $requestData,
                    'action' => 'updatebeneficiary',
                    'phone' => $request['user_data']['phoneNumber'],


                ];


                DB::table('datlogs')->Insert(
                    $datlogs
                );

                return response()->json([
                    'success' => true,
                    'message' => 'User data refreshed successfully',
                    //'data' => $userData // Optional: return updated data
                ]);
            } else {

                return response()->json([
                    'error' => 'Refresh failed: '
                ], 500);
            }
        }
    }



    public function withdrawstatement(Request $request, ApiController $apiController)
    {



        $paynumber = $request->paynumber;
        $type = $request->type;


        $countryCode = '254';
        $numberLength = strlen($paynumber);

        if ($numberLength < 11) {
            if (Str::startsWith($paynumber, '0')) {
                $paynumber = $countryCode . substr($paynumber, 1);
            } else {
                $paynumber = $countryCode . $paynumber;
            }
        }

        if (request()->ajax()) {






            $accesstoken = $this->accesstokensurepaylive();

            $url = env('SAFCOMWITHDRAW_URL');
            $requestData = [

                'beneficiaryIdentifier' => $paynumber,


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

            $getwithdraw = $apiController->safaricomsurepay($accesstoken, $requestData, $url);
            //$getbeneficiary =  $data->result[0]->accesstoken;
            //dd($getbeneficiary);

            $resCodes = $getwithdraw->resCode ?? null;;

            if ($resCodes == '0') {
                $resMsg = $getwithdraw->resMsg;

                if ($resMsg == 'success') {

                    $result = $getwithdraw->result;

                    $transactionList = $result->transactionList;

                    return datatables()->of($transactionList)
                        ->make(true);       // Finalizes the response and returns it in JSON format



                    // dd($new_array);



                }
            }




            //dd($result);






        }
    }







    public function idnumbersearchpaymobi(Request $request, ApiController $apiController)
    {



        $idnumber = $request->idnumbersearch;
        $type = $request->type;
        $action = 'mobilepaymentcheckerid';
        $arr = array('idno' => $idnumber);
        $result = $apiController->mobiapis($action, $arr);
        $paynumber = $result['PHONENUMBER'];


        $resultdetailsidno = collect([(object) $result]);


        $countryCode = '254';
        $numberLength = strlen($paynumber);

        if ($numberLength < 11) {
            if (Str::startsWith($paynumber, '0')) {
                $paynumber = $countryCode . substr($paynumber, 1);
            } else {
                $paynumber = $countryCode . $paynumber;
            }
        }

        if (request()->ajax()) {





            $action = 'mobilepaymentcheckerid';
            $arr = array('idno' => $idnumber);
            $result = $apiController->mobiapis($action, $arr);

            $resultdetailsidno = collect([(object) $result]);

            // dd($result);
            $accesstoken = $this->accesstokensurepaylive();

            $url = env('SAFCOMGETBENEFICIARY_URL');
            $requestData = [

                'idNumber' => $idnumber,


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

            $getbeneficiary = $apiController->safaricomsurepay($accesstoken, $requestData, $url);

            $resCodes = $getbeneficiary->resCode ?? null;;

            if ($resCodes == '0') {
                $resMsg = $getbeneficiary->resMsg;

                if ($resMsg == 'success') {

                    $result = $getbeneficiary->result;

                    $beneficiary = $result->beneficiary;
                    $beneficiary = json_decode(json_encode($beneficiary), true);

                    // $myarray = $beneficiary;

                    // $filter = $paynumber;

                    // $new_array = array_filter($myarray, function ($var) use ($filter) {
                    //     return ($var['phoneNumber'] == $filter);
                    // });


                    // dd($new_array);



                }
            }


            $url = env('SAFCOMALLOCATION_URL');
            $requestData = [

                'status' => 'Active',
                'beneficiaryIdentifier' => $paynumber,
                'fundType' => 'Upkeep'


            ];


            // Convert the array to a JSON string
            $requestData = json_encode($requestData);

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

                    $filter = $idnumber;

                    $Xnew_array = array_filter($myarray, function ($var) use ($filter) {
                        return isset($var['dynamicFields']['idno']) && $var['dynamicFields']['idno'] == $filter;
                    });


                    // dd($Xnew_array);



                }
            }


            //dd($result);



            $type = request()->get('type'); // Check for a query parameter to distinguish between requests

            // dd($type);
            if ($type == 'idnumbersearchpay') {

                return datatables()->of($resultdetailsidno)
                    ->make(true);       // Finalizes the response and returns it in JSON format

            }


            //  dd($new_array);

            if ($type == 'idnumbersearchstats') {

                // return datatables()->of($beneficiary)
                //     ->make(true);       // Finalizes the response and returns it in JSON format


                return datatables()->of($beneficiary)
                    ->addColumn('action', function ($row) {
                        // dd($row['userStatus']);
                        // Only show refresh button if userStatus is Active
                        if ($row['userStatus'] == 'Active') {
                            // Encode the entire row as JSON for the button
                            $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            return '<button class="btn btn-sm btn-primary refresh-btn" 
                                    data-row=\'' . $rowData . '\'
                                    data-id="' . $row['id'] . '">Refresh</button>';
                        }
                        return ''; // Return empty string if not Active
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            if ($type == 'idnumbersearchallocationstats') {

                return datatables()->of($Xnew_array)
                    ->make(true);       // Finalizes the response and returns it in JSON format

            }
        }
    }




    public function minorverify(Request $request, ApiController $apiController)
    {

        $user = auth()->user()->name ?? null;
        $rules = [
            'phoneverify' => 'required|string|min:5',
            'indexnumber' => 'required|string|min:5',


        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //dd($request);
        $applicationtype = $request->applicantsubmittedetails;


        // dd($applicationtype);

        list($name, $product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $applicationtype);



        $index = $request->academicyear . $request->indexnumber;

        $namba = $request->phoneverify;

        $countryCode = '254';
        $numberLength = strlen($namba);

        if ($numberLength < 11) {
            if (Str::startsWith($namba, '0')) {
                $namba = $countryCode . substr($namba, 1);
            } else {
                $namba = $countryCode . $namba;
            }
        }

        //dd()

        $result = DB::connection('sqlsrv')->table('LMIDENTIFICATIONDOCUMENTS as b')
            ->join('CUSTTABLE as c', 'b.ACCOUNTNUM', '=', 'c.ACCOUNTNUM')
            ->join('DIRPERSONNAME as d', 'c.PARTY', '=', 'd.PERSON')
            ->join('LMCUSTTABLENAME as lc', 'lc.ACCOUNTNUM', '=', 'b.ACCOUNTNUM')
            ->join('DIRPARTYTABLE as f', 'c.PARTY', '=', 'f.RECID')
            ->leftJoin(DB::raw('(SELECT locator, a.location, b.party 
                           FROM DIRPARTYLOCATION b 
                           JOIN LogisticsElectronicAddress a 
                           ON b.LOCATION = a.LOCATION 
                           WHERE a.TYPE = 2 AND a.ISPRIMARY = 1) as g'), 'g.party', '=', 'c.party')
            ->leftJoin(DB::raw('(SELECT locator, a.location, b.party 
                           FROM DIRPARTYLOCATION b 
                           JOIN LogisticsElectronicAddress a 
                           ON b.LOCATION = a.LOCATION 
                           WHERE (a.TYPE = 1 OR a.TYPE = 6) AND a.ISPRIMARY = 1) as e'), 'e.party', '=', 'c.party')
            ->where('b.IDENTITYREFERENCENO', $index)
            ->select(
                'b.IDENTITYREFERENCENO as IDNO',
                DB::raw("CASE WHEN f.GENDER = 0 THEN 'M'
                        WHEN f.GENDER = 1 THEN 'F'
                        ELSE 'F' END AS GENDER"),
                'f.BIRTHDAY',
                'f.BIRTHMONTH',
                'f.BIRTHYEAR',
                DB::raw("CONCAT(f.BIRTHDAY, '/', f.BIRTHMONTH, '/', f.BIRTHYEAR) as FULLBIRTHDATE"),
                'lc.NAME',
                'd.LASTNAME as LASTNAME',
                'd.FIRSTNAME as FIRSTNAME',
                'd.MIDDLENAME as MIDDLENAME',
                'b.ACCOUNTNUM',
                'g.LOCATOR as EMAIL',
                'e.LOCATOR as PHONE'
            )
            ->orderBy('d.RECID', 'DESC')
            ->first();  // Use first() to get the top 1 record

        //dd($result);

        $checkaxdd = $result->IDNO ?? null;

        //dd($checkaxdd);


        if (empty($checkaxdd)) {



            $response = Http::post('https://portal.hef.co.ke/auth/hefregistrationdetails');

            if ($response->successful()) {

                $curl2 = curl_init();

                curl_setopt($curl2, CURLOPT_URL, 'https://portal.hef.co.ke/auth/mobiportal');
                curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'POST');

                curl_setopt($curl2, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Setting custom header

                curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl2, CURLOPT_POST, true);
                curl_setopt(
                    $curl2,
                    CURLOPT_POSTFIELDS,
                    '{ "index": "' . $request->indexnumber . '", "year": "' . $request->academicyear . '"}'


                );

                $respons2 = curl_exec($curl2);

                if (curl_errno($curl2)) {
                    // If an error occurs, display the error message
                    $error_msg = curl_error($curl2);
                    curl_close($curl2);
                    return response()->json(['errors' => $request->indexnumber  . ' AN ERROR OCCURED FROM HEF PORTAL' . $index], 422);

                    //dd('cURL Error: ' . $error_msg);
                } else {
                    // Close the cURL session and display the response

                    $respons2 = json_decode($respons2);
                    curl_close($curl2);
                    if (isset($respons2->Valid) && $respons2->Valid === true) {






                        $Institutiondata = [
                            'ACADEMICYEAR' => '2025/2026',
                            'ADMISSIONCATEGORY' => $respons2->data[0]->AdmissionCategory,
                            'ADMISSIONNUMBER' => $respons2->data[0]->AdmissionNumber,
                            'ADMISSIONYEAR' => $respons2->data[0]->AdmiYear,
                            'COURSECODE' => $respons2->data[0]->CourseCode,
                            'INSTITUTIONBRANCHCODE' => $respons2->data[0]->InstitutionBranchCode,
                            'INSTITUTIONCODE' => $respons2->data[0]->InstitutionCode,
                            'ACCOUNTNUM' => $respons2->data[0]->accountnum,
                            'Productcode' => $productcode,
                            'IDNO' => $respons2->data[0]->idnumber,
                            'InstitutionName' => $respons2->data[0]->INSTITUTIONNAME,
                            'CourseDescription' => $respons2->data[0]->CourseName,
                            //'LOANSERIALNO' => '',
                        ];

                        DB::table('dminstitututions_2024')->updateOrInsert(
                            ['IDNO' => $respons2->data[0]->idnumber],  // The condition for checking existence
                            $Institutiondata    // The data to update or insert
                        );
                    } else {

                        return response()->json(['errors' => $request->indexnumber  . ' AN ERROR OCCURED FROM HEF PORTAL' . $index], 422);
                    }
                }




                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://portal.hef.co.ke/auth/hefregistrationdetails');
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Setting custom header

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt(
                    $curl,
                    CURLOPT_POSTFIELDS,
                    '{ "phone": "' . $namba . '"}'


                );

                $respons = curl_exec($curl);

                if (curl_errno($curl)) {
                    // If an error occurs, display the error message
                    $error_msg = curl_error($curl);
                    curl_close($curl);
                    //dd('cURL Error: ' . $error_msg);
                } else {
                    // Close the cURL session and display the response

                    $respons = json_decode($respons);
                    curl_close($curl);
                    // dd($respons);

                    // dd($respons->Valid);
                    // dd($respons->data[0]->idnumber);

                    if (isset($respons->result) && $respons->result == "success") {

                        //////pullinst/////


                        /////////////









                        $hefidnumber =  $respons->data[0]->idno;
                        // dd( $hefidnumber);

                        if (strpos($hefidnumber, $request->academicyear) == 0) {


                            if (strpos($hefidnumber, $index) == 0) {

                                $data = [
                                    'id_no' =>  $respons->data[0]->idno,
                                    'full_name' =>  $respons->data[0]->accountName,
                                    'gender' =>  $respons->data[0]->gender,
                                    'dob' => $respons->data[0]->dob,
                                    'cell_phone' => $namba,
                                    'last_name' => $respons->data[0]->Surname,
                                    'mid_name' =>  $respons->data[0]->other_Name,
                                    'first_name' =>  $respons->data[0]->first_Name,
                                    'email_add' =>  $respons->data[0]->email,
                                    'cell_verified' => '1',
                                    'updated_by' => 'updatenfmdetailsminorhef',
                                ];



                                $added = DB::table('tbl_users_nfm')->updateOrInsert(
                                    ['cell_phone' => $namba],
                                    $data
                                );



                                $updateData = [
                                    'phone' => $namba,
                                    'studentid' => $index,
                                    'update_name' => $user,



                                ];

                                //     dd($updateData);

                                try {
                                    DB::beginTransaction();

                                    DB::table('tbl_whitelisted')->updateOrInsert(
                                        ['phone' => $namba], // Match the record based on ACCOUNTNUM
                                        $updateData // Update or insert the data
                                    );

                                    // If both updates succeed, commit the transaction
                                    DB::commit();
                                } catch (\Illuminate\Database\QueryException $e) {
                                    // If an error occurs, rollback the transaction
                                    DB::rollBack();
                                    // return response()->json(['errors' => $e.'update failed'], 422);
                                    return response()->json(['errors' => 'update failed'], 422);





                                    // Optionally, handle the error (e.g., log it or display a message)
                                    //throw $e;
                                }


                                $datax = [
                                    'id_no' => $index,
                                    'product_id' => 'scholarship',
                                    'productname' => 'scholarshipminor',
                                    'added_by' => $user,
                                    'acad_year' => '2025/2026',
                                ];

                                DB::table('tbl_nfm_enabled')->insert($datax);
                                return response()->json([
                                    'result' => 'success',
                                    'message' => 'scholarship minor enabled by ' . $user
                                ], 200);
                            } else {

                                return response()->json(['errors' => $hefidnumber . ' DIFFERENT FROM ID ON PORTAL FROM HEF PORTAL' . $index], 422);
                            }
                        } else {
                            // $idnumber begins with 2022
                            return response()->json(['errors' => $hefidnumber . ' NOT MINOR FROM HEF PORTAL'], 422);
                        }
                    } else {
                        // $idnumber begins with 2022
                        return response()->json(['errors' =>  ' NOT MINOR FROM HEF PORTAL'], 422);
                    }
                }

                //  echo "Connection successful!";
            }

            //echo "no Connection successful!";




            return response()->json(['errors' => $index . ' NOT ON AX'], 422);
        }

        $phoneinax = $result->PHONE;
        $numberLength = strlen($phoneinax);

        if ($numberLength < 11) {
            if (Str::startsWith($phoneinax, '0')) {
                $namba = $countryCode . substr($phoneinax, 1);
            } else {
                $phoneinax = $countryCode . $phoneinax;
            }
        }
        if ($phoneinax != $namba) {







            return response()->json(['errors' => $namba . ' NOT ON AX FOR INDEX PASSED.PHONE ON AX IS ' . $phoneinax], 422);
        }

        $data = [
            'id_no' => $result->IDNO,
            'full_name' => $result->NAME,
            'gender' => $result->GENDER,
            'dob' => $result->FULLBIRTHDATE,
            'cell_phone' => $phoneinax,
            'last_name' => $result->LASTNAME,
            'mid_name' => $result->MIDDLENAME,
            'first_name' => $result->FIRSTNAME,
            'email_add' => $result->EMAIL,
            'cell_verified' => '1',
            'updated_by' => 'updatenfmdetailsminor',
        ];

        $added = DB::table('tbl_users_nfm')->updateOrInsert(
            ['cell_phone' => $namba],
            $data
        );



        $updateData = [
            'phone' => $namba,
            'studentid' => $index,
            'update_name' => $user,



        ];

        //     dd($updateData);

        try {
            DB::beginTransaction();

            DB::table('tbl_whitelisted')->where('phone', $namba)->delete();

            DB::table('tbl_whitelisted')->insert(array_merge(['phone' => $namba], $updateData));


            // If both updates succeed, commit the transaction
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            // If an error occurs, rollback the transaction
            DB::rollBack();
            // return response()->json(['errors' => $e.'update failed'], 422);
            return response()->json(['errors' => 'update failed'], 422);





            // Optionally, handle the error (e.g., log it or display a message)
            //throw $e;
        }


        $datax = [
            'id_no' => $index,
            'product_id' => 'scholarship',
            'productname' => 'scholarshipminor',
            'added_by' => $user,
            'acad_year' => '2025/2026',
        ];

        DB::table('tbl_nfm_enabled')->insert($datax);
        return response()->json([
            'result' => 'success',
            'message' => 'scholarship minor enabled by ' . $user
        ], 200);
    }



    public function schidformpost(Request $request, ApiController $apiController)
    {


        // dd($request);


        $user = auth()->user()->name;
        $rules = [
            'academicyear' => 'required|string|min:4|max:4',
            'indexnumber' => 'required|string|min:5',
            'idnumber' => 'required|string|min:5',
            // 'phoneverify' => 'required|string|min:5',



        ];


        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // $namba = $request->phoneverify;

        // $countryCode = '254';
        // $numberLength = strlen($namba);

        // if ($numberLength < 11) {
        //     if (Str::startsWith($namba, '0')) {
        //         $namba = $countryCode . substr($namba, 1);
        //     } else {
        //         $namba = $countryCode . $namba;
        //     }
        // }

        $idno = $request->idnumber;
        $index = $request->academicyear . $request->indexnumber;
        $updateData = [
            'IDNO' => $idno,

        ];

        try {
            DB::beginTransaction();

            // First update
            DB::table('cre_pastapplicationstwo')
                ->where('IDNO', $index)
                ->update($updateData);

            // Second update
            DB::table('dminstitututions_2024')
                ->where('IDNO', $index)
                ->update($updateData);

            // If both updates succeed, commit the transaction
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            // If an error occurs, rollback the transaction
            DB::rollBack();
            //  return response()->json(['errors' => $e.'update failed'], 422);
            return response()->json(['errors' => 'update failed'], 422);


            // Optionally, handle the error (e.g., log it or display a message)
            //throw $e;
        }
        $applicationtype = $request->applicantsubmittedetails;


        // dd($applicationtype);

        list($name, $product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $applicationtype);


        $response = Http::post('https://portal.hef.co.ke/auth/hefregistrationdetails');

        if ($response->successful()) {

            $curl2 = curl_init();

            curl_setopt($curl2, CURLOPT_URL, 'https://portal.hef.co.ke/auth/mobiportal');
            curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'POST');

            curl_setopt($curl2, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Setting custom header

            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl2, CURLOPT_POST, true);
            curl_setopt(
                $curl2,
                CURLOPT_POSTFIELDS,
                '{ "index": "' . $request->indexnumber . '", "year": "' . $request->academicyear . '"}'


            );

            $respons2 = curl_exec($curl2);

            if (curl_errno($curl2)) {
                // If an error occurs, display the error message
                $error_msg = curl_error($curl2);
                curl_close($curl2);
                return response()->json(['errors' => $request->indexnumber  . ' 1.AN ERROR OCCURED FROM HEF PORTAL' . $index], 422);

                //dd('cURL Error: ' . $error_msg);
            } else {
                // Close the cURL session and display the response

                $respons2 = json_decode($respons2);
                curl_close($curl2);
                if (isset($respons2->Valid) && $respons2->Valid === true) {






                    $Institutiondata = [
                        'ACADEMICYEAR' => '2025/2026',
                        'ADMISSIONCATEGORY' => $respons2->data[0]->AdmissionCategory,
                        'ADMISSIONNUMBER' => $respons2->data[0]->AdmissionNumber,
                        'ADMISSIONYEAR' => $respons2->data[0]->AdmiYear,
                        'COURSECODE' => $respons2->data[0]->CourseCode,
                        'INSTITUTIONBRANCHCODE' => $respons2->data[0]->InstitutionBranchCode,
                        'INSTITUTIONCODE' => $respons2->data[0]->InstitutionCode,
                        'ACCOUNTNUM' => $respons2->data[0]->accountnum,
                        'Productcode' => $productcode,
                        'IDNO' => $respons2->data[0]->idnumber,
                        'InstitutionName' => $respons2->data[0]->INSTITUTIONNAME,
                        'CourseDescription' => $respons2->data[0]->CourseName,
                        //'LOANSERIALNO' => '',
                    ];

                    DB::table('dminstitututions_2024')->updateOrInsert(
                        ['IDNO' => $respons2->data[0]->idnumber],  // The condition for checking existence
                        $Institutiondata    // The data to update or insert
                    );
                } else {

                    return response()->json(['errors' => $request->indexnumber  . ' 2.AN ERROR OCCURED FROM HEF PORTAL' . $index], 422);
                }
            }
        } else {
            return response()->json([
                'result' => 'success',
                'message' => 'institution details not found '
            ], 200);
        }







        $data = [
            'id_no' => $idno,
            'product_id' => 'scholarship',
            'productname' => 'scholarship',
            'added_by' => $user,
            'acad_year' => '2025/2026',
        ];

        DB::table('tbl_nfm_enabled')->insert($data);
        return response()->json([
            'result' => 'success',
            'message' => 'scholarship enabled by ' . $user
        ], 200);
    }


    public function addinstitutionbulk(Request $request, ApiController $apiController)
    {

        DB::table('cancelledloans')

            ->where('updated', '0') // Filter by current date


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
                    $TVET = $GRPresults['TVET'];
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
                        return response()->json("Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
                    }

                    if (!empty($result) && ($result['missing'] ?? false)) {


                        return response()->json($result['missing']);
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

                                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            } else {

                                DB::table('cre_pastapplicationstwo')->insert($updateData);

                                DB::table('tbl_nfm_enabled')->insert($data);

                                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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

                                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                            } else {

                                DB::table('cre_pastapplicationstwo')->insert($updateData);

                                DB::table('tbl_nfm_enabled')->insert($data);

                                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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

                            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
                        } else {

                            DB::table('cre_pastapplicationstwo')->insert($updateData);

                            DB::table('tbl_nfm_enabled')->insert($data);

                            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
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

                        return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                    } else {

                        DB::table('cre_pastapplicationstwo')->insert($updateData);

                        DB::table('tbl_nfm_enabled')->insert($data);

                        return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                    }
                }
            });
    }


    public function addinstitution(Request $request, ApiController $apiController)
    {


        // dd($request);
        $user = auth()->user()->name;


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
        $TVET = $GRPresults['TVET'];
        $UG = $GRPresults['UG'];



        // $idno = $request->idno;
        // $productcode = $request->productcode;
        // $STUDGROUPING2 = $request->studentgrouping;
        // $product_id = $request->productid;
        // $academicyear = $request->academicyear;


        $applicationtype = $request->addinstitutiondet;
        $idno = $request->idinstitution;

        list($product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $applicationtype);
        $arr = array(
            'idno' => $idno,
            'prod' => $productcode,
            'productcode' => $productcode,
            'group' => $STUDGROUPING2,
            'id' => $product_id,
            'academicyear' => $academicyear,
        );

        // $idno = $request->idno;
        // $arr = array(
        //     'idno' => $request->idno,
        //     'prod' => $request->productcode,
        //     'productcode' => $request->productcode,
        //     'group' => $request->studentgrouping,
        //     'id' => $request->productid,
        //     'academicyear' => $request->academicyear,
        // );

        //dd($arr);


        $cacheKey = 'blocked_nfm_' . $idno;
        $cacheDuration = 60; // Cache duration in minutes

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($idno) {
            return DB::table('tbl_blocked_nfm')
                ->where('idno', $idno)
                ->where('status', 'blocked')

                ->first();
        });

        if (!empty($data) && $data->idno === $idno) {
            $errorMessage = "ID number {$idno}  is restricted from accessing this service. Please contact lending team for assistance.";
            return response()->json(['error' => $errorMessage], 400);
        }









        $action = 'fetchSubsequentRecordscurrent';
        $result = $apiController->mobiapis($action, $arr);
        //dd($result);
        if (empty($result)) {

            // return response()->json("AX connection unavailable");
            return response()->json("Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
        }

        if (!empty($result) && ($result['missing'] ?? false)) {


            return response()->json($result['missing']);
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
        $rules = [
            'LOANSERIALNO' => 'required',
            'ACCOUNTNUM' => 'required',
            'IDNO' => 'required',

            'INSTITUTIONCODE' => 'required',
            'ADMISSIONNUMBER' => 'required',
            'COURSECODE' => 'required',
            'COURSEDESCRIPTION' => 'required',


            'ADMISSIONYEAR' => 'required',
            'YEAROFSTUDY' => 'required',

            'ACADEMIC_YEAR' => 'required',
            'PRODUCTCODE' => 'required',
            'INSTITUTIONNAME' => 'required',

            'indexno' => 'required',
            'examsityr' => 'required',
        ];

        // Custom error messages for each field
        $messages = [
            'LOANSERIALNO.required' => 'LOANSERIALNO field is required.',
            'ACCOUNTNUM.required' => 'ACCOUNTNUM field is required.',
            'IDNO.required' => 'IDNO field is required.',

            'INSTITUTIONCODE.required' => 'INSTITUTIONCODE field is required.',
            'ADMISSIONNUMBER.required' => 'ADMISSIONNUMBER field is required.',
            'COURSECODE.required' => 'COURSECODE field is required.',
            'COURSEDESCRIPTION.required' => 'COURSEDESCRIPTION field is required.',


            'ADMISSIONYEAR.required' => 'ADMISSIONYEAR field is required.',
            'YEAROFSTUDY.required' => 'YEAROFSTUDY field is required.',

            'ACADEMIC_YEAR.required' => 'ACADEMIC_YEAR field is required.',
            'PRODUCTCODE.required' => 'PRODUCTCODE field is required.',
            'INSTITUTIONNAME.required' => 'INSTITUTIONNAME field is required.',

            'indexno.required' => 'indexno field is required.',
            'examsityr.required' => 'examsityr field is required.',
        ];

        // Run validation
        $validator = Validator::make($result, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the first validation error message
            //$errorMessage = $validator->errors()->first();
            $errorMessage = 'verify if institution details exist on AX';

            return response()->json(['error' => $errorMessage], 400);
        }



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
            'added_by' => $user,
            'acad_year' => $academicyear,
        ];

        $message = $user . ' has SSQ enabled  ' . $idno . ' - ' . ' for product ' . $STUDGROUPING2 . '-' . $academicyear;
        $applicationrecord  = DB::table('cre_pastapplicationstwo')
            ->where('IDNO', $idno)
            ->orwhere('ADMISSIONO', $result['ADMISSIONNUMBER'])
            ->get();
        //dd($applicationrecord);

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

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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

                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
            } else {

                DB::table('cre_pastapplicationstwo')->insert($updateData);

                DB::table('tbl_nfm_enabled')->insert($data);

                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
            }
        }


        $qualifiedloanmodel = 'OFM';
        $qualifiedscholarship = 'NO';
        $qualifiedboth  = 'NO';
        $updateData['qualifiedloanmodel'] = '1';

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

            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        } else {

            DB::table('cre_pastapplicationstwo')->insert($updateData);

            DB::table('tbl_nfm_enabled')->insert($data);

            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        }
    }






    public function addlateapplicant(Request $request, ApiController $apiController)
    {


        // dd($request);
        $user = auth()->user()->name;



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
        $TVET = $GRPresults['TVET'];
        $UG = $GRPresults['UG'];



        // $idno = $request->idno;
        // $productcode = $request->productcode;
        // $STUDGROUPING2 = $request->studentgrouping;
        // $product_id = $request->productid;
        // $academicyear = $request->academicyear;
        $addlateapplicant = $request->addlateapplicant;
        $idno = $request->idlateapplicant;

        list($product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $addlateapplicant);

        $arr = array(
            'idno' => $idno,
            'prod' => $productcode,
            'productcode' => $productcode,
            'group' => $STUDGROUPING2,
            'id' => $product_id,
            'academicyear' => $academicyear,
        );


        $latedata = [
            'id_no' => $idno,
            'product_id' => $product_id,
            'productname' => $productcode,
            'reason' => "late applicant",
            'close_date' => Carbon::today()->addMonths(1)->toDateString(), // 'YYYY-MM-DD' format
            'added_by' => $user,
            'acad_year' => $academicyear,
        ];



        // $idno = $request->idno;
        // $arr = array(
        //     'idno' => $request->idno,
        //     'prod' => $request->productcode,
        //     'productcode' => $request->productcode,
        //     'group' => $request->studentgrouping,
        //     'id' => $request->productid,
        //     'academicyear' => $request->academicyear,
        // );

        //dd($arr);


        $cacheKey = 'blocked_nfm_' . $idno;
        $cacheDuration = 60; // Cache duration in minutes

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($idno) {
            return DB::table('tbl_blocked_nfm')
                ->where('idno', $idno)
                ->where('status', 'blocked')

                ->first();
        });

        if (!empty($data) && $data->idno === $idno) {
            $errorMessage = "ID number {$idno}  is restricted from accessing this service. Please contact lending team for assistance.";
            return response()->json(['error' => $errorMessage], 400);
        }









        $action = 'fetchSubsequentRecordscurrent';
        $result = $apiController->mobiapis($action, $arr);
        //dd($result);
        if (empty($result)) {

            // return response()->json("AX connection unavailable");
            return response()->json("Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
        }

        if (!empty($result) && ($result['missing'] ?? false)) {


            return response()->json($result['missing']);
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
        $rules = [
            'LOANSERIALNO' => 'required',
            'ACCOUNTNUM' => 'required',
            'IDNO' => 'required',

            'INSTITUTIONCODE' => 'required',
            'ADMISSIONNUMBER' => 'required',
            'COURSECODE' => 'required',
            'COURSEDESCRIPTION' => 'required',


            'ADMISSIONYEAR' => 'required',
            'YEAROFSTUDY' => 'required',

            'ACADEMIC_YEAR' => 'required',
            'PRODUCTCODE' => 'required',
            'INSTITUTIONNAME' => 'required',

            'indexno' => 'required',
            'examsityr' => 'required',
        ];

        // Custom error messages for each field
        $messages = [
            'LOANSERIALNO.required' => 'LOANSERIALNO field is required.',
            'ACCOUNTNUM.required' => 'ACCOUNTNUM field is required.',
            'IDNO.required' => 'IDNO field is required.',

            'INSTITUTIONCODE.required' => 'INSTITUTIONCODE field is required.',
            'ADMISSIONNUMBER.required' => 'ADMISSIONNUMBER field is required.',
            'COURSECODE.required' => 'COURSECODE field is required.',
            'COURSEDESCRIPTION.required' => 'COURSEDESCRIPTION field is required.',


            'ADMISSIONYEAR.required' => 'ADMISSIONYEAR field is required.',
            'YEAROFSTUDY.required' => 'YEAROFSTUDY field is required.',

            'ACADEMIC_YEAR.required' => 'ACADEMIC_YEAR field is required.',
            'PRODUCTCODE.required' => 'PRODUCTCODE field is required.',
            'INSTITUTIONNAME.required' => 'INSTITUTIONNAME field is required.',

            'indexno.required' => 'indexno field is required.',
            'examsityr.required' => 'examsityr field is required.',
        ];

        // Run validation
        $validator = Validator::make($result, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the first validation error message
            //$errorMessage = $validator->errors()->first();
            $errorMessage = 'verify if institution details exist on AX';

            return response()->json(['error' => $errorMessage], 400);
        }



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
            'added_by' => $user,
            'acad_year' => $academicyear,
        ];

        $message = $user . ' has SSQ enabled  ' . $idno . ' - ' . ' for product ' . $STUDGROUPING2 . '-' . $academicyear;
        $applicationrecord  = DB::table('cre_pastapplicationstwo')
            ->where('IDNO', $idno)
            ->orwhere('ADMISSIONO', $result['ADMISSIONNUMBER'])
            ->get();
        //dd($applicationrecord);

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
                    DB::table('tbl_late_applicants')->insert($latedata);


                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);
                    DB::table('tbl_late_applicants')->insert($latedata);


                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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
                    DB::table('tbl_late_applicants')->insert($latedata);


                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);
                    DB::table('tbl_late_applicants')->insert($latedata);


                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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
                DB::table('tbl_late_applicants')->insert($latedata);


                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
            } else {

                DB::table('cre_pastapplicationstwo')->insert($updateData);

                DB::table('tbl_nfm_enabled')->insert($data);
                DB::table('tbl_late_applicants')->insert($latedata);


                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
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
            DB::table('tbl_late_applicants')->insert($latedata);


            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        } else {

            DB::table('cre_pastapplicationstwo')->insert($updateData);

            DB::table('tbl_nfm_enabled')->insert($data);
            DB::table('tbl_late_applicants')->insert($latedata);


            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        }
    }

    public function addinstitutionextra(Request $request, ApiController $apiController)
    {

        $user = auth()->user()->name;


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
        $TVET = $GRPresults['TVET'];
        $UG = $GRPresults['UG'];



        // $idno = $request->idno;
        // $productcode = $request->productcode;
        // $STUDGROUPING2 = $request->studentgrouping;
        // $product_id = $request->productid;
        // $academicyear = $request->academicyear;
        $applicationtype = $request->addinstitutiondetextra;
        $idno = $request->idinstitutionextra;

        list($product_id, $STUDGROUPING2, $academicyear, $productcode) = explode('|', $applicationtype);
        $arr = array(
            'idno' => $idno,
            'prod' => $productcode,
            'productcode' => $productcode,
            'group' => $STUDGROUPING2,
            'id' => $product_id,
            'academicyear' => $academicyear,
        );

        // $idno = $request->idno;
        // $arr = array(
        //     'idno' => $request->idno,
        //     'prod' => $request->productcode,
        //     'productcode' => $request->productcode,
        //     'group' => $request->studentgrouping,
        //     'id' => $request->productid,
        //     'academicyear' => $request->academicyear,
        // );

        //dd($arr);


        $cacheKey = 'blocked_nfm_' . $idno;
        $cacheDuration = 60; // Cache duration in minutes

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($idno) {
            return DB::table('tbl_blocked_nfm')
                ->where('idno', $idno)
                ->where('status', 'blocked')

                ->first();
        });

        if (!empty($data) && $data->idno === $idno) {
            $errorMessage = "ID number {$idno}  is restricted from accessing this service. Please contact lending team for assistance.";
            return response()->json(['error' => $errorMessage], 400);
        }









        $action = 'fetchSubsequentRecordsextra';
        $result = $apiController->mobiapis($action, $arr);
        if (empty($result)) {

            // return response()->json("AX connection unavailable");
            return response()->json("Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
        }

        if (!empty($result) && ($result['missing'] ?? false)) {


            return response()->json($result['missing']);
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
        $rules = [
            'LOANSERIALNO' => 'required',
            'ACCOUNTNUM' => 'required',
            'IDNO' => 'required',

            'INSTITUTIONCODE' => 'required',
            'ADMISSIONNUMBER' => 'required',
            'COURSECODE' => 'required',
            'COURSEDESCRIPTION' => 'required',


            'ADMISSIONYEAR' => 'required',
            'YEAROFSTUDY' => 'required',

            'ACADEMIC_YEAR' => 'required',
            'PRODUCTCODE' => 'required',
            'INSTITUTIONNAME' => 'required',

            'indexno' => 'required',
            'examsityr' => 'required',
        ];

        // Custom error messages for each field
        $messages = [
            'LOANSERIALNO.required' => 'LOANSERIALNO field is required.',
            'ACCOUNTNUM.required' => 'ACCOUNTNUM field is required.',
            'IDNO.required' => 'IDNO field is required.',

            'INSTITUTIONCODE.required' => 'INSTITUTIONCODE field is required.',
            'ADMISSIONNUMBER.required' => 'ADMISSIONNUMBER field is required.',
            'COURSECODE.required' => 'COURSECODE field is required.',
            'COURSEDESCRIPTION.required' => 'COURSEDESCRIPTION field is required.',


            'ADMISSIONYEAR.required' => 'ADMISSIONYEAR field is required.',
            'YEAROFSTUDY.required' => 'YEAROFSTUDY field is required.',

            'ACADEMIC_YEAR.required' => 'ACADEMIC_YEAR field is required.',
            'PRODUCTCODE.required' => 'PRODUCTCODE field is required.',
            'INSTITUTIONNAME.required' => 'INSTITUTIONNAME field is required.',

            'indexno.required' => 'indexno field is required.',
            'examsityr.required' => 'examsityr field is required.',
        ];

        // Run validation
        $validator = Validator::make($result, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the first validation error message
            //$errorMessage = $validator->errors()->first();
            $errorMessage = 'verify if institution details exist on AX';

            return response()->json(['error' => $errorMessage], 400);
        }



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
            'added_by' => $user,
            'acad_year' => $academicyear,
        ];

        $message = $user . ' has SSQ enabled  ' . $idno . ' - ' . ' for product ' . $STUDGROUPING2 . '-' . $academicyear;
        $applicationrecord  = DB::table('cre_pastapplicationstwo')
            ->where('IDNO', $idno)
            ->orwhere('ADMISSIONO', $result['ADMISSIONNUMBER'])
            ->get();
        //dd($applicationrecord);

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

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                }
            } else {

                $updateData['qualifiedloanmodel'] = '1';
                //$updateData['qualifiedscholarship'] = '0';


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

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
                } else {

                    DB::table('cre_pastapplicationstwo')->insert($updateData);

                    DB::table('tbl_nfm_enabled')->insert($data);

                    return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
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

                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
            } else {

                DB::table('cre_pastapplicationstwo')->insert($updateData);

                DB::table('tbl_nfm_enabled')->insert($data);

                return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel);
            }
        }


        $qualifiedloanmodel = 'OFM';
        $qualifiedscholarship = 'NO';
        $qualifiedboth  = 'NO';
        $updateData['qualifiedloanmodel'] = '1';

        dd($applicationrecord);


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

            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        } else {

            DB::table('cre_pastapplicationstwo')->insert($updateData);

            DB::table('tbl_nfm_enabled')->insert($data);

            return response()->json($message . ' qualifiedloanmodel:' . $qualifiedloanmodel . ' qualifiedscholarship' . $qualifiedscholarship);
        }
    }



    public function pushinstitution($request, ApiController $apiController)
    {


        //
        //dd($request);

        $idno = $request['idno'];

        //dd($idno);
        $productcode = $request['productcode'];
        $STUDGROUPING2 = $request['STUDGROUPING2'];
        $product_id = $request['product_id'];
        $academicyear = $request['academicyear'];

        $arr = array(
            'idno' => $idno,
            'prod' => $productcode,
            'productcode' => $productcode,
            'group' => $STUDGROUPING2,
            'id' => $product_id,
            'academicyear' => $academicyear,
        );

        //dd($arr);












        $action = 'fetchSubsequentRecordsextra';
        $result = $apiController->mobiapis($action, $arr);
        if (empty($result)) {

            return 'instfailed: ' . $idno;

            // return response()->json("AX connection unavailable");
            /// return response()->json("Check if institution exist on AX for the specific loan product.UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to  the new  course and its  duration on AX");
        }

        if (!empty($result) && ($result['missing'] ?? false)) {

            return 'instmissing: ' . $idno;

            //return response()->json($result['missing']);
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
        $rules = [
            'LOANSERIALNO' => 'required',
            'ACCOUNTNUM' => 'required',
            'IDNO' => 'required',

            'INSTITUTIONCODE' => 'required',
            'ADMISSIONNUMBER' => 'required',
            'COURSECODE' => 'required',
            'COURSEDESCRIPTION' => 'required',


            'ADMISSIONYEAR' => 'required',
            'YEAROFSTUDY' => 'required',

            'ACADEMIC_YEAR' => 'required',
            'PRODUCTCODE' => 'required',
            'INSTITUTIONNAME' => 'required',

            'indexno' => 'required',
            'examsityr' => 'required',
        ];

        // Custom error messages for each field
        $messages = [
            'LOANSERIALNO.required' => 'LOANSERIALNO field is required.',
            'ACCOUNTNUM.required' => 'ACCOUNTNUM field is required.',
            'IDNO.required' => 'IDNO field is required.',

            'INSTITUTIONCODE.required' => 'INSTITUTIONCODE field is required.',
            'ADMISSIONNUMBER.required' => 'ADMISSIONNUMBER field is required.',
            'COURSECODE.required' => 'COURSECODE field is required.',
            'COURSEDESCRIPTION.required' => 'COURSEDESCRIPTION field is required.',


            'ADMISSIONYEAR.required' => 'ADMISSIONYEAR field is required.',
            'YEAROFSTUDY.required' => 'YEAROFSTUDY field is required.',

            'ACADEMIC_YEAR.required' => 'ACADEMIC_YEAR field is required.',
            'PRODUCTCODE.required' => 'PRODUCTCODE field is required.',
            'INSTITUTIONNAME.required' => 'INSTITUTIONNAME field is required.',

            'indexno.required' => 'indexno field is required.',
            'examsityr.required' => 'examsityr field is required.',
        ];

        // Run validation
        $validator = Validator::make($result, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the first validation error message
            //$errorMessage = $validator->errors()->first();
            $errorMessage = 'verify if institution details exist on AX';

            return  $errorMessage;

            //return response()->json(['error' => $errorMessage], 400);
        }



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

        return  'success update' . $idno;
    }




    public function updatepaymentdetails(Request $request, ApiController $apiController)
    {

        //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $cacheKey = "productparam";

        DB::table('tbl_products_submit_new')
            //  ->whereDate('date_loan_submit', $date_now) // Filter by current date
            //  ->whereIn('productcode', ['5637170076', '5637167826', '5637162576'])
            // ->where('submittedloan', '1') // Filter by current date
            //  ->where('sentoAX', '0') // Filter by current date
            ->orderBy('id', 'ASC')
            // ->where('serial_number', '2420000092') // Filter by current date
            //->whereIn('serial_number', ['2420035203', '2420027450', '2420044961'])
            ->whereIn('serial_number', [
                '2420028100',
                '2420271458',
                '2420376307',
                '2420418032',
                '2420043025',
                '2420216837',
                '2420424084',
                '2420044139',
                '2420018877',
                '2420005341',
                '2420376434',
                '2420016836',
                '2420369240',
                '2420057494',
                '2420059867',
                '2420016574',
                '2420443614',
                '2420045392',
                '2420006027',
                '2420046037',
                '2420029755',
                '2420443674',
                '2420072566',
                '2420020716',
                '2420056362',
                '2420035983',
                '2420066692',
                '2420010866',
                '2420036751',
                '2420387243',
                '2420021511',
                '2420025098',
                '2420017851',
                '2420070743',
                '2420376488',
                '2420035393',
                '2420113241',
                '2420443584',
                '2420045505',
                '2420072277',
                '2420032506',
                '2420067185',
                '2420058360',
                '2420056698',
                '2420053274',
                '2420443487',
                '2420038797',
                '2420051206',
                '2420009499',
                '2420033856',
                '2420030673',
                '2420038057',
                '2420009425',
                '2420033737',
                '2420443512',
                '2420376455',
                '2420019319',
                '2420056251',
                '2420062174',
                '2420073529',
                '2420065488',
                '2420057986',
                '2420037710',
                '2420064319',
                '2420013077',
                '2420038434',
                '2420052729',
                '2420050306',
                '2420095186',
                '2420443605',
                '2420025198',
                '2420015488',
                '2420019436',
                '2420029344',
                '2420010835',
                '2420070581',
                '2420329420',
                '2420040547',
                '2420058862',
                '2420443148',
                '2420259901',
                '2420443503',
                '2420023129',
                '2420376458',
                '2420032924',
                '2420016351',
                '2420376405',
                '2420035201',
                '2420035203',
                '2420038385',
                '2420201046',
                '2420065900',
                '2420022797',
                '2420439145',
                '2420043402',
                '2420000941',
                '2420015658',
                '2420013288',
                '2420027450',
                '2420004168',
                '2420034446',
                '2420018294',
                '2420031481',
                '2420035250',
                '2420248324',
                '2420443513',
                '2420032411',
                '2420011074',
                '2420025785',
                '2420265114',
                '2420029555',
                '2420037913',
                '2420443697',
                '2420443551',
                '2420443494',
                '2420443244',
                '2420053403',
                '2420376041',
                '2420376391',
                '2420024767',
                '2420065424',
                '2420037401',
                '2420020187',
                '2420061164',
                '2420443710',
                '2420443678',
                '2420097270',
                '2420071748',
                '2420064842',
                '2420042229',
                '2420428589',
                '2420030658',
                '2420115646',
                '2420439480',
                '2420408842',
                '2420031724',
                '2420058366',
                '2420044710',
                '2420015669',
                '2420058322',
                '2420003204',
                '2420031014',
                '2420295689',
                '2420013141',
                '2420032910',
                '2420063952',
                '2420026075',
                '2420358460',
                '2420443670',
                '2420000508',
                '2420021658',
                '2420015738',
                '2420017937',
                '2420028469',
                '2420056509',
                '2420094960',
                '2420443483',
                '2420002996',
                '2420031274',
                '2420443715',
                '2420006678',
                '2420376387',
                '2420039815',
                '2420281564',
                '2420024140',
                '2420007599',
                '2420063922',
                '2420030888',
                '2420284689',
                '2420017152',
                '2420376429',
                '2420018437',
                '2420028043',
                '2420063103',
                '2420059128',
                '2420041644',
                '2420062188',
                '2420015541',
                '2420028822',
                '2420376517',
                '2420033548',
                '2420024605',
                '2420009184',
                '2420443591',
                '2420095010',
                '2420057466',
                '2420042924',
                '2420011073',
                '2420443724',
                '2420010396',
                '2420376388',
                '2420376313',
                '2420443666',
                '2420023005',
                '2420035745',
                '2420065722',
                '2420002926',
                '2420070263',
                '2420043938',
                '2420005087',
                '2420035008',
                '2420018567',
                '2420443642',
                '2420037970',
                '2420371407',
                '2420059754',
                '2420031429',
                '2420042239',
                '2420376297',
                '2420033934',
                '2420037117',
                '2420031803',
                '2420037845',
                '2420376331',
                '2420052126',
                '2420011118',
                '2420034559',
                '2420020240',
                '2420443514',
                '2420097406',
                '2420065011',
                '2420036716',
                '2420294629',
                '2420031831',
                '2420057050',
                '2420038306',
                '2420067257',
                '2420062577',
                '2420035632',
                '2420019220',
                '2420006771',
                '2420074109',
                '2420045448',
                '2420026662',
                '2420034745',
                '2420425451',
                '2420095434',
                '2420003827',
                '2420026572',
                '2420038424',
                '2420443729',
                '2420074425',
                '2420017793',
                '2420443700',
                '2420031406',
                '2420074207',
                '2420051471',
                '2420443714',
                '2420044371',
                '2420376076',
                '2420443648',
                '2420002806',
                '2420015979',
                '2420072620',
                '2420009424',
                '2420064681',
                '2420001602',
                '2420008231',
                '2420045493',
                '2420443539',
                '2420018058',
                '2420354116',
                '2420443635',
                '2420058445',
                '2420030451',
                '2420376296',
                '2420004454',
                '2420018124',
                '2420376437',
                '2420063756',
                '2420030255',
                '2420443711',
                '2420034561',
                '2420036187',
                '2420055219',
                '2420060551',
                '2420033944',
                '2420059219',
                '2420374223',
                '2420000296',
                '2420032466',
                '2420031248',
                '2420058937',
                '2420025829',
                '2420376503',
                '2420351467',
                '2420443620',
                '2420014566',
                '2420376377',
                '2420376530',
                '2420376315',
                '2420015782',
                '2420376304',
                '2420441204',
                '2420062786',
                '2420006010',
                '2420030921',
                '2420415312',
                '2420016490',
                '2420064252',
                '2420260624',
                '2420376376',
                '2420053660',
                '2420376492',
                '2420013810',
                '2420016602',
                '2420051676',
                '2420443645',
                '2420376306',
                '2420096755',
                '2420443689',
                '2420031402',
                '2420005901',
                '2420033981',
                '2420001491',
                '2420002040',
                '2420043577',
                '2420436927',
                '2420065183',
                '2420417726',
                '2420032649',
                '2420443485',
                '2420023209',
                '2420006346',
                '2420039658',
                '2420025956',
                '2420039575',
                '2420003809',
                '2420416986',
                '2420032738',
                '2420027909',
                '2420058097',
                '2420020256',
                '2420443637',
                '2420032613',
                '2420042615',
                '2420007089',
                '2420415446',
                '2420014195',
                '2420000282',
                '2420045364',
                '2420443493',
                '2420443732',
                '2420285693',
                '2420043472',
                '2420034455',
                '2420095780',
                '2420057922',
                '2420443726',
                '2420043062',
                '2420376338',
                '2420068117',
                '2420000532',
                '2420443681',
                '2420443595',
                '2420443616',
                '2420054396',
                '2420375350',
                '2420064980',
                '2420443585',
                '2420016299',
                '2420036804',
                '2420034523',
                '2420096761',
                '2420051184',
                '2420066386',
                '2420062332',
                '2420443682',
                '2420061554',
                '2420181469',
                '2420376406',
                '2420366469',
                '2420046258',
                '2420027557',
                '2420034005',
                '2420035695',
                '2420003753',
                '2420443722',
                '2420067941',
                '2420443515',
                '2420051892',
                '2420065411',
                '2420376402',
                '2420035080',
                '2420063871',
                '2420048663',
                '2420443572',
                '2420029923',
                '2420038327',
                '2420376342',
                '2420015372',
                '2420058375',
                '2420020912',
                '2420019776',
                '2420443704',
                '2420011751',
                '2420036260',
                '2420012688',
                '2420009533',
                '2420022969',
                '2420032836',
                '2420024716',
                '2420031266',
                '2420061727',
                '2420442713',
                '2420443628',
                '2420376454',
                '2420376356',
                '2420443357',
                '2420375818',
                '2420066178',
                '2420039788',
                '2420028238',
                '2420062363',
                '2420062609',
                '2420024610',
                '2420058044',
                '2420376483',
                '2420017364',
                '2420437700',
                '2420033739',
                '2420009736',
                '2420009792',
                '2420376444',
                '2420376281',
                '2420018351',
                '2420029117',
                '2420056002',
                '2420376438',
                '2420059121',
                '2420046027',
                '2420443574',
                '2420063655',
                '2420003931',
                '2420376516',
                '2420074102',
                '2420443707',
                '2420376373',
                '2420043007',
                '2420443505',
                '2420443671',
                '2420440006',
                '2420420983',
                '2420050835',
                '2420443665',
                '2420411179',
                '2420030670',
                '2420440824'
            ])



            // ->get();

            ->chunk(50, function ($submittedata) use ($cacheKey) {


                //dd($submittedata);



                if ($submittedata->isEmpty()) {
                    // The collection is empty
                    echo "No records found for today.";
                } else {
                    // The collection is not empty




                    foreach ($submittedata as $record) {

                        $idnumber = $record->idno;
                        $serial_number = $record->serial_number;



                        $mobileoption = DB::table('safaricomkyclogs')

                            ->where('status', 'true')
                            ->where('nationalidno', $idnumber)

                            ->first();


                        if (!empty($mobileoption)) {

                            $phone = $mobileoption->phone;

                            //  DB::update("UPDATE tbl_products_submit_new SET disbursementoption = ? ,disbursementoptionvalue = ? WHERE serial_number = ?", ['mobile', $phone,$serial_number]);


                            try {
                                $updated = DB::update("UPDATE tbl_products_submit_new SET disbursementoption = ?, disbursementoptionvalue = ? WHERE serial_number = ?", ['mobile', $phone, $serial_number]);

                                if ($updated) {
                                    echo "Record mobile updated successfully." . $serial_number;
                                } else {
                                    echo "No records mobile  were updated." . $serial_number;
                                }
                            } catch (\Exception $e) {
                                // Handle error
                                echo "Error: " . $e->getMessage();
                            }
                        } else {




                            // $apicontroller = '';
                            $action = 'fetchbankax';
                            $arr = array('idno' => $idnumber);
                            $apiController =  new ApiController();
                            $result = $apiController->mobiapis($action, $arr);


                            if (!is_array($result)) {
                                $result = [$result];
                                // $result = [];

                            }

                            if (Arr::has($result, 'bankaccountnumber')) {

                                $bankaccountnumber = $result['bankaccountnumber'];



                                try {
                                    $updated = DB::update("UPDATE tbl_products_submit_new SET disbursementoption = ?, disbursementoptionvalue = ? WHERE serial_number = ?", ['bank', $bankaccountnumber, $serial_number]);

                                    if ($updated) {

                                        echo "Record bank updated successfully." . $serial_number;
                                    } else {
                                        echo "No records bank  were updated." . $serial_number;
                                    }
                                } catch (\Exception $e) {
                                    // Handle error
                                    echo "Error: " . $e->getMessage();
                                }
                            } else {

                                echo "No records bank and mobile were updated." . $serial_number;
                            }
                        }
                    }
                }
            });
        // $this->info('Loan processing completed.');








    }



    public function getBilledInfo(Request $request)
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






        try {
            $billed = DB::connection('sqlsrv')->select(
                "
            SELECT TOP 1 
                b.rateofpayment AS repaymentrate,
                b.accountnum AS accountnumber,
                (SELECT MAX(bb.billdate) FROM LMCONFIRMEDBILLINGline bb) AS billdate,
                c.BILLINGBATCHNO AS batchno
            FROM LMCONFIRMEDBILLINGline b
            LEFT JOIN LMCONFIRMEDBILLINGHEADER c ON b.BILLINGBATCHNO = c.BILLINGBATCHNO
            WHERE b.NATIONALIDNO = ?
            ORDER BY b.rateofpayment DESC
            ",
                [$idno]
            );

            return response()->json($billed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function getMiniAccountInfo(Request $request)
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






        try {
            $billed = DB::connection('sqlsrv')->select(
                "
            SELECT top 1
b.IDENTITYREFERENCENO as IDNO, i.PHONENUMBER as PAYMENTNUMBER,i.MODIFIEDDATETIME,i.createddatetime   from LMIDENTIFICATIONDOCUMENTS b


left join lmmobilepaymentcontacts i on b.ACCOUNTNUM=i.STUDENTACCOUNTNUM
where
b.IDENTITYREFERENCENO = ?


and i.ACTIVE = '1'
ORDER BY MODIFIEDDATETIME DESC
            ",
                [$idno]
            );

            return response()->json($billed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getLoanStatementInfo(Request $request)
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






        try {
            $billed = DB::connection('sqlsrv')->select(
                "
           SELECT 
    b.lastrepayment AS lastrepaymentdate,
    b.outstandingloaninterest AS outstandinginterest,
    b.outstandingloanpenalty AS outstandingpenalty,
    b.outstandingprincipalbalance AS outstandingprincipal,
    b.accruedinterest AS accruedinterest,
    b.runningloanbalance AS runningloanbalance,
    b.loanprincipalamount AS loanprincipal,

    (
        SELECT SUM(sub_b.runningloanbalance)
        FROM LMIDENTIFICATIONDOCUMENTS sub_a
        LEFT JOIN lmloans sub_b ON sub_a.ACCOUNTNUM = sub_b.ACCOUNTNUM
        WHERE sub_a.IDENTITYREFERENCENO = ?
          AND sub_b.LOANPRODUCTCODE = b.LOANPRODUCTCODE
    ) AS totalloanbalance,

    (
        SELECT SUM(c.amountcurcredit)
        FROM ledgerjournaltrans c
        LEFT JOIN ledgerjournaltable d ON c.journalnum = d.journalnum
        LEFT JOIN lmloans e ON c.LOANSERIALNO = e.LOANSERIALNO
        LEFT JOIN LMIDENTIFICATIONDOCUMENTS f ON e.ACCOUNTNUM = f.ACCOUNTNUM
        WHERE f.IDENTITYREFERENCENO = ?
          AND e.LOANPRODUCTCODE = b.LOANPRODUCTCODE
          AND c.receipttransactiontype IN ('1', '2')
          AND d.posted = '1'
    ) AS totalamountpaid,

    c.PRODUCTDESCRIPTION AS productdesc,
    d.LOANSPRODUCTCODE AS productname,
    b.runningloanbalance AS runningloanbalance

FROM LMIDENTIFICATIONDOCUMENTS a
LEFT JOIN lmloans b ON a.ACCOUNTNUM = b.ACCOUNTNUM
LEFT JOIN LMLOANPRODUCTTYPE c ON b.LOANPRODUCTCODE = c.RECID
LEFT JOIN LMLOANPRODUCTTYPESDEFINITION d ON b.LOANPRODUCTCODE = d.RECID

WHERE a.IDENTITYREFERENCENO = ?
  AND b.runningloanbalance != 0
            ",
                [$idno, $idno, $idno]
            );

            return response()->json($billed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLoanStatusInfo(Request $request)
    {
        $idno = $request->input('idno');
        $academicyear = $request->input('academicyear');


        $rules = [
            'idno' => 'required',
            'academicyear' => 'required'

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }






        try {
            $billed = DB::connection('sqlsrv')->select(
                "
           select a.ACADEMICYEAR as academicyear,
       a.APPLICANTTYPE as applicanttype,       f.ACTION as action,       f.REFDOCUMENTNO as batchno,f.INSTITUTIONCODE as institution,


       b.PRODUCTDESCRIPTION as productname,
       a.Loanstatus as loanstatus,
      CONVERT(VARCHAR(10),f.DATEPROCESSED,103)  as loanprocessingdate,
       e.admissioncategory as admissioncategory
       from lmloans a
       left join LMLOANPRODUCTTYPE b
       on a.LOANPRODUCTCODE = b.RECID
       left join LMIDENTIFICATIONDOCUMENTS c
       on a.ACCOUNTNUM = c.ACCOUNTNUM
       left join LMSTUDENTINSTITUTION e
       on a.LOANSERIALNO = e.LOANSERIALNO
       left join LMLOANPROCESSINGHISTORY f
       on a.LOANSERIALNO = f.LOANSERIALNO
       where c.IDENTITYREFERENCENO=?
       and a.ACADEMICYEAR =?
       and f.DATEPROCESSED is not NULL
     and f.LOANTRANSACTIONTYPE != '11'
            ",
                [$idno, $academicyear]
            );

            return response()->json($billed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getKuccpsInfo(Request $request)
    {
        $idno = $request->input('idno');
        $academicyear = $request->input('academicyear');


        $rules = [
            'idno' => 'required',
            'academicyear' => 'required'

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }






        try {
            $billed = DB::connection('sqlsrv')->select(
                "
           select a.ACADEMICYEAR as academicyear,
       a.APPLICANTTYPE as applicanttype,       f.ACTION as action,       f.REFDOCUMENTNO as batchno,f.INSTITUTIONCODE as institution,


       b.PRODUCTDESCRIPTION as productname,
       a.Loanstatus as loanstatus,
      CONVERT(VARCHAR(10),f.DATEPROCESSED,103)  as loanprocessingdate,
       e.admissioncategory as admissioncategory
       from lmloans a
       left join LMLOANPRODUCTTYPE b
       on a.LOANPRODUCTCODE = b.RECID
       left join LMIDENTIFICATIONDOCUMENTS c
       on a.ACCOUNTNUM = c.ACCOUNTNUM
       left join LMSTUDENTINSTITUTION e
       on a.LOANSERIALNO = e.LOANSERIALNO
       left join LMLOANPROCESSINGHISTORY f
       on a.LOANSERIALNO = f.LOANSERIALNO
       where c.IDENTITYREFERENCENO=?
       and a.ACADEMICYEAR =?
       and f.DATEPROCESSED is not NULL
     and f.LOANTRANSACTIONTYPE != '11'
            ",
                [$idno, $academicyear]
            );

            return response()->json($billed);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getBalanceInfo(Request $request)
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


        try {
            $row = DB::connection('sqlsrv')->selectOne(
                "
            SELECT 
                MAX(b.lastrepayment) AS lastrepaymentdate,
                COUNT(b.runningloanbalance) AS recs,
                CEILING(SUM(b.runningloanbalance)) AS runningloanbalance
            FROM LMIDENTIFICATIONDOCUMENTS a
            LEFT JOIN lmloans b ON a.ACCOUNTNUM = b.ACCOUNTNUM
            WHERE a.IDENTITYREFERENCENO = ?
            ",
                [$idno]
            );

            return response()->json([
                'runningloanbalance' => round($row->runningloanbalance ?? 0),
                'count' => $row->recs ?? 0,
                'lastrepaymentdate' => $row->lastrepaymentdate ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function importblock(Request $request)

    {

        //dd($request);

        try {
            Excel::import(new BlockpaysImport, request()->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            if (!empty($failures)) {
                return  response()->json($failures);
            }
        }
        //  return redirect()->route('smsmodule')->with('success', 'User Imported Successfully');

        return back();
    }





    public function downloadblocktemplate()
    {




        $template = 'bnk.csv';
        $headers = [
            'Content-Type:  application/force-download',
            'Content-disposition: attachment; filename=bnk.csv',
            'Cache-Control: must-revalidate',
            'post-check: 0',
            'pre-check: 0',
            'Expires: 0',
            'Pragma: public'
        ];

        $file = 'public/templates/bnk.csv';

        $path = Storage::path($file);


        return response()->download($path, $template, $headers);
    }






    public function pushloanstostagingax(Request $request, ApiController $apiController)
    {
        //$currentDate = date('Y-m-d'); // Get the current date in Y-m-d format
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $cacheKey = "productparam";

        DB::table('tbl_products_submit_new')
            //  ->whereDate('date_loan_submit', $date_now) // Filter by current date
            //  ->whereIn('productcode', ['5637170076', '5637167826', '5637162576'])
            ->where('submittedloan', '1') // Filter by current date
            ->where('sentoAX', '0') // Filter by current date
            ->orderBy('id', 'ASC')
            //  ->take(2) // Limit to only 2 records
            ->where('serial_number', '2420453187') // Filter by current date
            //->whereIn('serial_number', ['2420035203', '2420027450', '2420044961'])


            ->chunk(1000, function ($submittedata) use ($cacheKey) {


                // dd($submittedata);



                // Attempt to retrieve data from cache
                $ussd_products_test = Cache::remember($cacheKey, now()->addMinutes(1), function () {
                    return DB::table('ussd_products_test')->get();
                });

                // Retrieve the cached data
                $datacached = Cache::get($cacheKey);
                // dd($datacached);
                // Get the value of the product where id is 2


                if ($submittedata->isEmpty()) {
                    // The collection is empty
                    echo "No records found for today.";
                } else {
                    // The collection is not empty




                    foreach ($submittedata as $record) {

                        $disbursementoption = $record->disbursementoption;

                        if ($disbursementoption == 'mobile') {
                            $MOBILEPAYMENT = '1';
                        } else {

                            $MOBILEPAYMENT = '0';
                        }

                        //dd($disbursementoption.''.$MOBILEPAYMENT);

                        $query = DB::select("SELECT start_serial2 FROM tbl_recid_setup");
                        $recid = $query[0]->start_serial2;
                        $recid = (string) $recid;


                        // $added = DB::connection('sqlsrv')->table('DMLOANS')->insert($datasubmitted);
                        $query = DB::connection('sqlsrv')->select("SELECT RECID FROM DMLOANS WHERE RECID = ?", [$recid]);

                        //$recid = $recid;
                        // dd($query);

                        if (empty($query)) {
                            $nwrecid = $recid + 1;
                            $product = $datacached->where('productcode', $record->productcode)
                                // ->where('IDNO', $record->idno) // Filter by current date


                                ->first();

                            // dd( $product);
                            $submittedatainst =  DB::table('dminstitututions_2024')
                                ->where('IDNO', $record->idno) // Filter by current date
                                ->where('Productcode', $record->productcode) // Filter by current date

                                ->first();
                            if (!empty($submittedatainst)) {
                                echo "Records found:" . $record->serial_number;


                                $datasubmitted = [
                                    'AcademicYear' => $record->acad_year,
                                    'AccountNum' => $submittedatainst->ACCOUNTNUM,
                                    'AmountRaisable' => 0,
                                    'ApplicationDate' => Carbon::parse($record->date_loan_submit)->format('Y-m-d'),
                                    'AttachmentsVerified' => 0,
                                    'DeclarationVerified' => 0,
                                    'BursaryRequested' => 1,
                                    'CompletionDate' => '1900-01-01',
                                    'AmountRequested' => 60000.00,
                                    'LoanCleared' => 0,
                                    'LoanProductCode' => $product->studentgrouping,
                                    'LoanRegistered' => 1,
                                    'LoanRegistrationDate' => Carbon::parse($record->date_loan_submit)->format('Y-m-d'),
                                    'LoanSerialNo' => $record->serial_number,
                                    'LoanStatus' => '2',
                                    'LoanVerifiedDate' => Carbon::parse($record->date_loan_submit)->format('Y-m-d'),
                                    'LoanVerified' => 1,
                                    'NeedsBursary' => 1,
                                    'PaymentDueDate' => '1900-01-01',
                                    'ProcessingFeePaid' => 0,
                                    'RegisteredBy' => $record->source,
                                    'VerifiedBy' => 'Admin',
                                    'MaturityDate' => '1900-01-01',
                                    'ApplicantType' => 2,
                                    'Aggregated' => 0,
                                    'RECID' => $recid,
                                    'DATAAREAID' => 'helb',
                                    'IDNO' => '0',
                                    'id_no' => $record->idno,
                                    'MOBILEPAYMENT' => $MOBILEPAYMENT,
                                    'SCHOLARSHIPAPPLIED' => $record->submittedscholarship




                                ];


                                // $instarray = [
                                //     'AdmissionCategory' => $submittedatainst->ADMISSIONCATEGORY,
                                //     'AdmissionNumber' => $submittedatainst->ADMISSIONNUMBER,
                                //     'AdmiYear' => $submittedatainst->ADMISSIONYEAR,
                                //     'AnnualFees' => 0.00,
                                //     'InstitutionCode' => $submittedatainst->INSTITUTIONCODE,
                                //     'InstitutionBranchCode' => $submittedatainst->INSTITUTIONBRANCHCODE,
                                //     'AcademicYear' => $record->acad_year,
                                //     'YearOfStudy' =>  (int)$submittedatainst->YEAROFSTUDY + 1,
                                //     'CourseCode' => $submittedatainst->COURSECODE,
                                //     'LoanSerialNo' =>  $record->serial_number,
                                //     'CountryCode' => 'KE',
                                //     'LevelOfStudy' => $submittedatainst->LEVELOFSTUDY,
                                //     'InstitutionType' => $submittedatainst->STUDENTINSTITUTIONTYPE,
                                //     'CURRENT_' => 1,
                                //     'RECID' => $recid,
                                //     'DATAAREAID' => 'helb',
                                //     'ACCOUNTNUM' => $submittedatainst->ACCOUNTNUM,
                                //    // 'ENDYEAR' => $submittedatainst->ENDYEAR,


                                // ];

                                $paymentarray = [
                                    'ACTIVE' => '1',
                                    'LOANSERIALNUMBER' => $record->serial_number,
                                    'PHONENUMBER' => $record->disbursementoptionvalue,
                                    'STUDENTACCOUNTNUM' => $submittedatainst->ACCOUNTNUM,
                                    'DATAAREAID' => 'helb',
                                    'RECVERSION' => '1',
                                    'PARTITION' => '5637144576',
                                    'RECID' => $recid,
                                    'EXPORTED' => '0',



                                ];

                                //dd($datasubmitted);
                                // dd($instarray);
                                // dd($paymentarray);
                                //dd($datasubmitted.'-'.$instarray.'-'.$paymentarray);
                                //  dd(json_encode($datasubmitted) . '-' . json_encode($instarray) . '-' . json_encode($paymentarray));


                                try {


                                    DB::connection('sqlsrv')->transaction(function () use ($datasubmitted, $MOBILEPAYMENT, $paymentarray) {
                                        // Insert into DMSTUDENTINSTITUTIONDETAILS
                                        // $addedt = DB::connection('sqlsrv')->table('DMSTUDENTINSTITUTIONDETAILS')->insert($instarray);

                                        // Insert into DMLOANS
                                        $added = DB::connection('sqlsrv')->table('DMLOANS')->insert($datasubmitted);

                                        if ($MOBILEPAYMENT == '1') {
                                            //  $addedthree = DB::connection('sqlsrv')->table('DMMOBILEPAYMENTCONTACTS')->insert($paymentarray);

                                            $exists = DB::connection('sqlsrv')
                                                ->table('DMMOBILEPAYMENTCONTACTS')
                                                ->where('PHONENUMBER', $paymentarray['PHONENUMBER']) // Replace 'unique_column' with your actual column
                                                ->exists();

                                            if ($exists) {
                                                // Update if record exists
                                                DB::connection('sqlsrv')
                                                    ->table('DMMOBILEPAYMENTCONTACTS')
                                                    ->where('PHONENUMBER', $paymentarray['PHONENUMBER']) // Replace 'unique_column' with your actual column
                                                    ->update([
                                                        'PHONENUMBER' => $paymentarray['PHONENUMBER'], // Replace with the actual columns to update
                                                        'LOANSERIALNUMBER' => $paymentarray['LOANSERIALNUMBER'], // Add more columns as needed
                                                        'STUDENTACCOUNTNUM' => $paymentarray['STUDENTACCOUNTNUM'], // Add more columns as needed
                                                        'RECID' => $paymentarray['RECID'], // Add more columns as needed




                                                    ]);
                                            } else {
                                                // Insert if record does not exist
                                                $addedthree = DB::connection('sqlsrv')->table('DMMOBILEPAYMENTCONTACTS')->insert($paymentarray);
                                            }
                                        }
                                    });




                                    DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);

                                    DB::update("UPDATE tbl_products_submit_new SET sentoAX = ? WHERE serial_number = ?", ["1", $record->serial_number]);
                                    echo " Record updated:" . $record->serial_number;
                                } catch (\Illuminate\Database\QueryException $e) {
                                    DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);

                                    DB::update("UPDATE tbl_products_submit_new SET sentoAX = ? WHERE serial_number = ?", ["0", $record->serial_number]);

                                    echo 'Insert failed: ' . $record->serial_number . ' ' . $e->getMessage();

                                    Log::error('Insert failed: ' . $record->serial_number . ' ' . $e->getMessage());
                                }
                            } else {

                                $request = [
                                    'idno' => $record->idno,
                                    'productcode' => $record->productcode,
                                    'STUDGROUPING2' => $product->studentgrouping,
                                    'product_id' => $product->productid,
                                    'academicyear' => $record->acad_year,
                                ];
                                $apiController = new ApiController();
                                $results = $this->pushinstitution($request, $apiController);

                                echo $results;



                                DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);

                                echo "institution Record not found:" . $record->serial_number;
                            }
                        }
                        DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);
                    }
                }
            });
        // $this->info('Loan processing completed.');


    }

    public function phonenumberverify(Request $request, ApiController $apiController)
    {


        $user = auth()->user()->name;
        $phonenumberdropdown = $request->phonenumberdropdown;
        $firstname = $request->idphoneverifyname;

        $cellphone = $request->phoneverify;
        $idnumber = $request->idphoneverify;




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

        $cellphone = preg_replace('/\D/', '', $cellphone);




        switch ($phonenumberdropdown) {
            case 'safaricom':
                // dd("saf");

                // Code for safaricom
                $data = $this->kycregister($idnumber, $cellphone, $apiController);
                return $data;
            case 'airtel':
                //dd($firstname);

                $data = $this->kycregisterairtel($idnumber, $cellphone, $firstname, $apiController);

                return $data;
            default:
                //dd("non");

                // Code for other cases
                $data = $this->kycregister($idnumber, $cellphone, $apiController);
                return $data;
        }





        //return response()->json($result);


    }

    function kycregisterairtel($idnumber, $cellphone, $firstname, $apiController)
    {


        // dd("jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj");

        $trimmedCellphone = substr($cellphone, 3);

        $data = DB::table('airtelkyclogs')
            ->select('status')
            ->where('msisdn', $trimmedCellphone)
            ->where('airtelname', 'like', '%' . $firstname . '%')
            ->where('created_date_time', '>=', DB::raw('NOW() - INTERVAL 3 MONTH'))
            ->orderBy('created_date_time', 'desc')
            ->first();
        // ->toArray();

        //dd($data);

        $status = $data->status ?? null; // Safely access the status property

        if ($status !== null && $status === 'true') {

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' successfully verified';
            $status = 'UP';
            $verified = 'yes';

            $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


            return $data;
        } else {

            $result = $apiController->kycregisterairtelapi($idnumber, $cellphone, $firstname, $apiController);


            return $result;
        }
    }







    function kycregister($idnumber, $cellphone, ApiController $apiController)
    {




        $data = DB::table('safaricomkyclogs')
            ->select('status')
            ->where('nationalidno', $idnumber)
            ->where('phone', $cellphone)
            ->where('created_date_time', '>=', DB::raw('NOW() - INTERVAL 3 MONTH'))
            ->orderBy('created_date_time', 'desc')
            ->first();
        // ->toArray();

        $status = $data->status ?? null; // Safely access the status property

        if ($status !== null && $status === 'true') {

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' successfully verified';
            $status = 'UP';
            $verified = 'yes';


            $data = json_encode(array('message' => $message, 'status' => $status, 'verified' => $verified));


            return $data;
        } else {

            $result = $apiController->validatesafaricom($idnumber, $cellphone);


            return $result;
        }
    }





    function ussdverify(Request $request, ApiController $apiController)
    {

        //dd($request);


        $phoneussd = $request->phoneussd;
        $numberussd = $request->numberussd;

        $action = 'ussdphonerecords';
        $arr = array('phone' => $phoneussd, 'idno' => $numberussd);
        $result = $apiController->mobiapis($action, $arr);
        //   dd($result);

        if (request()->ajax()) {
            return datatables()->of($result)

                ->addIndexColumn()
                ->make(true);
        }
    }

    function updateussd(Request $request, ApiController $apiController)
    {

        $uniqueuserid = $request->uniqueuserid;
        $user = auth()->user()->name;
        $code = $apiController->uniqueStr2(4);

        // dd($code);


        $action = 'unblockussd';
        $arr = array('userid' => $uniqueuserid, 'code' => $code);
        $result = $apiController->mobiapis($action, $arr);

        if (!is_array($result)) {
            $result = [$result];
        }


        $first_namestudent = $result[0]['first_name'];
        $phonestudent = $result[0]['user_name'];



        $query = "
        INSERT INTO tbl_ussd_unblockupdates (student_name, student_user_id, updated_by)
        VALUES (?, ?, ?)
    ";

        DB::insert($query, [$first_namestudent, $phonestudent, $user]);

        if (!empty($first_namestudent)) {

            $action = 'sendphoneverificationCode';
            $arr = array('recipient' => $phonestudent, 'verificationcode' => $code, 'msg_priority' => '203', 'category' => '391');

            $smsent = $apiController->datapull($action, $arr);
            //dd($smsent);




        }

        // dd($result);

        if (request()->ajax()) {
            return datatables()->of($result)

                ->addIndexColumn()
                ->make(true);
        }


        // dd($result);





    }



    public function updateussdnumber(Request $request, ApiController $apiController)
    {

        //  dd($request);


        $user = auth()->user()->name;
        $phonenumberdropdown = $request->phonenumberdropdown;
        $firstname = $request->idphoneverifyname;

        $cellphone = $request->phoneverify;
        $idnumber = $request->idphoneverify;


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





        switch ($phonenumberdropdown) {
            case 'safaricom':
                // dd("saf");

                // Code for safaricom
                $data = $this->kycregister($idnumber, $cellphone, $apiController);
                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];

                if ($verified === 'yes') {

                    // dd($verified);

                    $data = $this->replaceline($idnumber, $cellphone, $apiController);

                    return $data;
                } else {


                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 1cant be verified';



                    return json_encode(array('message' => $message));
                }


            case 'airtel':

                $data = $this->kycregisterairtel($idnumber, $cellphone, $firstname, $apiController);

                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];
                if ($verified === 'yes') {
                    $data = $this->replaceline($idnumber, $cellphone, $apiController);

                    return $data;
                } else {

                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 2cant be verified';



                    return json_encode(array('message' => $message));
                }
                break;
            default:
                //dd("non");

                // Code for other cases
                $data = $this->kycregister($idnumber, $cellphone, $apiController);
                $responseArray = json_decode($data, true);

                // Extract the message
                $verified = $responseArray['verified'];
                if ($verified === 'yes') {
                    $data = $this->replaceline($idnumber, $cellphone, $apiController);

                    return $data;
                } else {

                    $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' 3cant be verified';



                    return json_encode(array('message' => $message));
                }
        }





        //return response()->json($result);


    }


    function replaceline($idnumber, $cellphone, ApiController $apiController)
    {

        $user = auth()->user()->name;


        $action = 'updatetelco';
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

            return json_encode(array('message' => $missing));
        }

        if (!empty($code)) {

            if ($code == -14) {

                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' updated';

                return json_encode(array('message' => $message));
            } else {

                $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . 'not updated';

                return json_encode(array('message' => $message));
            }
        }

        if (!empty($result) && ($result === 'success')) {


            $insert = DB::table('tbl_ussd_updates')->insert([
                'student_name' => $cellphone,
                'student_id' => $idnumber,
                'updated_by' => $user,
            ]);

            $message = 'Phone number ' . $cellphone . ' for ID number ' . $idnumber . ' updated';

            //dd($message);


            return json_encode(array('message' => $message));
        }
        return json_encode(array('message' => (string) $result96));
    }


    public function indexusers()
    {
        $you = auth()->user();
        $users = User::all();


        if (request()->ajax()) {
            return datatables()->of($users)
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm edituser">Edit</a>';
                    $btnr = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="View" class="edit btn btn-primary btn-sm viewuser">View</a>';

                    $btn = $btnr . ' ' . $btn . ' ' . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteproducts">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }



    public function datatablepaymentrealocation()
    {

        $datatablepaymentrealocation = DB::select(
            "select
        *
     from allocatelocked "
        );




        if (request()->ajax()) {
            return datatables()->of($datatablepaymentrealocation)
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" 
                
                 data-toggle="tooltip" 
                 data-clawbackid="' . $row->clawbackid . '" 
                 data-original-title="Edit" 
                 class="edit btn btn-primary btn-sm unblockuser">APPROVE</a>';

                    return $btn;
                })
                ->rawColumns(['action'])

                ->make(true);
        }
    }
    public function loanblocked()
    {
        $you = auth()->user();

        $datablocked = DB::select(
            "select
        *
     from tbl_blocked_nfm a"
        );

        // $blockedCollection = collect($datablocked);



        if (request()->ajax()) {
            return datatables()->of($datablocked)
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" 
                    
                     data-toggle="tooltip" 
                     data-id="' . $row->id . '" 
                      data-idno="' . $row->idno . '" 

                     data-original-title="Edit" 
                     class="edit btn btn-primary btn-sm unblockuser">UNBLOCK</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function useroles($id)
    {

        $user = User::find($id);
        $roles = $user->getRoleNames();

        //$roles =json_decode($roles);
        // $roles =  json_encode(array('result' => $roles));
        $vowels = array("[", "]", '"');
        $onlyconsonants = str_replace($vowels, "", $roles);

        $roless = array('result' => $onlyconsonants);
        $info['data'] = $roless;

        if (request()->ajax()) {
            return datatables()->of($info)


                ->addIndexColumn()
                ->make(true);
        }
    }
    public function userpermissions($id)
    {

        $user = User::find($id);
        //$user->givePermissionTo('browse bread 1');
        $roles = $user->getPermissionNames();

        // return  response()->json($roles);

        // $roles =  json_encode(array('result' => $roles));
        $vowels = array("[", "]", '"');
        $onlyconsonants = str_replace($vowels, "", $roles);

        $roless = array('result' => $onlyconsonants);
        $info['data'] = $roless;

        if (request()->ajax()) {
            return datatables()->of($info)


                ->addIndexColumn()
                ->make(true);
        }
    }





    public function unblockrecords($id)
    {


        // dd($id);

        $idno = $id;


        $user = auth()->user()->name;


        // Check if the user has any of the specified abilities
        //  if (Gate::any(['administrator', 'lending'])) {
        if (Gate::any(['unblocking'])) {


            //   if($user->hasAnyPermission(['g','g'])){






            $updateData = [
                'status' => "allowed",




            ];


            try {
                DB::beginTransaction();

                DB::table('tbl_blocked_nfm')
                    ->where('idno', $idno)
                    ->update($updateData);
                // If both updates succeed, commit the transaction
                DB::commit();
            } catch (\Illuminate\Database\QueryException $e) {
                // If an error occurs, rollback the transaction
                DB::rollBack();
                // return response()->json(['errors' => $e.'update failed'], 422);
                return response()->json(['errors' => 'update failed'], 422);


                // Optionally, handle the error (e.g., log it or display a message)
                //throw $e;
            }


            $data = [
                'id_no' =>  $idno,
                'product_id' => 'unblocked',
                'productname' => 'unblocked',
                'added_by' => $user,
                'acad_year' => '2025/2026',
            ];

            DB::table('tbl_nfm_enabled')->insert($data);
            return response()->json([
                'result' => 'success',
                'message' => 'student enabled by ' . $user
            ], 200);
        } else {
            return response()->json(['errors' => 'contact lending team for update'], 422);
        }
    }











    public function allroles($id)
    {

        $roles = DB::table('roles')->get();

        if (request()->ajax()) {
            return datatables()->of($roles)


                ->addIndexColumn()
                ->make(true);
        }


        //return response()->json($info);






    }
    public function allpermission($id)
    {

        $allpermission = DB::table('permissions')->get();

        if (request()->ajax()) {
            return datatables()->of($allpermission)


                ->addIndexColumn()
                ->make(true);
        }


        //return response()->json($info);






    }
}
