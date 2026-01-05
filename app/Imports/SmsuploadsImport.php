<?php

namespace App\Imports;

use App\Models\Bankpays;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Smsimports;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;


class SmsuploadsImport implements  ToModel,WithValidation, WithHeadingRow
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Smsimports([
            //
            'phonenumber'    => $row['phonenumber'],
            'message'    => $row['message'],
            'batchno'    => $row['batchno'],



        ]);
    }
    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'phonenumber' => function($attribute, $value, $onFailure) {

                $n = strlen($value);
                if ($n != 12) {
                     $onFailure('phone number should begin with 254 an 12 digits:- correct this:');
                }
            },
            'batchno' => function($attribute, $value, $onFailure) {

                if (empty($value)) {
                     $onFailure('batchno cant be empty begin with eg batchXXX :- correct this:');
                }
            }
        ];
    }
}
