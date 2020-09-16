<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subject')->index('FK_sys_users');
            $table->unsignedBigInteger('action')->index('FK_sys_log_details');
            $table->unsignedBigInteger('object')->index('FK_object')->nullable();
            $table->timestamps();
        });

        $seeder = new SysLogsSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_logs');
    }
}
