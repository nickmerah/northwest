<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseReg extends Model
{
    use HasFactory;

    protected $table = 'course_reg';

    protected $primaryKey = 'stdcourse_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;


    protected $fillable = [
        'log_id',
        'thecourse_id',
        'c_unit',
        'clevel_id',
        'cyearsession',
        'csemester',
        'cdate_reg',
        'c_title',
        'c_code'
    ];

    protected $casts = [
        'cdate_reg' => 'date',
    ];

    public static function getCourseRegistrations($sid)
    {
        return  self::select(
            DB::raw('SUM(c_unit) as totunit'),
            'clevel_id',
            'cyearsession',
            'cdate_reg',
            'status',
            'remark',
        )
            ->where(['log_id' => $sid])
            ->groupBy(
                'clevel_id',
                'cyearsession',
                'cdate_reg',
                'status',
                'remark',
            )
            ->get();
    }

    public function level()
    {
        return $this->belongsTo(Levels::class, 'clevel_id', 'level_id');
    }
}
