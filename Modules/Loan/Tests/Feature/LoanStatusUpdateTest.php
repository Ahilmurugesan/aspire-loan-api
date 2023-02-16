<?php

namespace Modules\Loan\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test function to check the loan approval
     *
     * @return void
     */
    public function test_loan_approval(): void
    {
        $loan   = Loan::factory()->create();
        $admin  = User::factory()->create(['is_admin' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.loanApproval', ['id' => $loan->id]), ['approved' => 1]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => LoanConstants::LOAN_APPROVED]);
    }

    /**
     * Test function to check the loan decline
     *
     * @return void
     */
    public function test_loan_decline(): void
    {
        $loan   = Loan::factory()->create();
        $admin  = User::factory()->create(['is_admin' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.loanApproval', ['id' => $loan->id]), ['approved' => 0]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => LoanConstants::LOAN_DECLINED]);
    }

    /**
     * Test function to check the loan decline
     *
     * @return void
     */
    public function test_non_admin_user_exception(): void
    {
        $loan   = Loan::factory()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.loanApproval', ['id' => $loan->id]), ['approved' => 0]);

        $response->assertStatus(401);
    }
}
