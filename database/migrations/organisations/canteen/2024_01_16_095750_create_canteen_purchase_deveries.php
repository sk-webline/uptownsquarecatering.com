<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenPurchaseDeveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_purchase_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('canteen_app_user_id')->nullable(false);
            $table->integer('canteen_purchase_id')->nullable(false);
            $table->integer('canteen_location_id')->nullable(false);
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
        Schema::dropIfExists('canteen_purchase_deliveries');
    }
}
