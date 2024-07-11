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
        Schema::create(config('social-interactions.tables.saves', 'social_interaction_saves'), function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('interactor');
            $table->nullableMorphs('saveable');
            $table->boolean('public')->nullable()->default(false);
            $table->string('list_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('social-interactions.tables.saves', 'social_interaction_saves'));
    }
};