<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    use HasFactory;

    protected $table = 'lga';
    protected $primaryKey = 'lga_id';
    public $timestamps = false;

    protected $guarded = ['*'];

    public function states()
    {
        return $this->belongsTo(StateOfOrigin::class);
    }
}
