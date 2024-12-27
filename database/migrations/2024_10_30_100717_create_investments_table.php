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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('investor_name'); // Name of the investor
            $table->foreignId('payment_id')->constrained('payment_types')->onDelete('cascade'); // Foreign key referencing payment_types table
            $table->decimal('amount', 10, 2); // Investment amount
            $table->string('image')->nullable(); // Investment amount
            $table->string('deed_image')->nullable(); // Investment amount
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
