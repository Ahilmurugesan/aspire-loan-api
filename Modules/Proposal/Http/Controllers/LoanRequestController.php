<?php

namespace Modules\Proposal\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Modules\Loan\Http\Resources\LoanResource;
use Modules\Loan\Loan\LoanConstants;
use Modules\Proposal\Http\Requests\LoanCreationRequest;
use Modules\Proposal\Services\LoanRequestServices;

class LoanRequestController extends Controller
{
    /**
     * @param  LoanRequestServices  $loanRequestServices
     */
    public function __construct(private LoanRequestServices $loanRequestServices) {}

    /**
     * Loan request from the customer
     *
     * @param  LoanCreationRequest  $request
     * @return LoanResource
     * @throws Exception
     */
    public function store(LoanCreationRequest $request): LoanResource
    {
        $loan = $this->loanRequestServices->handleLoanCreation([
                    'amount'    => round($request->amount, 2),
                    'period'    => $request->term,
                    'user_id'   => auth()->id(),
                    'status'    => LoanConstants::LOAN_PENDING
                ]);

        return new LoanResource($loan);
    }
}
