<?php

use Illuminate\Database\Migrations\Migration;

class DarchoodsAddNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function ($table) {
            $table->engine = 'InnoDB';

            $table->increments('id')->unsigned();
            $table->integer('author_id');
            $table->string('title');
            $table->text('content')->nullable();
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
        Schema::drop('notes');
    }
}
