<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('respins_gameslist', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('gid', 100);
            $table->string('slug', 100);
            $table->string('name', 100);
            $table->string('provider', 100);
            $table->string('type', 100);
            $table->string('typeRating', 100);
            $table->string('popularity', 100);
            $table->integer('bonusbuy')->default(1);
            $table->integer('jackpot')->default(1);
            $table->integer('demoplay')->default(1);
            $table->string('internal_origin_demolink', 255);
            $table->string('internal_origin_casino', 50);
            $table->json('internal_origin_realmoney', 1500);
            $table->json('internal_origin_rawobject', 1500);
            $table->string('internal_enabled', 100);
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
        Schema::dropIfExists('respins_gameslist');
    }
};
