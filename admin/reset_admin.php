<?php
/**
 * SCRIPT DE RÉINITIALISATION DE L'ADMINISTRATEUR
 * 
 * Ce script réinitialise le compte administrateur par défaut dans la base de données.
 * IMPORTANT : Supprimez ce fichier après utilisation pour des raisons de sécurité.
 */

require_once '../config/database.php';

echo "<h1>Réinitialisation de l'Administrateur</h1>";

try {
    $pdo = getDBConnection();

    // Informations du nouvel admin
    $username = 'admin';
    $password = 'admin123'; // À CHANGER APRÈS CONNEXION
    $fullName = 'Administrateur DSM';
    $email    = 'admin@dsm-samy.com';
    $role     = 'Superadmin';
    
    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si la table users existe (déjà fait par l'inspection, mais sécure)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        email VARCHAR(150),
        avatar VARCHAR(255),
        role ENUM('Admin', 'Superadmin') DEFAULT 'Admin',
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Supprimer l'ancien admin s'il existe pour éviter le conflit UNIQUE
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);

    // Insérer le nouvel admin
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (:u, :p, :f, :e, :r)");
    $success = $stmt->execute([
        ':u' => $username,
        ':p' => $hashedPassword,
        ':f' => $fullName,
        ':e' => $email,
        ':r' => $role
    ]);

    if ($success) {
        echo "<div style='color:green; font-weight:bold; border:2px solid green; padding:15px; background:#e6ffed;'>";
        echo "✅ Succès ! Le compte administrateur a été réinitialisé.<br><br>";
        echo "Identifiants :<br>";
        echo "- Utilisateur : <strong>$username</strong><br>";
        echo "- Mot de passe : <strong>$password</strong><br><br>";
        echo "👉 <a href='login.php'>Retourner à la page de connexion</a><br><br>";
        echo "<span style='color:red;'>⚠️ ATTENTION : Supprimez immédiatement le fichier <code>admin/reset_admin.php</code> !</span>";
        echo "</div>";
    } else {
        echo "<div style='color:red;'>❌ Erreur lors de l'insertion.</div>";
    }

} catch (PDOException $e) {
    echo "<div style='color:red;'>❌ Erreur : " . $e->getMessage() . "</div>";
}
?>
