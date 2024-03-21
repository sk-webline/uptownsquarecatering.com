<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('organisation_id')->nullable(false);
            $table->string('name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
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
        Schema::dropIfExists('canteen_locations');
    }
}
