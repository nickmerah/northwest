<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StdSession extends Model
{
    use HasFactory;

    protected $primaryKey = 'cs_id';
    public $timestamps = false;
    protected $table = 'stdcurrent_session';

    protected $guarded = ['*'];


    public static function getStdCurrentSession($progid = 1)
    {
        $currentSession = self::select('cs_session', 'cs_sem')
            ->where(['status' => 'current', 'prog_id' => $progid])
            ->first();

        return $currentSession ? ['cs_session' => $currentSession->cs_session, 'cs_sem' => $currentSession->cs_sem] : null;
    }
}
