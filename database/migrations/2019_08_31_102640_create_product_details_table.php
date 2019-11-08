<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_details', function (Blueprint $table) {
            $table->bigInteger('prod_id')->unsigned()->index('fk_prod');
            $table->bigInteger('branch_id')->unsigned()->index('fk_branch');
            $table->string('display_name', 500)->comment('judul yg tampil di website')->nullable();
            $table->date('purchase_date');
            $table->bigInteger('purchase_price')->unsigned();
            $table->string('km', 50);
            $table->date('tax');
            $table->string('seller_name', 100);
            $table->string('seller_phone', 30);
            $table->string('seller_bank_name', 20)->nullable();
            $table->string('seller_bank_account', 100)->nullable();
            $table->string('seller_idcard', 100)->nullable();
            $table->string('unit_in_tkp', 100);
            $table->string('plat_no', 25)->nullable();
            $table->text('qc_list')->nullable();
            $table->tinyInteger('qc_status')->default(0)->comment('1 jika QC lulus semua');
            $table->text('modif_list')->nullable();
            $table->tinyInteger('modif_status')->default(0)->comment('1 jika tidak ada modifikasi');
            $table->tinyInteger('post_toped')->default(0)->comment('1 jika sudah post di Toped');
            $table->tinyInteger('post_olx')->default(0)->comment('1 jika sudah post di OLX');
            $table->date('published_date')->nullable();
            $table->bigInteger('published_by')->unsigned()->index('fk_user_published')->nullable();
            $table->date('booked_date')->nullable();
            $table->bigInteger('booked_by')->index('fk_user_booked')->nullable();
            $table->bigInteger('booked_by_customer')->index('fk_customer')->nullable();
            $table->date('sold_date')->nullable();
            $table->bigInteger('sold_by')->index('fk_user_sold')->nullable();
            $table->bigInteger('sold_by_customer')->index('fk_customer2')->nullable();
            $table->bigInteger('profit')->comment('bisa minus klo rugi')->nullable();
            $table->tinyInteger('photo_status')->default(0)->comment('1 jika foto sudah upload');
            $table->tinyInteger('publish_status')->default(0)->comment('1 jika sudah publish');
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
        Schema::dropIfExists('product_details');
    }
}
