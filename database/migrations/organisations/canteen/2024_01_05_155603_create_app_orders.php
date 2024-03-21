<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable(true);
            $table->integer('guest_id')->nullable(true);;
            $table->longText('shipping_address')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('delivery_status', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('shipping_method', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->integer('pickup_point')->nullable(true);
            $table->double('shipping_cost',2)->nullable(true);
            $table->double('shipping_vat',2)->nullable(true);
            $table->string('payment_type', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->integer('manual_payment')->nullable(false);
            $table->text('manual_payment_data')->nullable(true);
            $table->string('payment_status', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('payment_details')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('vat_btms_code', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->double('vat_percentage',2)->nullable(false);
            $table->double('vat_amount',2)->nullable(false);
            $table->double('subtotal',2)->nullable(false);
            $table->double('grand_total',2)->nullable(false);
            $table->double('coupon_discount',2)->nullable(false);
            $table->mediumText('code')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('tracking_number', 200)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->integer('date')->nullable(false);
            $table->integer('viewed')->default(0)->nullable(false);
            $table->integer('delivery_viewed')->default(1)->nullable(false);
            $table->integer('payment_status_viewed')->default(1)->nullable(true);
            $table->tinyInteger('confirm_page_seen')->default(0)->nullable(false);
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
        Schema::dropIfExists('app_orders');
    }
}
