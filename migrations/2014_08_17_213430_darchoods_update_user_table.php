<?php

use Illuminate\Database\Migrations\Migration;

class DarchoodsUpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->text('nicks')->nullable()->after('last_name');
            $table->integer('use_nick')->default(0)->after('nicks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
