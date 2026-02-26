<?php

require_once __DIR__ . '/vendor/autoload.php';

use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;

// Configuratie
$config = [
    'database_type' => 'mysql', // Opties: mysql, pgsql, sqlite
    'db_name' => 'your_database_name',
    'username' => 'your_username',
    'password' => 'your_password',
    'host' => 'localhost',
    'port' => 3306, // 3306 voor MySQL, 5432 voor PostgreSQL
    'backup_path' => __DIR__ . '/backups',
    'sqlite_path' => __DIR__ . '/database.sqlite', // Alleen voor SQLite
];

// Maak backup directory aan indien niet bestaat
if (!file_exists($config['backup_path'])) {
    mkdir($config['backup_path'], 0755, true);
}

// Hoofdmenu
function showMenu() {
    echo "\n";
    echo "╔════════════════════════════════════════════╗\n";
    echo "║   DATABASE BACKUP & RESTORE SYSTEEM       ║\n";
    echo "╚════════════════════════════════════════════╝\n";
    echo "\n";
    echo "1. Backup maken\n";
    echo "2. Backup terugzetten\n";
    echo "3. Beschikbare backups tonen\n";
    echo "4. Oude backups verwijderen\n";
    echo "5. Afsluiten\n";
    echo "\n";
    echo "Kies een optie (1-5): ";
}

// Backup maken
function createBackup($config) {
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "{$config['backup_path']}/{$config['db_name']}_{$timestamp}.sql";

    try {
        echo "\n";
        echo "═══════════════════════════════════════════\n";
        echo "  BACKUP MAKEN\n";
        echo "═══════════════════════════════════════════\n";
        echo "Database: {$config['db_name']}\n";
        echo "Type: {$config['database_type']}\n";
        echo "Bestand: {$filename}\n\n";
        echo "Bezig met backup maken...\n";

        switch ($config['database_type']) {
            case 'mysql':
                MySql::create()
                    ->setDbName($config['db_name'])
                    ->setUserName($config['username'])
                    ->setPassword($config['password'])
                    ->setHost($config['host'])
                    ->setPort($config['port'])
                    ->addExtraOption('--single-transaction')
                    ->addExtraOption('--quick')
                    ->dumpToFile($filename);
                break;

            case 'pgsql':
                PostgreSql::create()
                    ->setDbName($config['db_name'])
                    ->setUserName($config['username'])
                    ->setPassword($config['password'])
                    ->setHost($config['host'])
                    ->setPort($config['port'])
                    ->dumpToFile($filename);
                break;

            case 'sqlite':
                Sqlite::create()
                    ->setDbName($config['sqlite_path'])
                    ->dumpToFile($filename);
                break;

            default:
                throw new Exception("Niet ondersteund database type: {$config['database_type']}");
        }

        echo "Backup succesvol aangemaakt!\n";
        echo "Bestandsgrootte: " . formatBytes(filesize($filename)) . "\n";

        // Optioneel: Comprimeren
        if (extension_loaded('zlib')) {
            echo "\nBezig met comprimeren...\n";
            compressFile($filename);
            echo "Backup gecomprimeerd!\n";
        }

        echo "\n";

    } catch (Exception $e) {
        echo "Backup mislukt: " . $e->getMessage() . "\n";
    }
}

// Backup terugzetten
function restoreBackup($config) {
    echo "\n";
    echo "═══════════════════════════════════════════\n";
    echo "  BACKUP TERUGZETTEN\n";
    echo "═══════════════════════════════════════════\n";
    
    $backups = listBackups($config['backup_path']);
    
    if (empty($backups)) {
        echo "Geen backups gevonden!\n";
        return;
    }

    echo "\nBeschikbare backups:\n\n";
    foreach ($backups as $index => $backup) {
        echo ($index + 1) . ". {$backup['name']} ({$backup['size']}) - {$backup['date']}\n";
    }

    echo "\nKies een backup nummer (of 0 om te annuleren): ";
    $choice = trim(fgets(STDIN));

    if ($choice == 0 || !isset($backups[$choice - 1])) {
        echo "Geannuleerd.\n";
        return;
    }

    $selectedBackup = $backups[$choice - 1];
    
    echo "\nWAARSCHUWING: Huidige database wordt overschreven!\n";
    echo "Weet u zeker dat u wilt doorgaan? (ja/nee): ";
    $confirm = trim(fgets(STDIN));

    if (strtolower($confirm) !== 'ja') {
        echo "Geannuleerd.\n";
        return;
    }

    try {
        echo "\nBezig met terugzetten...\n";

        // Decomprimeer indien nodig
        $sqlFile = $selectedBackup['path'];
        if (pathinfo($sqlFile, PATHINFO_EXTENSION) === 'gz') {
            $decompressed = decompressFile($sqlFile);
            $sqlFile = $decompressed;
        }

        // Restore database
        switch ($config['database_type']) {
            case 'mysql':
                $cmd = sprintf(
                    'mysql -h%s -P%d -u%s -p%s %s < %s',
                    escapeshellarg($config['host']),
                    $config['port'],
                    escapeshellarg($config['username']),
                    escapeshellarg($config['password']),
                    escapeshellarg($config['db_name']),
                    escapeshellarg($sqlFile)
                );
                exec($cmd, $output, $returnCode);
                break;

            case 'pgsql':
                $cmd = sprintf(
                    'PGPASSWORD=%s psql -h %s -p %d -U %s %s < %s',
                    escapeshellarg($config['password']),
                    escapeshellarg($config['host']),
                    $config['port'],
                    escapeshellarg($config['username']),
                    escapeshellarg($config['db_name']),
                    escapeshellarg($sqlFile)
                );
                exec($cmd, $output, $returnCode);
                break;

            case 'sqlite':
                $cmd = sprintf(
                    'sqlite3 %s < %s',
                    escapeshellarg($config['sqlite_path']),
                    escapeshellarg($sqlFile)
                );
                exec($cmd, $output, $returnCode);
                break;
        }

        // Verwijder tijdelijk gedecomprimeerd bestand
        if (isset($decompressed) && file_exists($decompressed)) {
            unlink($decompressed);
        }

        if ($returnCode === 0) {
            echo "Backup succesvol teruggezet!\n";
        } else {
            echo "Er is een fout opgetreden bij het terugzetten.\n";
        }

    } catch (Exception $e) {
        echo "Terugzetten mislukt: " . $e->getMessage() . "\n";
    }
}

