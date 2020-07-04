<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('thumbnail')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->string('author')->nullable();
            $table->text('keywords')->nullable();
            $table->text('summary')->nullable();
            $table->text('content');
            $table->boolean('status')->default('0')->comment('0:draft | 1:published');
            $table->timestamps();
            $table->softDeletes();
        });

        $seeder = new ArticlesSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
