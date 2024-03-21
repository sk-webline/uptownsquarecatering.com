<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('app_order_id')->nullable(false);
//            $table->string('type', 200)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
//            $table->integer('type_id')->nullable(false);
            $table->integer('product_id')->nullable(false);
            $table->integer('price_type_id')->nullable(true);
            $table->double('price',2)->nullable(false);
            $table->double('vat_percentage',2)->nullable(false);
            $table->double('vat_amount',2)->nullable(false);
            $table->double('total',2)->nullable(false);
            $table->double('total_quantity',2)->nullable(false);
            $table->double('disc_amount',2)->nullable(true);
            $table->double('disc_percentage',2)->nullable(true);
            $table->tinyInteger('refunded')->default(0)->nullable(false);
            $table->integer('refunded_items')->default(0)->nullable(false);
            $table->string('payment_status', 15)->default('unpaid')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('delivery_status', 20)->default('pending')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
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
        Schema::dropIfExists('app_order_details');
    }
}
