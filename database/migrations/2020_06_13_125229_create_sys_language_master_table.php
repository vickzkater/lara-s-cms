<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysLanguageMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_language_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phrase')->unique();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $seeder = new SysLanguageMasterSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_language_master');
    }
}
