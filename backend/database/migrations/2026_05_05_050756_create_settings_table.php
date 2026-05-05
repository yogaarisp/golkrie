<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'Golkrie'],
            ['key' => 'app_tagline', 'value' => 'Golek Kringet, Jalin Seduluran.'],
            ['key' => 'footer_text', 'value' => '© 2024 Golkrie Community. All rights reserved.'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/golkrie'],
            ['key' => 'whatsapp_contact', 'value' => '08123456789'],
            ['key' => 'hero_description', 'value' => 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau dua kali seminggu. Fun football yang kompetitif namun tetap seru.'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
