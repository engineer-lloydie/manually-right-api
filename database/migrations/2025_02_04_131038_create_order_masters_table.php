<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->integer('guest_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('transaction_id');
            $table->integer('total_price');
            $table->integer('payment_method');
            $table->dateTime('purchase_date');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_masters');
    }
};
