<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('expo-notifications.database.notifications_table_name', 'expo_notifications'), function (Blueprint $table) {
            $table->nullableMorphs('receiver');
        });
    }

    public function down(): void
    {
        Schema::table(config('expo-notifications.database.notifications_table_name', 'expo_notifications'), function (Blueprint $table) {
            $table->dropMorphs('receiver');
        });
    }
};
