<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OFee extends Model
{
    use HasFactory;

    protected $primaryKey = 'of_id';
    public $timestamps = false;
    protected $table = 'ofield';

    protected $guarded = ['*'];
}
