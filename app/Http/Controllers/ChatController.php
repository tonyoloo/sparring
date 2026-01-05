<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Products;
use App\serial;
use App\claimsbenefit;
use App\claimsloan;
use App\Mail\BeautifulMail;
use App\Mail\CustoMails;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use DateTime;
use DatePeriod;
use DateIntercal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Notifications\SendSms;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Models\Loandetails;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\USSDController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Arr;
use Spatie\PdfToImage\Pdf as PDF;
use thiagoalessio\TesseractOCR\TesseractOCR;

//use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
use DataTables;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
// use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
// use Barryvdh\DomPDF\Facade as PDF;
//use Illuminate\Support\Facades\Storage;
use Imagick;
use Illuminate\Support\Facades\Log;
//use Intervention\Image\ImageManagerStatic as Image;
// use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Image;
use App\Models\ChatSession;  // Or App\ChatSession in older versions
class ChatController extends Controller
{

    // Add more intent patterns as needed
    public $intentPatterns = [
        'loan_application' => [
            'patterns' => [
                '/\b(loan|financing|mortgage)\b/i',  // Catches any of these standalone words
                '/\b(apply|get|want|need|obtain)\b.*\b(loan|financing|mortgage)\b/i',
                '/\b(interested in|looking for)\b.*\b(loan options?|financing)\b/i'
            ],
            'response' => 'loan_options'
        ],
        'support_request' => [
            'patterns' => [
                '/\b(help|support|problem|issue)\b/i',
                '/\b(contact|talk to)\b.*\b(human|agent)\b/i'
            ],
            'response' => 'support_response'
        ],
        'statement_request' => [
            'patterns' => [
                '/\b(statement|transactions|history|record)\b/i',
                '/\b(view|see|get)\b.*\b(statement|transactions)\b/i'
            ],
            'response' => 'statement_response'
        ],
    ];








