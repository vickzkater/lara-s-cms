<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('division_id')->index('FK_sys_divisions');
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('gmaps')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedInteger('ordinal')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $seeder = new SysBranchSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_branches');
    }
}
