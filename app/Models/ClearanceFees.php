<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceFees extends Model
{
    use HasFactory;

    protected $table = 'cfees_amt';
    protected $primaryKey = 'fee_id';

    public function feeField()
    {
        return $this->belongsTo(ClearanceFeesFields::class, 'item_id', 'field_id');
    }

    public static function getPackFromTransaction($transno)
    {
        $fee = CTransaction::select('fee_id')->where('trans_no', $transno)->first();

        if ($fee) {
            return ClearanceFeesFields::where('field_id', $fee->fee_id)->select('pack_id')->get();
        }
        return collect();
    }
}
