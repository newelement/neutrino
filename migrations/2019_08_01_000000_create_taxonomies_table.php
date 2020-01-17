<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 300);
			$table->string('slug', 300)->unique();
            $table->text('description')->nullable();
			$table->bigInteger('parent_id')->default(0);
			$table->bigInteger('taxonomy_type_id')->default(0);
			$table->string('taxonomy_image', 300)->nullable();
			$table->integer('sort')->default(0);
            $table->timestamps();
			$table->index('slug');
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
        Schema::drop('taxonomies');
    }
}
