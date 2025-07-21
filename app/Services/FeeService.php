<?php

namespace App\Services;

use App\Models\StdSession;
use App\Models\STransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeService
{
    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * 
     * @param int $progId
     * @param int $levelId
     * @param int $progType
     * @param string $residentStatus
     * @param int $sessionId
     * @param string $semester
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getFees($progId, $levelId, $progType, $residentStatus, $sessionId, $semester)
    {

        return DB::table('fees_amt')
            ->join('field', 'fees_amt.field_id', '=', 'field.field_id')
            ->where(function ($query) use ($progId) {
                $query->where('fees_amt.prog_id', '=', $progId)
                    ->orWhere('fees_amt.prog_id', '=', 0);
            })
            ->where(function ($query) use ($levelId) {
                $query->where('fees_amt.level_id', '=', $levelId)
                    ->orWhere('fees_amt.level_id', '=', 0);
            })
            ->where(function ($query) use ($progType) {
                $query->where('fees_amt.prog_type', '=', $progType)
                    ->orWhere('fees_amt.prog_type', '=', 0);
            })
            ->where(function ($query) use ($residentStatus) {
                $query->where('fees_amt.resident_status', '=', $residentStatus)
                    ->orWhere('fees_amt.resident_status', '=', 'general');
            })
            ->where('fees_amt.sessionid', '=', $sessionId)
            ->where('fees_amt.semester', '=', $semester)
            ->select('fees_amt.*', 'field.field_name', 'field.group')
            ->get();
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getStudentFees()
    {
        $sessSem = StdSession::getStdCurrentSession($this->student->stdprogramme_id);

        $progId = $this->student->stdprogramme_id;
        $levelId = $this->student->stdlevel;
        $progType = $this->student->stdprogrammetype_id;
        $residentStatus = $this->student->state_of_origin == 10 ? 'resident' : 'non-resident';
        $sessionId = $sessSem['cs_session'];
        $semester = $sessSem['cs_sem'];

        return $this->getFees($progId, $levelId, $progType, $residentStatus, $sessionId, $semester);
    }

    public function getStudentCompulsoryAndRemainingFees(array $fees)
    {
        //get compulsory fee
        $compulsoryfee = array_filter($fees, function ($item) {
            return $item->group == 1;
        });

        //get other fee
        $otherfee = array_filter($fees, function ($item) {
            return $item->group == 0;
        });

        $field_ids = array_map(function ($item) {
            return $item->field_id;
        }, $compulsoryfee);

        //check if it has been paid
        $paidTransactions = STransaction::getPaidTransactionsForSession($this->student->std_logid, $field_ids);

        if ($paidTransactions->isEmpty()) {
            $fees = $compulsoryfee;
        } else {
            $fees = $otherfee;
        }


        $schoolfield_ids = array_map(function ($item) {
            return $item->field_id;
        }, $otherfee);

        //check if schools has been paid
        $paidTransaction = STransaction::getPaidTransactionsForSession($this->student->std_logid, $schoolfield_ids);

        if ($paidTransaction->isEmpty()) {
            return $fees;
        } else {
            return [];
        }
    }

    public function insertJsonData($jsonData, $requestType)
    {
        try {

            // Check if the JSON data contains HTML tags
            if ($this->containsHtmlTags($jsonData)) {
                $jsonData = '503 Service Unavailable';
            }

            $inserted = DB::table('remitalogs')->insert([
                'json_data' => $jsonData,
                'requesttype' => $requestType,
            ]);

            return $inserted ? true : false;
        } catch (\Exception $e) {
            Log::error('Error inserting JSON data: ' . $e->getMessage());
            return false;
        }
    }

    private function containsHtmlTags($string)
    {
        return preg_match('/<[^>]*>/', $string) === 1;
    }

    public function getStudentFeeExclusion(): int|null
    {
        $sessSem = StdSession::getStdCurrentSession($this->student->stdprogramme_id);
        $balance = DB::table('exclusion')
            ->where('matno', $this->student->matric_no)
            ->where('sess', 2023)
            // ->where('sess', $sessSem['cs_session'])
            ->value('balance');

        return $balance ?? -1;
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getStudentPreviousFees()
    {

        $progId = $this->student->stdprogramme_id;
        $levelId = $this->student->stdlevel;
        $progType = $this->student->stdprogrammetype_id;
        $residentStatus = $this->student->state_of_origin == 10 ? 'resident' : 'non-resident';
        $sessionId = 2023; //leveraging this for past fees
        $semester = 'First Semester';

        return $this->getFees($progId, $levelId, $progType, $residentStatus, $sessionId, $semester);
    }


    public function getStudentPreviousFeesToPay($fees, $psess)
    {

        //get compulsory fee
        $compulsoryfee = array_filter($fees, function ($item) {
            return $item->group == 1;
        });

        //get school fee
        $schoolfee = array_filter($fees, function ($item) {
            return $item->group == 0;
        });

        $compulsoryfield_ids = array_map(function ($item) {
            return $item->field_id;
        }, $compulsoryfee);

        //check if it compulsory fee has been paid
        $paidCompulsoryFee = STransaction::getPaidTransactionsForSession($this->student->std_logid, $compulsoryfield_ids, 1, $psess);

        if ($paidCompulsoryFee->isEmpty()) {
            $fees = $compulsoryfee;
        } else {
            $fees = $schoolfee;
        }

        $schoolfield_ids = array_map(function ($item) {
            return $item->field_id;
        }, $schoolfee);

        //check if schools has been paid
        $paidSchoolFees = STransaction::getPaidTransactionsForSession($this->student->std_logid, $schoolfield_ids, 1, $psess);

        if ($paidSchoolFees->isEmpty()) {
            return $fees;
        } else {
            return [];
        }

        return $fees;
    }

    /**
     * 
     * @param int $progId
     * @param int $levelId
     * @param int $progType
     * @param string $residentStatus
     * @param int $sessionId
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getFeesSplit($levelId, $progType, $residentStatus, $sessionId)
    {

        return DB::table('tuition_fees_amt')
            ->join('fields', 'tuition_fees_amt.field_id', '=', 'fields.id')
            /*->where(function ($query) use ($progId) {
                $query->where('tuition_fees_amt.prog_id', '=', $progId)
                    ->orWhere('tuition_fees_amt.prog_id', '=', 0);
            })*/
            ->where(function ($query) use ($levelId) {
                $query->where('tuition_fees_amt.level_id', '=', $levelId)
                    ->orWhere('tuition_fees_amt.level_id', '=', 0);
            })
            ->where(function ($query) use ($progType) {
                $query->where('tuition_fees_amt.prog_type', '=', $progType)
                    ->orWhere('tuition_fees_amt.prog_type', '=', 0);
            })
            ->where(function ($query) use ($residentStatus) {
                $query->where('tuition_fees_amt.resident_status', '=', $residentStatus)
                    ->orWhere('tuition_fees_amt.resident_status', '=', 'general');
            })
            ->where('tuition_fees_amt.sessionid', '=', $sessionId)
            ->select('tuition_fees_amt.*', 'fields.field_name')
            ->get();
    }

    public function checkSchoolFeesCompletePaid()
    {

        $paidTransaction = STransaction::getPaidTransactionsForSession($this->student->std_logid, 0)->toArray();
        $policyPaid = array_sum(array_column($paidTransaction, 'policy'));

        if ($policyPaid == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getStudentBalanceFees(array $fees)
    {

        //get school fee
        $schoolfee = array_filter($fees, function ($item) {
            return $item->group == 0;
        });


        //check if schools fees is completely paid
        $checkifBalanceIsleft = self::checkSchoolFeesCompletePaid();

        if (!$checkifBalanceIsleft) {
            return $schoolfee;
        } else {
            return [];
        }
    }
}
