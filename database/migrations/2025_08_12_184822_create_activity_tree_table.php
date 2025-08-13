<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('_lft')->default(0);
            $table->unsignedInteger('_rgt')->default(0);
            $table->timestamps();

            $table->index(['parent_id', '_lft', '_rgt']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('activities');
    }
};