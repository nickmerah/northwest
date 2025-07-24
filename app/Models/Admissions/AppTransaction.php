<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppTransaction extends Model
{
    use HasFactory;

    protected $table = 'jtransaction';

    protected $primaryKey = 'trans_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;


    protected $fillable = [
        'log_id',
        'fee_id',
        'fee_name',
        'trans_no',
        'fee_amount',
        'generated_date',
        'trans_date',
        't_date',
        'trans_year',
        'trans_custom1',
        'fullnames',
        'appno',
        'paychannel',
        'semail',
        'rrr',
        'redirect_url'
    ];

    protected $casts = [
        'generated_date' => 'datetime',
        'trans_date' => 'datetime',
        't_date' => 'date'
    ];

    public static function getApplicantPaymentStatus(int $userId): array
    {
        $feePayments = self::where('log_id', $userId)->where('trans_custom1', 'Paid')->get();

        if ($feePayments->isNotEmpty()) {
            return $feePayments->map(function ($fee) {
                return [
                    'feeId' => $fee->fee_id,
                    'feeName' => $fee->fee_name,
                    'amount' => $fee->fee_amount,
                    'rrr' => $fee->rrr,
                    'datePaid' => $fee->t_date,
                ];
            })->toArray();
        }

        return [];
    }
}
