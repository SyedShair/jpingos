<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('method'); // cod, card
            $table->string('status')->default('pending'); // pending, paid, failed, refunded, cancelled

            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('GBP');

            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};