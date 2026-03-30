<?php
require_once '../config/database.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    // Log de la déconnexion via la fonction centralisée
    logAdminAction($_SESSION['admin_id'], 'deconnexion');
}

session_destroy();
header('Location: ' . SITE_URL . '/admin');
exit;
