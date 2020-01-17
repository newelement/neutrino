<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('entry_id');
			$table->bigInteger('parent_id')->default(0);
			$table->bigInteger('user_id')->nullable();
			$table->string('email', 255)->nullable();
			$table->string('author_name', 255)->nullable();
			$table->text('comment');
			$table->tinyInteger('approved')->default(1);
			$table->bigInteger('approved_by')->nullable();
			$table->timestamp('approved_at')->nullable();
            $table->timestamps();
			$table->softDeletes();
        });

		Schema::create('comment_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('comment_id');
			$table->bigInteger('user_id')->nullable();
			$table->bigInteger('user_ip')->nullable();
			$table->tinyInteger('up_vote')->default(0);
			$table->tinyInteger('down_vote')->default(0);
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
        Schema::drop('comments');
		Schema::drop('comment_votes');
    }
}
