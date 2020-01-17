<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('slug', 300);
			$table->string('location_name', 400);
			$table->text('description')->nullable();
			$table->string('address', 400)->nullable();
			$table->string('address2', 400)->nullable();
			$table->string('city', 200)->nullable();
			$table->string('state', 200)->nullable();
			$table->string('postal', 50)->nullable();
			$table->string('phone', 50)->nullable();
			$table->string('email', 255)->nullable();
			$table->string('country', 100)->nullable();
			$table->string('url', 300)->nullable();
			$table->float('lon')->nullable();
			$table->float('lat')->nullable();
            $table->timestamps();
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
        Schema::drop('places');
    }
}
