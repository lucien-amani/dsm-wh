<?php
header("Content-Type: application/xml; charset=utf-8");
require_once 'config/database.php';
require_once 'config/router.php'; // Inclut Hashids

$pdo = getDBConnection();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Pages Statiques -->
    <url>
        <loc><?php echo SITE_URL; ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/a-propos</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/actualites</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/projets</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Actualités Dynamiques -->
    <?php
    $stmt = $pdo->query("SELECT id, nanoid, slug, updated_at FROM news WHERE published = 1 ORDER BY created_at DESC");
    while($row = $stmt->fetch()):
        // Utiliser nanoid si présent, sinon hashids
        $hash = $row['nanoid'] ?: $hashids->encode($row['id']);
        $url = SITE_URL . "/view/news/" . $hash . ($row['slug'] ? "/" . $row['slug'] : "");
    ?>
    <url>
        <loc><?php echo htmlspecialchars($url); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($row['updated_at'] ?? 'now')); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endwhile; ?>

    <!-- Projets Dynamiques -->
    <?php
    $stmt = $pdo->query("SELECT id, nanoid, slug, updated_at FROM projects WHERE published = 1 OR status = 'En cours' OR status = 'Terminé' ORDER BY created_at DESC");
    while($row = $stmt->fetch()):
        // Utiliser nanoid si présent, sinon hashids
        $hash = $row['nanoid'] ?: $hashids->encode($row['id']);
        $url = SITE_URL . "/view/projects/" . $hash . ($row['slug'] ? "/" . $row['slug'] : "");
    ?>
    <url>
        <loc><?php echo htmlspecialchars($url); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($row['updated_at'] ?? 'now')); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endwhile; ?>
</urlset>
