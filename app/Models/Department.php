<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'departments_id';
    public $timestamps = false;
    protected $table = 'departments';

    protected $guarded = ['*'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'fac_id', 'faculties_id');
    }
}
