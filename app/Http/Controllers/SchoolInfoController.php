<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Http\Request;

class SchoolInfoController extends Controller
{
    public function index()
    {
        $schoolInfo = SchoolInfo::first();

        return view('home', ['schoolName' => $schoolInfo ? $schoolInfo : '']);
    }


    public function downloadAllPassports(Request $request)
    {
        $remoteUrl = "https://portal.mydspg.edu.ng/eportal/storage/app/public/passport/";
        $zipFileName = 'passports.zip';
        $zipPath = storage_path("app/{$zipFileName}");

        $prog = $request->query('p');
        $progtype = $request->query('pt');
        $level = $request->query('l');

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'Could not create ZIP file'], 500);
        }

        // Process passports in chunks of 100
        StudentProfile::where([
            'stdprogramme_id' => $prog,
            'stdprogrammetype_id' => $progtype,
            'stdlevel' => $level,
        ])
            ->select('std_photo')
            ->chunk(100, function ($passports) use ($zip, $remoteUrl) {
                foreach ($passports as $passport) {
                    $file = $passport->std_photo;
                    $fileUrl = $remoteUrl . $file;

                    try {
                        $fileContents = Http::timeout(10)->get($fileUrl)->body();

                        if ($fileContents) {
                            $localTempPath = storage_path("app/temp_{$file}");
                            file_put_contents($localTempPath, $fileContents);
                            $zip->addFile($localTempPath, $file);
                        }
                    } catch (\Exception $e) {
                    }
                }
            });

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
