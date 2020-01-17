<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cf_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('field_id', 50);
			$table->bigInteger('group_id');
			$table->string('field_type');
			$table->json('field_config')->nullable();
            $table->string('field_label', 255)->nullable();
			$table->string('field_name', 255)->nullable();
			$table->tinyInteger('field_required')->default('0');
			$table->tinyInteger('multiple_files')->default('0');
			$table->tinyInteger('empty_first_option')->default('0');
			$table->string('allowed_filetypes', 300)->default('*');
            $table->text('description')->nullable();
			$table->string('repeater_id', 50)->nullable();
			$table->integer('sort')->default('0');
            $table->timestamps();
			$table->index('group_id');
			$table->index('sort');
			$table->index('field_id');
			$table->index('field_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cf_fields');
    }
}
