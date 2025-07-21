<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClearanceStudents extends Model
{
    use HasFactory;

    protected $table = 'cprofile';
    protected $primaryKey = 'csid';

    protected $fillable = [
        'surname',
        'firstname',
        'othernames',
        'matricno',
        'graduation_year',
        'dept_id',
        'prog_id',
        'phone',
        'email',
        'level_id',
        'password',
        'spassword'
    ];

    protected $hidden = [
        'password',
        'spassword',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setSpasswordAttribute($value)
    {
        $this->attributes['spassword'] = Hash::make($value);
    }

    public function programme()
    {
        return $this->belongsTo(Programmes::class, 'prog_id', 'programme_id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentOptions::class, 'dept_id', 'do_id');
    }

    public function level()
    {
        return $this->belongsTo(Levels::class, 'level_id');
    }
}
