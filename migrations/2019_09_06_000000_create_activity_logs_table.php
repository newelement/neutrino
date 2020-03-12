<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('activity_package', 100);
			$table->string('activity_group', 100);
			$table->string('object_type', 100)->nullable();
            $table->bigInteger('object_id')->nullable();
			$table->text('content')->nullable();
            $table->tinyInteger('log_level')->default(0);
            $table->string('created_by_string', 255)->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_by');
            $table->index('created_by_string');
            $table->index('updated_by');
            $table->index('activity_group');
            $table->index('activity_package');
            $table->index('object_type');
            $table->index('object_id');
            $table->index('log_level');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activity_logs');
    }
}
