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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('comment')->nullable(); // Optional comment field
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade'); // Foreign key referencing payment_types table
            $table->foreignId('bill_id')->nullable()->constrained('bills')->onDelete('set null'); // Optional foreign key referencing bills table
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null'); // Optional foreign key referencing expenses table
            $table->foreignId('investment_id')->nullable()->constrained('investments')->onDelete('set null'); // Optional foreign key referencing investments table
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->onDelete('set null'); // Optional foreign key referencing investments table
            $table->string('others')->nullable(); // Optional "others" field for additional information
            $table->enum('transaction_type', ['in', 'out']); // Defines transaction type as either credit or debit
            $table->decimal('amount', 15, 2); // Transaction amount
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
