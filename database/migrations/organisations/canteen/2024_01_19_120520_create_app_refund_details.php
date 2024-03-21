<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppRefundDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_refund_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('app_order_id')->nullable(false);
            $table->string('app_order_code', 200)->nullable(false);
            $table->integer('app_order_detail_id')->nullable(false);
            $table->integer('items_refunded_quantity')->nullable(false);
            $table->decimal('price')->nullable(false);
            $table->decimal('amount_refunded')->nullable(false);
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
        Schema::dropIfExists('app_refund_details');
    }
}
