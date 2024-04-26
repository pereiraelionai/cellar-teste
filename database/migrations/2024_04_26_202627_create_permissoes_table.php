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
        Schema::create('permissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->boolean('categorias')->default(true);
            $table->boolean('produtos')->default(true);
            $table->boolean('criar_editar')->default(true);
            $table->boolean('excluir')->default(true);
            $table->timestamps();
        });

        Schema::table('permissoes', function(Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissoes', function(Blueprint $table) {
            $table->dropForeign('permissoes_usuario_id_foreign');
        });

        Schema::dropIfExists('permissoes');
    }
};
