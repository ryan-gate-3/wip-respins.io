<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('respins_players', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id')->primaryKey;
            $table->foreignUuid('uuid', 150)->unique();
            $table->string('pid', 100)->nullable();
            $table->string('secret', 100)->nullable();
            $table->string('nickname', 155)->nullable();
            $table->boolean('active', 10);
            $table->json('data', 1500);
            $table->string('ownedBy', 120);
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
        Schema::dropIfExists('respins_players');
    }
};