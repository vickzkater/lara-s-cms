<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_system', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('subject')->index('fk_users');
            $table->unsignedInteger('action')->index('fk_log_details');
            $table->unsignedInteger('object')->index('fk_users_2')->nullable();
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
        Schema::dropIfExists('logs_system');
    }
}
