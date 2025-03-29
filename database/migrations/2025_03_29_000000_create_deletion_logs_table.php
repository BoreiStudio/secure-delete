<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('secure_deletions', function (Blueprint $table) {
            $table->id();
            $table->string('deletable_type'); // 'file', 'directory', 'database'
            $table->string('deletable_path'); // Ruta o tabla afectada
            $table->string('method');         // 'dod_5220', 'gutmann', etc.
            $table->text('error')->nullable();
            $table->integer('file_size')->nullable(); // Tamaño en bytes (para archivos)
            $table->string('original_checksum')->nullable(); // Hash SHA-256 previo (opcional)
            $table->foreignId('user_id')->nullable()->constrained(); // Usuario que ejecutó la acción
            $table->timestamps();
        });
    }
};