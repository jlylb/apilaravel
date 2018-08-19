<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarouselIdToCarousels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flashes', function ($table) {
            $table->integer('carousels_id')->default(0)->after('id')->comment('所属幻灯片');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flashes', function ($table) {
            $table->dropColumn('carousels_id');
        });
    }
}
