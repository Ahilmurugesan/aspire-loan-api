<?php

namespace Modules\Loan\Tests\Unit;

use App\Models\User;
use Exception;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Modules\Loan\Services\LoanServices;
use Tests\TestCase;

class LoanStatusUpdateTest extends TestCase
{
    /**
     * Function to test the loan approval
     *
     * @return void
     * @throws Exception
     */
    public function test_loan_approval(): void
    {
        $loan = Loan::factory()->create();
        $user = User::factory()->create();

        $controller = new LoanServices();
        $response = $controller->handleApproval([
            'id'            => $loan->id,
            'approved'      => 1,
            'comments'      => null,
            'analyst_id'    => $user->id
        ]);

        $this->assertEquals(LoanConstants::LOAN_APPROVED, $response->status);
    }

    /**
     * Function to test the loan decline
     *
     * @return void
     * @throws Exception
     */
    public function test_loan_decline(): void
    {
        $loan = Loan::factory()->create();
        $user = User::factory()->create();

        $controller = new LoanServices();
        $response = $controller->handleApproval([
            'id'            => $loan->id,
            'approved'      => 0,
            'comments'      => null,
            'analyst_id'    => $user->id
        ]);

        $this->assertEquals(LoanConstants::LOAN_DECLINED, $response->status);
    }
}
