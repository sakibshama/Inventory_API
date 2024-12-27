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
        Schema::create('sells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Foreign key referencing customers table
            $table->decimal('total_amount', 20, 2)->nullable(); // Optional total amount
            $table->decimal('total_profit', 20, 2)->nullable(); // Optional total amount
            $table->decimal('total_comission', 20, 2)->nullable(); // Optional total amount
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
