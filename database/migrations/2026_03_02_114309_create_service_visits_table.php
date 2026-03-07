<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
    Schema::create('service_visits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('installation_id')->constrained()->cascadeOnDelete();
        $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
        $table->date('serviced_on');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->index(['installation_id', 'serviced_on']);
        $table->index(['technician_id', 'serviced_on']);
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('service_visits');
    }
};
