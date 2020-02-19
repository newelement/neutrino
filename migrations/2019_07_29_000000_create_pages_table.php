<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 300);
			$table->string('slug', 300)->unique();
            $table->text('content')->nullable();
            $table->json('block_content')->nullable();
            $table->text('short_content')->nullable();
			$table->text('keywords')->nullable();
			$table->text('meta_description')->nullable();
			$table->text('social_image')->nullable();
			$table->text('protected')->nullable();
			$table->char('status', 1)->default('P');
            $table->char('editor_type', 1)->default('B');
            $table->tinyInteger('system_page')->default(0);
			$table->bigInteger('parent_id')->default(0);
			$table->bigInteger('created_by')->nullable();
			$table->bigInteger('updated_by')->nullable();
            $table->timestamps();
			$table->softDeletes();
			$table->index('slug');
            $table->index('title');
            $table->index('content');
            $table->index('short_content');
            //$table->index('block_content');
			$table->index('status');
			$table->index('created_by');
			$table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
