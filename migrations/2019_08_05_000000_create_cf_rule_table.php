<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cf_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('group_id');
			$table->string('rule_category', 50)->nullable();
			$table->string('rule_category_type', 50)->nullable();
			$table->string('rule_category_specific', 50)->nullable();
			$table->string('title', 300)->nullable();
			$table->bigInteger('object_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cf_rules');
    }
}
