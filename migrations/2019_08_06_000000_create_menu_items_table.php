<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('menu_id');
			$table->string('title', 300);
			$table->text('url')->nullable();
			$table->string('target', 40)->nullable();
			$table->string('parameters', 50)->nullable();
			$table->bigInteger('parent_id')->default(0);
			$table->integer('order')->default(0);
            $table->timestamps();
			$table->index('menu_id');
			$table->index('order');
        });

	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menu_items');
    }
}
