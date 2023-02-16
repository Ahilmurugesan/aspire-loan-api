<?php

namespace Modules\Loan\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test case to list all data with pagination
     *
     * @return void
     */
    public function test_admin_can_get_all_loan_list_data_with_pagination(): void
    {
        Loan::factory()->count(20)->create();
        $admin = User::factory()->create(['is_admin' => 1]);
        $response = $this->actingAs($admin)->get('/api/loan');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure($this->paginationStructure());
        $response = $this->actingAs($admin)->get($response->json('links.next'));
        $response->assertJsonCount(5, 'data');
    }

    /**
     * Test case to list only user  data with pagination
     *
     * @return void
     */
    public function test_user_can_only_get_their_own_loans(): void
    {
        $user       = User::factory()->create();
        $otherUser  = User::factory()->create();

        Loan::factory()->count(20)->create([
            'user_id' => $user->id
        ]);
        Loan::factory()->count(20)->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->get('/api/loan');

        $response->assertOk();
        $response->assertJsonFragment([
            'user_id' => $user->id,
        ]);
        $response->assertJsonStructure($this->paginationStructure());
    }

    /**
     * Function to get the loan based on the status filter
     *
     * @return void
     */
    public function test_get_loan_based_on_status_filter(): void
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        Loan::factory()->count(20)->create();
        Loan::factory()->count(5)->create([
            'status' => LoanConstants::LOAN_APPROVED
        ]);

        $response = $this->actingAs($admin)->get('/api/loan?status='.LoanConstants::LOAN_APPROVED);

        $response->assertOk();
        $response->assertJsonFragment([
            'status' => LoanConstants::LOAN_APPROVED,
        ]);
        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure($this->paginationStructure());
    }
}
