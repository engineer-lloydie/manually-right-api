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
        Schema::create('guest_orders', function (Blueprint $table) {
            $table->id();
            $table->string('guest_id');
            $table->foreignId('order_master_id')->constrained('order_masters');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_orders');
    }
};
