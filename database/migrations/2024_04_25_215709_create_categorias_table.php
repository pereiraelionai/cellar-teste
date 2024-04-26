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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedBigInteger('usuario_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('categorias', function(Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function(Blueprint $table) {
            $table->dropForeign('categorias_usuario_id_foreign');
        });

        Schema::dropIfExists('categorias');
    }
};