    public function idnumberocrvalidationz(Request $request)
    {





        $rules = [
            'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // allow images and PDFs up to 5MB
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('image');
        $extension = strtolower($file->getClientOriginalExtension());
        $combinedText = '';

        // Handle single image
        $imagePath = $file->getPathname();
        $combinedText = (new TesseractOCR($imagePath))->lang('eng')->run();


        return response()->json([
            'message' => 'Text extracted successfully',
            'extracted_text' => trim($combinedText),
        ], 200);
    }

    public function idnumberocrvalidationx()
    {
        try {
            // Define paths
            $pdfPath = 'D:\xampp\htdocs\mobileportal\storage\app\idnumber.pdf';
            $outputImagePath = 'D:\xampp\htdocs\mobileportal\storage\app\page1.jpg';

            // Verify PDF exists
            if (!file_exists($pdfPath)) {
                Log::error('PDF file not found at: ' . $pdfPath);
                return response()->json(['error' => 'PDF file not found'], 500);
            }

            // Verify output directory is writable
            $outputDir = dirname($outputImagePath);
            if (!is_writable($outputDir)) {
                Log::error('Output directory not writable: ' . $outputDir);
                return response()->json(['error' => 'Output directory not writable'], 500);
            }

            // Define the full path to magick executable
            $magickPath = 'C:\Program Files\ImageMagick-7.1.1-Q16-HDRI\magick.exe'; // Adjust to your ImageMagick path
            if (!file_exists($magickPath)) {
                Log::error('Magick executable not found at: ' . $magickPath);
                return response()->json(['error' => 'Magick executable not found'], 500);
            }

            // Define the ImageMagick command with full path
            $command = '"' . $magickPath . '" -density 144 "' . $pdfPath . '[0]" "' . $outputImagePath . '"';

            // Log the command and environment for debugging
            Log::info('Executing ImageMagick command: ' . $command);
            Log::info('PHP PATH: ' . getenv('PATH'));
            Log::info('Running as user: ' . shell_exec('whoami'));

            // Execute the command using shell_exec
            $output = shell_exec($command . ' 2>&1');

            // Check if the command failed
            if ($output !== null && !file_exists($outputImagePath)) {
                Log::error('ImageMagick Command Failed: ' . $output);
                return response()->json(['error' => 'Command failed: ' . $output], 500);
            }

            // Verify output image exists
            if (!file_exists($outputImagePath)) {
                Log::error('Output image not created at: ' . $outputImagePath);
                return response()->json(['error' => 'Output image not created'], 500);
            }

            return response()->json(['success' => true, 'image' => $outputImagePath]);
        } catch (\Exception $e) {
            Log::error('ImageMagick Test Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function idnumberocrvalidation(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'idno' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Allow images and PDFs up to 5MB
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }


            $idno = $request->idno;
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $imagePath = null;

            // Handle PDF or Image
            if ($extension === 'pdf') {
                // Define paths for PDF conversion
                $pdfPath = $file->getPathname();
                $outputImagePath = storage_path('app/' . uniqid() . '_page1.jpg');

                // Verify output directory is writable
                $outputDir = dirname($outputImagePath);
                if (!is_writable($outputDir)) {
                    Log::error('Output directory not writable: ' . $outputDir);
                    return response()->json(['error' => 'Output directory not writable'], 500);
                }

                // Define ImageMagick path
                $magickPath = 'C:\Program Files\ImageMagick-7.1.1-Q16-HDRI\magick.exe';
                if (!file_exists($magickPath)) {
                    Log::error('Magick executable not found at: ' . $magickPath);
                    return response()->json(['error' => 'Magick executable not found'], 500);
                }

                // Construct and execute ImageMagick command
                $command = '"' . $magickPath . '" -density 144 "' . $pdfPath . '[0]" "' . $outputImagePath . '"';
                Log::info('Executing ImageMagick command: ' . $command);

                $output = shell_exec($command . ' 2>&1');

                // Check for conversion errors
                if ($output !== null && !file_exists($outputImagePath)) {
                    Log::error('ImageMagick Command Failed: ' . $output);
                    return response()->json(['error' => 'PDF conversion failed: ' . $output], 500);
                }

                // Verify output image exists
                if (!file_exists($outputImagePath)) {
                    Log::error('Output image not created at: ' . $outputImagePath);
                    return response()->json(['error' => 'Output image not created'], 500);
                }

                $imagePath = $outputImagePath;
            } else {
                // For image files, use the uploaded file directly
                $imagePath = $file->getPathname();
            }

            // Extract text using TesseractOCR
            $combinedText = (new TesseractOCR($imagePath))->lang('eng')->run();

            // Clean up converted image if it was a PDF
            if ($extension === 'pdf' && file_exists($imagePath)) {
                unlink($imagePath);
            }

            // return response()->json([
            //     'message' => 'Text extracted successfully',
            //     'extracted_text' => trim($combinedText),
            // ], 200);

            // Check if 'jamhuri' exists (case-insensitive) and $idno matches exactly
            $isValid = stripos($combinedText, 'jamhuri') !== false &&
                preg_match('/\b' . preg_quote($idno, '/') . '\b/', $combinedText);
            return response()->json([
                'message' => 'Text extracted successfully',
                'extracted_text' => trim($combinedText),
                'status' => $isValid
            ], 200);
        } catch (\Exception $e) {
            Log::error('File Processing Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function birthcertificatevalidation(Request $request)
    {
        try {
            // Step 1: Validate input
            $rules = [
                'birthno' => 'required',
                'birthname' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Max 5MB
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $birthno = $request->birthno;
            $birthname = $request->birthname;
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            // File processing paths
            $imagePath = null;
            $croppedPath = null;

            if ($extension === 'pdf') {
                // Step 2: Convert scanned PDF to PNG (first page only, 150 DPI)
                $pdfPath = $file->getPathname();
                $outputBasePath = storage_path('app/' . uniqid('converted_'));

                $popplerPath = 'C:\poppler\poppler-24.08.0\Library\bin\pdftoppm.exe';
                if (!file_exists($popplerPath)) {
                    return response()->json(['error' => 'Poppler not found'], 500);
                }

                $command = 'powershell -Command "& \'' . $popplerPath . '\' -png -r 300 -f 1 -l 1 \'' . $pdfPath . '\' \'' . $outputBasePath . '\' 2>&1 | Out-File -FilePath \'' . storage_path('app/poppler_error.txt') . '\'"';
                shell_exec($command);

                $imagePath = $outputBasePath . '-1.png';
                if (!file_exists($imagePath)) {
                    Log::error('PDF to Image conversion failed: ' . $imagePath);
                    return response()->json(['error' => 'Failed to convert PDF to image'], 500);
                }
            } else {
                // Step 2: For image files, use path directly
                $imagePath = $file->getPathname();
            }

            // Step 3: Crop image to remove border whitespace
            try {
                // $croppedPath = storage_path('app/cropped_' . uniqid() . '.png');
                // $image = new \Imagick($imagePath);
                // $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                // $image->trimImage(0); // Auto-trim all whitespace
                // $image->writeImage($croppedPath);
                // $image->clear();
                // $image->destroy();

                $croppedPath = storage_path('app/cropped_' . uniqid() . '.png');
                $image = new \Imagick($imagePath);
                $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $image->trimImage(0);
                $image->setImageFormat("png");
                $image->setImageDepth(8);
                $image->setImageBackgroundColor('white');
                $image = $image->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                $image->enhanceImage(); // Optional: enhance contrast
                $image->sharpenImage(2, 1); // Sharpen
                $image->writeImage($croppedPath);
                $image->clear();
                $image->destroy();
            } catch (\Exception $e) {
                Log::error('Image cropping error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to process image'], 500);
            }

            // Step 4: Run Tesseract OCR
            try {

                //dd($croppedPath);
                // $combinedText = (new TesseractOCR($croppedPath))
                //     ->lang('eng')
                //     ->psm(4)
                //     ->oem(1)
                //     ->run();


                $combinedText = (new TesseractOCR($croppedPath))
                ->lang('eng')
                ->psm(6)
                  ->oem(3)

                ->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/,. ')
                ->run();

                // $combinedText = (new TesseractOCR($croppedPath))
                //     ->lang('eng')
                //     ->psm(6)
                //     ->oem(3)
                //     ->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/,. ')
                //     ->run();
            } catch (\Exception $e) {
                Log::error('Tesseract error: ' . $e->getMessage());
                return response()->json(['error' => 'OCR failed'], 500);
            }

            // Step 5: Clean and compare
            $cleanText = preg_replace('/[^a-z0-9]/i', '', strtolower($combinedText));
            $cleanBirthNo = preg_replace('/[^0-9]/', '', $birthno);
            $cleanName = preg_replace('/[^a-z]/i', '', strtolower($birthname));

            $hasCertificate = preg_match('/birth/i', $cleanText);


            $hasBirthNumber = false;
            $combinations = [];

            for ($i = 0; $i < strlen($cleanBirthNo) - 1; $i++) {
                $pair = substr($cleanBirthNo, $i, 2);
                $combinations[] = $pair;

                if (strpos($combinedText, $pair) !== false) {
                    $hasBirthNumber = true;
                    break;
                }
            }








            //$hasDeathNumber = strpos($cleanText, $cleanDeathNo) !== false;
            $hasName = stripos($cleanText, $cleanName) !== false;

            $isValid = $hasCertificate && $hasBirthNumber && $hasName;

            // Step 6: Extract actual values (optional)
            $matchedDetails = [
                'certificate_found' => $hasCertificate,
                'birth_number_found' => $hasBirthNumber,
                'name_found' => $hasName,
            ];

            if ($isValid) {
                //  preg_match('/\d{10}/', $combinedText, $deathNumberMatches);
                // preg_match('/Name[:\s]*(.*?)\n/i', $combinedText, $nameMatches);

                $matchedDetails['actual_values'] = [
                    // 'death_number_in_document' => $deathNumberMatches[0] ?? 'Not found',
                    // 'name_in_document' => trim($nameMatches[1] ?? 'Not found'),
                ];
            }

            // Clean up temporary files
            if ($extension === 'pdf' && file_exists($imagePath)) {
                unlink($imagePath);
            }
            if ($croppedPath && file_exists($croppedPath)) {
                unlink($croppedPath);
            }
            if (file_exists(storage_path('app/poppler_error.txt'))) {
                unlink(storage_path('app/poppler_error.txt'));
            }

            return response()->json([
                'message' => 'Text extracted successfully',
                'extracted_text' => trim($combinedText),
                'status' => $isValid,
                'matched_details' => $matchedDetails,
                'input_parameters' => [
                    'provided_birthno' => $birthno,
                    'provided_name' => $birthname,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fatal Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function deathcertificatevalidation_withcomments(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'deathno' => 'required',
                'deceasedname' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Allow images and PDFs up to 5MB
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $deathno = $request->birthno;
            $deceasedname = $request->birthname;

            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $imagePath = null;

            // Handle PDF or Image
            if ($extension === 'pdf') {
                // Define paths for PDF conversion
                $pdfPath = $file->getPathname();
                $outputImagePath = storage_path('app/' . uniqid() . '_page1');

                // Verify output directory is writable
                $outputDir = dirname($outputImagePath);
                if (!is_writable($outputDir)) {
                    Log::error('Output directory not writable: ' . $outputDir);
                    return response()->json(['error' => 'Output directory not writable'], 500);
                }

                $popplerPath = 'C:\poppler\poppler-24.08.0\Library\bin\pdftoppm.exe';

                if (!file_exists($popplerPath)) {
                    Log::error('Poppler executable not found at: ' . $popplerPath);
                    return response()->json(['error' => 'Poppler executable not found'], 500);
                }

                // Construct and execute Poppler command with error redirection
                $command = 'powershell -Command "& \'' . $popplerPath . '\' -png -r 300 -f 1 -l 1 \'' . $pdfPath . '\' \'' . $outputImagePath . '\' 2>&1 | Out-File -FilePath \'' . storage_path('app/poppler_error.txt') . '\'"';
                shell_exec($command);
                //$output = shell_exec($command);

                // The actual output image will have a suffix like -1.png
                $generatedImagePath = $outputImagePath . '-1.png';


                // Check for conversion errors
                if (!file_exists($generatedImagePath)) {
                    Log::error('Poppler Command Failed. Check error log at: ' . storage_path('app/poppler_error.txt'));
                    return response()->json(['error' => 'PDF conversion failed. Check error log.'], 500);
                }

                $imagePath = $generatedImagePath;
                //$imagePath ="D:\xampp\htdocs\mobileportal\storage\app\a.png";




                try {
                    //$imagePath = storage_path('app/a.PNG');

                    // $manager = ImageManager::withDriver(Driver::class);

                    // $img = $manager->read($imagePath)->trim(15, 'top-left'); // ✅ Fixed order
                    // $trimmedPath = storage_path('app/trimmed_a.png');
                    // $img->save($trimmedPath);

                    $imagePath = storage_path('app/a.PNG');
                    $img = Image::make($imagePath)->trim('top-left', null, 15);
                    $trimmedPath = storage_path('app/trimmed_a.png');

                    $img->save($trimmedPath);

                    //     $manager = new ImageManager(['driver' => 'imagick']);
                    // $img = $manager->make(storage_path('app/a.PNG'))->trim('top-left', null, 15);
                    // $img->save(storage_path('app/trimmed_a.png'));

                } catch (\Exception $e) {
                    Log::error('Imagick Error: ' . $e->getMessage());
                    return response()->json(['error' => 'Image processing failed: ' . $e->getMessage()], 500);
                }
                // ... in your deathcertificatevalidation function:


                // // Load image and trim white space
                // $img = Image::make($imagePath)->trim('top-left', null, 15);

                // // Save the trimmed image to a temporary file
                // $trimmedPath = storage_path('app/trimmed_a.png');
                // $img->save($trimmedPath);

                // app\Http\Controllers\ChatController.php:451'
                // dd($imagePath);


                // dd($combinedText,);

                $imagePath = $trimmedPath;
            } else {
                // For image files, use the uploaded file directly
                $imagePath = $file->getPathname();
            }

            // Extract text using TesseractOCR
            $combinedText = (new TesseractOCR($trimmedPath))->lang('eng')->psm(6)->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/,. ')->run();

            // dd($combinedText);

            // Clean up converted image if it was a PDF
            if ($extension === 'pdf' && file_exists($imagePath)) {
                unlink($imagePath);
                // Also clean up the error log file
                if (file_exists(storage_path('app/poppler_error.txt'))) {
                    unlink(storage_path('app/poppler_error.txt'));
                }
            }

            // Get clean versions for comparison
            $cleanText = preg_replace('/[^a-z0-9]/i', '', strtolower($combinedText));
            $cleanDeathNo = preg_replace('/[^0-9]/', '', $deathno);
            $cleanName = preg_replace('/[^a-z]/i', '', strtolower($deceasedname));

            // Strict validation
            $hasCertificate = preg_match('/certificateofdeath/i', $cleanText);
            $hasDeathNumber = strpos($cleanText, $cleanDeathNo) !== false;
            $hasName = strpos($cleanText, $cleanName) !== false;

            $isValid = $hasCertificate && $hasDeathNumber && $hasName;

            // Prepare matched details
            $matchedDetails = [
                'certificate_found' => $hasCertificate,
                'death_number_found' => $hasDeathNumber,
                'name_found' => $hasName,
            ];

            // If valid, show what was matched
            if ($isValid) {
                // Extract the actual death number from text (first sequence of 10 digits)
                preg_match('/\d{10}/', $combinedText, $deathNumberMatches);
                $actualDeathNo = $deathNumberMatches[0] ?? 'Not found';

                // Extract the actual name (simplified example - adjust based on your document structure)
                preg_match('/Name[:\s]*(.*?)\n/i', $combinedText, $nameMatches);
                $actualName = trim($nameMatches[1] ?? 'Not found');

                $matchedDetails['actual_values'] = [
                    'death_number_in_document' => $actualDeathNo,
                    'name_in_document' => $actualName,
                ];
            }

            return response()->json([
                'message' => 'Text extracted successfully',
                'extracted_text' => trim($combinedText),
                'status' => $isValid,
                'matched_details' => $matchedDetails,
                'input_parameters' => [
                    'provided_deathno' => $deathno,
                    'provided_name' => $deceasedname
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('File Processing Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return response()->json(['error1' => $e->getMessage()], 500);
        }
    }

    public function deathcertificatevalidation(Request $request)
    {
        try {
            // Step 1: Validate input
            $rules = [
                'deathno' => 'required',
                'deceasedname' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Max 5MB
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $deathno = $request->input('deathno');
            $deceasedname = $request->input('deceasedname');
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            // File processing paths
            $imagePath = null;
            $croppedPath = null;

            if ($extension === 'pdf') {
                // Step 2: Convert scanned PDF to PNG (first page only, 150 DPI)
                $pdfPath = $file->getPathname();
                $outputBasePath = storage_path('app/' . uniqid('converted_'));

                $popplerPath = 'C:\poppler\poppler-24.08.0\Library\bin\pdftoppm.exe';
                if (!file_exists($popplerPath)) {
                    return response()->json(['error' => 'Poppler not found'], 500);
                }

                $command = 'powershell -Command "& \'' . $popplerPath . '\' -png -r 150 -f 1 -l 1 \'' . $pdfPath . '\' \'' . $outputBasePath . '\' 2>&1 | Out-File -FilePath \'' . storage_path('app/poppler_error.txt') . '\'"';
                shell_exec($command);

                $imagePath = $outputBasePath . '-1.png';
                if (!file_exists($imagePath)) {
                    Log::error('PDF to Image conversion failed: ' . $imagePath);
                    return response()->json(['error' => 'Failed to convert PDF to image'], 500);
                }
            } else {
                // Step 2: For image files, use path directly
                $imagePath = $file->getPathname();
            }

            // Step 3: Crop image to remove border whitespace
            try {
                $croppedPath = storage_path('app/cropped_' . uniqid() . '.png');
                $image = new \Imagick($imagePath);
                $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $image->trimImage(0);
                $image->setImageFormat("png");
                $image->setImageDepth(8);
                $image->setImageBackgroundColor('white');
                $image = $image->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                $image->enhanceImage(); // Optional: enhance contrast
                $image->sharpenImage(2, 1); // Sharpen
                $image->writeImage($croppedPath);
                $image->clear();
                $image->destroy();
            } catch (\Exception $e) {
                Log::error('Image cropping error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to process image'], 500);
            }

            // Step 4: Run Tesseract OCR
            try {

                //dd($croppedPath);
                // $combinedText = (new TesseractOCR($croppedPath))
                //     ->lang('eng')
                //     ->psm(4)
                //     ->oem(1)
                //     ->run();



                $combinedText = (new TesseractOCR($croppedPath))
                ->lang('eng')
                ->psm(6)
                  ->oem(3)

                ->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/,. ')
                ->run();            } catch (\Exception $e) {
                Log::error('Tesseract error: ' . $e->getMessage());
                return response()->json(['error' => 'OCR failed'], 500);
            }

            // Step 5: Clean and compare
            $cleanText = preg_replace('/[^a-z0-9]/i', '', strtolower($combinedText));
            $cleanDeathNo = preg_replace('/[^0-9]/', '', $deathno);
            $cleanName = preg_replace('/[^a-z]/i', '', strtolower($deceasedname));

            $hasCertificate = preg_match('/death/i', $cleanText);


            $hasDeathNumber = false;
            $combinations = [];

            for ($i = 0; $i < strlen($cleanDeathNo) - 1; $i++) {
                $pair = substr($cleanDeathNo, $i, 2);
                $combinations[] = $pair;

                if (strpos($combinedText, $pair) !== false) {
                    $hasDeathNumber = true;
                    break;
                }
            }








            //$hasDeathNumber = strpos($cleanText, $cleanDeathNo) !== false;
            $hasName = strpos($cleanText, $cleanName) !== false;

            $isValid = $hasCertificate && $hasDeathNumber && $hasName;

            // Step 6: Extract actual values (optional)
            $matchedDetails = [
                'certificate_found' => $hasCertificate,
                'death_number_found' => $hasDeathNumber,
                'name_found' => $hasName,
            ];

            if ($isValid) {
                preg_match('/\d{10}/', $combinedText, $deathNumberMatches);
                preg_match('/Name[:\s]*(.*?)\n/i', $combinedText, $nameMatches);

                $matchedDetails['actual_values'] = [
                    // 'death_number_in_document' => $deathNumberMatches[0] ?? 'Not found',
                    // 'name_in_document' => trim($nameMatches[1] ?? 'Not found'),
                ];
            }

            // Clean up temporary files
            if ($extension === 'pdf' && file_exists($imagePath)) {
                unlink($imagePath);
            }
            if ($croppedPath && file_exists($croppedPath)) {
                unlink($croppedPath);
            }
            if (file_exists(storage_path('app/poppler_error.txt'))) {
                unlink(storage_path('app/poppler_error.txt'));
            }

            return response()->json([
                'message' => 'Text extracted successfully',
                'extracted_text' => trim($combinedText),
                'status' => $isValid,
                'matched_details' => $matchedDetails,
                'input_parameters' => [
                    'provided_deathno' => $deathno,
                    'provided_name' => $deceasedname,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fatal Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }



    public function deathcertificatevalidation_bk(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'deathno' => 'required',
                'deceasedname' => 'required',
                'image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Allow images and PDFs up to 5MB
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }


            $deathno = $request->birthno;
            $deceasedname = $request->birthname;

            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $imagePath = null;

            // Handle PDF or Image
            if ($extension === 'pdf') {
                // Define paths for PDF conversion
                $pdfPath = $file->getPathname();
                $outputImagePath = storage_path('app/' . uniqid() . '_page1.jpg');

                // Verify output directory is writable
                $outputDir = dirname($outputImagePath);
                if (!is_writable($outputDir)) {
                    Log::error('Output directory not writable: ' . $outputDir);
                    return response()->json(['error' => 'Output directory not writable'], 500);
                }

                // Define ImageMagick path
                $magickPath = 'C:\Program Files\ImageMagick-7.1.1-Q16-HDRI\magick.exe';
                if (!file_exists($magickPath)) {
                    Log::error('Magick executable not found at: ' . $magickPath);
                    return response()->json(['error' => 'Magick executable not found'], 500);
                }

                // Construct and execute ImageMagick command
                // $command = '"' . $magickPath . '" -density 144 "' . $pdfPath . '[0]" "' . $outputImagePath . '"';
                // Log::info('Executing ImageMagick command: ' . $command);

                // $output = shell_exec($command . ' 2>&1');


                // Preprocess image with ImageMagick
                $command = '"' . $magickPath . '" -density 144 -normalize -colorspace Gray -unsharp 0x1 -deskew 40% "' . $pdfPath . '[0]" "' . $outputImagePath . '"';
                Log::info('Executing ImageMagick command: ' . $command);
                $output = shell_exec($command . ' 2>&1');





                // Check for conversion errors
                if ($output !== null && !file_exists($outputImagePath)) {
                    Log::error('ImageMagick Command Failed: ' . $output);
                    return response()->json(['error' => 'PDF conversion failed: ' . $output], 500);
                }

                // Verify output image exists
                if (!file_exists($outputImagePath)) {
                    Log::error('Output image not created at: ' . $outputImagePath);
                    return response()->json(['error' => 'Output image not created'], 500);
                }

                $imagePath = $outputImagePath;
            } else {
                // For image files, use the uploaded file directly
                $imagePath = $file->getPathname();
            }

            // // Extract text using TesseractOCR
            // $combinedText = (new TesseractOCR($imagePath))->lang('eng')->run();

            // Extract text with TesseractOCR
            $combinedText = (new TesseractOCR($imagePath))->lang('eng')->psm(6)->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/,. ')->run();



            // Clean up converted image if it was a PDF
            if ($extension === 'pdf' && file_exists($imagePath)) {
                unlink($imagePath);
            }

            // return response()->json([
            //     'message' => 'Text extracted successfully',
            //     'extracted_text' => trim($combinedText),
            // ], 200);

            // Check if 'jamhuri' exists (case-insensitive) and $idno matches exactly
            // More flexible certificate matching with optional spaces around "of"
            $isValid = preg_match('/certificate\s+of\s+birth/i', $combinedText) !== false
                && preg_match("/" . preg_quote($deathno, '/') . "/i", $combinedText)
                && stripos($combinedText, $deceasedname) !== false;
            return response()->json([
                'message' => 'Text extracted successfully',
                'extracted_text' => trim($combinedText),
                'status' => $isValid
            ], 200);
        } catch (\Exception $e) {
            Log::error('File Processing Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // public function testrainedloanai(Request $request)

    // {
    //     $userMessage = $request->input('message');

    //     //dd($userMessage);

    //     $response = Http::post('http://127.0.0.1:8003/intent', ['text' => $userMessage]);


    //     $data = $response->json();
    //     $intent = $data['intent'];
    //     $confidence = $data['confidence'];
    //     return $intent;
    // }


    // private function detectIntent($message)
    // {

    //     // $userMessage = $request->input('message');

    //     //dd($userMessage);

    //     $response = Http::post('http://127.0.0.1:8003/intent', ['text' => $message]);


    //     $data = $response->json();
    //     // dd($data);

    //     $intent = $data['intent'];
    //     $confidence = $data['confidence'];

    //     //  dd($intent);
    //     return $intent;
    // }




    private function detectIntent($message)
    {
        foreach ($this->intentPatterns as $intent => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $message)) {
                    return $config['response'];
                }
            }
        }
        return 'default_response';
    }
    public function handleChat(Request $request)

    {

        // dd($request);
        // 1. Get the user's message
        $userMessage = $request->input('message');
        $userIdno = $request->input('idno');
        $userPhone = $request->input('phone');


        // Clean up old sessions (optional)
        //ChatSession::where('updated_at', '<', now()->subHours(24))->delete();

        // // 2. Get or create chat session
        // $sessionId = session()->getId();
        // $context = ChatSession::firstOrCreate(
        //     ['session_id' => $sessionId],
        //     ['context' => json_encode(['step' => 'initial'])]
        // );

        // Use user ID as the session key instead of session ID
        $context = ChatSession::firstOrCreate(
            ['session_id' => $userIdno],
            ['context' => json_encode(['step' => 'initial'])]
        );



        // 3. Process the message
        $response = $this->processMessage($userIdno, $userPhone, $userMessage, json_decode($context->context, true));
        // dd($response);



        // 4. Update session context
        $context->update(['context' => json_encode($response['newContext'])]);

        // 5. Return JSON response
        return response()->json([
            'response' => $response['reply'],
            'buttons' => $response['buttons'] ?? []
        ]);
    }


    private function processMessage($userIdno, $userPhone, $message, $context)
    {




        // Maintain conversation history (last 5 messages)
        $context['previous_messages'] = array_slice(
            array_merge($context['previous_messages'] ?? [], [$message]),
            -5
        );

        $lowerMessage = strtolower(trim($message));
        $response = ['reply' => '', 'newContext' => $context];

        // Check for proactive loan suggestion triggers
        // Determine current step
        $currentStep = $context['step'] ?? 'initial';


        //dd($currentStep);
        // Route to appropriate handler
        switch ($currentStep) {
            case 'initial':
                return $this->handleInitialState($userIdno, $userPhone, $message, $context);
            case 'select_loan':
                return $this->handleLoanSelection($message, $context);
            case 'select_statement':
                return $this->handleStatementSelection($message, $context);
            case 'confirm_disbursement_option':

                return $this->confirm_disbursement_option($message, $context);

            case 'statement_response':

                return $this->handleStatementInquiry($message, $context);
                // Add more cases as needed

            case 'collect_details':

                //


                return   $this->handleLoanDetails($message, $context);

                break;

            case 'submit_loan':

                //


                return   $this->productsubmitchat($message, $context);

                break;


            case 'confirm_submit_loan':

                //


                return   $this->confirm_productsubmitchat($message, $context);

                break;
            case 'processtatement':

                //


                $reply =   $this->Processtatement($message, $context);

                return $reply;

                // return [
                //     'reply' => $reply,
                //     'newContext' => ['step' => 'initial']
                // ];

                break;
            default:
                return $this->handleDefaultResponse($message, $context);
        }
    }



    function productphonevalidate(
        $idno,
        $phone,
        $productname,
        $type,
        $userfirstname,
        $productid,
        $academicyear,
        $serialnumber,
        $productcode,

        $qualifiedboth,
        $qualifiedscholarship,
        $qualifiedloan
    ) {
        // Function logic here



        //dd("phone selected".$idno.'----'.$cellphone);


        $apiController = new ApiController();

        $userController = new UserController();







        $result = $userController->kycregister($idno, $phone, $apiController);

        $verified = json_decode($result)->verified;
        if ($verified == 'yes') {

            $message = 'Phone number ' . $phone . ' Are the details correct?';


            return [
                'reply' => $message,
                'newContext' => [
                    'academicyear' => $academicyear,
                    'disbursementoption' => 'mobile',
                    'disbursementoptionvalue' => $phone,
                    'idno' => $idno,
                    'phone' => $phone,
                    'productcode' => $productcode,
                    'productid' => $productid,
                    'productname' => $productname,
                    'qualifiedboth' => $qualifiedboth,
                    'qualifiedloan' => $qualifiedloan,
                    'qualifiedscholarship' => $qualifiedscholarship,
                    'serialnumber' => $serialnumber,
                    'step' => 'confirm_submit_loan',
                    'type' => $type,
                    'userfirstname' => $userfirstname,

                ]
            ];
        } else {

            $message = 'phone number ' . $phone . ' failed verification for id ' . $idno . ' Select another option.';


            return [
                'reply' => $message,
                'newContext' => [
                    'step' => 'initial'

                ]
            ];
        }
    }

    function productbankvalidate(
        $idno,
        $phone,
        $productname,
        $type,
        $userfirstname,
        $productid,
        $academicyear,
        $serialnumber,
        $productcode,
        $qualifiedboth,
        $qualifiedscholarship,
        $qualifiedloanmodel
    ) {
        // Function logic here




        // dd("bank selected".$idno);

        // $apicontroller = '';
        // ApiController $apiController
        $apiController = new ApiController();

        $action = 'fetchbankax';
        $arr = array('idno' => $idno);
        $result = $apiController->mobiapis($action, $arr);


        if (!is_array($result)) {
            $result = [$result];
            // $result = [];

        }

        if (Arr::has($result, 'bankaccountnumber')) {

            $bankaccountnumber = $result['bankaccountnumber'];

            $message = 'Bank account number ' . $bankaccountnumber . ' Are the details correct?';
            // public function  productsubmitchat($phone,$productname,$type,$userfirstname,$productid,$academicyear,$idno,$serialnumber,$productcode,$source,$qualifiedboth,$qualifiedscholarship,$qualifiedloanmodel,$disbursementoptionvalue,$disbursementoption)

            return [
                'reply' => $message,
                'newContext' => [
                    [
                        'academicyear' => $academicyear,
                        'disbursementoption' => 'bank',
                        'disbursementoptionvalue' => $bankaccountnumber,
                        'idno' => $idno,
                        'phone' => $phone,
                        'productcode' => $productcode,
                        'productid' => $productid,
                        'productname' => $productname,
                        'qualifiedboth' => $qualifiedboth,
                        'qualifiedloanmodel' => $qualifiedloanmodel,
                        'qualifiedscholarship' => $qualifiedscholarship,
                        'serialnumber' => $serialnumber,

                        'step' => 'submit_loan',
                        'type' => $type,
                        'userfirstname' => $userfirstname,
                    ]


                ]
            ];
        } else {

            $message = 'Bank account number failed verification for id ' . $idno . ' select another option.';
            return [
                'reply' => $message,
                'newContext' => [
                    'step' => 'initial'

                ]
            ];
        }
    }

    public function  confirm_productsubmitchat($message, $context)

    // public function  productsubmitchat($phone, $productname, $type, $userfirstname, $productid, $academicyear, $idno, $serialnumber, $productcode, $qualifiedboth, $qualifiedscholarship, $qualifiedloanmodel, $disbursementoptionvalue, $disbursementoption)
    {


        //dd($message);

        $idno                   = $context['idno'] ?? [];
        $productname            = $context['productname'] ?? [];

        $type                   = $context['type'] ?? [];
        $userfirstname          = $context['userfirstname'] ?? [];
        $productid              = $context['productid'] ?? [];

        $academicyear           = $context['academicyear'] ?? [];
        $serialnumber           = $context['serialnumber'] ?? [];
        $productcode            = $context['productcode'] ?? [];

        $qualifiedboth          = $context['qualifiedboth'] ?? [];
        $qualifiedscholarship   = $context['qualifiedscholarship'] ?? [];

        $qualifiedloan    = $context['qualifiedloan'] ?? [];
        $disbursementoptionvalue     = $context['disbursementoptionvalue'] ?? [];
        $disbursementoption     = $context['disbursementoption'] ?? [];

        $phone     = $context['phone'] ?? [];



        $source = 'mobile';
        $updateData = [
            'source' => $source,
            'disbursementoptionvalue' => $disbursementoptionvalue,
            'disbursementoption' => $disbursementoption


        ];
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');




        //$messageresponse= $context['message'];

        ///  dd(stripos($message, 'yes'));

        if (stripos($message, 'yes') !== false) {
            //echo "Message contains 'yes'.";


            return [
                'reply' => "Do you want to proceed with the application of " . $productname . ' ' . $type,
                'newContext' => [
                    'academicyear' => $academicyear,
                    'disbursementoption' => $disbursementoption,
                    'disbursementoptionvalue' => $phone,
                    'idno' => $idno,
                    'phone' => $phone,
                    'productcode' => $productcode,
                    'productid' => $productid,
                    'productname' => $productname,
                    'qualifiedboth' => $qualifiedboth,
                    'qualifiedloan' => $qualifiedloan,
                    'qualifiedscholarship' => $qualifiedscholarship,
                    'serialnumber' => $serialnumber,
                    'step' => 'submit_loan',
                    'type' => $type,
                    'userfirstname' => $userfirstname,

                ]
            ];
        } else {
            $message = 'kindly begin the aplication process and select a valid';
            return [
                'reply' => $message,
                'newContext' => [
                    'step' => 'initial',


                ]
            ];
        }
    }

    public function  productsubmitchat($message, $context)

    // public function  productsubmitchat($phone, $productname, $type, $userfirstname, $productid, $academicyear, $idno, $serialnumber, $productcode, $qualifiedboth, $qualifiedscholarship, $qualifiedloanmodel, $disbursementoptionvalue, $disbursementoption)
    {


        $idno                   = $context['idno'] ?? [];
        $productname            = $context['productname'] ?? [];

        $type                   = $context['type'] ?? [];
        $userfirstname          = $context['userfirstname'] ?? [];
        $productid              = $context['productid'] ?? [];

        $academicyear           = $context['academicyear'] ?? [];
        $serialnumber           = $context['serialnumber'] ?? [];
        $productcode            = $context['productcode'] ?? [];

        $qualifiedboth          = $context['qualifiedboth'] ?? [];
        $qualifiedscholarship   = $context['qualifiedscholarship'] ?? [];

        $qualifiedloan    = $context['qualifiedloan'] ?? [];
        $disbursementoptionvalue     = $context['disbursementoptionvalue'] ?? [];
        $disbursementoption     = $context['disbursementoption'] ?? [];

        $phone     = $context['phone'] ?? [];



        $source = 'mobile';
        $updateData = [
            'source' => $source,
            'disbursementoptionvalue' => $disbursementoptionvalue,
            'disbursementoption' => $disbursementoption


        ];
        $date_now = Carbon::now('Africa/Nairobi')->format('Y-m-d');

        $message                  = $context['message'] ?? [];





        if ($qualifiedloan >= 1) {
            $updateData['submittedloan'] = '1';
            $updateData['date_loan_submit'] = $date_now;
        }


        if ($qualifiedscholarship >= 1) {
            $updateData['submittedscholarship'] = '1';
            $updateData['date_sch_submit'] = $date_now;
        }

        if ($qualifiedboth >= 1) {
            $updateData['submittedloan'] = '1';
            $updateData['submittedboth'] = '1';


            $updateData['submittedscholarship'] = '1';
            $updateData['date_sch_submit'] = $date_now;
            $updateData['date_loan_submit'] = $date_now;
        }

        //dd($academicyear);

        //dd('productcode '.$productcode.' serialnumber'.$serialnumber.' idno'.$idno.' academicyear'.$academicyear);
        $affectedRows = DB::table('tbl_products_submit_new')
            ->where('productcode', $productcode)
            ->where('serial_number', $serialnumber)
            ->where('idno', $idno)
            ->where('acad_year', $academicyear)
            ->update($updateData);





        //dd($affectedRows);


        if ($affectedRows > 0) {
            $incrementValue = 1;



            $countrows = DB::table('ussd_products_count')
                ->where('productid', $productid)
                ->where('academicyear', $academicyear);


            if ($source == 'mobile') {
                $countrows->update([
                    'count' => DB::raw('count + ' . $incrementValue),
                    'mobile' => DB::raw('mobile + ' . $incrementValue)
                ]);
            }
            if ($source == 'miniapp') {
                $countrows->update([
                    'count' => DB::raw('count + ' . $incrementValue),
                    'miniapp' => DB::raw('miniapp + ' . $incrementValue)
                ]);
            }

            if ($source == 'ios') {
                $countrows->update([
                    'count' => DB::raw('count + ' . $incrementValue),
                    'ios' => DB::raw('ios + ' . $incrementValue)
                ]);
            }











            //  $mcode = 'Congratulations ' . $userfirstname . ', Your ' . $academicyear . ' ' . $name . ' ' . $type . ' application of serial ' . $serialnumber . ' has been received.';
            $mcode = "Bravo!{$userfirstname}. Your {$academicyear} {$productname} {$type} application Serial no. {$serialnumber} has been received. Track progress on www.hef.co.ke";


            $action = 'sendphoneverificationCode';
            $arr = [
                'recipient' => $phone,
                'verificationcode' => $mcode,
                'msg_priority' => '205',
                'category' => '400'
            ];
            $apiController = new ApiController();

            // Assuming callapion96 is a method in AuthModel and it returns a response
            $result = $apiController->datapull($action, $arr);


            return [
                'reply' => $mcode,
                'newContext' => [
                    'step' => 'initial',
                    'IDNO' => $idno

                ]
            ];
        } else {

            //dd($affectedRows);

            $message = 'Loan not submitted for ID number ' . $idno . '. Please retry later.';

            return [
                'reply' => $message,
                'newContext' => [
                    'step' => 'initial',
                    'IDNO' => $idno

                ]
            ];
        }
    }





    function handleInitialState($userIdno, $userPhone, $message, $context)
    {


        $step = $this->detectIntent($message);
        //dd($userIdno);

        switch ($step) {
            case 'loan_request':

                // $loans = Loan::all();
                $loans = DB::table('cre_pastapplicationstwo')
                    //->select('id', 'ProductType', 'ProductName', 'Maximum', 'Minimum', 'interestrate')
                    ->where('IDNO', '=', $userIdno)
                    ->where('ACADEMIC_YEAR', '=', '2025/2026')

                    ->get();

                dd($loans);

                if ($loans->isEmpty()) {
                    return [
                        'reply' => "Currently, we don't have any loan products available.",
                        'newContext' => ['step' => 'initial']
                    ];
                }

                $loanList = $loans->map(function ($loan, $index) {
                    return ($index + 1) . ". {$loan->STUDGROUPING2} ({$loan->ACADEMIC_YEAR})";
                })->implode("\n");

                return [
                    'reply' => "You qualify for these product options:\n$loanList\n\nPlease reply with the number of the product you're interested in:",
                    'newContext' => [
                        'step' => 'select_loan',
                        // 'available_loans' => $loans->pluck('STUDGROUPING2')->toArray(),
                        'available_loans' => $loans->map(function ($loan) {
                            return "{$loan->STUDGROUPING2} (productcode: {$loan->productcode}, IDNO: {$loan->IDNO}%)";
                        })->toArray(),
                        'loan_details' => $loans

                    ]
                ];
            case 'support_request':
                // return [
                //     'reply' => "Our support team is available 24/7. Please call 0727045828 or email toloo@helb.co.ke",
                //     'newContext' => $context
                // ];

                // $loans = Loan::all();
                $loans = DB::table('cre_pastapplicationstwo')
                    ->where('IDNO', '=', $userIdno)
                    ->where('ACADEMIC_YEAR', '=', '2025/2026')
                    ->get();

                if ($loans->isEmpty()) {
                    return [
                        'reply' => "Currently, we don't have any loan products available.",
                        'newContext' => ['step' => 'initial']
                    ];
                }

                $options = [];

                foreach ($loans as $loan) {
                    if ($loan->qualifiedloanmodel > 0) {
                        $options[] = "{$loan->STUDGROUPING2} loan ({$loan->ACADEMIC_YEAR})";
                    }

                    if ($loan->qualifiedscholarship > 0) {
                        $options[] = "{$loan->STUDGROUPING2} scholarship ({$loan->ACADEMIC_YEAR})";
                    }

                    if ($loan->qualifiedboth > 0) {
                        $options[] = "{$loan->STUDGROUPING2} loan and scholarship ({$loan->ACADEMIC_YEAR})";
                    }
                }


                $fulloptions = [];

                foreach ($loans as $loan) {
                    if ($loan->qualifiedloanmodel > 0) {
                        $fulloptions[] = "productqualified {$loan->STUDGROUPING2}  productcode {$loan->productcode} flag: submittedloan IDNO {$loan->IDNO}";
                    }

                    if ($loan->qualifiedscholarship > 0) {
                        $fulloptions[] = "productqualified {$loan->STUDGROUPING2} productcode {$loan->productcode} flag: submittedscholarship IDNO {$loan->IDNO}";
                    }

                    if ($loan->qualifiedboth > 0) {
                        $fulloptions[] = "productqualified {$loan->STUDGROUPING2} productcode {$loan->productcode} flag: submittedboth IDNO {$loan->IDNO}";
                    }
                }

                $loanList = collect($options)
                    ->map(function ($item, $index) {
                        return ($index + 1) . ". " . $item;
                    })
                    ->implode("\n");

                return [
                    //'reply' => "Here are the available options:\n$loanList\nPlease select the number corresponding to your preferred option.",
                    'reply' => "You qualify for these product options:\n$loanList\n\nPlease reply with the number of the product you're interested in:",

                    'newContext' => [
                        'step' => 'select_loan',
                        'available_loans' => $options,
                        'loan_details' => $fulloptions, // Store the raw options for reference
                        'idno' => $userIdno, // Store the raw options for reference
                        'phone' => $userPhone // Store the raw options for reference

                    ]
                ];



            case 'statement_response':

                return [
                    'reply' => "To get your statement, please provide:\n\n- ID number\n\n- PIN\n\nExample: \"My ID is 12345678 and PIN is 1980\"",
                    'newContext' => [
                        'step' => 'processtatement'
                    ]
                ];

            default:
                return [
                    'reply' => "I can help with:\n- Loan applications\n- Statement inquiries\n- Support requests\n\nHow can I assist you?",
                    'newContext' => ['step' => 'initial']
                ];
        }
    }



    function confirm_disbursement_option($message, $context)
    {



        $idno                   = $context['idno'] ?? [];
        $productname            = $context['productname'] ?? [];

        $type                   = $context['type'] ?? [];
        $userfirstname          = $context['userfirstname'] ?? [];
        $productid              = $context['productid'] ?? [];

        $academicyear           = $context['academicyear'] ?? [];
        $serialnumber           = $context['serialnumber'] ?? [];
        $productcode            = $context['productcode'] ?? [];

        $qualifiedboth          = $context['qualifiedboth'] ?? [];
        $qualifiedscholarship   = $context['qualifiedscholarship'] ?? [];

        $qualifiedloan     = $context['qualifiedloan'] ?? [];











        $query = DB::table('tbl_users_mobile')
            ->select('cell_phone')
            ->where('idno', $idno)

            ->where('cell_verified', '1')
            ->orderBy('time_added', 'desc')
            ->first();

        $phone = $query->cell_phone;




        $disbursementoption = $message;

        if (stripos($message, 'mobile') !== false) {




            return  $this->productphonevalidate(
                $idno,
                $phone,
                $productname,
                $type,
                $userfirstname,
                $productid,
                $academicyear,
                $serialnumber,
                $productcode,

                $qualifiedboth,
                $qualifiedscholarship,
                $qualifiedloan
            );
        }
        if (stripos($message, 'bank') !== false) {


            return   $this->productbankvalidate(
                $idno,
                $phone,
                $productname,
                $type,
                $userfirstname,
                $productid,
                $academicyear,
                $serialnumber,
                $productcode,

                $qualifiedboth,
                $qualifiedscholarship,
                $qualifiedloan
            );
        }


        return [
            'reply' => "Please select a valid option:mobile or bank",
            'newContext' => $context
        ];
    }
    function handleLoanSelection($message, $context)
    {


        $loanNumber = intval(trim($message));
        $availableLoans = $context['available_loans'] ?? [];
        $loanDetails = $context['loan_details'] ?? [];
        $phone = $context['phone'] ?? [];


        if ($loanNumber > 0 && $loanNumber <= count($availableLoans)) {

            // dd("jjjjjjjjjjjj");
            $selectedLoanText = $availableLoans[$loanNumber - 1];
            $selectedLoanDetails = $loanDetails[$loanNumber - 1];


            // Extract using regex
            $pattern = '/productqualified\s+(?P<productqualified>\w+)\s+productcode\s+(?P<productcode>\d+)\s+flag:\s+(?P<flag>\w+)\s+IDNO\s+(?P<IDNO>\d+)/';

            if (preg_match($pattern, $selectedLoanDetails, $matches)) {
                $productqualified = $matches['productqualified'];
                $productcode = $matches['productcode'];
                $flag = $matches['flag'];
                $IDNO = $matches['IDNO'];
                $academicyear = '2025/2026';
                $cell_verified = '1';

                $cacheKeyNFM = "tbl_users_nfm_{$IDNO}_{$cell_verified}";

                $user = Cache::remember($cacheKeyNFM, 300, function () use ($IDNO, $cell_verified) {
                    return DB::table('tbl_users_nfm')
                        ->where('id_no', $IDNO)
                        ->where('cell_verified', '1')

                        ->first(); // Convert to array for resulADMISSIONCATEGORYt_array() compatibility
                });

                // Check if data was found in cache and is not null
                if (!empty($user)) {
                    $userfirstname = $user->first_name;
                    $useremail = $user->email_add;
                } else {
                    $message = "try later";
                    $ussdController = new USSDController();
                    $apiController = new ApiController();

                    $userupdate = $ussdController->updatenfmdetails($IDNO, $phone, $apiController);

                    return [
                        'reply' => " $message\n",
                        'newContext' => [
                            'step' => 'initial',

                        ]
                    ];
                }









                if ($flag == 'submittedloan') {


                    $query = DB::table('ussd_products_test')
                        ->select('name', 'productid')
                        ->where('productcode', $productcode)
                        ->where('type', 'loan')

                        ->first();



                    $type = 'loan';
                    $qualifiedloan = '1';
                    $productid = $query->productid;
                    $productname = $query->name;
                } else {

                    $qualifiedloan = '0';
                }

                if ($flag == 'submittedscholarship') {

                    $query = DB::table('ussd_products_test')
                        ->select('name', 'productid')
                        ->where('productcode', $productcode)
                        ->where('type', 'scholarship')

                        ->first();


                    $type = 'scholarship';
                    $qualifiedscholarship = '1';
                    $productid = $query->productid;
                    $productname = $query->name;
                } else {

                    $qualifiedscholarship = '0';
                }

                if ($flag == 'submittedboth') {


                    $query = DB::table('ussd_products_test')
                        ->select('name', 'productid')
                        ->where('productcode', $productcode)
                        ->where('type', 'loan & Scholarship')

                        ->first();

                    $qualifiedboth = '1';
                    $type = 'loan and scholarship';
                    $productid = $query->productid;
                    $productname = $query->name;
                } else {

                    $qualifiedboth = '0';
                }

                // // Now you can use these variables as needed
                // echo "Product Qualified: $productqualified\n";
                // echo "Product Code: $productcode\n";
                // echo "Flag: $flag\n";
                // echo "ID Number: $IDNO\n";
            } else {


                echo "Could not parse loan details.\n";
            }


            ///////////////////NEW/////////////////



            $cacheKey = 'blocked_nfm_' . $IDNO;
            $cacheDuration = 60; // Cache duration in minutes

            $data = Cache::remember($cacheKey, $cacheDuration, function () use ($IDNO) {
                return DB::table('tbl_blocked_nfm')
                    ->where('idno', $IDNO)
                    ->where('status', 'blocked')

                    ->first();
            });

            if (!empty($data) && $data->idno === $IDNO) {
                $message = "ID number {$IDNO}  cannot access this service. Please contact our customer experience team for assistance.";


                return [
                    'reply' => " $message\n",
                    'newContext' => [
                        'step' => 'initial',

                    ]
                ];
            }


            //  dd($qualifiedboth);



            $cacheKey = "product_submit_new_loans_{$IDNO}_{$academicyear}_{$productcode}_{$qualifiedloan}_{$qualifiedscholarship}_{$qualifiedboth}";

            $datatwo = Cache::remember($cacheKey, 60, function () use ($IDNO, $academicyear, $productcode, $qualifiedloan, $qualifiedscholarship, $qualifiedboth) {
                return DB::table('tbl_products_submit_new')
                    ->where('idno', $IDNO)
                    ->when($qualifiedloan == 1, function ($query) {
                        $query->where('submittedloan', 1);
                    })
                    ->when($qualifiedscholarship == 1, function ($query) {
                        $query->where('submittedscholarship', 1);
                    })
                    ->when($qualifiedboth == 1, function ($query) {
                        $query->where('submittedboth', 1);
                    })
                    ->where('acad_year', $academicyear)
                    ->where('productcode', $productcode)
                    ->first();
            });


            //dd($datatwo);

            if (!empty($datatwo) && $datatwo != "null") {


                // Redundant, since empty() already checks for NULL

                $Bser = $datatwo->serial_number;
                $Bdat = $datatwo->date_updated;



                $message = "Your {$academicyear} {$productqualified} {$type} application of serial {$Bser} had been received. Application date {$Bdat}";

                return [
                    'reply' => " $message\n",
                    'newContext' => [
                        'step' => 'initial',

                    ]
                ];
            }



            $serialcode =  DB::table('tbl_products_submit_new')
                //  ->select('submittedloan', 'serial_number', 'date_created')
                ->where('idno', $IDNO)
                ->where('acad_year', $academicyear)
                ->where('productcode', $productcode)


                ->first();

            /// dd($serialcode->serial_number);


            if (empty($serialcode->serial_number)) {




                // UssdController = $ussdController;
                $ussdController = new USSDController();

                $serialresult = $ussdController->serialgenerator($productcode, $IDNO, 'mobile', $academicyear);

                // Get the JSON content from the response object
                $responseContent = $serialresult->getContent();

                // Decode the JSON content to an array
                $responseData = json_decode($responseContent, true);

                // Check if decoding was successful and if result is 'success'
                if (json_last_error() === JSON_ERROR_NONE && isset($responseData['result']) && $responseData['result'] == 'success') {
                    // Success case
                    $serial = $responseData['serialnumber'];

                    $institutiondetails = DB::table('dminstitututions_2024')
                        ->select('InstitutionName', 'CourseDescription')
                        ->where('IDNO', '=', $IDNO)
                        ->where('Productcode', '=', $productcode)

                        ->first();

                    $institutionname = $institutiondetails->InstitutionName;
                    $coursedescription = $institutiondetails->CourseDescription;

                    return [
                        'reply' => "Excellent choice! For the $productqualified , please confirm:\n"
                            . "1. Your Institution name is $institutionname:\n"
                            . "2. Your Course  is $coursedescription)\n"
                            . "3. Do you want mobile or bank disbursement option?\n",
                        'newContext' => [
                            'step' => 'confirm_disbursement_option',
                            'selected_loan' => $productqualified,
                            'course' => $coursedescription,
                            'institution' => $institutionname,
                            'loanserial' => $serial,

                            'academicyear' => $academicyear,

                            'idno' => $IDNO,
                            'productcode' => $productcode,
                            'productid' => $productid,
                            'productname' => $productname,
                            'qualifiedboth' => $qualifiedboth,
                            'qualifiedloan' => $qualifiedloan,
                            'qualifiedscholarship' => $qualifiedscholarship,
                            'type' => $type,
                            'userfirstname' => $userfirstname,
                        ]
                    ];









                    // Proceed with your success logic
                } else {


                    $message = "try later";

                    return [
                        'reply' => " $message\n",
                        'newContext' => [
                            'step' => 'initial',

                        ]
                    ];
                }
            } else {

                $serial = $serialcode->serial_number;

                $institutiondetails = DB::table('dminstitututions_2024')
                    ->select('InstitutionName', 'CourseDescription')
                    ->where('IDNO', '=', $IDNO)
                    ->where('Productcode', '=', $productcode)

                    ->first();

                $institutionname = $institutiondetails->InstitutionName;
                $coursedescription = $institutiondetails->CourseDescription;

                return [
                    'reply' => "Excellent choice! For the $productqualified , please confirm:\n"
                        . "1. Your Institution name is $institutionname:\n"
                        . "2. Your Course  is $coursedescription)\n"
                        . "3. Do you want mobile or bank disbursement option?\n",
                    'newContext' => [
                        'step' => 'confirm_disbursement_option',
                        'selected_loan' => $productqualified,
                        'course' => $coursedescription,
                        'institution' => $institutionname,
                        'serialnumber' => $serial,

                        'academicyear' => $academicyear,

                        'idno' => $IDNO,
                        'productcode' => $productcode,
                        'productid' => $productid,
                        'productname' => $productname,
                        'qualifiedboth' => $qualifiedboth,
                        'qualifiedloan' => $qualifiedloan,
                        'qualifiedscholarship' => $qualifiedscholarship,
                        'type' => $type,
                        'userfirstname' => $userfirstname,
                    ]
                ];
            }
        } else {


            // dd("jjjjjyyyy");


            $loanList = collect($context['available_loans'] ?? [])
                ->map(fn($loan, $index) => ($index + 1) . ". $loan")
                ->implode("\n");

            return [
                'reply' => "Please select a valid option:\n$loanList",
                'newContext' => $context
            ];
        }
    }

    function Processtatement($message, $context)
    {
        // Simple pattern matching for loan details
        preg_match_all('/(\d+[\.,]?\d*)/', $message, $matches);
        $numbers = array_filter(array_map('floatval', $matches[0] ?? []));
        if (count($numbers) < 2) {
            return [
                'reply' => "To get your statement, please provide:\n\n- ID number\n\n- PIN\n\nExample: \"My ID is 12345678 and PIN is 1980 (pin is your year of birth)\"",

                'newContext' => $context
            ];
        }

        preg_match_all('/\d+/', $message, $matches);
        $numbers = $matches[0];

        // Assign values based on position
        $idno = $numbers[0] ?? 'Not specified';
        $pin = $numbers[1] ?? 'Not specified';


        $user = DB::table('users')
            ->select('first_name', 'id', 'phone', 'work_email', 'pin', 'id')
            ->where('nationalid', '=', $idno)
            ->where('pin', '=', $pin)

            ->first();


        if (empty($user->phone)) {
            $varmatch = "User with id " . $idno . " does not exist or PIN incorrect use year of birth as PIN ";
            return $varmatch;
        }


        $userx = $user->id;






        $loans = DB::table('singleloandetails')
            ->where('user_id', '=',  $userx)
            ->where('runningloanbalance', '>', 0)

            ->get();


        if ($loans->isEmpty()) {
            return [
                'reply' => "Currently, you don't have any loan balance .",
                'newContext' => ['step' => 'initial']
            ];
        }

        $loanList = $loans->map(function ($loan, $index) {
            return ($index + 1) . ". {$loan->loanserial} ({$loan->runningloanbalance} balance)";
        })->implode("\n");

        return [
            'reply' => "You have these loans:\n$loanList\n\nPlease reply with the loan serial to generate statement:",
            'newContext' => [
                'step' => 'select_statement',
                'available_loanstatement' => $loans->pluck('loanserial')->toArray()
            ]
        ];
    }



    // private function handleLoanDetails($message, $context)
    // {
    //     $previousMessages = $context['previous_messages'] ?? [];
    //     $channel = 'none';

    //     // Check previous messages for channel type
    //     if (in_array('mobile', $previousMessages)) {
    //         $channel = 'mobile';
    //     } elseif (in_array('bank', $previousMessages)) {
    //         $channel = 'bank';
    //     }

    //    // dd($channel);

    //     // Now you can use $channel in your logic
    //     switch ($channel) {
    //         case 'mobile':
    //             // Handle mobile channel specific logic
    //             break;
    //         case 'bank':
    //             // Handle bank channel specific logic
    //             break;
    //         default:
    //             // Handle case when neither is found
    //             break;
    //     }

    //     // Rest of your function logic...
    //     // For example:
    //     $selectedLoan = $context['selected_loan'] ?? '';
    //     $productCode = $context['productcode'] ?? '';
    //     $idNumber = $context['IDNO'] ?? '';

    //     return [
    //         'reply' => "Processing your $selectedLoan application (Product: $productCode)...",
    //         'newContext' => $context
    //     ];
    // }

    function handleLoanDetails($message, $context)
    {
        // Extract loan details from context
        $selectedLoan = $context['selected_loan'] ?? 'Unknown Loan'; // "TVET" in this case
        $productCode = $context['productcode'] ?? ''; // "5637144605" in this case
        $idNumber = $context['IDNO'] ?? ''; // "28613556" in this case


        $course = $context['course'] ?? '';
        $institution =  $context['institution'] ?? '';

        // Check previous messages for channel type
        $previousMessages = $context['previous_messages'] ?? [];
        $channel = 'none';

        if (in_array('mobile', $previousMessages)) {
            $channel = 'mobile';
        } elseif (in_array('bank', $previousMessages)) {
            $channel = 'bank'; // This will be selected in your case
        }

        // Example usage of all extracted values:
        return [
            'reply' => "Processing your $selectedLoan application:\n"
                . "Institution:  $institution \n"
                . "course:  $course \n"

                . "ID Number: $idNumber\n"
                . "Channel: $channel\n"
                . "Please confirm these details are correct.",
            'newContext' => [
                'step' => 'submit_details',
                'selected_loan' => $selectedLoan,
                'productcode' => $productCode,
                'IDNO' => $idNumber,
                'channel' => $channel,
                'previous_messages' => $previousMessages
            ]
        ];
    }

    // private function handleLoanDetails($message, $context)
    // {

    //     // Clean the message to remove commas
    //     $message = str_replace(',', '', $message);
    //     // Simple pattern matching for loan details
    //     preg_match_all('/(\d+[\.,]?\d*)/', $message, $matches);
    //     $numbers = array_filter(array_map('floatval', $matches[0] ?? []));
    //     if (count($numbers) < 3) {
    //         return [
    //             'reply' => "Please provide:\n\n- Loan amount\n\n- ID number\n\n- pin\n\nExample: \"I need \\ksh 10000 ID number 1700080 pin 1234 (pin is your year of birth)\"",
    //             'newContext' => $context
    //         ];
    //     }

    //     preg_match_all('/\d+/', $message, $matches);
    //     $numbers = $matches[0];

    //     // Assign values based on position
    //     $loanAmount = $numbers[0] ?? 'Not specified';
    //     $idNumber = $numbers[1] ?? 'Not specified';
    //     $pin = $numbers[2] ?? 'Not specified';
    //     $reply = (string) $this->applyloans($loanAmount, $idNumber, $pin);



    //     return [
    //         'reply' => $reply,
    //         'newContext' => ['step' => 'initial']
    //     ];
    // }


    public function  handleStatementSelection($message, $context)
    {

        // dd($context);

        $collection = DB::table('loanstable')
            ->select('description', 'transactiondate', 'amountdebit', 'amountcredit', 'loanserial', 'id')

            ->where('loanserial', '=', $message)
            ->get();

        //  dd($user);

        if ($collection->isEmpty()) {
            $loanList = collect($context['available_loanstatement'] ?? [])
                ->map(fn($loan, $index) => ($index + 1) . ". $loan")
                ->implode("\n");

            return [
                'reply' => "Please select a valid option:\n$loanList",
                'newContext' => $context
            ];
        }

        $reply = view('dashboard.aistatement', [
            'loanStatements' => $collection, // your Illuminate\Support\Collection
        ])->render();

        return [
            'reply' => $reply,
            'newContext' => ['step' => 'initial']
        ];
    }
}
