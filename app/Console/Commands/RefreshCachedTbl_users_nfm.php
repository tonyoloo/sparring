<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendSms;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\BeautifulMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserRequest;
use App\Mail\ApplicationCountMail;
use Illuminate\Support\Facades\Hash;

use DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use App\Mail\CustoMails;
class RefreshCachedTbl_users_nfm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-cached-tbl_users_nfm';

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
        $user = DB::table('tbl_users_nfm')
        ->where('cell_verified', '1')

        ->get(); 
        $cacheKey = "tbl_users_nfm_all_verified";


        Cache::put($cacheKey, $user, now()->addHours(1)); // Cache for 1 hour
        $this->info('tbl_users_nfm_all_verified has been run successfully');





    }
}
