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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_id')->constrained()->onDelete('cascade'); // Foreign key referencing sells table
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Foreign key referencing customers table
            $table->foreignId('user_id')->constrained('all_users')->onDelete('cascade'); // Foreign key referencing customers table
            $table->decimal('total_amount', 15, 2); // Total bill amount
            $table->decimal('paid_amount', 15, 2)->default(0); // Amount paid by the customer
            $table->decimal('due_amount', 15, 2)->default(0); // Remaining amount to be paid
            $table->decimal('discount', 10, 2)->default(0); // Discount applied to the bill
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
