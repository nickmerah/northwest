<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StateOfOrigin extends Model
{
    use HasFactory;

    protected $table = 'state';
    protected $primaryKey = 'state_id';
    public $timestamps = false;

    protected $guarded = ['*'];

    public function lgas()
    {
        return $this->hasMany(Lga::class);
    }
}
