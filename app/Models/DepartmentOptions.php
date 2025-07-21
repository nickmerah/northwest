<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentOptions extends Model
{
    use HasFactory;

    protected $table = 'dept_options';
    protected $primaryKey = 'do_id';

    protected $guarded = ['*'];

    public function programme()
    {
        return $this->belongsTo(Programmes::class, 'programme_id');
    }
}
