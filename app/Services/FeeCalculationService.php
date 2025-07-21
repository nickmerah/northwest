<?php

namespace App\Services;

use App\Models\OFee;

class FeeCalculationService
{

    public function calculateFees(array $selectedFees, int $libraryBindingCopies = 1)
    {
        $ofees = OFee::whereIn('of_id', $selectedFees)->get();

        $totalBindingFee = 0;
        $totalAmount = 0;
        $serviceCharge = 100;

        foreach ($ofees as $ofee) {
            $feeAmount = $ofee->of_amount;

            if ($ofee->of_id == 11) {
                // If fee_id is 11 (Library Binding), multiply by the number of copies
                $totalBindingFee = $libraryBindingCopies * $feeAmount;
                $totalAmount += $totalBindingFee;
                $totalAmount = $totalAmount + $serviceCharge;
            } else {
                $totalAmount += $feeAmount;
            }
        }

        $grandTotal = $totalAmount;

        return [
            'totalBindingFee' => $totalBindingFee,
            'totalAmount' => $totalAmount,
            'serviceCharge' => $serviceCharge,
            'grandTotal' => $grandTotal,
        ];
    }
}
