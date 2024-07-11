<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('social-interactions.tables.interactions', 'social_interactions'), function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('interactor');
            $table->nullableMorphs('interactable');
            $table->boolean('saved')->nullable()->default(false);
            $table->integer('votes')->default(0);
            $table->boolean('liked')->nullable()->default(false);
            $table->boolean('disliked')->nullable()->default(false);
            $table->tinyInteger('reaction')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('social-interactions.tables.interactions', 'social_interactions'));
    }
};