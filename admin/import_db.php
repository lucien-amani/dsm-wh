<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Requête non autorisée.']);
    exit;
}

if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner un fichier de sauvegarde valide.']);
    exit;
}

$fileTmp = $_FILES['backup_file']['tmp_name'];
$fileName = $_FILES['backup_file']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$pdo = getDBConnection();

// Enregistrer l'action d'importation dans les logs
logAdminAction($_SESSION['admin_id'], "tentative d'importation/restauration de base de données : " . $fileName);

try {
    // 1. IMPORTATION DU FORMAT SQL
    if ($fileExtension === 'sql') {
        $sqlContent = file_get_contents($fileTmp);
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $pdo->exec($sqlContent);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
        
        echo json_encode(['success' => true, 'message' => 'Base de données SQL restaurée avec succès !']);
        exit;
    }

    // 2. IMPORTATION DU FORMAT JSON
    if ($fileExtension === 'json') {
        $jsonContent = file_get_contents($fileTmp);
        $data = json_decode($jsonContent, true);
        
        if (!$data || !isset($data['tables'])) {
            throw new Exception("Le fichier JSON est invalide ou ne contient pas de section 'tables'.");
        }
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        foreach ($data['tables'] as $table => $rows) {
            // Vider la table
            $pdo->exec("TRUNCATE TABLE `$table`");
            
            if (empty($rows)) continue;
            
            $columns = array_keys($rows[0]);
            $colList = implode('`, `', $columns);
            
            // Insertion par lots (chunking) pour de meilleures performances
            $chunks = array_chunk($rows, 100);
            foreach ($chunks as $chunk) {
                $insertQueries = [];
                $params = [];
                foreach ($chunk as $idx => $row) {
                    $rowParams = [];
                    foreach ($columns as $col) {
                        $paramName = ":" . $col . "_" . $idx;
                        $rowParams[] = $paramName;
                        $params[$paramName] = $row[$col];
                    }
                    $insertQueries[] = "(" . implode(', ', $rowParams) . ")";
                }
                $sql = "INSERT INTO `$table` (`$colList`) VALUES " . implode(', ', $insertQueries);
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
        
        echo json_encode(['success' => true, 'message' => 'Base de données JSON restaurée avec succès !']);
        exit;
    }

    // 3. IMPORTATION DU FORMAT CSV (ARCHIVE ZIP)
    if ($fileExtension === 'zip') {
        $zip = new ZipArchive();
        if ($zip->open($fileTmp) === TRUE) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
            
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                
                // Ignorer les fichiers non-CSV
                if (strtolower($fileinfo['extension'] ?? '') !== 'csv') continue;
                
                $table = $fileinfo['filename'];
                $csvContent = $zip->getFromIndex($i);
                
                // Supprimer le marqueur BOM UTF-8 s'il est présent
                if (substr($csvContent, 0, 3) === "\xEF\xBB\xBF") {
                    $csvContent = substr($csvContent, 3);
                }
                
                $lines = explode("\r\n", trim($csvContent));
                if (empty($lines)) continue;
                
                // Première ligne : En-têtes
                $headersLine = array_shift($lines);
                $headers = str_getcsv($headersLine, ';');
                
                // Vider la table
                $pdo->exec("TRUNCATE TABLE `$table`");
                
                $rows = [];
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $rowValues = str_getcsv($line, ';');
                    
                    // Aligner les colonnes
                    if (count($rowValues) !== count($headers)) {
                        $rowValues = array_slice(array_pad($rowValues, count($headers), null), 0, count($headers));
                    }
                    
                    $rowAssoc = [];
                    foreach ($headers as $idx => $h) {
                        $val = $rowValues[$idx];
                        $rowAssoc[$h] = ($val === 'NULL' || $val === '') ? null : $val;
                    }
                    $rows[] = $rowAssoc;
                }
                
                if (empty($rows)) continue;
                
                $colList = implode('`, `', $headers);
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    $insertQueries = [];
                    $params = [];
                    foreach ($chunk as $idx => $row) {
                        $rowParams = [];
                        foreach ($headers as $col) {
                            $paramName = ":" . $col . "_" . $idx;
                            $rowParams[] = $paramName;
                            $params[$paramName] = $row[$col];
                        }
                        $insertQueries[] = "(" . implode(', ', $rowParams) . ")";
                    }
                    $sql = "INSERT INTO `$table` (`$colList`) VALUES " . implode(', ', $insertQueries);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                }
            }
            
            $zip->close();
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
            
            echo json_encode(['success' => true, 'message' => 'Base de données restaurée depuis l\'archive CSV avec succès !']);
            exit;
        } else {
            throw new Exception("Impossible d'ouvrir l'archive ZIP.");
        }
    }

    throw new Exception("Format de fichier non supporté (.{$fileExtension}). Utilisez .sql, .json ou .zip.");

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }
    echo json_encode(['success' => false, 'message' => 'Erreur de restauration : ' . $e->getMessage()]);
    exit;
}
?>
