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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('postal_code', 15); // 郵便番号(ハイフン込み)
            $table->string('address_line1', 255); // 住所
            $table->string('address_line2', 255)->nullable(); // 建物名
            $table->string('issuer_name', 255);
            $table->string('issuer_number', 20)->nullable(); // 登録番号
            $table->string('tel_fixed', 20)->nullable(); // 固定電話(ハイフン込み)
            $table->string('tel_mobile', 20)->nullable(); // 携帯電話(ハイフン込み)
            $table->string('responsible_name', 255);
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
        Schema::dropIfExists('receipt_settings');
    }
};
