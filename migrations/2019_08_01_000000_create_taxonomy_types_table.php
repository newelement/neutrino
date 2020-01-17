<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 300);
			$table->string('slug', 300)->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('hierarchical')->default(1);
			$table->string('show_on', 100)->nullable();
			$table->integer('sort')->default(0);
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
        Schema::drop('taxonomy_types');
    }
}
