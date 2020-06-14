<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysLanguageMasterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_language_master_details', function (Blueprint $table) {
            $table->bigInteger('language_id')->index('FK_sys_languages');
            $table->bigInteger('language_master_id')->index('FK_sys_language_master');
            $table->string('translate');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $seeder = new SysLanguageMasterDetailsSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_language_master_details');
    }
}
