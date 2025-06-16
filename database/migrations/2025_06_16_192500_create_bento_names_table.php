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
        Schema::create('bento_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bento_brand_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->timestamps();
            $table->unique(['bento_brand_id', 'name']); // ブランド単位で弁当名の重複を禁止
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bento_names');
    }
};
