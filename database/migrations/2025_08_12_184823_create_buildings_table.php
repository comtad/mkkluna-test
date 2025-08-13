<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsTable extends Migration
{
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->geometry('coordinates', 'POINT', 4326)->nullable();
            $table->timestamps();
            $table->spatialIndex('coordinates');
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropSpatialIndex(['coordinates']);
        });
        Schema::dropIfExists('buildings');
    }
}