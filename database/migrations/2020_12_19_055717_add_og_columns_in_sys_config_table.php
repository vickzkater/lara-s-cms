<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOgColumnsInSysConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_config', function (Blueprint $table) {
            $table->string('og_type')->nullable()->after('meta_author');
            $table->string('og_site_name')->nullable()->after('og_type');
            $table->string('og_title')->nullable()->after('og_site_name');
            $table->string('og_image')->nullable()->after('og_title');
            $table->text('og_description')->nullable()->after('og_image');

            $table->string('fb_app_id')->nullable()->after('og_description');
            
            $table->enum('twitter_card', ["summary", "summary_large_image", "app", "player"])->default('summary')->after('fb_app_id');
            $table->string('twitter_site')->nullable()->after('twitter_card')->comment('@username for the website used in the card footer. Used with summary, summary_large_image, app, player cards.');
            $table->string('twitter_site_id')->nullable()->after('twitter_site')->comment('Same as twitter:site, but the user’s Twitter ID. Either twitter:site or twitter:site:id is required. Used with summary, summary_large_image, player cards.');
            $table->string('twitter_creator')->nullable()->after('twitter_site_id')->comment('@username for the content creator/author. Used with summary_large_image cards.');
            $table->string('twitter_creator_id')->nullable()->after('twitter_creator')->comment('Twitter user ID of content creator. Used with summary, summary_large_image cards.');
        });

        $seeder = new OgSeederInitial();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_config', function (Blueprint $table) {
            //
        });
    }
}
