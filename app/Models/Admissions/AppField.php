<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppField extends Model
{
    use HasFactory;

    protected $table = 'field_pass';

    protected $primaryKey = 'field_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;


    protected $fillable = [];


}
