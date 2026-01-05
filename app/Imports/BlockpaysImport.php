<?php

namespace App\Imports;

use App\Models\Bankpays;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Tbl_blocked_nfm;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
class BlockpaysImport implements   ToModel,WithValidation, WithHeadingRow
{
    use Importable;
/*
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Tbl_blocked_nfm([
            //

            'idno'    => $row['idno'],
            'reason'    => $row['reason'],
            'academicyear'     => $row['academicyear'],
            'status'     => $row['status'],
            'updated_by'    => $row['updated_by']



        ]);
    }
    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'idno' => function($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                     $onFailure('national id number should be numeric:- correct this record: ');
                }
            },
            'reason' => function($attribute, $value, $onFailure) {
               if (empty($value)) {
                     $onFailure('status cant be empty e.g double allocation :- correct this:');
                }
            },
            'status' => function($attribute, $value, $onFailure) {

                if (empty($value)) {
                     $onFailure('status cant be empty e.g blocked :- correct this:');
                }
            },
            'updated_by' => function($attribute, $value, $onFailure) {

                if (empty($value)) {
                     $onFailure('updated_by cant be empty  :- correct this:');
                }
            }
        ];
    }
}
