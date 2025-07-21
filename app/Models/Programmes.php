<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programmes extends Model
{
    use HasFactory;

    protected $table = 'programme';
    protected $primaryKey = 'programme_id';

    public function departments()
    {
        return $this->hasMany(DepartmentOptions::class, 'prog_id');
    }

    public function getProgrammeAbbreviationAttribute(): string
    {
        return $this->aprogramme_name;
    }
}
