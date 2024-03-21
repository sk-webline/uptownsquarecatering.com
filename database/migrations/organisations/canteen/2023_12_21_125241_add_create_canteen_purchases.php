<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreateCanteenPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('canteen_app_user_id')->nullable(false);
            $table->integer('canteen_setting_id')->nullable(false);
            $table->integer('canteen_product_id')->nullable(false);
            $table->integer('organisation_break_id')->nullable(false);
            $table->date('date')->nullable(false);
            $table->decimal('price', 10,2)->nullable(false);
            $table->tinyInteger('custom_price_status')->nullable(false);
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
        //
    }
}
