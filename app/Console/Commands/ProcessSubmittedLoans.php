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

use Illuminate\Support\Collection;
use HasRoles;
class ProcessSubmittedLoans extends Command
{



  
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:process-submitted-loans';

    /**
     * The console command description.
     *
     * @var string
     */

    // The description of the command
    protected $description = 'Process submitted loans in chunks and update status';

    /**
     * Execute the console command.
     */
    public function handle()
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
           //->where('serial_number', '2420443884') // Filter by current date
//             ->whereIn('serial_number', [

// 2420011074, 2420013810, 2420035203, 2420073529, 2420074207, 2420113241, 2420115646, 2420181469, 2420201046, 2420216837, 2420248324, 2420259901, 2420271458, 2420284689, 2420285693, 2420294629, 2420295689, 2420329420, 2420351467, 2420354116, 2420358460, 2420366469, 2420369240, 2420371407, 2420374223, 2420375350, 2420375818, 2420376041, 2420376076, 2420376281, 2420376296, 2420376297, 2420376304, 2420376306, 2420376307, 2420376313, 2420376315, 2420376331, 2420376338, 2420376342, 2420376356, 2420376373, 2420376376, 2420376377, 2420376387, 2420376388, 2420376391, 2420376402, 2420376405, 2420376406, 2420376429, 2420376434, 2420376437, 2420376438, 2420376444, 2420376454, 2420376455, 2420376458, 2420376483, 2420376488, 2420376492, 2420376503, 2420376516, 2420376517, 2420376530, 2420387243, 2420411179, 2420415312, 2420415446, 2420416986, 2420417726, 2420418032, 2420420983, 2420425451, 2420436927, 2420439145, 2420439480, 2420440006, 2420440824, 2420441204, 2420442713, 2420443148, 2420443244, 2420443357, 2420443483, 2420443485, 2420443487, 2420443493, 2420443494, 2420443503, 2420443505, 2420443512, 2420443513, 2420443514, 2420443515, 2420443539, 2420443551, 2420443572, 2420443574, 2420443584, 2420443585, 2420443591, 2420443595, 2420443605, 2420443614, 2420443616, 2420443620, 2420443628, 2420443635, 2420443637, 2420443642, 2420443645, 2420443648, 2420443665, 2420443666, 2420443670, 2420443671, 2420443674, 2420443678, 2420443681, 2420443682, 2420443689, 2420443697, 2420443700, 2420443704, 2420443707, 2420443710, 2420443711, 2420443714, 2420443715, 2420443722, 2420443724, 2420443726, 2420443729, 2420443732])


//            // ->get();

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


                            DB::connection('sqlsrv')->transaction(function () use ( $datasubmitted,$MOBILEPAYMENT,$paymentarray) {
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
                        DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);

                        echo "institution Record not found:" . $record->serial_number;
                    }
                }
                DB::update("UPDATE tbl_recid_setup SET start_serial2 = ? WHERE id >= ?", [$nwrecid, "1"]);
            }
        }
    });
    $this->info('Loan processing completed.');

    
    }
}
