<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('agent_id')->nullable()->constrained('users');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->foreignId('asset_id')->nullable()->constrained('assets');
            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->string('subject', 255);
            $table->text('description');
            $table->string('priority', 20)->default('Medium');
            $table->string('status', 20)->default('New');
            $table->string('source', 20)->default('Web');
            $table->foreignId('sla_policy_id')->nullable()->constrained('sla_policies');
            $table->dateTime('sla_respond_at')->nullable();
            $table->dateTime('sla_resolve_at')->nullable();
            $table->dateTime('sla_paused_at')->nullable();
            $table->integer('sla_paused_total_minutes')->default(0);
            $table->dateTime('first_response_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
