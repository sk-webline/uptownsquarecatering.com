<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenLanguages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('code', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('lang_code', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->default(null);
            $table->integer('rtl')->nullable(false)->default(0);
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
        Schema::dropIfExists('canteen_languages');
    }
}
