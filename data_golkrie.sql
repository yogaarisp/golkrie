-- 1. Bersihkan data lama agar tidak bentrok
TRUNCATE TABLE registrations, members, matches RESTART IDENTITY CASCADE;

-- 2. Masukkan Data Pertandingan
INSERT INTO matches (title, match_name, date_time, end_time, location, location_url, quota_gk, quota_df, quota_mf, quota_fw, quota, price, status, created_at, updated_at)
VALUES ('BIG PITCH', 'GOLEK JATIDIRI #6', '2026-05-14 14:00:00', '2026-05-14 17:00:00', 'Jatidiri Internasional Stadium', 'https://share.google/bi50JvoOMfKBIIcuC', 4, 12, 12, 12, 40, '275000', 'upcoming', NOW(), NOW());

-- 3. Masukkan Members dan Registrasi secara masal
DO $$
DECLARE
    v_match_id bigint;
    v_member_id bigint;
    player RECORD;
BEGIN
    -- Ambil ID pertandingan yang baru dibuat
    SELECT id INTO v_match_id FROM matches WHERE match_name = 'GOLEK JATIDIRI #6' LIMIT 1;

    -- Loop untuk memasukkan semua pemain
    FOR player IN SELECT * FROM (VALUES 
        ('Wisnu', 'GK'), ('Hasan', 'GK'), ('Alfi', 'GK'),
        ('Panji', 'DF'), ('Abil', 'DF'),
        ('Rizal', 'MF'), ('AW', 'MF'), ('Bayu', 'MF'), ('Debri', 'MF'), ('Ken', 'MF'), 
        ('Pak Kris', 'MF'), ('Yanu', 'MF'), ('Rizky Pahlevi', 'MF'), ('Ilham', 'MF'), ('Lenglolo', 'MF'),
        ('Arif Is', 'FW'), ('Sandro', 'FW'), ('Xenod', 'FW'), ('Ryu', 'FW'), ('Dwi', 'FW')
    ) AS t(name, pos)
    LOOP
        -- Masukkan Member
        INSERT INTO members (full_name, phone_number, created_at, updated_at) 
        VALUES (player.name, '0812' || floor(random() * 9000000 + 1000000)::text, NOW(), NOW())
        RETURNING id INTO v_member_id;

        -- Masukkan Registrasi
        INSERT INTO registrations (match_id, member_id, player_name, position, is_accepted, created_at, updated_at)
        VALUES (v_match_id, v_member_id, player.name, player.pos, true, NOW(), NOW());
    END LOOP;
END $$;
