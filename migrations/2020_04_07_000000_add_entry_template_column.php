<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryTemplateColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('entries', 'template')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->string('template', 300)->nullable();
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
        if (Schema::hasColumn('entries', 'template')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('template');
            });
        }
    }
}
