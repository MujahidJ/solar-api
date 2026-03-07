<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
    Schema::create('maintenance_plans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('installation_id')->constrained()->cascadeOnDelete();
        $table->unsignedInteger('interval_days');         // e.g. 30, 90
        $table->date('next_due_date');
        $table->boolean('active')->default(true);
        $table->timestamps();

        $table->index(['installation_id', 'next_due_date']);
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
