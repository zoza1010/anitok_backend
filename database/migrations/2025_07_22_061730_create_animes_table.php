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
        Schema::create('animes', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('title_eng')->nullable();
            $table->text('description')->nullable();

            $table->string('poster_url')->nullable();

            $table->date('aired_on')->nullable();
            $table->date('released_on')->nullable();
            $table->timestamp('next_episode_at')->nullable();
            
            $table->integer('duration')->nullable();
            $table->integer('episodes')->nullable();
            $table->integer('episodes_aired')->nullable();
            
            $table->foreignId('age_rating_id')
                ->nullable()
                ->constrained('age_ratings')
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('statuses')
                ->nullOnDelete();
            
            $table->foreignId('type_id')
                ->nullable()
                ->constrained('types')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
