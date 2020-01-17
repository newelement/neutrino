<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_types', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('entry_type', 50)->nullable();
			$table->string('slug', 50);
			$table->string('label_plural', 50)->nullable();
			$table->tinyInteger('searchable')->default('1');
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
        Schema::drop('entry_types');
    }
}
