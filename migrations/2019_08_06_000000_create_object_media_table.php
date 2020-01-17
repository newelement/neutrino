<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_media', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('object_id');
			$table->string('object_type', 50);
			$table->string('file_path', 300)->nullable();
			$table->tinyInteger('featured')->default('0');
			$table->string('media_group_type', 100)->nullable();
            $table->timestamps();
			$table->index('object_id');
			$table->index('object_type');
			$table->index('featured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('object_media');
    }
}
