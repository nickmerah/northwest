<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLevel extends Model
{
    use HasFactory;

    protected $primaryKey = 'o_id';
    public $timestamps = false;
    protected $table = 'jolevels';

    protected $fillable = ['std_id', 'subname', 'certname', 'grade', 'eyear', 'emonth', 'centerno', 'examno', 'sitting'];

    public function setSubnameAttribute($value)
    {
        $this->attributes['subname'] = strtoupper($value);
    }

    public function setEmonthAttribute($value)
    {
        $this->attributes['emonth'] = strtoupper($value);
    }

    public function setSittingAttribute($value)
    {
        $this->attributes['sitting'] = ucfirst($value);
    }
}
