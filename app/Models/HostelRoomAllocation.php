<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoomAllocation extends Model
{
    use HasFactory;

    protected $table = 'hostelroom_allocations';
    protected $primaryKey = 'id';

    protected $fillable = ['room_id', 'std_logid'];

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id', 'roomid');
    }

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'std_logid');
    }
}
