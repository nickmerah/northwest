<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentLogin extends Model
{
    use HasFactory;

    protected $table = 'stdlogin';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'log_surname',
        'log_firstname',
        'log_othernames',
        'log_username',
        'log_matno',
        'log_email',
        'log_password',
        'log_spassword',
        'token',
        'token_expires_at',
        'datereg',
    ];

    protected $hidden = [
        'log_password',
        'log_spassword',
    ];

    public function setLogPasswordAttribute($value)
    {
        $this->attributes['log_password'] = Hash::make($value);
    }

    public function setLogSPasswordAttribute($value)
    {
        $this->attributes['log_spassword'] = Hash::make($value);
    }
}
