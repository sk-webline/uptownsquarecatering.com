<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('canteen_product_category_id')->nullable(false);
            $table->string('name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->decimal('price', 10, 2)->nullable(false);
            $table->integer('thumbnail_img');
            $table->tinyInteger('status')->nullable(false)->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canteen_products');
    }
}



