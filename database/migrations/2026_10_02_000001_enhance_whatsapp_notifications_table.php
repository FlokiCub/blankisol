<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_notifications', function (Blueprint $table) {
            $table->string('provider', 40)->default('log')->after('status');
            $table->string('template_name', 80)->nullable()->after('provider');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
            $table->timestamp('read_at')->nullable()->after('delivered_at');
            $table->jsonb('raw_response')->nullable()->after('error_details');
            
            $table->index('gateway_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_notifications', function (Blueprint $table) {
            $table->dropIndex(['gateway_message_id']);
            $table->dropColumn([
                'provider',
                'template_name',
                'delivered_at',
                'read_at',
                'raw_response',
            ]);
        });
    }
};
