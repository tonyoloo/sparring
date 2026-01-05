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

use App\Mail\ApplicationCountMail;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\BeautifulMail;
use App\Mail\CustoMails;

class UpdateCountLoanStatusEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:update-count-loan-status-email';

    /**
     * The console command description.php artisan loans:update-count-loan-status-email

     *
     * @var string
     */
    protected $description = 'Command update-count-loan-status-email';

    /**
     * Execute the console command.
     */

    //  public function __construct()
    //  {
    //      parent::__construct();
    //  }
    
    public function handle()
    {
        //
       
        $this->sendsubemail();
        $this->info('update-count-loan-status-email complete.');
  

      
    }

    function sendsubemail()
    {
        $this->subsequentcountemail();
    }

    function subsequentcountemail()
    {




            $data = DB::table('ussd_products_count_email as a')
            ->select(
                DB::raw('a.id AS ID'),
                DB::raw('a.name AS NAME'),
                DB::raw('a.type AS TYPE'),
                DB::raw('a.OFM AS OFM'),
                DB::raw('a.NFM AS NFM_LOAN'),
                DB::raw('a.SCHOLARSHIP AS SCHOLARSHIP'),

                DB::raw('a.academicyear AS ACADEMICYEAR')
            )            ->where('academicyear', '=', '2025/2026')

            ->orderBy('a.academicyear', 'DESC')
            ->get();
        


        //  $tableHtml = $this->renderTableHtml($cached_loans);
        // $cached_loans = array();
        $tableHtml = $this->renderTableHtml($data);
        // dd($tableHtml);











       //$email_add = 'toloo@helb.co.ke';
        //$ccemail_add = ['toloo@helb.co.ke', 'bkiprono@helb.co.ke', 'pwambugu@helb.co.ke'];
       //$ccAddresses = ['tonyoloo15@gmail.com', 'tonyoloo@ymail.com']; // Add your CC addresses here

        // $email_add = 'jnzuki@helb.co.ke';
        // $ccAddresses = ['bkiprono@helb.co.ke', 'pwambugu@helb.co.ke', 'emacharia@helb.co.ke', 'toloo@helb.co.ke', 'wwanjohi@helb.co.ke'];
        

        $email_add =['gmonari@helb.co.ke','edwin.wanyonyi@ufb.go.ke'] ;
        $ccAddresses = ['sgichimu@helb.co.ke',
         'immaculate.njoroge@ufb.go.ke', 'Leah.miano@ufb.go.ke', 'emmanuel.abook@ufb.go.ke',
          'mercy.gikonyo@ufb.go.ke', 'samuel.nandasaba@ufb.go.ke', 'jkiplagatuwei@gmail.com',
           'bmasinde@helb.co.ke', 'wwanjohi@helb.co.ke', 'jnzuki@helb.co.ke',
            'nkingori@helb.co.ke',  'mplalampaa@helb.co.ke',
             'cmwaikwasi@helb.co.ke', 'jgachari@helb.co.ke', 'jswanya@helb.co.ke', 
             'mwanyingi@helb.co.ke', 'mboke@helb.co.ke', 'bnzioka@helb.co.ke', 
             'fndege@helb.co.ke', 'jmungai@helb.co.ke', 'fokoth@helb.co.ke', 
             'bkiprono@helb.co.ke', 'wmbala@helb.co.ke', 'pwambugu@helb.co.ke', 
             'emacharia@helb.co.ke', 'toloo@helb.co.ke', 'swanyama@helb.co.ke',
              'dpepela@helb.co.ke', 'enafula@helb.co.ke', 'rngeno@helb.co.ke', 
              'aomondi@helb.co.ke', 'akibugu@helb.co.ke', 'cwenje@helb.co.ke', 
              'batandi@helb.co.ke', 'jkoech@helb.co.ke', 'jalex@helb.co.ke',
               'mmbiti@helb.co.ke','toloo@helb.co.ke','JThiuri@helb.co.ke',
            'wambmd@gmail.com','joseph.njau@education.go.ke','emilio.ireri@education.go.ke','secretarytvet@education.go.ke'];
            



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
                $this->sendsubemail();

            }
        }
    }
    function renderTableHtml($data)
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
