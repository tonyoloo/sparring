<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentData;
use PhpParser\Node\Stmt\Use_;
use App\Models\User;


class StudentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'gender' => 'required|string',
            'id_number' => 'required|string',
            'institution_name' => 'required|string',
            'course' => 'required|string',
            'year_of_study' => 'required|string',
            'county' => 'required|string',
            'town' => 'required|string',
            'attachment_from' => 'required|date',
            'attachment_to' => 'required|date',
            'department' => 'required|string',
        ]);
        $validated['user_id'] = auth()->id();
        // Update if exists, else insert
        $existing = DB::table('studentdata')->where('user_id', $validated['user_id'])->first();
        if ($existing) {

            DB::statement('SET SQL_SAFE_UPDATES = 0');

            DB::table('studentdata')->where('user_id', $validated['user_id'])->update($validated);
        } else {
            DB::table('studentdata')->insert($validated);
        }
        return response()->json(['success' => true]);
    }

    public function index()
    {
        $datastudents = StudentData::all();

       // dd($students);


              if (request()->ajax()) {
            return datatables()->of($datastudents)
                            ->make(true);

               
        }

        // return response()->json(['data' => $students]);
    }
} 