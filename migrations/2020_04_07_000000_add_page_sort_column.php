<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageSortColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('pages', 'sort')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->integer('sort')->default(0);
                $table->index('sort');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sort')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('sort');
                $table->dropIndex('sort_index');
            });
        }
    }
}
