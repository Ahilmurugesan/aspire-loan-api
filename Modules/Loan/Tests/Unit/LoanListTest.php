<?php

namespace Modules\Loan\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Tests\TestCase;

class LoanListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Function to test loan lists are not empty
     *
     * @return void
     */
    public function test_loan_list_is_not_empty_if_data_is_present(): void
    {
        Loan::factory(5)->create();

        $this->assertDatabaseCount('loans', 5);
    }

    /**
     * Function to test the list contains all loans
     *
     * @return void
     */
    public function test_loan_list_contains_all_loans(): void
    {
        Loan::factory(10)->create();
        $allLoans = Loan::all();

        $statuses = [LoanConstants::LOAN_PENDING, LoanConstants::LOAN_APPROVED, LoanConstants::LOAN_APPROVED, LoanConstants::LOAN_PAID];

        foreach ($statuses as $status) {
            $statusLoans = Loan::where('status', $status)->get();
            foreach ($statusLoans as $statusLoan) {
                $this->assertContains($statusLoan->toArray(), $allLoans->toArray());
            }
        }
    }

    /**
     * Function to test the approved loan list contains all approved loans
     *
     * @return void
     */
    public function test_approved_loan_list_contains_all_approved_loans(): void
    {
        Loan::factory(5)->create([
            'status' => LoanConstants::LOAN_APPROVED
        ]);

        $approvedLoans = Loan::where('status', LoanConstants::LOAN_APPROVED)->get();
        $allLoans = Loan::all();

        foreach ($approvedLoans as $approvedLoan) {
            $this->assertContains($approvedLoan->toArray(), $allLoans->toArray());
        }
    }

    /**
     * Function to test the pending loan list does not contain approved loans
     *
     * @return void
     */
    public function test_pending_loan_list_does_not_contain_approved_loans(): void
    {
        Loan::factory(5)->create([
            'status' => LoanConstants::LOAN_APPROVED
        ]);
        Loan::factory(5)->create([
            'status' => LoanConstants::LOAN_PENDING
        ]);
        $approvedLoans = Loan::where('status', LoanConstants::LOAN_APPROVED)->get();
        $pendingLoans = Loan::where('status', LoanConstants::LOAN_PENDING)->get();

        foreach ($pendingLoans as $rejectedLoan) {
            $this->assertNotContains($rejectedLoan->toArray(), $approvedLoans->toArray());
        }
    }
}
