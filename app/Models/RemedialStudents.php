<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RemedialStudents extends Model
{
    use HasFactory;

    protected $table = 'rprofile';
    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $hidden = [
        'password',
    ];
}
