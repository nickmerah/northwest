<?php

namespace App\Models\Admissions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin \Laravel\Passport\HasApiTokens
 * @method \Laravel\Passport\Token|null token()
 */
class AppLogin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'jlogin';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'log_surname',
        'log_firstname',
        'log_othernames',
        'log_username',
        'jambno',
        'log_email',
        'log_password',
        'log_gsm',
        'log_status',
        'datereg',
    ];

    protected $hidden = [
        'log_password',
    ];

    public function getKeyName()
    {
        return 'log_id';
    }

    public function getAuthPassword()
    {
        return $this->log_password;
    }

    public function findForPassport($username)
    {
        return $this->where('log_username', $username)->first();
    }

    public function setLogPasswordAttribute($value)
    {
        $this->attributes['log_password'] = Hash::make(strtolower($value));
    }

    public function setLogSurnameAttribute($value)
    {
        $this->attributes['log_surname'] = strtoupper($value);
    }

    public function setLogFirstnameAttribute($value)
    {
        $this->attributes['log_firstname'] = strtoupper($value);
    }

    public function setLogOthernamesAttribute($value)
    {
        $this->attributes['log_othernames'] = strtoupper($value);
    }

    public function setLogEmailAttribute($value)
    {
        $this->attributes['log_email'] = strtolower($value);
    }

    public static function getNos(int $prog, int $ptid, string $sess, string $prefix, string $prefixType): string
    {
        $prefixString = $prefix . $prefixType . $sess;

        $latestApp = AppProfile::where('stdprogramme_id', $prog)
            ->where('std_programmetype', $ptid)
            ->where('app_no', 'like', $prefixString . '%')
            ->orderByDesc('app_no')
            ->first();

        $nos = 0;

        if ($latestApp) {
            // Get the numeric part after the 9th character
            $nos = (int) substr($latestApp->app_no, 9);
        }

        $genNumber = str_pad($nos + 1, 6, '0', STR_PAD_LEFT);

        return $prefixString . $genNumber;
    }
}
