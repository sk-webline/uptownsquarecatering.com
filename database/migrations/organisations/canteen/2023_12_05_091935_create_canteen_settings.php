<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('organisation_id')->nullable(false)->nullable(false);
            $table->dateTime('date_from', $precision = 0)->nullable(false);
            $table->dateTime('date_to', $precision = 0)->nullable(false);
            $table->integer('minimum_preorder_minutes')->nullable(false)->comment('Minimum minutes before for the user to preorder a meal');
            $table->integer('minimum_cancellation_minutes')->nullable(false)->comment('Minimum minutes before for the user to cancel a meal');;
            $table->integer('access_minutes')->nullable(false)->comment('Minutes to access break before and after');
            $table->integer('max_undo_delivery_minutes')->nullable(false)->comment('Max Minutes the canteen cashier can undo a delivery after its delivery');
            $table->longText('working_week_days')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->longText('working_days_january')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_february')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_march')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_april')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_may')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_june')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_july')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_august')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_september')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_october')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_november')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('working_days_december')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(true);
            $table->longText('holidays')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
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
        Schema::dropIfExists('canteen_settings');
    }
}
