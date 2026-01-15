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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');              // titre du livre
            $table->string('author')->nullable(); // auteur (optionnel)
            $table->unsignedSmallInteger('published_year')->nullable(); // année de publication (0-65535)
            $table->string('genre')->nullable();  // genre/catégorie
            $table->timestamps();                 // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
