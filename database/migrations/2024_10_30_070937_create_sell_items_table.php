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
        Schema::create('sell_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_id')->constrained()->onDelete('cascade'); // Foreign key referencing sells table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key referencing products table
            $table->foreignId('stock_id')->constrained()->onDelete('cascade'); // Foreign key referencing products table
            $table->foreignId('container_id')->constrained()->onDelete('cascade'); // Foreign key referencing products table
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2); // Calculated as quantity * price
            $table->decimal('comission', 15, 2); // Calculated as quantity * price
            $table->decimal('profit', 15, 2)->nullable(); // Optional profit calculation
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_items');
    }
};
