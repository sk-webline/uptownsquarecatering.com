<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInCanteenPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('canteen_purchases', function (Blueprint $table) {
            $table->integer('canteen_order_detail_id')->nullable(false)->after('id');
            $table->integer('meal_code')->nullable(false)->after('canteen_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('canteen_purchases', function (Blueprint $table) {
            //
        });
    }
}
