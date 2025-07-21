<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RemedialCourseReg extends Model
{
    use HasFactory;

    protected $table = 'remedialcourse_reg';

    protected $primaryKey = 'stdcourse_id';

    protected $keyType = 'int';

    public $timestamps = false;


    protected $fillable = [
        'std_id',
        'clevel_id',
        'cyearsession',
        'c_code'
    ];
}
