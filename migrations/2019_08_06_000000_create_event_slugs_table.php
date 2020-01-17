<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSlugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_slugs', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('event_id');
			$table->string('slug', 300);
            $table->timestamps();
			$table->index('event_id');
			$table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_slugs');
    }
}
