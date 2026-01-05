<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSoapIPRSRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id_number;
    protected $serial_number;

    public function __construct($id_number, $serial_number)
    {
        $this->id_number = $id_number;
        $this->serial_number = $serial_number;
    }

    public function handle()
    {
        // Enforce a 2-second delay between requests
        Cache::lock('soap_request_rate_limit', 2)->block(2);

        // Make the SOAP request
        $client = new SoapClient('http://10.1.1.6:9004/IPRSServerWCF?wsdl', [
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'exceptions' => 1,
            'keep_alive' => false,
            'connection_timeout' => 3000,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version' => SOAP_1_1,
            'encoding' => 'ISO-8859-1'
        ]);

        $response = $client->GetDataByIdCard([
            'log' => 'pwanyonyi',
            'pass' => 'iq3w4xCkt8Q8AxB',
            'id_number' => $this->id_number,
            'serial_number' => $this->serial_number
        ]);

        // Process the response (e.g., save to database, log, etc.)
        // Example: Log::info('SOAP Response:', (array) $response);

        unset($client);
    }
}
