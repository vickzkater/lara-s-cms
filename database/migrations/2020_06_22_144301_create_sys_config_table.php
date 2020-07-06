<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_config', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('Lara-S-CMS');
            $table->string('app_url_site')->default('http://localhost/lara-s-cms/public/');
            $table->string('app_url_main')->nullable()->default('http://localhost/lara-s-cms/public/');
            $table->string('app_version', 10)->default('1.0');
            $table->string('app_favicon_type', 10)->default('ico');
            $table->string('app_favicon')->default('favicon.ico');
            $table->string('app_logo', 20)->default('laptop')->comment('using Font Awesome')->nullable();
            $table->string('app_logo_image')->default('uploads/config/logo-square.png')->nullable();
            $table->string('help')->default('Content Management System for Website Lara-S-CMS');
            $table->string('powered')->default('KINIDI Tech')->nullable();
            $table->string('powered_url')->default('https://kiniditech.com')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('meta_title')->default('Lara-S-CMS is a PHP Laravel Skeleton');
            $table->text('meta_description')->nullable();
            $table->string('meta_author')->default('KINIDI Tech');
            $table->timestamps();
        });

        $seeder = new SysConfigSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_config');
    }
}
