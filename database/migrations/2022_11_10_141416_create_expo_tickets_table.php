<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('expo-notifications.database.tickets_table_name', 'expo_tickets'), function (Blueprint $table) {
            $table->id();

            $table->string('ticket_id')->unique();
            $table->string('token');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('expo-notifications.database.tickets_table_name', 'expo_tickets'));
    }
};
