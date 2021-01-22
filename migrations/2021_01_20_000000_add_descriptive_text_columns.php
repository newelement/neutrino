<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptiveTextColumns extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        if (!Schema::hasColumn('form_fields', 'descriptive_text')) {
            Schema::table('form_fields', function (Blueprint $table) {
                $table->text('descriptive_text')->nullable();
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
        if (Schema::hasColumn('form_fields','descriptive_text')) {
            Schema::table('form_fields', function (Blueprint $table) {
                $table->dropColumn('descriptive_text');
            });
        }
    }
}
