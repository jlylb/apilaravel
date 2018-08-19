<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarouselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carousels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('幻灯片标题');
            $table->string('height')->default('auto')->comment('显示高度');
            $table->tinyInteger('value')->default(0)->comment('幻灯片的索引');
            $table->boolean('loop')->default(false)->comment('是否开启循环');
            $table->boolean('autoplay')->default(false)->comment('是否自动切换');
            $table->smallInteger('autoplay_speed')->default(2000)->comment('自动切换的时间间隔，单位为毫秒');
            $table->enum('dots',['inside','outside','none'])->default('inside')->comment('指示器的位置');
            $table->boolean('radius_dot')->default(false)->comment('是否显示圆形指示器');
            $table->enum('trigger',['click','hover'])->default('click')->comment('指示器的触发方式');
            $table->enum('arrow',['hover','always', 'never'])->default('hover')->comment('切换箭头的显示时机');
            $table->string('easing')->default('ease')->comment('动画效果');
            $table->boolean('status')->default(true)->comment('幻灯片是否启用');
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
        Schema::dropIfExists('carousels');
    }
}
