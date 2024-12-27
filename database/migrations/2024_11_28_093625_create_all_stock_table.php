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
        Schema::create('all_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('container_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('buy_price', 20, 2);
            $table->decimal('sell_price', 20, 2);
            $table->decimal('comission', 20, 2);
            $table->date('stock_date');
            $table->decimal('paid_amount', 20, 2);
            $table->decimal('dues', 20, 2);
            $table->decimal('total', 20, 2);
            $table->integer('priority')->default(1); // Default priority to 1
            $table->string('image')->nullable(); // Optional image column
            $table->boolean('status')->default(1); // Active or inactive status
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_stock');
    }
};
