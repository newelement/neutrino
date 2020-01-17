<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
			$table->string('type', 20);
			$table->string('label');
            $table->text('value')->nullable();
			$table->tinyInteger('protected')->default(0);
			$table->tinyInteger('value_bool')->default(0);
            $table->text('details')->nullable();
            $table->integer('order')->default('1');
			$table->string('group')->nullable();
			$table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
