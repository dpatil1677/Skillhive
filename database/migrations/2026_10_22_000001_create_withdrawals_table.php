<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('method'); // 'upi' or 'bank'
            $table->json('payout_details'); // upi_id OR {account_number, ifsc, bank_name, account_holder}
            $table->string('status')->default('pending'); // 'pending', 'processing', 'completed', 'rejected'
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
