<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->string('title')->after('installation_id');
            $table->string('trigger_type')->default('time')->after('title'); // time, condition, hybrid
            $table->string('condition_rule')->nullable()->after('interval_days');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'trigger_type',
                'condition_rule',
            ]);
        });
    }
};
