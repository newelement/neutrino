<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('redirects')) {
            Schema::create('redirects', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('old_url')->unique();
                $table->string('new_url')->nullable();
                $table->smallInteger('status')->default(301);
                $table->timestamps();
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
        Schema::dropIfExists('redirects');
    }
}
