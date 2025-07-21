<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceFeesFields extends Model
{
    use HasFactory;

    protected $table = 'cfield';
    protected $primaryKey = 'field_id';
}
