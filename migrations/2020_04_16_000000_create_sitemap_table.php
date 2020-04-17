<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitemapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sitemap', function (Blueprint $table) {
			$table->string('page_default_change', 10)->default('monthly');
            $table->string('entry_default_change', 10)->default('monthly');
            $table->string('entry_type_default_change', 10)->default('monthly');
            $table->string('taxonomy_default_change', 10)->default('monthly');
            $table->string('term_default_change', 10)->default('monthly');
            $table->string('event_default_change', 10)->default('monthly');
            $table->string('product_default_change', 10)->default('monthly');

            $table->decimal('page_default_priority', 1, 1)->default(0.5);
            $table->decimal('entry_default_priority', 1, 1)->default(0.5);
            $table->decimal('entry_type_default_priority', 1, 1)->default(0.5);
            $table->decimal('taxonomy_default_priority', 1, 1)->default(0.5);
            $table->decimal('term_default_priority', 1, 1)->default(0.5);
            $table->decimal('event_default_priority', 1, 1)->default(0.5);
            $table->decimal('product_default_priority', 1, 1)->default(0.5);

            $table->integer('cache_hours')->default(1);

            $table->json('options')->nullable();
			$table->bigInteger('updated_by')->nullable();
        });

        \DB::table('sitemap')->insert(
        [
            'page_default_change' => 'monthly',
            'entry_default_change' => 'monthly',
            'entry_type_default_change' => 'monthly',
            'taxonomy_default_change' => 'monthly',
            'term_default_change' => 'monthly',
            'event_default_change' => 'monthly',
            'product_default_change' => 'monthly',

            'page_default_priority' => 0.5,
            'entry_default_priority' => 0.5,
            'entry_type_default_priority' => 0.5,
            'taxonomy_default_priority' => 0.5,
            'term_default_priority' => 0.5,
            'event_default_priority' => 0.5,
            'product_default_priority' => 0.5
        ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sitemap');
    }
}
