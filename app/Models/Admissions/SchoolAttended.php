<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolAttended extends Model
{
    use HasFactory;

    protected $primaryKey = 'eh_id';
    public $timestamps = false;
    protected $table = 'jeduhistory';

    //certObtained = grade
    protected $fillable = ['std_id', 'schoolname', 'ndmatno', 'cos', 'grade', 'fromdate', 'todate'];


    public function setSchoolnameAttribute($value)
    {
        $this->attributes['schoolname'] = strtoupper($value);
    }

    public function setNdmatnoAttribute($value)
    {
        $this->attributes['ndmatno'] = strtoupper($value);
    }

    public function setCosAttribute($value)
    {
        $this->attributes['cos'] = strtoupper($value);
    }

    public function setGradeAttribute($value)
    {
        $this->attributes['grade'] = strtoupper($value);
    }
}
