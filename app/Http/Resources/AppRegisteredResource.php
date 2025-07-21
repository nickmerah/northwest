<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppRegisteredResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->std_logid,
            'surname'      => $this->surname,
            'firstname'    => $this->firstname,
            'othernames'   => $this->othernames,
            'applicationNo'     => $this->app_no,
            'email'        => $this->student_email,
            'phoneNumber'          => $this->student_mobiletel,
            'programmeId'          => $this->stdprogramme_id,
            'programmeType'        => $this->std_programmetype,
            'firstCourse'          => $this->stdcourse,
            'secondCourse'         => $this->std_course,
        ];
    }
}
