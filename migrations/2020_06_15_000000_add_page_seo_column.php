<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageSeoColumn extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        if (!Schema::hasColumn('pages', 'seo_title')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('seo_title', 400)->nullable();
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
        if (Schema::hasColumn('pages','seo_title')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('seo_title');
            });
        }
    }
}
