<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gallery_id');
            $table->string('title', 255)->nullable();
            $table->text('image_path');
            $table->boolean('featured')->default('0');
            $table->text('description')->nullable();
            $table->text('caption')->nullable();
            $table->integer('sort')->default('0');
            $table->timestamps();
            $table->index('sort');
            $table->index('gallery_id');
            $table->index('featured');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gallery_images');
    }
}
