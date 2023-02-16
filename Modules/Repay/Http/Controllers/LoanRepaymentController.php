<?php

namespace Modules\Repay\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Modules\Repay\Http\Requests\AddRepaymentRequest;
use Modules\Repay\Http\Resources\LoanRepaymentResource;
use Modules\Repay\Services\LoanRepaymentServices;

class LoanRepaymentController extends Controller
{
    /**
     * @param  LoanRepaymentServices  $loanRepaymentServices
     */
    public function __construct(private LoanRepaymentServices $loanRepaymentServices) {}

    /**
     * Function to add the repayment amount
     *
     * @param  AddRepaymentRequest  $request
     * @param $id
     * @return LoanRepaymentResource
     * @throws AuthorizationException
     * @throws Exception
     */
    public function update(AddRepaymentRequest $request, $id): LoanRepaymentResource
    {
        $loan = Loan::findOrFail($id);

        $this->authorize('update', $loan);

        $repayment = $this->loanRepaymentServices->addRepayment($loan, $request->amount);

        return new LoanRepaymentResource($repayment);
    }
}
