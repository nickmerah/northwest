<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamb extends Model
{
    use HasFactory;

    protected $primaryKey = 'o_id';
    public $timestamps = false;
    protected $table = 'jamb';

    protected $fillable = ['std_id', 'jambno', 'subjectname', 'jscore'];

    public function setSubjectnameAttribute($value)
    {
        $this->attributes['subjectname'] = strtoupper($value);
    }

    public function setJambnoAttribute($value)
    {
        $this->attributes['jambno'] = strtoupper($value);
    }
}
