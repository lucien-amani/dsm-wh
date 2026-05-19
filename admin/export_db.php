<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

$format = $_GET['format'] ?? 'sql';
$timestamp = date('Y-m-d_H-i-s');
$dbName = defined('DB_NAME') ? DB_NAME : 'dsm_website';

// Récupérer toutes les tables de la base de données
$tables = [];
try {
    $query = $pdo->query("SHOW TABLES");
    while ($row = $query->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
} catch (Exception $e) {
    die("Erreur de récupération des tables : " . $e->getMessage());
}

if (empty($tables)) {
    die("La base de données est vide.");
}

// Enregistrer l'action dans les logs d'administration
logAdminAction($_SESSION['admin_id'], "export de la base de données au format : " . strtoupper($format));

// 1. Export au format SQL
if ($format === 'sql') {
    $filename = $dbName . "_backup_" . $timestamp . ".sql";
    
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "-- --------------------------------------------------------\n";
    echo "-- DSM-WH DATABASE BACKUP DUMP\n";
    echo "-- Base de données : " . $dbName . "\n";
    echo "-- Date de génération : " . date('d-m-Y H:i:s') . "\n";
    echo "-- --------------------------------------------------------\n\n";
    
    echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        echo "-- --------------------------------------------------------\n";
        echo "-- Structure de la table `" . $table . "`\n";
        echo "-- --------------------------------------------------------\n";
        echo "DROP TABLE IF EXISTS `" . $table . "`;\n";
        
        try {
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $createRow = $stmt->fetch(PDO::FETCH_NUM);
            echo $createRow[1] . ";\n\n";
        } catch (Exception $e) {
            echo "-- Erreur structure table $table : " . $e->getMessage() . "\n\n";
            continue;
        }
        
        try {
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            
            if (!empty($rows)) {
                echo "-- --------------------------------------------------------\n";
                echo "-- Chargement des données de la table `" . $table . "`\n";
                echo "-- --------------------------------------------------------\n";
                echo "LOCK TABLES `" . $table . "` WRITE;\n";
                echo "INSERT INTO `" . $table . "` VALUES \n";
                
                $insertRows = [];
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $values[] = "NULL";
                        } else {
                            $values[] = $pdo->quote($val);
                        }
                    }
                    $insertRows[] = "(" . implode(', ', $values) . ")";
                }
                echo implode(",\n", $insertRows) . ";\n";
                echo "UNLOCK TABLES;\n\n";
            }
        } catch (Exception $e) {
            echo "-- Erreur données table $table : " . $e->getMessage() . "\n\n";
        }
    }
    
    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}

// 2. Export au format JSON
if ($format === 'json') {
    $filename = $dbName . "_backup_" . $timestamp . ".json";
    
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $fullData = [
        'metadata' => [
            'database' => $dbName,
            'exported_at' => date('Y-m-d H:i:s'),
            'tables_count' => count($tables)
        ],
        'tables' => []
    ];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $fullData['tables'][$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $fullData['tables'][$table] = ['error' => $e->getMessage()];
        }
    }
    
    echo json_encode($fullData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Export au format CSV (fichiers compressés en ZIP)
if ($format === 'csv') {
    $filename = $dbName . "_backup_" . $timestamp . "_csv.zip";
    
    $zip = new ZipArchive();
    $tempZipPath = tempnam(sys_get_temp_dir(), 'dsm_export_') . '.zip';
    
    if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT * FROM `$table`");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM pour Excel
                
                if (!empty($rows)) {
                    // Headers
                    $headers = array_keys($rows[0]);
                    $csvContent .= implode(';', array_map(function($h) {
                        return '"' . str_replace('"', '""', $h) . '"';
                    }, $headers)) . "\r\n";
                    
                    // Données
                    foreach ($rows as $row) {
                        $csvContent .= implode(';', array_map(function($val) {
                            if ($val === null) return '';
                            return '"' . str_replace('"', '""', $val) . '"';
                        }, $row)) . "\r\n";
                    }
                } else {
                    $csvContent .= "Table vide\r\n";
                }
                
                $zip->addFromString($table . '.csv', $csvContent);
            } catch (Exception $e) {
                $zip->addFromString($table . '_error.txt', "Erreur : " . $e->getMessage());
            }
        }
        $zip->close();
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Length: ' . filesize($tempZipPath));
        
        readfile($tempZipPath);
        unlink($tempZipPath);
        exit;
    } else {
        die("Impossible de créer le fichier ZIP d'exportation.");
    }
}
?>
