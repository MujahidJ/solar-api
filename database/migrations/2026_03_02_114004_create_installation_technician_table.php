<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
    Schema::create('installation_technician', function (Blueprint $table) {
        $table->id();
        $table->foreignId('installation_id')->constrained()->cascadeOnDelete();
        $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
        $table->timestamp('assigned_at')->useCurrent();
        $table->timestamps();

        $table->unique(['installation_id', 'technician_id']);
        $table->index(['technician_id']);
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('installation_technician');
    }
};
