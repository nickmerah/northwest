<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceFeePack extends Model
{
    use HasFactory;

    protected $table = 'cfeepack';
    protected $primaryKey = 'pack_id';

     
    public static function getPacks()
    {
        return self::pluck('pack_name', 'pack_id')->toArray();
    }
}
