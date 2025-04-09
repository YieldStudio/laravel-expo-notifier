<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('expo-notifications.database.tokens_table_name', 'expo_tokens'), function (Blueprint $table) {
            $table->unique(['value', 'owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::table(config('expo-notifications.database.tokens_table_name', 'expo_tokens'), function (Blueprint $table) {
            $table->dropUnique(['value', 'owner_type', 'owner_id']);
        });
    }
};
