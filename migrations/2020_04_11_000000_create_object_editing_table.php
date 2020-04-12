<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectEditingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_editing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('object_type', 40);
            $table->bigInteger('object_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->index('object_type');
            $table->index('object_id');
            $table->index('user_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('object_editing');
    }
}
