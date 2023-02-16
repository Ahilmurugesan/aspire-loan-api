<?php

namespace Modules\Loan\Database\factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;
use Modules\Repay\Entities\LoanRepayment;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Loan $loan) {
            //
        })->afterCreating(function (Loan $loan) {
            $this->repayments($loan);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => rand(100, 500000),
            'period' => rand(3, 10),
            'status' => LoanConstants::LOAN_PENDING,
        ];
    }

    /**
     * @param $loan
     * @return void
     */
    private function repayments($loan): void
    {
        $amount = $loan->amount;
        $period = $loan->period;
        $repaymentAmount = Loan::calculateRepay($amount, $period);

        for ($i = 1; $i <= $period; $i++) {
            LoanRepayment::create([
                'loan_id' => $loan->id,
                'due_amount' => Loan::calculateDues($i, $amount, $repaymentAmount, $period),
                'due_date' => now()->addWeeks($i),
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}
