<?php

namespace Modules\Loan\Tests\Unit;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\LoanException;
use Modules\Loan\Exceptions\UnAuthorizedException;
use Modules\Loan\Http\Controllers\LoanController;
use Modules\Loan\Services\LoanServices;
use Tests\TestCase;

class LoanShowTest extends TestCase
{
    /**
     * Test Function to show the loan details of the user
     *
     * @return void
     * @throws AuthorizationException
     */
    public function test_loan_show_returns_loan_details(): void
    {
        $user1 = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user1->id]);
        $service = $this->mock(LoanServices::class);
        $service->shouldReceive('handleShow')->once()->with($loan)->andReturn($loan);

        $this->actingAs($user1);
        $controller = new LoanController($service);
        $response = collect($controller->show($loan))->toArray();

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($loan->period, $response['period']);
        $this->assertEquals($loan->amount, $response['amount']);
    }

    /**
     * Test function for unauthorized user
     *
     * @return void
     * @throws AuthorizationException
     */
    public function test_loan_show_returns_403_for_unauthorized_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $loan1 = Loan::factory()->create(['user_id' => $user1->id]);
        $loan2 = Loan::factory()->create(['user_id' => $user2->id]);

        $service = $this->mock(LoanServices::class);

        $this->expectException(UnAuthorizedException::class);

        $this->actingAs($user1);
        $controller = new LoanController($service);
        $controller->show($loan2);
    }

    /**
     * Test function for throwing exception for non existent loan
     *
     * @return void
     * @throws AuthorizationException
     */
    public function test_loan_show_throws_exception_for_nonexistent_loan(): void
    {
        $service = $this->mock(LoanServices::class);

        $this->expectException(ModelNotFoundException::class);

        $controller = new LoanController($service);
        $controller->show(Loan::findOrFail(1));

    }
}
