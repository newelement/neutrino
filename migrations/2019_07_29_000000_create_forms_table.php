<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 300);
			$table->string('slug', 300)->unique();
            $table->text('content')->nullable();
			$table->char('status', 1)->default('A');
			$table->text('email_to')->nullable();
			$table->text('email_from')->nullable();
			$table->text('email_cc')->nullable();
			$table->text('email_bcc')->nullable();
			$table->string('subject', 300)->nullable();
			$table->string('form_style', 100)->nullable();
			$table->string('submit_button_label', 100)->nullable();
			$table->string('submit_button_size', 20)->nullable();
            $table->timestamps();
			$table->softDeletes();
			$table->index('slug');
			$table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('forms');
    }
}
