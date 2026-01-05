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

class UpdateCountLoanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:update-count-loan-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update-count-loan-status';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $results = DB::table('tbl_products_submit_new as a')
        // ->select('c.name', 'c.productcode', 'a.ACAD_YEAR', DB::raw('COUNT(a.serial_number) as count'))

        //   //  ->leftJoin('cre_pastapplicationstwo as d', 'a.productcode', '=', 'd.productcode')
        //   ->leftJoin('cre_pastapplicationstwo as d', 'a.idno', '=', 'd.IDNO')
        //     ->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')


        //     ->where('a.submittedloan', '1')
        //     ->where('d.qualifiedloanmodel', '1')
        //     //->where('d.qualifiedscholarship', '0')
        //     ->where('a.acad_year', '2025/2026')
        //    ->where('d.ACADEMIC_YEAR', '2025/2026')

        //      //->where('d.productcode', '5637173076')


        //     ->groupBy('d.productcode','c.productcode', 'a.ACAD_YEAR','c.name')
        //     ->get();

        $subquery = DB::table('cre_pastapplicationstwo')
    ->select('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel')
    ->where('qualifiedloanmodel', '1')
    ->where('ACADEMIC_YEAR', '2025/2026')
    ->groupBy('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel'); // remove duplicates

$results = DB::table('tbl_products_submit_new as a')
    ->select( 'a.ACAD_YEAR','d.productcode', DB::raw('COUNT(DISTINCT a.serial_number) as count'))
    ->leftJoinSub($subquery, 'd', function ($join) {
        $join->on('a.idno', '=', 'd.IDNO');
    })
    //->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')
    ->where('a.submittedloan', '1')
    ->where('a.acad_year', '2025/2026')
    ->groupBy('d.productcode',  'a.ACAD_YEAR')
    ->get();


         //  dd($results);

        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACAD_YEAR;
           // $name =  $val->name;


            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'loan-OFM')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'OFM' => $count
                ]);

            echo 'updated OFM loan '.' ' . $productcode . ' count ' . $count.PHP_EOL;
        }


        // $results = DB::table('tbl_products_submit_new as a')
        // ->select('c.name', 'c.productcode', 'a.ACAD_YEAR', DB::raw('COUNT(a.idno) as count'))

        //     ->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')
        //   ->leftJoin('cre_pastapplicationstwo as d', 'a.idno', '=', 'd.IDNO')


        //     ->where('a.submittedloan', '1')
        //     ->where('d.qualifiedloanmodel', '2')
        //    // ->where('d.qualifiedscholarship', '0')
        //    // ->where('c.productcode', '5637173076')

        //     ->groupBy('c.productcode', 'a.ACAD_YEAR','c.name')
        //     ->get();

          $subquery = DB::table('cre_pastapplicationstwo')
    ->select('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel')
    ->where('qualifiedloanmodel', '2')
    ->where('ACADEMIC_YEAR', '2025/2026')
    ->groupBy('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel'); // remove duplicates

$results = DB::table('tbl_products_submit_new as a')
    ->select( 'a.ACAD_YEAR','d.productcode', DB::raw('COUNT(DISTINCT a.serial_number) as count'))
    ->leftJoinSub($subquery, 'd', function ($join) {
        $join->on('a.idno', '=', 'd.IDNO');
    })
    //->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')
    ->where('a.submittedloan', '1')
    ->where('a.acad_year', '2025/2026')
    ->groupBy('d.productcode',  'a.ACAD_YEAR')
    ->get();

        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACAD_YEAR;
           // $name =  $val->name;


            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'loan-NFM')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'NFM' => $count
                ]);

                echo 'updated NFM loan '.'  ' . $productcode . ' count ' . $count.PHP_EOL;
            }

     

            // $results = DB::table('tbl_products_submit_new as a')
            // ->select('c.name', 'c.productcode', 'a.ACAD_YEAR', DB::raw('COUNT(a.idno) as count'))
    
            //     ->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')
            //   ->leftJoin('cre_pastapplicationstwo as d', 'a.idno', '=', 'd.IDNO')
    
    
            
            //     ->where('d.submittedscholarship', '1')
            //     ->where('d.qualifiedscholarship', '1')
            //    // ->where('c.productcode', '5637173076')
    
            //     ->groupBy('c.productcode', 'a.ACAD_YEAR','c.name')
            //     ->get();

  $subquery = DB::table('cre_pastapplicationstwo')
    ->select('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel')
    ->where('qualifiedscholarship', '1')
    ->where('ACADEMIC_YEAR', '2025/2026')
    ->groupBy('IDNO', 'productcode', 'ACADEMIC_YEAR', 'qualifiedloanmodel'); // remove duplicates

$results = DB::table('tbl_products_submit_new as a')
    ->select( 'a.ACAD_YEAR','d.productcode', DB::raw('COUNT(DISTINCT a.serial_number) as count'))
    ->leftJoinSub($subquery, 'd', function ($join) {
        $join->on('a.idno', '=', 'd.IDNO');
    })
    //->leftJoin('ussd_products_test as c', 'a.productcode', '=', 'c.productcode')
    ->where('a.submittedscholarship', '1')
    ->where('a.acad_year', '2025/2026')
    ->groupBy('d.productcode',  'a.ACAD_YEAR')
    ->get();



        foreach ($results as $val) {

            $count =  $val->count;
            $productcode =  $val->productcode;
            $academicyear =  $val->ACAD_YEAR;

            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('ussd_products_count_email')
                ->where('productcode', '=', $productcode)
                ->where('type', '=', 'scholarship')
                ->where('academicyear', '=', $academicyear)


                ->update([
                    'SCHOLARSHIP' => $count
                ]);

            echo 'updated NFM scholarship ' . $productcode . ' count ' . $count;
        }

        $totalCountOFM = DB::table('ussd_products_count_email')
        ->where('productcode', '!=', '1000')

        ->where('academicyear', '2025/2026')
        ->sum('OFM');
        $totalCountNFM = DB::table('ussd_products_count_email')
        ->where('productcode', '!=', '1000')

        ->where('academicyear', '2025/2026')
        ->sum('NFM');
        $totalCountSCHOLARSHIP = DB::table('ussd_products_count_email')
        ->where('productcode', '!=', '1000')

        ->where('academicyear', '2025/2026')
        ->sum('SCHOLARSHIP');

        DB::table('ussd_products_count_email')
        ->where('productcode', '=', '1000')


        ->update([
            'SCHOLARSHIP' => $totalCountSCHOLARSHIP,
            'NFM' => $totalCountNFM,
            'OFM' => $totalCountOFM


        ]);





        $this->info('update-count-loan-status completed.');

    }
}
