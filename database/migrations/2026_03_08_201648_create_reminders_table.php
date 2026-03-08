<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending, sent, completed, cancelled
            $table->string('trigger_source')->default('time'); // time, condition, hybrid
            $table->timestamps();

            $table->index(['due_date', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['technician_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};

