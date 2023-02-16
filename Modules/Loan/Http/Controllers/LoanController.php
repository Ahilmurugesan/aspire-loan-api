<?php

namespace Modules\Loan\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Http\Requests\LoanApprovalRequest;
use Modules\Loan\Http\Resources\LoanCollection;
use Modules\Loan\Http\Resources\LoanResource;
use Modules\Loan\Services\LoanServices;

class LoanController extends Controller
{
    /**
     * @param  LoanServices  $loanServices
     */
    public function __construct(private LoanServices $loanServices) {}

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return LoanCollection
     */
    public function index(Request $request): LoanCollection
    {
        $status = null;
        if ($request->has('status')) {
            $status = $request->input('status');
        }

        $loans = $this->loanServices->handleListing(auth()->user(), $status);

        return new LoanCollection($loans);
    }

    /**
     * Function to show the loan data
     *
     * @param  Loan  $loan
     * @return LoanResource
     * @throws AuthorizationException
     */
    public function show(Loan $loan): LoanResource
    {
        $this->authorize('view', $loan);

        $loan = $this->loanServices->handleShow($loan);

        return new LoanResource($loan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  LoanApprovalRequest  $request
     * @param  int  $id
     * @return LoanResource
     * @throws Exception
     */
    public function update(LoanApprovalRequest $request, int $id): LoanResource
    {
        $loan = $this->loanServices->handleApproval([
            'id'            => $id,
            'approved'      => $request->approved,
            'comments'      => $request->comments ?? null,
            'analyst_id'    => auth()->id()
        ]);

        return new LoanResource($loan);
    }
}
