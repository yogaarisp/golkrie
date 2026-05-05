<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GolkrieMatch;
use App\Models\Registration;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Pertandingan
        $match = GolkrieMatch::create([
            'title' => 'BIG PITCH',
            'match_name' => 'GOLEK JATIDIRI #6',
            'date_time' => '2026-05-14 14:00:00',
            'end_time' => '2026-05-14 17:00:00',
            'location' => 'Jatidiri Internasional Stadium',
            'location_url' => 'https://share.google/bi50JvoOMfKBIIcuC',
            'quota_gk' => 4,
            'quota_df' => 12,
            'quota_mf' => 12,
            'quota_fw' => 12,
            'quota' => 40,
            'price' => 275000,
            'status' => 'upcoming'
        ]);

        // 2. Daftar Pemain (Registrasi)
        $players = [
            // GK
            ['name' => 'Wisnu', 'pos' => 'GK'],
            ['name' => 'Hasan', 'pos' => 'GK'],
            ['name' => 'Alfi', 'pos' => 'GK'],
            
            // DF
            ['name' => 'Panji', 'pos' => 'DF'],
            ['name' => 'Abil', 'pos' => 'DF'],
            
            // MF
            ['name' => 'Rizal', 'pos' => 'MF'],
            ['name' => 'AW', 'pos' => 'MF'],
            ['name' => 'Bayu', 'pos' => 'MF'],
            ['name' => 'Debri', 'pos' => 'MF'],
            ['name' => 'Ken', 'pos' => 'MF'],
            ['name' => 'Pak Kris', 'pos' => 'MF'],
            ['name' => 'Yanu', 'pos' => 'MF'],
            ['name' => 'Rizky Pahlevi', 'pos' => 'MF'],
            ['name' => 'Ilham', 'pos' => 'MF'],
            ['name' => 'Lenglolo', 'pos' => 'MF'],
            
            // FW
            ['name' => 'Arif Is', 'pos' => 'FW'],
            ['name' => 'Sandro', 'pos' => 'FW'],
            ['name' => 'Xenod', 'pos' => 'FW'],
            ['name' => 'Ryu', 'pos' => 'FW'],
            ['name' => 'Dwi', 'pos' => 'FW'],
        ];

        foreach ($players as $p) {
            // Buat atau cari member
            $member = \App\Models\Member::updateOrCreate(
                ['full_name' => $p['name']],
                ['phone_number' => '0812' . rand(1000000, 9999999)]
            );

            Registration::create([
                'match_id' => $match->id,
                'member_id' => $member->id,
                'player_name' => $p['name'],
                'position' => $p['pos'],
                'is_accepted' => true
            ]);
        }
    }
}
