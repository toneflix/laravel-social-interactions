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
        Schema::table(config('social-interactions.tables.saves', 'social_interaction_saves'), function (Blueprint $table) {
            $table->foreignId('interaction_id')
                ->nullable()
                ->constrained(config('social-interactions.tables.interactions', 'social_interactions'))
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn(config('social-interactions.tables.saves', 'social_interaction_saves'), 'interaction_id')) {
            Schema::table(
                config('social-interactions.tables.saves', 'social_interaction_saves'),
                function (
                    Blueprint $table
                ) {
                    $table->dropForeign(['interaction_id']);
                    $table->dropColumn('interaction_id');
                }
            );
        }
    }
};