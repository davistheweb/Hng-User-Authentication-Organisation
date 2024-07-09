<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('organisation_id')->index();
            $table->uuid('user_id')->index();
            $table->timestamps();

            $table->foreign('organisation_id')->references('orgId')->on('organisations');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_user');
    }
};
