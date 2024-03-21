<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationBreaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_breaks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('organisation_id')->nullable(false);
            $table->integer('canteen_setting_id')->nullable(false);
            $table->time('hour_from', $precision = 0);
            $table->time('hour_to', $precision = 0);
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
        Schema::dropIfExists('organisation_breaks');
    }
}
