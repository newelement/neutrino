<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormPrivateColumn extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        if (!Schema::hasColumn('forms', 'private')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->boolean('private');
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
        if (Schema::hasColumn('forms','private')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('private');
            });
        }
    }
}
