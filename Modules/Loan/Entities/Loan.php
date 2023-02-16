<?php

namespace Modules\Loan\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Loan\Database\Factories\LoanFactory;
use Modules\Repay\Entities\LoanRepayment;

class Loan extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return LoanFactory::new();
    }

    /**
     * Relationship between user and the loan
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship between analyst user and the loan
     *
     * @return BelongsTo
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship between loans and repayments
     *
     * @return HasMany
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    /**
     * Function to calculate to repay
     *
     * @param $amount
     * @param $period
     * @return float
     */
    public static function calculateRepay($amount, $period ): float
    {
        return round($amount / $period, 2);
    }

    /**
     * Function to calculate to dues
     *
     * @param $key
     * @param $amount
     * @param $repaymentAmount
     * @param $period
     * @return float
     */
    public static function calculateDues($key, $amount, $repaymentAmount, $period): float
    {
        return $key == $period ? ($amount - ($repaymentAmount * ($period - 1))) : $repaymentAmount;
    }
}
