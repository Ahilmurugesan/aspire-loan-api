<?php

namespace Modules\Repay\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\LoanException;
use Modules\Loan\Loan\LoanConstants;

class LoanRepaymentServices
{
    /**
     * Function to add the repayment and close the loan
     *
     * @param  Loan  $loan
     * @param  $amount
     * @return Model|HasMany
     * @throws Exception
     */
    public function addRepayment(Loan $loan, $amount): Model|HasMany
    {
        $repayment = $this->checkRepayEligibility($loan, $amount);

        try {
            DB::beginTransaction();

            $repayment->amount_paid = $amount;
            $repayment->paid_date = now();
            $repayment->status = LoanConstants::REPAYMENT_PAID;
            $repayment->save();

            DB::commit();

            if ($amount > $repayment->due_amount) {
                $this->reviseDuesAmount($loan);
            }

            $this->reviseLoanStatus($loan);

            return $repayment;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw new LoanException('Failed to add repayment');
        }

    }

    /**
     * Function to revise the loan status
     *
     * @param $loan
     * @return bool
     */
    protected function reviseLoanStatus($loan): bool
    {
        $pendingRepays = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PENDING)->count();

        if ($pendingRepays === 0) {
            $loan->status = LoanConstants::LOAN_PAID;
            $loan->save();
        }

        return true;
    }

    /**
     * @param $loan
     * @throws Exception
     */
    protected function reviseDuesAmount($loan): void
    {
        try {
            $pendingRepays = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PENDING)->get();
            $duesPaid   = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PAID)->sum('amount_paid');
            $loanAmount = $loan->amount;
            $noOfDues = $pendingRepays->count();

            $remainingLoanAmount = $loanAmount - $duesPaid;
            $repaymentAmount = Loan::calculateRepay($remainingLoanAmount, $noOfDues);

            DB::beginTransaction();
            $i = 0;
            foreach ($pendingRepays as $pendingRepay)
            {
                $amount = Loan::calculateDues($i, $remainingLoanAmount, $repaymentAmount, $noOfDues);
                $pendingRepay->due_amount = $amount;

                if((int) $amount === 0)
                {
                    $pendingRepay->status = LoanConstants::REPAYMENT_PAID;
                }
                $pendingRepay->save();
                $i++;
            }
            DB::commit();
        }catch (Exception $e)
        {
            Log::error($e->getMessage());
            throw new Exception();
        }
    }

    /**
     * Function to validate whether to process the add repayments
     *
     * @param $loan
     * @param $amount
     * @return mixed
     * @throws LoanException
     */
    protected function checkRepayEligibility($loan, $amount): mixed
    {
        if ($loan->status !== LoanConstants::LOAN_APPROVED) {
            throw new LoanException("Repayment cannot be done because loan has not been approved");
        }

        $duesPaid = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PAID)->sum('amount_paid');

        if( ($duesPaid + $amount) > $loan->amount)
        {
            throw new LoanException("Repayment amount {$amount} with the aggregate of dues paid should not be greater than the loan amount");
        }

        $repayment = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PENDING)->firstOrFail();

        if ($amount < $repayment->due_amount) {
            throw new LoanException("Repayment amount {$amount} should be greater than or equal to due amount {$repayment->due_amount}");
        }

        return $repayment;
    }
}
