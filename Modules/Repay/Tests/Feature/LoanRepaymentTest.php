<?php

namespace Modules\Repay\Tests\Feature;

use App\Models\User;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\LoanException;
use Modules\Loan\Exceptions\UnAuthorizedException;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase
{
    /**
     * Test that a user can add a repayment with an amount equal to the scheduled repayment amount.
     *
     * @return void
     */
    public function test_customer_can_add_repayment_with_amount_equal_to_scheduled_repayment_amount(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);
        $data = [
            'amount' => $loan->repayments->first()->due_amount,
        ];

        $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('loan_repayments', [
            'id'            => $loan->repayments->first()->id,
            'amount_paid'   => $data['amount'],
        ]);
    }

    /**
     * Test that a user can add a repayment with an amount greater to the scheduled repayment amount.
     *
     * @return void
     */
    public function test_customer_can_add_repayment_with_greater_amount_to_scheduled_repayment_amount(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);
        $data = [
            'amount' => $loan->repayments->first()->due_amount + 20,
        ];

        $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);

        $response->assertStatus(200);
        $this->assertGreaterThan($loan->repayments->first()->due_amount, $data['amount']);
        $this->assertDatabaseHas('loan_repayments', [
            'id'            => $loan->repayments->first()->id,
            'amount_paid'   => $data['amount'],
        ]);
    }

    /**
     * Test that a user can add a repayment and status has been changed to paid
     *
     * @return void
     */
    public function test_repayments_changes_to_paid_status(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);
        $data = [
            'amount' => $loan->repayments->first()->due_amount,
        ];

        $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('loan_repayments', [
            'id'        => $loan->repayments->first()->id,
            'status'    => LoanConstants::REPAYMENT_PAID,
        ]);
    }

    /**
     * Test that a user can add all repayments and loan status has been changed to paid
     *
     * @return void
     */
    public function test_loan_changes_to_paid_status_if_all_repayments_done(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);

        foreach ($loan->repayments as $repayment)
        {
            $data = [
                'amount' => $repayment->due_amount,
            ];
            $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);
            $response->assertStatus(200);
        }

        $this->assertDatabaseHas('loans', [
            'id'        => $loan->id,
            'status'    => LoanConstants::LOAN_PAID,
        ]);
    }

    /**
     * Test that a user is not allowed to enter the smaller amount
     *
     * @return void
     */
    public function test_customer_is_not_allowed_to_enter_smaller_amount(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(LoanException::class);

        $data = [
            'amount' => 10,
        ];
        $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);
    }

    /**
     * Test that a user can add do full repayment and status gets changed to paid in both tables
     *
     * @return void
     */
    public function test_customer_is_allowed_o_pre_close_full_loan(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'amount'    => 1000,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);

        $data = [
            'amount' => 1000,
        ];

        $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('loans', [
            'id'        => $loan->id,
            'status'    => LoanConstants::LOAN_PAID,
        ]);
    }

    /**
     * Test that a user is not allowed to update the payment if the inputs are wrong
     *
     * @return void
     */
    public function test_customer_is_not_allowed_to_add_payment_with_wrong_inputs(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id,
            'amount'    => 1000,
            'status'    => LoanConstants::LOAN_APPROVED
        ]);

        $data = [
            'amount' => 0,
        ];

        $response = $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /**
     * Test that customer is not allowed to add payment for non-approved loans
     *
     * @return void
     */
    public function test_customer_is_not_allowed_to_add_payment_for_non_approval_loan(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(LoanException::class);

        $data = [
            'amount' => 100,
        ];
        $this->actingAs($user)->postJson("/api/loan/{$loan->id}/repayments", $data);
    }

    /**
     * Test that customer is not allowed to add payments to other users loan
     *
     * @return void
     */
    public function test_customer_is_not_allowed_to_add_payment_to_other_users_loan(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id'   => $user->id
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(UnAuthorizedException::class);

        $data = [
            'amount' => 100,
        ];
        $this->actingAs($user2)->postJson("/api/loan/{$loan->id}/repayments", $data);
    }
}
