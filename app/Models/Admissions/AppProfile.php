<?php

namespace App\Models\Admissions;

use App\Models\Lga;
use App\Models\Programmes;
use Illuminate\Support\Str;
use App\Models\ProgrammeType;
use App\Models\StateOfOrigin;
use App\Models\DepartmentOptions;
use App\Traits\HasDynamicIncludes;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppProfile extends Model
{
    use HasFactory, HasDynamicIncludes;

    protected $table = 'jprofile';
    protected $primaryKey = 'std_id';
    public $timestamps = false;

    protected $fillable = [
        'std_logid',
        'app_no',
        'jambno',
        'student_id',
        'profile_id', // uuid
        'surname',
        'firstname',
        'othernames',
        'gender',
        'marital_status',
        'birthdate',
        'local_gov',
        'state_of_origin',
        'contact_address',
        'student_email',
        'student_homeaddress',
        'student_mobiletel',
        'hometown',
        'next_of_kin',
        'nok_address',
        'nok_email',
        'nok_tel',
        'nok_rel',
        'stdprogramme_id',
        'stdcourse',
        'std_course',
        'std_programmetype',
        'biodata',
        'biodata',
        'isjamb',
        'appyear',
        'std_photo',
        'std_custome5',
        'std_custome6',
        'std_custome7',
        'std_custome8',
        'std_custome9',
        'ndcert'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->profile_id = (string) Str::uuid();
        });
    }

    public function programme()
    {
        return $this->belongsTo(Programmes::class, 'stdprogramme_id', 'programme_id');
    }

    public function programmeType()
    {
        return $this->belongsTo(ProgrammeType::class, 'std_programmetype', 'programmet_id');
    }

    public function firstChoiceCourse()
    {
        return $this->belongsTo(DepartmentOptions::class, 'stdcourse', 'do_id');
    }

    public function secondChoiceCourse()
    {
        return $this->belongsTo(DepartmentOptions::class, 'std_course', 'do_id');
    }

    public function stateoforigin()
    {
        return $this->belongsTo(StateOfOrigin::class, 'state_of_origin', 'state_id');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'local_gov', 'lga_id');
    }

    public function setSurnameAttribute($value)
    {
        $this->attributes['surname'] = strtoupper($value);
    }

    public function setFirstnameAttribute($value)
    {
        $this->attributes['firstname'] = strtoupper($value);
    }

    public function setOthernamesAttribute($value)
    {
        $this->attributes['othernames'] = strtoupper($value);
    }

    public function setStudentEmailAttribute($value)
    {
        $this->attributes['student_email'] = strtolower($value);
    }

    public static function getUserData(int $userId, ?Request $request): array
    {
        $applicant = AppProfile::query()
            ->withDynamicIncludes($request)
            ->where('std_logid', $userId)
            ->first();

        if (!$applicant) {
            return [];
        }

        $data = (object) $applicant->toArrayWithDynamicIncludes($request);

        if ($data) {
            return [
                'applicationNumber' => $data->app_no,
                'surname' => $data->surname,
                'firstname' => $data->firstname,
                'othernames' => $data->othernames,
                'gender' => $data->gender,
                'profile_id' => $data->profile_id,
                'maritalStatus' => $data->marital_status,
                'birthDate' => $data->birthdate,
                'lga' => $data?->local_gov,
                'stateofOrigin' => $data?->state_of_origin,
                'contactAddress' => $data->contact_address,
                'studentEmail' => $data->student_email,
                'studentHomeAddress' => $data?->student_homeaddress,
                'studentPhoneNo' => $data?->student_mobiletel,
                'homeTown' => $data?->hometown,
                'nextofKin' => $data?->next_of_kin,
                'nextofKinAddress' => $data?->nok_address,
                'nextofKinEmail' => $data?->nok_email,
                'nextofKinPhoneNo' => $data?->nok_tel,
                'nextofKinRelationship' => $data?->nok_rel,
                'programme' => $data?->stdprogramme_id,
                'programmeType' => $data?->std_programmetype,
                'firstChoiceCourse' => $data->stdcourse,
                'secondChoiceCourse' => $data?->std_course,
                'profilePicture' => $data?->std_photo,
            ];
        }

        return [];
    }

    public static function getDashboardData(int $userId): array
    {
        $applicant = self::where('std_logid', $userId)->first();

        if ($applicant) {
            return [
                'appyear' => $applicant->appyear,
                'biodata' => $applicant->biodata,
                'schoolattended' => $applicant->std_custome5,
                'olevels' => $applicant->std_custome6,
                'jambResult' => $applicant->std_custome7,
                'declaration' => $applicant->std_custome8,
                'applicationSubmit' => $applicant->std_custome9,
                'admissionStatus' => $applicant->adm_status,
                'eClearance' => $applicant->eclearance,
                'reject' => $applicant->reject,
                'ndcertificate' => $applicant->ndcert,
            ];
        }

        return [];
    }
}
