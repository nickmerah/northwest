<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Level extends Model
{
    use HasFactory;

    protected $primaryKey = 'level_id';
    public $timestamps = false;
    protected $table = 'stdlevel';

    protected $fillable = ['level_name', 'programme_id'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programmes::class, 'programme_id', 'programme_id');
    }
}
