<?php
/**
 * admin/api/autosave.php
 * Sauvegarde automatique des brouillons pour actualités et projets.
 */
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$pdo = getDBConnection();

$type = $_POST['type'] ?? ''; // 'news' ou 'project'
$id   = (int)($_POST['id'] ?? 0);
$data = $_POST['data'] ?? [];

if (!in_array($type, ['news', 'project'])) {
    echo json_encode(['error' => 'Type invalide']);
    exit;
}

$table = ($type === 'news') ? 'news' : 'projects';

// Préparer les données
$title   = trim($data['title'] ?? '');
if (empty($title)) {
    $title = "Brouillon auto - " . date('d/m/Y H:i:s');
}

$slug    = $data['slug'] ?? generateSlug($title);
$content = $data['content'] ?? '';
$author_id = $_SESSION['admin_id'];

// Pour les news
$excerpt = $data['excerpt'] ?? '';
$category = $data['category'] ?? '';
$embed_code = $data['embed_code'] ?? '';

// Pour les projets
$description = $data['description'] ?? ''; // C'est l'équivalent de l'excerpt pour les projets
$location = $data['location'] ?? '';
$status = $data['status'] ?? 'En cours';
$beneficiaries = $data['beneficiaries'] ?? '';

try {
    if ($id > 0) {
        // Mise à jour du brouillon existant
        if ($type === 'news') {
            $stmt = $pdo->prepare("UPDATE news SET title = :title, excerpt = :excerpt, content = :content, category = :category, embed_code = :embed, published = 0, author_id = :author WHERE id = :id");
            $stmt->execute([
                ':title' => $title, ':excerpt' => $excerpt, ':content' => $content, ':category' => $category, ':embed' => $embed_code, ':author' => $author_id, ':id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE projects SET title = :title, description = :desc, content = :content, location = :loc, status = :status, beneficiaries = :ben, published = 0, author_id = :author WHERE id = :id");
            $stmt->execute([
                ':title' => $title, ':desc' => $description, ':content' => $content, ':loc' => $location, ':status' => $status, ':ben' => $beneficiaries, ':author' => $author_id, ':id' => $id
            ]);
        }
        $new_id = $id;
    } else {
        // Création d'un nouveau brouillon
        // On vérifie si un brouillon "vide" récent existe déjà pour ce user pour éviter de multiplier les lignes
        $stmt_check = $pdo->prepare("SELECT id FROM $table WHERE author_id = :aid AND published = 0 AND title LIKE 'Brouillon auto%' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY id DESC LIMIT 1");
        $stmt_check->execute([':aid' => $author_id]);
        $existing = $stmt_check->fetch();

        if ($existing) {
            $new_id = $existing['id'];
            // On met à jour celui-ci
            if ($type === 'news') {
                $stmt = $pdo->prepare("UPDATE news SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, published = 0 WHERE id = :id");
                $stmt->execute([':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content, ':id' => $new_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE projects SET title = :title, slug = :slug, description = :desc, content = :content, published = 0 WHERE id = :id");
                $stmt->execute([':title' => $title, ':slug' => $slug, ':desc' => $description, ':content' => $content, ':id' => $new_id]);
            }
        } else {
            // Unicité du slug
            $original_slug = $slug;
            $counter = 1;
            while (true) {
                $st_c = $pdo->prepare("SELECT id FROM $table WHERE slug = ?");
                $st_c->execute([$slug]);
                if ($st_c->rowCount() > 0) {
                    $slug = $original_slug . '-' . $counter;
                    $counter++;
                } else { break; }
            }

            if ($type === 'news') {
                $stmt = $pdo->prepare("INSERT INTO news (title, slug, excerpt, content, category, embed_code, published, author_id) VALUES (:title, :slug, :excerpt, :content, :cat, :embed, 0, :author)");
                $stmt->execute([':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content, ':cat' => $category, ':embed' => $embed_code, ':author' => $author_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO projects (title, slug, description, content, location, status, beneficiaries, published, author_id) VALUES (:title, :slug, :desc, :content, :loc, :status, :ben, 0, :author)");
                $stmt->execute([':title' => $title, ':slug' => $slug, ':desc' => $description, ':content' => $content, ':loc' => $location, ':status' => $status, ':ben' => $beneficiaries, ':author' => $author_id]);
            }
            $new_id = $pdo->lastInsertId();
        }
    }

    echo json_encode([
        'success' => true,
        'id' => $new_id,
        'time' => date('H:i:s'),
        'message' => 'Brouillon enregistré automatiquement'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
