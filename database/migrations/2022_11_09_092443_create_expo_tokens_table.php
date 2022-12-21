<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('expo-notifications.database.tokens_table_name', 'expo_tokens'), function (Blueprint $table) {
            $table->id();

            $table->morphs('owner');
            $table->string('value');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('expo-notifications.database.tokens_table_name', 'expo_tokens'));
    }
};
