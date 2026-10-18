<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->index(['appointable_type', 'appointable_id', 'date', 'status', 'time'], 'appointments_owner_slot_index');
            $table->index(['customer_id', 'date', 'status', 'time'], 'appointments_customer_slot_index');
            $table->index(['status', 'date', 'end_at'], 'appointments_completion_index');
        });
        Schema::table('notifications', function (Blueprint $table): void {
            $table->index(['belongTo_type', 'belongTo_id', 'is_viewed'], 'notifications_recipient_unread_index');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex('appointments_owner_slot_index');
            $table->dropIndex('appointments_customer_slot_index');
            $table->dropIndex('appointments_completion_index');
        });
        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropIndex('notifications_recipient_unread_index');
        });
    }
};
