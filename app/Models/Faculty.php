<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $table = 'faculties';
    protected $primaryKey = 'faculties_id';
    public $timestamps = false;

    protected $guarded = ['*'];
}
