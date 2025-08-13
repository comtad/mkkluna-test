<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_organization', function (Blueprint $table) {
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->primary(['activity_id', 'organization_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_organization');
    }
};