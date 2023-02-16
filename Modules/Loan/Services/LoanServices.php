<?php

namespace Modules\Loan\Services;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;

class LoanServices
{

    /**
     * Function to get loans
     *
     * @param $user
     * @param $status
     * @return LengthAwarePaginator
     */
    public function handleListing($user, $status): LengthAwarePaginator
    {
        $loans = Loan::query();

        if (!$user->is_admin) {
            $loans = $loans->where('user_id', $user->id);
        }

        if(!is_null($status))
        {
            $loans = $loans->where('status', $status);
        }
        return $loans->with('user', 'analyst', 'repayments')->paginate();
    }

    /**
     * Function to get single loan data
     *
     * @param $loan
     * @return mixed
     */
    public function handleShow($loan): mixed
    {
        return $loan->load('repayments', 'user', 'analyst');
    }

    /**
     * Function to update the loan approval status
     *
     * @param  array  $loanData
     * @return mixed
     * @throws Exception
     */
    public function handleApproval(array $loanData): mixed
    {
        try {
            DB::beginTransaction();

            $loan = Loan::findOrFail($loanData['id']);

            $loan->update([
                'status'     => $loanData['approved'] ? LoanConstants::LOAN_APPROVED : LoanConstants::LOAN_DECLINED,
                'analyst_id' => $loanData['analyst_id']
            ]);

            DB::commit();

            return $loan->load('user', 'analyst', 'repayments');

        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception('Loan status update failed');
        }
    }
}