// Toon beschikbare backups
function showBackups($backupPath) {
    echo "\n";
    echo "═══════════════════════════════════════════\n";
    echo "  BESCHIKBARE BACKUPS\n";
    echo "═══════════════════════════════════════════\n\n";

    $backups = listBackups($backupPath);

    if (empty($backups)) {
        echo "Geen backups gevonden.\n";
        return;
    }

    foreach ($backups as $backup) {
        echo "• {$backup['name']}\n";
        echo "  Grootte: {$backup['size']}\n";
        echo "  Datum: {$backup['date']}\n\n";
    }

    echo "Totaal: " . count($backups) . " backup(s)\n";
}

// Oude backups verwijderen
function cleanOldBackups($backupPath) {
    echo "\n";
    echo "═══════════════════════════════════════════\n";
    echo "  OUDE BACKUPS VERWIJDEREN\n";
    echo "═══════════════════════════════════════════\n\n";
    echo "Hoeveel dagen wilt u bewaren? (bijv. 7 voor een week): ";
    
    $days = (int)trim(fgets(STDIN));
    
    if ($days <= 0) {
        echo "Ongeldige waarde.\n";
        return;
    }

    $files = glob($backupPath . '/*.{sql,gz}', GLOB_BRACE);
    $now = time();
    $deleted = 0;

    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = $now - filemtime($file);
            $daysOld = floor($fileAge / (60 * 60 * 24));

            if ($daysOld > $days) {
                unlink($file);
                $deleted++;
                echo "Verwijderd: " . basename($file) . " ({$daysOld} dagen oud)\n";
            }
        }
    }

    if ($deleted > 0) {
        echo "\n{$deleted} oude backup(s) verwijderd\n";
    } else {
        echo "Geen oude backups gevonden.\n";
    }
}

// Hulpfuncties
function listBackups($backupPath) {
    $files = glob($backupPath . '/*.{sql,gz}', GLOB_BRACE);
    $backups = [];

    foreach ($files as $file) {
        $backups[] = [
            'path' => $file,
            'name' => basename($file),
            'size' => formatBytes(filesize($file)),
            'date' => date('d-m-Y H:i:s', filemtime($file)),
            'timestamp' => filemtime($file)
        ];
    }

    usort($backups, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    return $backups;
}

function compressFile($filename) {
    $gzFilename = $filename . '.gz';
    $fp = fopen($filename, 'rb');
    $gzFp = gzopen($gzFilename, 'wb9');

    while (!feof($fp)) {
        gzwrite($gzFp, fread($fp, 1024 * 512));
    }

    fclose($fp);
    gzclose($gzFp);
    unlink($filename);

    return $gzFilename;
}

function decompressFile($gzFilename) {
    $filename = str_replace('.gz', '', $gzFilename);
    $gzFp = gzopen($gzFilename, 'rb');
    $fp = fopen($filename, 'wb');

    while (!gzeof($gzFp)) {
        fwrite($fp, gzread($gzFp, 1024 * 512));
    }

    gzclose($gzFp);
    fclose($fp);

    return $filename;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

// Hoofdprogramma
while (true) {
    showMenu();
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case '1':
            createBackup($config);
            break;
        case '2':
            restoreBackup($config);
            break;
        case '3':
            showBackups($config['backup_path']);
            break;
        case '4':
            cleanOldBackups($config['backup_path']);
            break;
        case '5':
            echo "\nTot ziens!\n\n";
            exit(0);
        default:
            echo "\nOngeldige keuze. Probeer opnieuw.\n";
    }

    echo "\nDruk op ENTER om door te gaan...";
    fgets(STDIN);
}