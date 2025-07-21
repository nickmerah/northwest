<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class STransaction extends Model
{
    use HasFactory;

    protected $table = 'stdtransaction';

    protected $primaryKey = 'trans_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;


    protected $fillable = [
        'log_id',
        'fee_id',
        'trans_name',
        'trans_no',
        'user_faculty',
        'user_dept',
        'levelid',
        'trans_amount',
        'trans_year',
        'trans_semester',
        'pay_status',
        'policy',
        'fullnames',
        'prog_id',
        'prog_type',
        'stdcourse',
        'appno',
        'appsor',
        'paychannel',
        'fee_id',
        'fee_type',
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
            // $sessSem = self::getCurrentSemesterSession();
            $transactions = self::where([
                ['trans_no', '=', $transno],
                ['pay_status', '=', 'Paid'],
            ])->get();

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

    public static function getPendingTransactions($csid, $progid = 1)
    {
        $sessSem = self::getCurrentSemesterSession($progid);

        return self::where([
            'log_id' => $csid,
            'pay_status' => 'Pending'
        ])
            ->select('trans_no')
            ->groupBy('trans_no')
            ->get();
    }

    public static function getPaidTransactions($csid)
    {
        return  self::select(
            DB::raw('SUM(trans_amount) as totalamount'),
            'trans_no',
            'rrr',
            't_date',
            'pay_status'
        )
            ->where([
                'log_id' => $csid,
                'pay_status' => 'Paid'
            ])
            ->groupBy(
                'trans_no',
                'rrr',
                't_date',
                'pay_status'
            )
            ->get();
    }

    public static function getAllTransactions($sid)
    {
        return  self::select(
            DB::raw('SUM(trans_amount) as totalamount'),  // Sum of fee_amount
            'trans_no',
            'rrr',
            't_date',
            'trans_year',
            'pay_status'
        )
            ->where('log_id', $sid)
            ->groupBy(
                'trans_no',
                'rrr',
                't_date',
                'trans_year',
                'pay_status'
            )
            ->get();
    }

    public static function getPaidTransactionsForSession($sid, $field_ids = [], $progid = 1, $sess = "")
    {
        $sessSem = self::getCurrentSemesterSession($progid);

        $psess = !empty($sess) ? $sess : $sessSem['cs_session'];

        return self::select(
            DB::raw('SUM(trans_amount) as totalamount'),
            'trans_no',
            'rrr',
            't_date',
            'pay_status',
            'policy'
        )
            ->where([
                'log_id' => $sid,
                'pay_status' => 'Paid',
                'fee_type' => 'fees',
                'trans_year' => $psess,
            ])
            ->when(!empty($field_ids), function ($query) use ($field_ids) {
                return $query->whereIn('fee_id', $field_ids);
            })
            ->groupBy(
                'trans_no',
                'rrr',
                't_date',
                'pay_status',
                'policy'
            )
            ->get();
    }

    public static function getCurrentSemesterSession($progid = 1)
    {
        $currentSession = StdSession::getStdCurrentSession($progid);

        if (!$currentSession) {
            throw new \Exception("No current session found for prog_id: " . $progid);
        }

        return $currentSession;
    }

    public static function isEligibleGorCourseReg($sid, $progid, $sess = "")
    {
        $sessSem = self::getCurrentSemesterSession($progid);

        $csess = !empty($sess) ? $sess : $sessSem['cs_session'];

        return STransaction::where([
            'log_id' => $sid,
            'pay_status' => 'Paid',
            'fee_type' => 'fees',
            'trans_year' => $csess,
            'fee_id' => 1
        ])->get();
    }
}
