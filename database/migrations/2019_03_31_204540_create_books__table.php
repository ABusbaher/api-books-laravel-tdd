<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('slug');
                $table->integer('year_of_publish');
                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id')
                    ->references('id')->on('languages')
                    ->onDelete('cascade');
                $table->unsignedBigInteger('original_language_id');
                $table->foreign('original_language_id')
                    ->references('id')->on('languages')
                    ->onDelete('cascade');
                $table->unsignedBigInteger('author_id');
                $table->foreign('author_id')
                    ->references('id')->on('authors')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('books');
    }

}
