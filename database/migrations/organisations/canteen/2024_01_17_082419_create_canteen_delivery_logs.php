<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenDeliveryLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_delivery_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['delivery', 'return'])->nullable(false);
            $table->integer('canteen_app_user_id')->nullable(false);
            $table->integer('canteen_purchase_id')->nullable(false);
            $table->integer('canteen_location_id')->nullable(false);
            $table->integer('canteen_cashier_id')->nullable(false);
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
        Schema::dropIfExists('canteen_delivery_logs');
    }
}
