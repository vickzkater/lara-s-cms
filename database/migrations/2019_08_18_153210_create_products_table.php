<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('currency', 5)->default('Rp');
            $table->unsignedBigInteger('price_normal')->nullable();
            $table->unsignedBigInteger('price_now')->nullable();
            $table->double('discount')->nullable()->unsigned();
            $table->unsignedInteger('stock');
            $table->unsignedInteger('brand_id')->index('brand_fk');
            $table->unsignedInteger('category_id')->index('category_fk')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->text('images')->nullable()->comment('array JSON Object');
            $table->tinyInteger('image_primary')->unsigned()->default(0)->comment('index image di column images');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('isDeleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
