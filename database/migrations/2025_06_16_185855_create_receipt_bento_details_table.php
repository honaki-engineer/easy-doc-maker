<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_bento_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->onDelete('cascade');
            $table->string('bento_brand_name', 50);
            $table->string('bento_name', 255);
            $table->integer('bento_fee');
            $table->boolean('tax_rate');
            $table->integer('bento_quantity');
            
            // ----- 新規 -----
            // 単価(税抜)
            $table->integer('unit_price'); 
            // 金額
            $table->integer('amount');
            // ----- 新規 -----

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
        Schema::dropIfExists('receipt_bento_details');
    }
};
