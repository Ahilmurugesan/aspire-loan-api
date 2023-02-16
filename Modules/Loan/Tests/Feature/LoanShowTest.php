<?php

namespace Modules\Loan\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\LoanException;
use Modules\Loan\Exceptions\UnAuthorizedException;
use Tests\TestCase;

class LoanShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test function to check the user can receive the correct data
     *
     * @return void
     */
    public function test_customer_can_view_their_own_loan(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('loan.show', ['loan' => $loan->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->loanResourceStructure());
    }

    /**
     * Test function to catch the unauthorized exception
     *
     * @return void
     */
    public function test_customer_cannot_view_another_customers_loan(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Loan::factory()->create(['user_id' => $user1->id]);
        $loan2 = Loan::factory()->create(['user_id' => $user2->id]);

        $this->withoutExceptionHandling();
        $this->expectException(UnAuthorizedException::class);

        $this->actingAs($user1)
            ->get(route('loan.show', ['loan' => $loan2->id]))
            ->assertStatus(401);
    }

    /**
     * Test function to catch if guest tries to view loan details
     *
     * @return void
     */
    public function test_guest_cannot_view_loan_details(): void
    {
        $loan = Loan::factory()->create();

        $this->get(route('loan.show', ['loan' => $loan->id]), ['Accept' => 'application/json'])
                        ->assertStatus(401);
    }
}
