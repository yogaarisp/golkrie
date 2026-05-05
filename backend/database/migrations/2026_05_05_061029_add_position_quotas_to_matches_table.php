<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('quota_gk')->default(2)->after('quota');
            $table->integer('quota_df')->default(4)->after('quota_gk');
            $table->integer('quota_mf')->default(4)->after('quota_df');
            $table->integer('quota_fw')->default(4)->after('quota_mf');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['quota_gk', 'quota_df', 'quota_mf', 'quota_fw']);
        });
    }
};
