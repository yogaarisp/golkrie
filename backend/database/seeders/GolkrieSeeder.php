<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GolkrieMatch;
use App\Models\Member;
use App\Models\Registration;

class GolkrieSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Main Match: GOLEK JATIDIRI #6
        $jatidiri = GolkrieMatch::create([
            'title' => 'Big Pitch',
            'match_name' => 'GOLEK JATIDIRI #6',
            'date_time' => '2026-05-14 14:00:00',
            'location' => 'Jatidiri International Stadium',
            'location_url' => 'https://share.google/bi50JvoOMfKBIIcuC',
            'quota' => 42,
            'price' => '275K (Player) / 195K (GK)',
            'status' => 'upcoming',
            'media_url' => null,
        ]);

        // 2. Pre-fill Players from your list
        $players = [
            // GK
            ['name' => 'Wisnu', 'pos' => 'GK', 'accepted' => true],
            ['name' => 'Hasan', 'pos' => 'GK', 'accepted' => true],
            ['name' => 'Alfi', 'pos' => 'GK', 'accepted' => true],
            // CB
            ['name' => 'Panji', 'pos' => 'DF', 'accepted' => true],
            // RLB
            ['name' => 'Abil', 'pos' => 'DF', 'accepted' => true],
            // MF
            ['name' => 'Rizal', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'AW', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Bayu', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Debri', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Ken', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Pak Kris', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Yanu', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Rizky Pahlevi', 'pos' => 'MF', 'accepted' => true],
            ['name' => 'Ilham', 'pos' => 'MF', 'accepted' => true],
            // CF
            ['name' => 'Sandro', 'pos' => 'FW', 'accepted' => true],
            ['name' => 'Xenod', 'pos' => 'FW', 'accepted' => true],
            ['name' => 'Ryu', 'pos' => 'FW', 'accepted' => true],
        ];

        foreach ($players as $p) {
            $member = Member::firstOrCreate(
                ['full_name' => $p['name']],
                ['phone_number' => '08' . rand(100000000, 999999999)] // Dummy WA
            );

            Registration::create([
                'match_id' => $jatidiri->id,
                'member_id' => $member->id,
                'player_name' => $member->full_name,
                'position' => $p['pos'],
                'is_accepted' => $p['accepted'],
            ]);
        }

        // Add some dummy history
        GolkrieMatch::create([
            'title' => 'Mini Soccer',
            'match_name' => 'Golkrie Night Fun',
            'date_time' => '2026-05-01 19:00:00',
            'location' => 'Kickoff Arena',
            'quota' => 14,
            'status' => 'finished',
            'media_url' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018'
        ]);
    }
}
