<?php

namespace Modules\Proposal\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Function to test that customer can create loan request successfully
     *
     * @return void
     */
    public function test_customer_can_create_loan_request(): void
    {
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3
        ];

        $response = $this->actingAs($user)->postJson('/api/loan/create', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure($this->loanResourceStructure());
        $response->assertJson([
            'data' => [
                'amount' => 10000,
                'period' => 3,
                'status' => LoanConstants::LOAN_PENDING
            ]
        ]);
        $this->assertCount(3, $response->json('data.repayments'));
    }

    /**
     * Function to test that customer cannot create the loan request with invalid data
     *
     * @return void
     */
    public function test_customer_cannot_create_loan_request_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $data = [
            'amount' => -5000,
            'term' => 0
        ];

        $response = $this->actingAs($user)->postJson('/api/loan/create', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount', 'term']);
    }

    /**
     * Function to test that admin should not create a loan request
     *
     * @return void
     */
    public function test_admin_cannot_create_a_loan_request(): void
    {
        $admin = User::factory()->create([
            'is_admin' => 1
        ]);
        $data = [
            'amount' => 5000,
            'term' => 5
        ];

        $response = $this->actingAs($admin)->postJson('/api/loan/create', $data);

        $response->assertUnauthorized();
    }
}
