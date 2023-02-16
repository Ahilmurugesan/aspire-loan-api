<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Loan\LoanConstants;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Loan::class)->index();
            $table->decimal('due_amount');
            $table->decimal('amount_paid')->nullable();
            $table->date('due_date');
            $table->datetime('paid_date')->nullable();
            $table->string('status')->default(LoanConstants::REPAYMENT_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
