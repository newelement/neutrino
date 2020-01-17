<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('object_id');
			$table->bigInteger('taxonomy_type_id');
			$table->bigInteger('taxonomy_id');
			$table->string('object_type', 50);
            $table->timestamps();
			$table->index('object_id');
			$table->index('object_type');
			$table->index('taxonomy_id');
			$table->index('taxonomy_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('object_terms');
    }
}
