<?php

namespace Modules\Proposal\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Loan\Entities\Loan;
use Modules\Repay\Entities\LoanRepayment;

class LoanRequestServices
{
    /**
     * @param  array  $loanData
     * @return mixed
     * @throws Exception
     */
    public function handleLoanCreation(array $loanData): mixed
    {
        try {
            DB::beginTransaction();

            $loan = Loan::create($loanData);
            $this->generateScheduledRepayments($loan->id, $loan->amount, $loan->period);

            DB::commit();

            return $loan->load('user', 'analyst', 'repayments');

        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception('Loan creation failed');
        }
    }

    /**
     * Function to save the loan repayments
     *
     * @param $id
     * @param $amount
     * @param $period
     * @return true
     * @throws Exception
     */
    public function generateScheduledRepayments($id, $amount, $period): bool
    {
        try {
            $repaymentAmount = Loan::calculateRepay($amount, $period);

            $data = [];
            for ($i = 1; $i <= $period; $i++) {
                $array = [
                    'due_amount' => Loan::calculateDues($i, $amount, $repaymentAmount, $period),
                    'due_date' => now()->addWeeks($i),
                    'loan_id' => $id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
                $data[] = $array;
            }

            DB::beginTransaction();

            LoanRepayment::insert($data);

            DB::commit();

            return true;

        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception();
        }
    }
}
