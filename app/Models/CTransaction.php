<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CTransaction extends Model
{
    use HasFactory;

    protected $table = 'ctransaction';

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
        't_date',
        'trans_year',
        'trans_custom1',
        'fullnames',
        'matno',
        'course',
        'paychannel',
        'rrr'
    ];

    protected $casts = [
        'generated_date' => 'datetime',
        'trans_date' => 'datetime',
        't_date' => 'date'
    ];

    public static function getPaidTransaction($transno)
    {
        try {

            if (!is_numeric($transno)) {
                throw new \InvalidArgumentException('Invalid transaction number format.');
            }

            $transno = (int) $transno;

            $transactions = self::where([
                ['trans_no', '=', $transno],
                ['trans_custom1', '=', 'Paid']
            ])->orderBy('fee_id', 'asc')
                ->get();

            if ($transactions->isEmpty()) {
                throw new \Exception('No paid transactions found for the given transaction number.');
            }

            return $transactions;
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public static function getPendingTransactions($csid)
    {
        return self::where([
            'log_id' => $csid,
            'trans_custom1' => 'Pending'
        ])
            ->select('trans_no')
            ->groupBy('trans_no')
            ->get();
    }

    public static function getPaidTransactions($csid)
    {
        return  self::select(
            DB::raw('SUM(fee_amount) as totalamount'),  // Sum of fee_amount
            'trans_no',
            'rrr',
            't_date',
            'trans_custom1'
        )
            ->where([
                'log_id' => $csid,
                'trans_custom1' => 'Paid'
            ])
            ->groupBy(
                'trans_no',
                'rrr',
                't_date',
                'trans_custom1'
            )
            ->get();
    }

    public static function getAllTransactions($csid)
    {
        return  self::select(
            DB::raw('SUM(fee_amount) as totalamount'),  // Sum of fee_amount
            'trans_no',
            'rrr',
            't_date',
            'trans_custom1'
        )
            ->where('log_id', $csid)
            ->groupBy(
                'trans_no',
                'rrr',
                't_date',
                'trans_custom1'
            )
            ->get();
    }

    public static function checkIfTransactionAlreadyExists($csid, $feeid)
    {
        return self::where([
            'log_id' => $csid,
            'fee_id' => $feeid
        ])
            ->select('rrr')
            ->groupBy('rrr')
            ->get();
    }
}
