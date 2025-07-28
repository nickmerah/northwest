<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificates extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'jcertificates';

    protected $fillable = ['stdid', 'docname', 'uploadname'];
}
