<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenAppUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_app_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('card_id')->nullable(false);
            $table->string('username')->nullable(false);
            $table->string('password')->nullable(false);
            $table->integer('credit_card_token_id');
            $table->decimal('daily_limit',10,2)->nullable(false)->default(0);
            $table->text('cart')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->default(null)->nullable(false);
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
        Schema::dropIfExists('canteen_app_users');
    }
}
