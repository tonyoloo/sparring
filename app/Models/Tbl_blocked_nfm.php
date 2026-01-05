<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_blocked_nfm extends Model
{

    protected $table = 'tbl_blocked_nfm'; // <-- Add this line
    public $timestamps = false; // <--- Add this line



    protected $fillable = [

        'idno',
        'reason',
        'academicyear',
        'status',
        'updated_by'

    ];
}
