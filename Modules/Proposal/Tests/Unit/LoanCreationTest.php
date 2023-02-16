<?php

namespace Modules\Proposal\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Function to test the loan request submission
     *
     * @return void
     */
    public function test_loan_request_submission(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'period' => 3,
            'status' => LoanConstants::LOAN_PENDING,
        ]);

        $this->assertEquals(10000, $loan->amount);
        $this->assertEquals(3, $loan->period);
        $this->assertEquals(LoanConstants::LOAN_PENDING, $loan->status);
    }

    /**
     * Function to test the scheduled repayments generated correctly
     *
     * @return void
     */
    public function test_scheduled_repayments_generated_correctly(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'period' => 3,
            'status' => LoanConstants::LOAN_PENDING,
        ]);

        // assert that the scheduled repayments have been generated correctly
        $scheduledRepayments = $loan->repayments()->get();
        $this->assertCount(3, $scheduledRepayments);
        $this->assertEquals(now()->addWeek()->format('Y-m-d'), $scheduledRepayments[0]->due_date);
        $this->assertEquals(now()->addWeeks(2)->format('Y-m-d'), $scheduledRepayments[1]->due_date);
        $this->assertEquals(now()->addWeeks(3)->format('Y-m-d'), $scheduledRepayments[2]->due_date);
        $this->assertEquals(3333.33, $scheduledRepayments[0]->due_amount);
        $this->assertEquals(3333.33, $scheduledRepayments[1]->due_amount);
        $this->assertEquals(3333.34, $scheduledRepayments[2]->due_amount);
    }
}
