<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Mail\ApplicationCountMail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\BeautifulMail;
use App\Mail\CustoMails;
use Illuminate\Support\Facades\Log;



class FaceController extends Controller
{
    public function uploadAndDetectFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store image in Laravel's storage directory
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = storage_path('app/public/' . $imageName);

        $image->move(storage_path('app/public/'), $imageName);

        // Call Python script for face detection
        $output = shell_exec("python3 " . escapeshellarg(storage_path('app/python/detect_face.py')) . " " . escapeshellarg($imagePath));

        if (trim($output) == "True") {
            return response()->json(['message' => '✅ Image contains a human face.', 'status' => true, 'image' => asset('storage/' . $imageName)]);
        } else {
            // Delete the invalid image
            unlink($imagePath);
            return response()->json(['message' => '❌ No human face detected. Please upload a valid image.', 'status' => false]);
        }
    }
}
