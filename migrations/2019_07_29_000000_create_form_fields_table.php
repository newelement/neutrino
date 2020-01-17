<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('form_id');
			$table->string('field_id', 50);
			$table->string('field_type', 100);
			$table->string('field_label', 255);
			$table->string('field_name', 255);
			$table->tinyInteger('required')->default(0);
			$table->tinyInteger('select_multiple')->default(0);
			$table->string('placeholder', 255)->nullable();
			$table->integer('max_length')->default(0);
			$table->integer('min_length')->default(0);
			$table->json('settings')->nullable();
			$table->integer('sort')->default(0);
            $table->timestamps();
			$table->index('form_id');
			$table->index('field_id');
			$table->index('field_type');
			$table->index('field_name');
			$table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('form_fields');
    }
}
