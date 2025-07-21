<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppFee extends Model
{
    use HasFactory;

    protected $table = 'fees_amt_pass';

    protected $primaryKey = 'fee_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [];

    public function fieldname()
    {
        return $this->belongsTo(AppField::class, 'item_id', 'field_id');
    }

    public static function getApplicantFees(int $progId, int $progTypeId): array
    {
        // Retrieve all matching fee records
        $fees = self::where(function ($query) use ($progId) {
            $query->where('prog_id', $progId)
                ->orWhere('prog_id', 0);
        })
            ->where(function ($query) use ($progTypeId) {
                $query->where('f_p_time', $progTypeId)
                    ->orWhere('f_p_time', 0);
            })
            ->get();

        if ($fees->isNotEmpty()) {
            return $fees->map(function ($fee) {
                return [
                    'feeId' => $fee->item_id,
                    'feeName' => $fee->fieldname->field_name,
                    'amount' => $fee->amount,
                    'statusStatus' => $fee->stdstatus == 1 ? 'Deltan' : 'General',
                ];
            })->toArray();
        }

        return [];
    }
}
