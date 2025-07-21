<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use App\Models\CTransaction;
use App\Models\ClearanceFees;
use App\Traits\ValidatesUser;
use App\Models\ClearanceFeePack;

class ClearanceController extends Controller
{
    use ValidatesUser;

    protected $schoolInfo;

    protected $student;

    public function __construct()
    {
        $this->schoolInfo = SchoolInfo::first();
        $this->middleware(function ($request, $next) {
            $response = $this->validateUser();
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            return $next($request);
        });
    }

    public function home()
    {
        $data = $this->prepareTransactionData(function ($csid) {
            return CTransaction::getPaidTransactions($csid);
        });

        return view('clearance.dashboard', $data);
    }

    public function clearancefees()
    {
        $fees = ClearanceFees::with('feeField')->where('prog_id', $this->student->prog_id)->get()->toArray();
        if (empty($fees)) {
            return redirect('/clearanceFees')->with('error', 'Fee Pack not found.');
        }
        // Group fees by pack_id
        $groupedFees = [];
        foreach ($fees as $fee) {
            $packId = $fee['pack_id'];
            if (!isset($groupedFees[$packId])) {
                $groupedFees[$packId] = [];
            }
            $groupedFees[$packId][] = $fee;
        }

        // Define pack names
        $packNames =  ClearanceFeePack::getPacks();


        return view('clearance.fees', [
            'student' => $this->student,
            'schoolName' => $this->schoolInfo,
            'groupedFees' => $groupedFees,
            'packNames' => $packNames
        ]);
    }

    public function viewFeePack(int $packId)
    {

        $fees = ClearanceFees::with('feeField')->where(['pack_id' => $packId, 'prog_id' => $this->student->prog_id])->get();
        if ($fees->isEmpty()) {
            return redirect('/clearanceFees')->with('error', 'Fee Pack not found.');
        }

        $packNames = ClearanceFeePack::pluck('pack_name', 'pack_id')->toArray();

        return view('clearance.viewfees', [
            'student' => $this->student,
            'schoolName' => $this->schoolInfo,
            'fees' => $fees,
            'packName' => $packNames[$packId] ?? 'Unknown Pack'
        ]);
    }

    public function viewFees(int $rrr)
    {
        $trans = CTransaction::where(['rrr' => $rrr, 'trans_custom1' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/clearanceFees')->with('error', 'Transaction not found.');
        }

        return view('clearance.viewfee', [
            'student' => $this->student,
            'schoolName' => $this->schoolInfo,
            'trans' => $trans
        ]);
    }

    public function phistory()
    {
        $data = $this->prepareTransactionData(function ($csid) {
            return CTransaction::getAllTransactions($csid);
        });

        return view('clearance.paymenthistory', $data);
    }

    public function printReceipt(int $transno)
    {
        $transIds = CTransaction::getPaidTransaction($transno)->toArray();

        foreach ($transIds as &$trans) {
            $trans['packs'] = ClearanceFees::getPackFromTransaction($trans['trans_no'])->pluck('pack_id');
        }


        $packId =  $trans['packs'][0];



        $packName = ClearanceFeePack::where('pack_id', $packId)->pluck('pack_name')->implode(', ');

        return view('clearance.paymentreceipt', [
            'student' => $this->student,
            'trans' => $transIds,
            'schoolName' => $this->schoolInfo,
            'packName' =>  $packName
        ]);
    }

    private function prepareTransactionData($transactionFetcher)
    {
        $transactions = $transactionFetcher($this->student->csid)->toArray();

        $trans = json_decode(json_encode($transactions));

        $packNames = ClearanceFeePack::getPacks();

        foreach ($trans as $tran) {
            $packIds = ClearanceFees::getPackFromTransaction($tran->trans_no)->pluck('pack_id');
            $tran->packs = $packIds->toArray();
        }

        return [
            'student' => $this->student,
            'trans' => $trans,
            'schoolName' => $this->schoolInfo,
            'packNames' => $packNames
        ];
    }
}
