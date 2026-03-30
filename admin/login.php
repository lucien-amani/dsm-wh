<?php
require_once '../config/database.php';
session_start();

// Si déjà connecté, vérifier le timeout et rediriger
if (isset($_SESSION['admin_id'])) {
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        header('Location: ' . SITE_URL . '/admin/connexion?timeout=1');
        exit;
    }
    header('Location: ' . SITE_URL . '/admin/tableau-de-bord');
    exit;
}

$error = '';
$info = ''; // Message d'information (ex: timeout)

if (isset($_GET['timeout'])) {
    $info = 'Votre session a expiré après une période d\'inactivité. Veuillez vous reconnecter.';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        $pdo  = getDBConnection();
        // Recherche par email OU téléphone
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone OR username = :username");
        $stmt->execute([
            ':email' => $username, 
            ':phone' => $username,
            ':username' => $username
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);

            // Log de la connexion via la fonction centralisée (l'IP est détectée automatiquement)
            logAdminAction($user['id'], 'connexion');

            header('Location: ' . SITE_URL . '/admin/tableau-de-bord');
            exit;
        } else {
            $error = 'Identifiants invalides (E-mail ou Téléphone incorrect).';
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — DSM Admin</title>
    <meta name="description" content="Accédez à l'espace d'administration sécurisé de la plateforme DSM.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════
           RESET & TOKENS
        ═══════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:      #3aed58ff;
            --accent-mid:  #5cf67dff;
            --accent-glow: rgba(58, 237, 88, 0.35);
            --accent-soft: rgba(73, 237, 58, 0.12);
            --gold:        #F59E0B;

            --bg-left:     #0F0A1E;
            --bg-right:    #ffffff;

            --text-on-dark:   #E2D9F3;
            --text-muted:     rgba(226,217,243,0.55);

            --input-border:   #E5E7EB;
            --input-focus:    #3aed70ff;
            --text-field:     #1F2937;

            --radius-card:  24px;
            --radius-input: 14px;
            --radius-btn:   14px;

            --transition: 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        /* ═══════════════════════════════════════════════
           LAYOUT SPLIT-SCREEN
        ═══════════════════════════════════════════════ */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ── PANEL GAUCHE ── */
        .panel-left {
            position: relative;
            width: 52%;
            background: var(--bg-left);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 48px 56px;
        }

        /* Grille décorative */
        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,58,237,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,58,237,0.07) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Vignette sur les bords */
        .panel-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 40%, rgba(15,10,30,0.75) 100%);
            pointer-events: none;
        }

        /* Canvas particules */
        #particles-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* Orbs lumineux */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.55;
            pointer-events: none;
        }
        .orb-1 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, #7C3AED 0%, #4F46E5 60%, transparent 100%);
            top: -60px; left: -80px;
            animation: orbFloat 8s ease-in-out infinite;
        }
        .orb-2 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, #EC4899 0%, #8B5CF6 60%, transparent 100%);
            bottom: -40px; right: -60px;
            animation: orbFloat 10s ease-in-out infinite reverse;
        }
        .orb-3 {
            width: 180px; height: 180px;
            background: radial-gradient(circle, #06B6D4 0%, #3B82F6 60%, transparent 100%);
            top: 50%; left: 60%;
            transform: translate(-50%,-50%);
            animation: orbFloat 12s ease-in-out infinite 2s;
            opacity: 0.25;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%  { transform: translate(20px,-25px) scale(1.05); }
            66%  { transform: translate(-15px, 15px) scale(0.95); }
        }

        /* Contenu du panel gauche */
        .left-content {
            position: relative;
            z-index: 2;
            max-width: 420px;
            width: 100%;
        }

        /* Badge DSM */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }
        .brand-logo {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 24px rgba(124,58,237,0.6);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 0 24px rgba(124,58,237,0.5); }
            50%       { box-shadow: 0 0 40px rgba(124,58,237,0.9), 0 0 60px rgba(124,58,237,0.3); }
        }
        .brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
        }
        .brand-name span { color: var(--accent-mid); }

        /* Tagline */
        .tagline {
            font-size: 2.4rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }
        .tagline .gradient-text {
            background: linear-gradient(135deg, #A78BFA, #60A5FA, #F472B6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline-sub {
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.65;
            margin-bottom: 44px;
            max-width: 340px;
        }

        /* Features list */
        .features-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            opacity: 0;
            transform: translateX(-18px);
            animation: featureIn 0.5s var(--transition) forwards;
        }
        .feature-item:nth-child(1) { animation-delay: 0.3s; }
        .feature-item:nth-child(2) { animation-delay: 0.45s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }
        .feature-item:nth-child(4) { animation-delay: 0.75s; }

        @keyframes featureIn {
            to { opacity: 1; transform: translateX(0); }
        }

        .feature-icon {
            width: 36px; height: 36px; min-width: 36px;
            border-radius: 10px;
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.3);
            display: flex; align-items: center; justify-content: center;
        }
        .feature-icon svg { width: 16px; height: 16px; color: #A78BFA; }

        .feature-text {
            font-size: 0.83rem;
            color: var(--text-on-dark);
            line-height: 1.4;
        }
        .feature-text strong { color: #fff; font-weight: 600; }

        /* Footer gauche */
        .left-footer {
            margin-top: 52px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.74rem;
            color: var(--text-muted);
        }
        .left-footer svg { width: 13px; height: 13px; opacity: 0.6; }

        /* ── PANEL DROIT ── */
        .panel-right {
            width: 48%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-right);
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
        }

        /* Fond droit léger */
        .panel-right::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(124,58,237,0.06) 0%, transparent 70%);
            top: -200px; right: -200px;
            pointer-events: none;
        }
        .panel-right::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,0.05) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }

        /* Carte formulaire */
        .form-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: scale(0.96) translateY(16px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* En-tête formulaire */
        .form-header {
            margin-bottom: 36px;
        }
        .form-greeting {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .form-title {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        .form-title .emoji { font-style: normal; }
        .form-subtitle {
            margin-top: 10px;
            font-size: 0.875rem;
            color: #6B7280;
        }

        /* ── ALERTES ── */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            font-size: 0.82rem;
            margin-bottom: 24px;
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        .alert-error svg { flex-shrink: 0; width: 16px; height: 16px; margin-top: 1px; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%  { transform: translateX(-6px); }
            30%  { transform: translateX(6px); }
            45%  { transform: translateX(-5px); }
            60%  { transform: translateX(5px); }
            75%  { transform: translateX(-3px); }
            90%  { transform: translateX(3px); }
        }

        /* ── GROUPES DE CHAMPS ── */
        .field-group {
            margin-bottom: 20px;
        }
        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            letter-spacing: 0.01em;
        }
        .field-wrap {
            position: relative;
        }
        .field-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            display: flex;
            align-items: center;
            transition: color var(--transition);
        }
        .field-icon-left svg { width: 18px; height: 18px; color: #9CA3AF; transition: color var(--transition); }

        .field-input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid var(--input-border);
            border-radius: var(--radius-input);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            color: var(--text-field);
            background: #F9FAFB;
            outline: none;
            transition: border-color var(--transition), background var(--transition), box-shadow var(--transition);
        }
        .field-input::placeholder { color: #D1D5DB; }
        .field-input:focus {
            border-color: var(--input-focus);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(124,58,237,0.1);
        }
        .field-input:focus + .field-icon-left svg,
        .field-wrap:focus-within .field-icon-left svg {
            color: var(--accent);
        }

        /* Toggle password */
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            color: #9CA3AF;
            transition: color var(--transition);
        }
        .toggle-pw:hover { color: var(--accent); }
        .toggle-pw svg { width: 17px; height: 17px; }

        /* Barre de force du mot de passe */
        .pw-strength-bar {
            margin-top: 7px;
            height: 3px;
            border-radius: 99px;
            background: #F3F4F6;
            overflow: hidden;
        }
        .pw-strength-fill {
            height: 100%;
            border-radius: 99px;
            width: 0%;
            transition: width 0.4s ease, background 0.4s ease;
        }

        /* ── OPTIONS : remember + forget ── */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #4B5563;
            user-select: none;
        }
        .remember-cb {
            appearance: none;
            width: 17px; height: 17px;
            border: 1.5px solid #D1D5DB;
            border-radius: 5px;
            background: #fff;
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition);
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .remember-cb:checked {
            background: var(--accent);
            border-color: var(--accent);
        }
        .remember-cb:checked::after {
            content: '';
            position: absolute;
            width: 4px; height: 7px;
            border-right: 2px solid #fff;
            border-bottom: 2px solid #fff;
            transform: rotate(45deg) translate(-1px,-1px);
        }
        .forget-link {
            font-size: 0.8rem;
            color: var(--accent);
            font-weight: 500;
            text-decoration: none;
            transition: color var(--transition), text-decoration var(--transition);
        }
        .forget-link:hover { color: #5B21B6; text-decoration: underline; }

        /* ── BOUTON CONNEXION ── */
        .btn-login {
            width: 100%;
            padding: 15px 24px;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            border: none;
            border-radius: var(--radius-btn);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 20px rgba(124,58,237,0.4);
            transition: transform var(--transition), box-shadow var(--transition), filter var(--transition);
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
        }

        /* Shine effect */
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-20deg);
            transition: left 0.5s ease;
        }
        .btn-login:hover::before { left: 150%; }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(124,58,237,0.55);
            filter: brightness(1.05);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(124,58,237,0.3);
        }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        /* Spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        .btn-login.is-loading .btn-label { display: none; }
        .btn-login.is-loading .btn-spinner { display: block; }

        /* ── SÉPARATEUR OR ── */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 24px;
        }
        .divider-line { flex: 1; height: 1px; background: #F3F4F6; }
        .divider-text { font-size: 0.75rem; color: #9CA3AF; font-weight: 500; letter-spacing: 0.05em; }

        /* ── BOUTONS SOCIAUX ── */
        .social-row {
            display: flex; gap: 12px; margin-bottom: 36px;
        }
        .social-btn {
            flex: 1;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px 16px;
            border: 1.5px solid #E5E7EB;
            border-radius: 12px;
            background: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            cursor: pointer;
            transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition),background var(--transition);
        }
        .social-btn:hover {
            border-color: #D1D5DB;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            transform: translateY(-1px);
            background: #FAFAFA;
        }
        .social-btn svg { width: 18px; height: 18px; }

        /* ── FOOTER FORMULAIRE ── */
        .form-footer {
            text-align: center;
        }
        .ssl-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.73rem;
            color: #9CA3AF;
        }
        .ssl-badge svg { width: 12px; height: 12px; color: #6EE7B7; }

        /* ══════════════════════════════════════════
           RESPONSIVE — MOBILE
        ══════════════════════════════════════════ */
        @media (max-width: 860px) {
            html, body { overflow: auto; }
            .login-wrapper { flex-direction: column; min-height: 100vh; }

            .panel-left {
                width: 100%;
                min-height: 220px;
                padding: 32px 24px;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                gap: 24px;
            }
            .left-content {
                display: flex;
                align-items: center;
                gap: 20px;
                max-width: 100%;
            }
            .tagline { font-size: 1.4rem; margin-bottom: 0; }
            .tagline-sub, .features-list, .left-footer { display: none; }
            .brand-badge { margin-bottom: 0; }

            .panel-right {
                width: 100%;
                padding: 36px 24px 40px;
                align-items: flex-start;
            }
            .panel-right::before, .panel-right::after { display: none; }
            .form-card { max-width: 100%; }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <!-- ══════════════ PANEL GAUCHE ══════════════ -->
    <div class="panel-left">
        <!-- Orbs -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Particules canvas -->
        <canvas id="particles-canvas"></canvas>

        <!-- Contenu -->
        <div class="left-content">

            <!-- Logo / Badge -->
            <div class="brand-badge">
                <div class="brand-logo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                         stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="brand-name">DYNAMIQUE<span>Samy Magadju</span></div>
            </div>

            <!-- Titre -->
            <h1 class="tagline">
                Panneau de<br>
                <span class="gradient-text">contrôle</span>
            </h1>

            <p class="tagline-sub">
                Gérez votre plateforme DSM en toute sécurité.
                Accès réservé aux administrateurs autorisés.
            </p>

            <!-- Features -->
            <div class="features-list" aria-label="Fonctionnalités">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="feature-text">
                        <strong>Connexion sécurisée SSL</strong> — Données chiffrées de bout en bout
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                    </div>
                    <div class="feature-text">
                        <strong>Tableau de bord complet</strong> — Statistiques & analytiques en temps réel
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="feature-text">
                        <strong>Gestion des utilisateurs</strong> — Rôles & permissions avancés
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="feature-text">
                        <strong>Monitoring live</strong> — Activité & journaux en continu
                    </div>
                </div>
            </div>

            <!-- Footer gauche -->
            <div class="left-footer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Connexion protégée &bull; DSM v2.0 &bull; <?php echo date('Y'); ?>
            </div>

        </div><!-- /left-content -->
    </div><!-- /panel-left -->


    <!-- ══════════════ PANEL DROIT ══════════════ -->
    <div class="panel-right">
        <div class="form-card">

            <!-- En-tête -->
            <div class="form-header">
                <p class="form-greeting">Espace Administrateur</p>
                <h2 class="form-title">Bienvenue <span class="emoji">👋</span></h2>
                <p class="form-subtitle">Connectez-vous pour accéder à votre espace</p>
            </div>

            <!-- Alerte erreur -->
            <?php if ($error): ?>
            <div class="alert-error" role="alert" aria-live="assertive">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Alerte info (Timeout) -->
            <?php if ($info): ?>
            <div class="alert-info" role="status" style="display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; background:#EFF6FF; border:1px solid #BFDBFE; color:#1E40AF; font-size:0.82rem; margin-bottom:24px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; width:16px; height:16px; margin-top:1px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <?php echo htmlspecialchars($info); ?>
            </div>
            <?php endif; ?>


            <!-- Formulaire -->
            <form id="loginForm" method="POST" action="login.php" novalidate>

                <!-- Identifiant -->
                <div class="field-group">
                    <label for="username" class="field-label">E-mail ou Téléphone</label>
                    <div class="field-wrap">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="field-input"
                            placeholder="votre@email.com ou 09..."
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            required
                            autocomplete="username"
                            aria-required="true">
                        <div class="field-icon-left">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="field-group">
                    <label for="password" class="field-label">Mot de passe</label>
                    <div class="field-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            aria-required="true"
                            style="padding-right: 44px;">
                        <div class="field-icon-left">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <!-- Toggle visibilité -->
                        <button type="button" id="togglePw" class="toggle-pw" aria-label="Afficher ou masquer le mot de passe">
                            <svg id="iconEye" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="iconEyeOff" class="hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Barre force mot de passe -->
                    <div class="pw-strength-bar">
                        <div class="pw-strength-fill" id="pwStrengthFill"></div>
                    </div>
                </div>

                <!-- Options -->
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="rememberMe" class="remember-cb">
                        Se souvenir de moi
                    </label>
                    <a href="forgot-password" class="forget-link">Mot de passe oublié ?</a>
                </div>

                <!-- Bouton connexion -->
                <button type="submit" id="submitBtn" class="btn-login">
                    <span class="btn-label">Se connecter</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>

            </form>

            <!-- Séparateur -->
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">ou continuer avec</span>
                <div class="divider-line"></div>
            </div>

            <!-- Boutons sociaux -->
            <div class="social-row">
                <!-- Microsoft -->
                <a href="#" class="social-btn" aria-label="Connexion Microsoft">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#F25022" d="M1 1h10v10H1z"/>
                        <path fill="#00A4EF" d="M13 1h10v10H13z"/>
                        <path fill="#7FBA00" d="M1 13h10v10H1z"/>
                        <path fill="#FFB900" d="M13 13h10v10H13z"/>
                    </svg>
                    Microsoft
                </a>
                <!-- Google -->
                <a href="#" class="social-btn" aria-label="Connexion Google">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </a>
            </div>

            <!-- Retour à l'accueil -->
            <div style="margin-top:-1rem; margin-bottom: 2rem; text-align:center;">
                <a href="<?php echo SITE_URL; ?>/" class="social-btn" style="width:100%; justify-content:center; border-style:dashed; border-color:#d1d5db; background:rgba(249,250,251,0.5);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retourner à l'accueil du site
                </a>
            </div>

            <!-- Pied de page -->
            <div class="form-footer">
                <span class="ssl-badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Connexion sécurisée SSL 256-bit
                </span>
            </div>

        </div><!-- /form-card -->
    </div><!-- /panel-right -->

</div><!-- /login-wrapper -->


<script>
/* ──────────────────────────────────────
   PARTICULES CANVAS (panel gauche)
──────────────────────────────────────── */
(function() {
    const canvas = document.getElementById('particles-canvas');
    const panel  = canvas.parentElement;
    const ctx    = canvas.getContext('2d');
    let particles = [];
    const PARTICLE_COUNT = 55;
    const COLORS = ['rgba(167,139,250,', 'rgba(99,102,241,', 'rgba(236,72,153,', 'rgba(96,165,250,'];

    function resize() {
        canvas.width  = panel.offsetWidth;
        canvas.height = panel.offsetHeight;
    }

    function createParticle() {
        return {
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 2.2 + 0.4,
            vx: (Math.random() - 0.5) * 0.4,
            vy: (Math.random() - 0.5) * 0.4,
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
            alpha: Math.random() * 0.5 + 0.15,
        };
    }

    function initParticles() {
        particles = Array.from({ length: PARTICLE_COUNT }, createParticle);
    }

    function drawConnections() {
        const maxDist = 120;
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < maxDist) {
                    const alpha = (1 - dist / maxDist) * 0.15;
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(167,139,250,${alpha})`;
                    ctx.lineWidth = 0.8;
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        drawConnections();

        particles.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width)  p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height)  p.vy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = p.color + p.alpha + ')';
            ctx.fill();
        });

        requestAnimationFrame(animate);
    }

    resize();
    initParticles();
    animate();
    window.addEventListener('resize', () => { resize(); initParticles(); });
})();


/* ──────────────────────────────────────
   TOGGLE MOT DE PASSE
──────────────────────────────────────── */
const togglePw   = document.getElementById('togglePw');
const pwInput    = document.getElementById('password');
const iconEye    = document.getElementById('iconEye');
const iconEyeOff = document.getElementById('iconEyeOff');

togglePw.addEventListener('click', () => {
    const show = pwInput.type === 'password';
    pwInput.type = show ? 'text' : 'password';
    iconEye.style.display    = show ? 'none'  : '';
    iconEyeOff.style.display = show ? ''      : 'none';
});


/* ──────────────────────────────────────
   FORCE DU MOT DE PASSE
──────────────────────────────────────── */
const pwStrengthFill = document.getElementById('pwStrengthFill');
pwInput.addEventListener('input', () => {
    const val = pwInput.value;
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/\d/.test(val))   score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const pct    = (score / 5) * 100;
    const colors = ['#EF4444','#F97316','#EAB308','#22C55E','#7C3AED'];
    const color  = score > 0 ? colors[Math.min(score - 1, 4)] : '#EF4444';

    pwStrengthFill.style.width      = pct + '%';
    pwStrengthFill.style.background = color;
});


/* ──────────────────────────────────────
   LOADER AU SUBMIT
──────────────────────────────────────── */
const loginForm = document.getElementById('loginForm');
const submitBtn = document.getElementById('submitBtn');

loginForm.addEventListener('submit', e => {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        e.preventDefault();
        return;
    }

    submitBtn.classList.add('is-loading');
    submitBtn.disabled = true;
});

/* ──────────────────────────────────────
   FOCUS ICON COLOR (field-icon-left)
──────────────────────────────────────── */
document.querySelectorAll('.field-input').forEach(input => {
    const wrap = input.closest('.field-wrap');
    const icon = wrap?.querySelector('.field-icon-left svg');
    if (!icon) return;
    input.addEventListener('focus',  () => icon.style.color = '#7C3AED');
    input.addEventListener('blur',   () => icon.style.color = '');
});
</script>

</body>
</html>
