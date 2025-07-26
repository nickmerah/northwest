<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\Admissions\AppProfile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ProfileRepositoryInterface;


class ProfileRepository implements ProfileRepositoryInterface
{

    public function getBiodataDetails(int $applicantId): array
    {
        $requestDetails = new Request(['include' => 'stateofOrigin,lga,programme,programmeType,firstChoiceCourse,secondChoiceCourse']);
        return AppProfile::getUserData($applicantId, $requestDetails);
    }

    public function saveBiodata(array $applicant, Request $request): array
    {
        $updateData = [
            'othernames' => $request->input('othernames'),
            'gender' => $request->input('gender'),
            'marital_status' => $request->input('maritalStatus'),
            'birthdate' => $request->input('birthDate'),
            'contact_address' => $request->input('contactAddress'),
            'student_homeaddress' => $request->input('studentHomeAddress'),
            'hometown' => $request->input('homeTown'),
            'local_gov' => $request->input('lga'),
            'state_of_origin' => $request->input('stateofOrigin'),
            'next_of_kin' => $request->input('nextofKin'),
            'nok_address' => $request->input('nextofKinAddress'),
            'nok_email' => $request->input('nextofKinEmail'),
            'nok_tel' => $request->input('nextofKinPhoneNo'),
            'nok_rel' => $request->input('nextofKinRelationship'),
            'biodata' => 1,
        ];

        if ($request->boolean('updateWithPassport') && $request->hasFile('profilePicture')) {
            $image = $request->file('profilePicture');
            $filename = $applicant['applicationNumber'] . '.' . $image->getClientOriginalExtension();

            $resizedImage = Image::make($image)->fit(300, 300)->encode();
            Storage::put("public/app_passport/{$filename}", $resizedImage);

            $updateData['std_photo'] = "{$filename}";
        }

        $appProfile = AppProfile::where('std_logid', $applicant['id'])->first();

        if ($appProfile) {
            $appProfile->update($updateData);
        }

        return self::getBiodataDetails($applicant['id']);
    }
}
