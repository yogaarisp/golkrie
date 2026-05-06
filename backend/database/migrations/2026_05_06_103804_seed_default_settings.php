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
        $defaults = [
            'app_name' => 'Golkrie',
            'app_tagline' => 'Golek Kringet, Jalin Seduluran.',
            'instagram_url' => 'https://instagram.com/golkrie',
            'whatsapp_contact' => '08123456789',
            'hero_description' => 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau.',
            'about_description' => 'Golkrie adalah komunitas sepakbola yang berfokus pada kesehatan dan tali persaudaraan. Kami percaya bahwa olahraga adalah cara terbaik untuk menjaga tubuh tetap bugar sekaligus menambah relasi baru.',
            'about_quote' => 'Bukan sekadar mengejar bola, tapi mengejar keringat dan mempererat tali silaturahmi antar pecinta sepakbola di Semarang.',
            'about_est' => 'EST 2024',
            'about_hashtag' => '#GolekKringet',
            'footer_text' => '© 2026 GOLKRIE COMMUNITY. Golek Kringet, Jalin Seduluran.',
        ];

        foreach ($defaults as $key => $value) {
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
