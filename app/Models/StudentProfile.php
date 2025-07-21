<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'stdprofile';
    protected $primaryKey = 'std_id';
    protected $fillable = [
        'std_logid',
        'matric_no',
        'surname',
        'firstname',
        'othernames',
        'gender',
        'marital_status',
        'birthdate',
        'matset',
        'local_gov',
        'state_of_origin',
        'religion',
        'nationality',
        'contact_address',
        'student_email',
        'student_homeaddress',
        'student_mobiletel',
        'std_genotype',
        'std_bloodgrp',
        'hometown',
        'next_of_kin',
        'nok_address',
        'nok_tel',
        'stdprogramme_id',
        'stdprogrammetype_id',
        'stdfaculty_id',
        'stddepartment_id',
        'stdcourse',
        'stdlevel',
        'std_admyear',
        'std_photo',
        'cs_status',
        'std_status',
        'student_status',
        'promote_status',
        'is_repeating',
    ];

    public function programme()
    {
        return $this->belongsTo(Programmes::class, 'stdprogramme_id', 'programme_id');
    }

    public function programmeType()
    {
        return $this->belongsTo(ProgrammeType::class, 'stdprogrammetype_id', 'programmet_id');
    }

    public function departmentOption()
    {
        return $this->belongsTo(DepartmentOptions::class, 'stdcourse', 'do_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'stddepartment_id', 'departments_id');
    }

    public function level()
    {
        return $this->belongsTo(Levels::class, 'stdlevel', 'level_id');
    }

    public function school()
    {
        return $this->belongsTo(Faculty::class, 'stdfaculty_id', 'faculties_id');
    }

    public function stateor()
    {
        return $this->belongsTo(StateOfOrigin::class, 'state_of_origin', 'state_id');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'local_gov', 'lga_id');
    }

    public function setStudentEmailAttribute($value)
    {
        $this->attributes['student_email'] = strtolower($value);
    }

    public function setContactAddressAttribute($value)
    {
        $this->attributes['contact_address'] = strtoupper($value);
    }

    public function setStudentHomeaddressAttribute($value)
    {
        $this->attributes['student_homeaddress'] = strtoupper($value);
    }

    public function setNextOfKinAttribute($value)
    {
        $this->attributes['next_of_kin'] = strtoupper($value);
    }

    public function setNokAddressAttribute($value)
    {
        $this->attributes['nok_address'] = strtoupper($value);
    }

    public function roomAllocations()
    {
        return $this->hasMany(HostelRoomAllocation::class, 'std_logid');
    }
}
