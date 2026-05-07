<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE registrations DROP CONSTRAINT IF EXISTS registrations_position_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to restore the exact enum check constraint without knowing the previous values
    }
};
