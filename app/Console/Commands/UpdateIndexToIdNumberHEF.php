<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\UserController;

class UpdateIndexToIdNumberHEF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:updateindextoidumberhef';
    protected $description = 'Run updateindextoidumberHEF method every 10 minutes';

    public function handle()
    {
        // Call the controller method (optional: inject properly if needed)
        $controller = new UserController();
        $controller->updateindextoidumberHEF();
    }
}
