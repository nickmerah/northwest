<?php

namespace App\Services;

use App\Http\Requests\Jamb;
use App\Http\Requests\Olevel;
use App\Helpers\AccountHelper;
use App\Http\Requests\Certificate;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SchoolAttended;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ResultsRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ResultsService
{
    protected $resultsRepository;

    public function __construct(ResultsRepositoryInterface $resultsRepository)
    {
        $this->resultsRepository = $resultsRepository;
    }

    public function getOlevels()
    {
        return $this->resultsRepository->getOlevelDetails(AccountHelper::getUserId());
    }

    public function saveOlevels(Olevel $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if biodata has been saved
        if ($applicant['biodata'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save olevel details after updating your profile');
        }

        return $this->resultsRepository->saveOlevel($applicantId, $request);
    }

    public function getJamb()
    {
        return $this->resultsRepository->getJambDetails(AccountHelper::getUserId());
    }

    public function saveJamb(Jamb $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if olevel has been saved
        if ($applicant['olevels'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save jamb details after saving your olevel results');
        }

        return $this->resultsRepository->saveJamb($applicantId, $request);
    }

    public function getSchoolAttended()
    {
        return $this->resultsRepository->getSchoolAttended(AccountHelper::getUserId());
    }

    public function saveSchoolAttended(SchoolAttended $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if olevel has been saved
        if ($applicant['olevels'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save jamb details after saving your olevel results');
        }

        return $this->resultsRepository->saveSchoolAttended($applicantId, $request);
    }

    public function getUploadedResults()
    {
        return $this->resultsRepository->getUploadedResults(AccountHelper::getUserId());
    }

    public function saveUploadedResult(Certificate $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if olevel has been saved
        if ($applicant['olevels'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only upload results after saving your olevel results');
        }

        $documents = [
            'jamb_result' => 'Jamb Result',
            'o_level_result' => 'O Level Result',
            'birth_certificate' => 'Birth Certificate',
        ];

        $errors = [];
        $savedDocs = [];
        $certificatesToSave = [];

        DB::beginTransaction();

        try {
            foreach ($documents as $field => $documentName) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);

                    // Validate
                    if (
                        strtolower($file->getClientOriginalExtension()) !== 'pdf' ||
                        $file->getMimeType() !== 'application/pdf'
                    ) {
                        $errors[] = "$documentName: Only PDF files are allowed.";
                        continue;
                    }

                    if ($file->getSize() > 102400) {
                        $errors[] = "$documentName: File exceeds 100KB size limit.";
                        continue;
                    }

                    $timestamp = now()->format('dmYHis');
                    $fileName = "{$timestamp}_" . strtolower(str_replace(' ', '_', $documentName)) . '.pdf';
                    $file->storeAs('documents', $fileName, 'public');

                    $certificatesToSave[] = [
                        'stdid' => $applicantId,
                        'docname' => $documentName,
                        'uploadname' => $fileName,
                    ];

                    $savedDocs[] = [
                        'documentName' => $documentName,
                        'uploadName' => $fileName,
                    ];
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
            }

            $savedResults = $this->resultsRepository->saveCertificates($certificatesToSave, $applicantId);

            DB::commit();
            return  $savedResults;
        } catch (\Exception $e) {
            DB::rollBack();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function removeResult()
    {
        $applicantId = AccountHelper::getUserId();

        $documents = $this->resultsRepository->getUploadedResults($applicantId);

        foreach ($documents as $doc) {
            $filePath = 'documents/' . $doc['uploadName'];
            Storage::disk('public')->exists($filePath);

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        return $this->resultsRepository->deleteDocumentRecords($applicantId);
    }
}
