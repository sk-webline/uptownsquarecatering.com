<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('canteen_setting_id')->nullable(false);
            $table->integer('canteen_product_id')->nullable(false);
            $table->integer('organisation_break_id')->nullable(false);
            $table->integer('organisation_break_num')->nullable(false);
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable(false);
            $table->tinyInteger('custom_price_status')->nullable(false)->default('0');
            $table->decimal('custom_price', 10, 2)->nullable(false)->default('0');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canteen_menus');
    }
}
