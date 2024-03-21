<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnsInVivaWalletLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viva_wallet_logs', function (Blueprint $table) {
            $table->string('transaction_type')->default('order')->nullable(false)->after('guest_id');
            $table->integer('canteen_user_id')->nullable(true)->after('transaction_type')->comment('Assign card to this canteen user');
            $table->string('nickname')->default(null)->nullable(true)->after('transaction_type');
            $table->integer('ErrorCode')->default(null)->nullable(true)->after('total');
            $table->string('ErrorMessage')->default(null)->nullable(true)->after('ErrorCode');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viva_wallet_logs', function (Blueprint $table) {
            //
        });
    }
}
