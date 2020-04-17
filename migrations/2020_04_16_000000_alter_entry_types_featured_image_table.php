<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEntryTypesFeaturedImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('entry_types', 'featured_image')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->string('featured_image', 300)->nullable();
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
        if (Schema::hasColumn('entry_types','featured_image')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->dropColumn('featured_image');
            });
        }
    }
}
