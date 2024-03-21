<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppVivaWalletLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_viva_wallet_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->nullable(false);
            $table->integer('user_id')->nullable(true);
            $table->integer('parent_user_id')->nullable(true);;
            $table->string('transaction_type', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('customerTrns', 250)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->text('customer_details')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('sourceCode', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('SourceName', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->bigInteger('merchantTrns')->nullable(false);
            $table->text('all_requests')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('BankId', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('OrderCode', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('CardNumber', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('TransactionId', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('ReferenceNumber', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('TransactionStatusId', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->integer('TransactionTypeId')->nullable(true);
            $table->string('CardCountryCode', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->text('Tags')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('RetrievalReferenceNumber', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('CorrelationId', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->bigInteger('EventTypeId')->nullable(true);
            $table->string('Delay', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('MessageId', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->string('RecipientId', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->integer('MessageTypeId')->nullable(true);
            $table->text('cart_items')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->double('vat_percentage',10, 2)->nullable(false);
            $table->string('vat_btms_code', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->double('subtotal',10, 2)->nullable(false);
            $table->double('vat',10, 2)->nullable(false);
            $table->double('total',10, 2)->nullable(false);
            $table->integer('ErrorCode')->default(null)->nullable(true);
            $table->string('ErrorMessage')->default(null)->nullable(true);
            $table->tinyInteger('start_process')->default(0)->nullable(false);
            $table->tinyInteger('run_script')->default(0)->nullable(false);
            $table->tinyInteger('callback')->default(0)->nullable(false);
            $table->tinyInteger('pending_page_seen')->default(0)->nullable(false);
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
        Schema::dropIfExists('app_viva_wallet_logs');
    }
}
