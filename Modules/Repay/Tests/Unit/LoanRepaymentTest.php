<?php

namespace Modules\Repay\Tests\Unit;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\LoanException;
use Modules\Loan\Loan\LoanConstants;
use Modules\Repay\Services\LoanRepaymentServices;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     * @throws Exception
     */
    public function test_repayment_status_changes_when_paid(): void
    {
        $loan = Loan::factory()->create([
            'amount' => 10000,
            'period' => 3,
            'status' => LoanConstants::LOAN_APPROVED,
        ]);

        $repaymentAmount = 3333.33;

        $service = new LoanRepaymentServices();
        $response = $service->addRepayment($loan, $repaymentAmount);

        $this->assertEquals(LoanConstants::REPAYMENT_PAID, $response->status);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_loan_status_changes_to_paid_if_all_repayments_are_paid(): void
    {
        $loan = Loan::factory()->create([
            'amount' => 300,
            'period' => 3,
            'status' => LoanConstants::LOAN_APPROVED,
        ]);

        $repaymentAmount = 100;

        $service = new LoanRepaymentServices();
        for ($i=0; $i<$loan->period; $i++)
        {
            $service->addRepayment($loan, $repaymentAmount);
        }

        $this->assertEquals(LoanConstants::LOAN_PAID, $loan->status);
    }

    /**
     * Function to test that user should not be able to add low due amount
     *
     * @return void
     * @throws Exception
     */
    public function test_repayment_should_not_accept_low_due_amount(): void
    {
        $loan = Loan::factory()->create([
            'amount' => 1000,
            'period' => 3,
            'status' => LoanConstants::LOAN_APPROVED,
        ]);

        $repaymentAmount = 100;

        $this->expectException(LoanException::class);

        $service = new LoanRepaymentServices();
        $service->addRepayment($loan, $repaymentAmount);
    }

    /**
     * Function to test the revise due payment
     *
     * @return void
     * @throws Exception
     */
    public function test_repayment_due_amount_gets_revised_if_paid_amount_is_greater_than_due_amount(): void
    {
        $loan = Loan::factory()->create([
            'amount' => 300,
            'period' => 3,
            'status' => LoanConstants::LOAN_APPROVED,
        ]);

        $repaymentAmount = 100;
        $payingAmount    = 200;

        $service = new LoanRepaymentServices();
        $service->addRepayment($loan, $payingAmount);

        $due_amounts = $loan->repayments()->where('status', LoanConstants::REPAYMENT_PENDING)->pluck('due_amount');

        $this->assertNotEquals($repaymentAmount, $due_amounts[0]);
        $this->assertNotEquals($repaymentAmount, $due_amounts[1]);
    }
}
