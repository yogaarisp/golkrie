<?php
/**
 * Script Backup Data Golkrie ke SQL
 * Jalankan: php backup_data.php
 * Output: backup/golkrie_backup_TANGGAL.sql
 */

$db = new PDO(
    'sqlite:' . __DIR__ . '/backend/database/database.sqlite',
    null, null,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$tables = ['matches', 'members', 'registrations', 'settings', 'sponsors', 'users'];
$date   = date('Y-m-d_H-i-s');
$dir    = __DIR__ . '/backup';
$file   = "$dir/golkrie_backup_$date.sql";

if (!is_dir($dir)) mkdir($dir, 0755, true);

$sql  = "-- Golkrie Backup - $date\n";
$sql .= "-- Paste ke SQL Editor Supabase setelah schema sudah dibuat\n\n";
$sql .= "TRUNCATE TABLE registrations, members, matches, settings, sponsors, users RESTART IDENTITY CASCADE;\n\n";

foreach ($tables as $table) {
    $rows = $db->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) continue;

    $sql .= "-- Table: $table (" . count($rows) . " rows)\n";
    $columns = implode(', ', array_keys($rows[0]));

    foreach ($rows as $row) {
        $values = implode(', ', array_map(function ($v) use ($db) {
            if ($v === null) return 'NULL';
            return "'" . str_replace("'", "''", $v) . "'";
        }, array_values($row)));
        $sql .= "INSERT INTO $table ($columns) VALUES ($values);\n";
    }
    $sql .= "\n";
}

file_put_contents($file, $sql);
echo "✅ Backup berhasil: $file\n";
echo "📋 Tinggal paste ke SQL Editor Supabase kalau project ke-pause.\n";
