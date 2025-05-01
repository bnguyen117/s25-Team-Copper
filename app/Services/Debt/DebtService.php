<?php

namespace App\Services\Debt;

use App\Models\Transaction;
use App\Models\Debt;
use Illuminate\Validation\ValidationException;

class DebtService
{
    /**
     * Validate Debt Transaction
     */
    public function validateDebt(array $data, callable $notify): void
    {
        // Return if transaction is not a debt.
        if ($data['transaction_type'] !== 'debt') return;

        // Ensure payment for a debt is atleast the interest owed.
        $debt = Debt::find($data['debt_id']);
        $interestOwed = $debt->amount * ($debt->interest_rate/1200);
        if ($data['amount'] < $interestOwed) {
            $notify('Payment is Too Low', "Payment must be atleast the interest owed of \$" . number_format($interestOwed, 2));
            throw ValidationException::withMessages([]);
        }
    }

    /**
     * Update the Debt record's amount.
     */
    public function updateDebt(Transaction $record, bool $reverse = false): void
    {
        if (!$record->debt) return;
        $debt = $record->debt->fresh();
        
        // Add the transaction's impact back onto the debt amount.
        if ($reverse) $debt->amount += $record->principal_paid ?? 0;
        else {
            $split = $this->calculatePaymentSplit($debt, $record->amount);
            $record->interest_paid = $split['interest_paid'];
            $record->principal_paid = $split['principal_paid'];
            $debt->amount -= $split['principal_paid'];
            $record->save();
        }
        $debt->save();
    }

    /**
     * Calculates the split between interest paid and principal paid for a payment 
     */
    private function calculatePaymentSplit (Debt $debt, float $paymentAmount): array {
        $monthlyRate = $debt->interest_rate / 1200;                         // Convert annual rate to monthly
        $interestPaid = $debt->amount * $monthlyRate;                       // Interest paid based on current debt amount
        $principalPaid = min($paymentAmount - $interestPaid, $debt->amount);// Principal paid based on interest paid
        return ['interest_paid' => $interestPaid, 'principal_paid' => $principalPaid];
    }
}