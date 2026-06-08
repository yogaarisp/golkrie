-- Golkrie Backup - 2026-06-08_05-47-26
-- Paste ke SQL Editor Supabase setelah schema sudah dibuat

TRUNCATE TABLE registrations, members, matches, settings, sponsors, users RESTART IDENTITY CASCADE;

-- Table: matches (2 rows)
INSERT INTO matches (id, title, match_name, date_time, location, location_url, quota, price, media_url, status, created_at, updated_at, end_time, quota_gk, quota_df, quota_mf, quota_fw, price_gk, team_config, facilities) VALUES ('1', 'Big Pitch', 'GOLEK JATIDIRI #6', '2026-05-14 14:00:00', 'Jatidiri International Stadium', 'https://share.google/bi50JvoOMfKBIIcuC', '42', '275K (Player) / 195K (GK)', NULL, 'upcoming', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '2', '4', '4', '4', '0', NULL, NULL);
INSERT INTO matches (id, title, match_name, date_time, location, location_url, quota, price, media_url, status, created_at, updated_at, end_time, quota_gk, quota_df, quota_mf, quota_fw, price_gk, team_config, facilities) VALUES ('2', 'Mini Soccer', 'Golkrie Night Fun', '2026-05-01 19:00:00', 'Kickoff Arena', NULL, '14', '0', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018', 'finished', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '2', '4', '4', '4', '0', NULL, NULL);

-- Table: members (17 rows)
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('1', 'Wisnu', '08495156996', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('2', 'Hasan', '08849001887', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('3', 'Alfi', '08722312356', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('4', 'Panji', '08531514999', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('5', 'Abil', '08858603986', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('6', 'Rizal', '08397800900', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('7', 'AW', '08131524054', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('8', 'Bayu', '08754694827', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('9', 'Debri', '08821527898', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('10', 'Ken', '08853822662', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('11', 'Pak Kris', '08753681811', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('12', 'Yanu', '08931113836', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('13', 'Rizky Pahlevi', '08105750790', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('14', 'Ilham', '08558478101', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('15', 'Sandro', '08344251994', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('16', 'Xenod', '08346566908', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO members (id, full_name, phone_number, created_at, updated_at) VALUES ('17', 'Ryu', '08901387332', '2026-06-08 02:38:29', '2026-06-08 02:38:29');

-- Table: registrations (17 rows)
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('1', '1', '1', 'Wisnu', 'GK', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('2', '1', '2', 'Hasan', 'GK', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('3', '1', '3', 'Alfi', 'GK', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('4', '1', '4', 'Panji', 'DF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('5', '1', '5', 'Abil', 'DF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('6', '1', '6', 'Rizal', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('7', '1', '7', 'AW', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('8', '1', '8', 'Bayu', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('9', '1', '9', 'Debri', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('10', '1', '10', 'Ken', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('11', '1', '11', 'Pak Kris', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('12', '1', '12', 'Yanu', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('13', '1', '13', 'Rizky Pahlevi', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('14', '1', '14', 'Ilham', 'MF', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('15', '1', '15', 'Sandro', 'FW', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('16', '1', '16', 'Xenod', 'FW', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');
INSERT INTO registrations (id, match_id, member_id, player_name, position, is_accepted, created_at, updated_at, team_name, is_paid) VALUES ('17', '1', '17', 'Ryu', 'FW', '1', '2026-06-08 02:38:29', '2026-06-08 02:38:29', NULL, '0');

-- Table: settings (10 rows)
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('1', 'app_name', 'Golkrie', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('2', 'app_tagline', 'Golek Kringet, Jalin Seduluran.', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('3', 'footer_text', '© 2024 Golkrie Community. All rights reserved.', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('4', 'instagram_url', 'https://instagram.com/golkrie', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('5', 'whatsapp_contact', '08123456789', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('6', 'hero_description', 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau dua kali seminggu. Fun football yang kompetitif namun tetap seru.', NULL, NULL);
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('7', 'about_description', 'Golkrie adalah komunitas sepakbola yang berfokus pada kesehatan dan tali persaudaraan. Kami percaya bahwa olahraga adalah cara terbaik untuk menjaga tubuh tetap bugar sekaligus menambah relasi baru.', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('8', 'about_quote', 'Bukan sekadar mengejar bola, tapi mengejar keringat dan mempererat tali silaturahmi antar pecinta sepakbola di Semarang.', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('9', 'about_est', 'EST 2024', '2026-06-08 02:38:29', '2026-06-08 02:38:29');
INSERT INTO settings (id, key, value, created_at, updated_at) VALUES ('10', 'about_hashtag', '#GolekKringet', '2026-06-08 02:38:29', '2026-06-08 02:38:29');

