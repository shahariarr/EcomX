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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('sku')->unique();
            $table->string('upc_ean_barcode')->nullable();
            $table->string('model_number')->nullable();
            $table->decimal('shipping_cost', 8, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('slug');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->decimal('price', 8, 2);
            $table->decimal('discount_price', 8, 2)->nullable();
            $table->integer('stock_quantity');
            $table->enum('stock_status', ['In Stock', 'Out of Stock', 'Pre-order']);
            $table->integer('reorder_level')->nullable();
            $table->string('front_view_image')->nullable();
            $table->string('back_view_image')->nullable();
            $table->string('side_view_image')->nullable();
            $table->string('video')->nullable();
            $table->text('short_description');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('user_id'); // Add this line
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Add this line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
