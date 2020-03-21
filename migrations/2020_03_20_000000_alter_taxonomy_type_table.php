<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaxonomyTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomy_types', function ($table) {
			$table->string('social_image_1', 300)->nullable();
            $table->string('social_image_2', 300)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('social_description', 300)->nullable();
            $table->string('keywords', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxonomy_types', function ($table) {
            $table->dropColumn('social_image_1');
            $table->dropColumn('social_image_2');
            $table->dropColumn('meta_description');
            $table->dropColumn('social_description');
            $table->dropColumn('keywords');
        });
    }
}
