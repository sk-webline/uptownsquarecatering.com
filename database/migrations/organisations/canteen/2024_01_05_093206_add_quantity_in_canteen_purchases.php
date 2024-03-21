<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityInCanteenPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('canteen_purchases', function (Blueprint $table) {
            $table->time('break_hour_to', $precision = 0)->after('canteen_product_id')->nullable(false);
            $table->time('break_hour_from', $precision = 0)->after('canteen_product_id')->nullable(false);
            $table->integer('break_num')->default(0)->after('canteen_product_id')->nullable(false);
            $table->integer('quantity')->default(0)->after('date')->nullable(false);
            $table->dropColumn('organisation_break_id');
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
