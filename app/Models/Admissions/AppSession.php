<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSession extends Model
{
    use HasFactory;

    protected $primaryKey = 'cs_id';
    public $timestamps = false;
    protected $table = 'j_current_session';

    protected $guarded = ['*'];


    public static function getAppCurrentSession(): ?string
    {
        return self::where('status', 'current')->value('cs_session');
    }
}
