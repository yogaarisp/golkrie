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
        $newFields = [
            'about_quote' => 'Bukan sekadar mengejar bola, tapi mengejar keringat dan mempererat tali silaturahmi antar pecinta sepakbola di Semarang.',
            'about_est' => 'EST 2024',
            'about_hashtag' => '#GolekKringet',
        ];

        foreach ($newFields as $key => $value) {
            if (!\DB::table('settings')->where('key', $key)->exists()) {
                \DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
