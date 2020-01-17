<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfObjectDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cf_object_data', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('object_id');
			$table->string('object_type', 50);
			$table->text('object')->nullable();
			$table->string('field_id', 50);
			$table->string('field_name', 100);
			$table->string('field_type', 50);
			$table->text('field_text')->nullable();
			$table->integer('field_number')->nullable();
			$table->float('field_decimal', 10, 2)->nullable();
			$table->text('field_editor')->nullable();
			$table->text('field_file')->nullable();
			$table->text('field_image')->nullable();
			$table->json('field_config')->nullable();
			$table->dateTime('field_date')->nullable();
			$table->string('parent_field_id', 50)->nullable();
			$table->string('batch_id', 50)->nullable();
			$table->string('content_id', 50)->nullable();
			$table->integer('batch_sort')->default('0');
            $table->timestamps();
			$table->index('object_id');
			$table->index('object_type');
			$table->index('field_id');
			$table->index('field_name');
			$table->index('parent_field_id');
			$table->index('batch_id');
			$table->index('content_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cf_object_data');
    }
}
