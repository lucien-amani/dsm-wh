<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo strip_tags($page_description ?? 'Site officiel de Samy Magadju et de la Dynamique Samy Magadju (DSM/Wema ni Hakiba). Engagés pour le développement intégral du Sud-Kivu, Bukavu.'); ?>">
    <meta name="keywords" content="Samy magadju, Ir Samy Magadju, Dsm, dsm-wh, dynamique samy magadju, wema ni hakiba, Sud-Kivu, Bukavu, Magadju Samy, Ir Iragi magadju samy, Député Samy Magadju, RDC, développement Sud-Kivu">
    <meta name="author" content="Dynamique Samy Magadju/Wema ni Hakiba">
    <title><?php echo $page_title ?? 'Samy Magadju - Dynamique Samy Magadju/Wema ni Hakiba'; ?></title>
    
    <!-- SEO Avancé -->
    <link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    
    <!-- Données Structurées JSON-LD (Organisation & Personne) -->
    <script type="application/ld+json">
    [
      {
        "@context": "https://schema.org",
        "@type": "NGO",
        "@id": "<?php echo SITE_URL; ?>/#organization",
        "name": "Dynamique Samy Magadju (DSM/Wema ni Hakiba)",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg",
        "description": "ASBL engagée pour le développement intégral, les énergies renouvelables et le bien-être social au Sud-Kivu.",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Bukavu",
          "addressRegion": "Sud-Kivu",
          "addressCountry": "RDC"
        },
        "sameAs": [
          "https://facebook.com/samymagadju",
          "https://twitter.com/samymagadju"
        ]
      },
      {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "Samy Magadju",
        "jobTitle": "Ingénieur & Député",
        "description": "Leader de la Dynamique Samy Magadju (DSM), engagé pour le développement du Sud-Kivu.",
        "image": "<?php echo SITE_URL; ?>/assets/images/profil.jpg",
        "url": "<?php echo SITE_URL; ?>",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Bukavu",
          "addressRegion": "Sud-Kivu",
          "addressCountry": "RDC"
        },
        "knowsAbout": ["Génie Civil", "Développement Rural", "Politique"],
        "honorificPrefix": "Ir"
      }
    ]
    </script>

    <?php if (isset($is_article) && $is_article): ?>
    <!-- Données Structurées pour Article -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "headline": "<?php echo addslashes($article_title); ?>",
      "image": ["<?php echo $page_image ?? (SITE_URL . '/assets/logo/logo-dsm.jpg'); ?>"],
      "datePublished": "<?php echo $article_date; ?>",
      "author": {
        "@type": "Person",
        "name": "<?php echo $article_author ?? 'Rédaction DSM'; ?>"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Dynamique Samy Magadju",
        "logo": {
          "@type": "ImageObject",
          "url": "<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg"
        }
      }
    }
    </script>
    <?php endif; ?>

    <!-- Meta Balises Open Graph / Réseaux Sociaux (WhatsApp, Facebook, LinkedIn) -->
    <meta property="og:type" content="<?php echo isset($is_article) ? 'article' : 'website'; ?>">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo strip_tags($page_title ?? 'Samy Magadju - Dynamique Samy Magadju/Wema ni Hakiba'); ?>">
    <meta property="og:description" content="<?php echo strip_tags($page_description ?? 'Site officiel de Samy Magadju et de la DSM. Ensemble pour le développement du Sud-Kivu.'); ?>">
    <meta property="og:site_name" content="Dynamique Samy Magadju/Wema ni Hakiba ASBL">
    
    <!-- Image de partage optimisée -->
    <?php 
        $og_image = $page_image ?? (SITE_URL . '/assets/logo/logo-dsm.jpg');
        // WhatsApp/FB préfèrent les URLs absolues sans caractères spéciaux complexes
    ?>
    <meta property="og:image" content="<?php echo $og_image; ?>">
    <meta property="og:image:secure_url" content="<?php echo $og_image; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:alt" content="<?php echo strip_tags($page_title ?? 'Samy Magadju'); ?>">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo strip_tags($page_title ?? 'Samy Magadju - Dynamique Samy Magadju/Wema ni Hakiba'); ?>">
    <meta name="twitter:description" content="<?php echo strip_tags($page_description ?? 'Site officiel de Samy Magadju et de la DSM. Ensemble pour le développement du Sud-Kivu.'); ?>">
    <meta name="twitter:image" content="<?php echo $og_image; ?>">
    <meta name="twitter:image:alt" content="<?php echo strip_tags($page_title ?? 'Samy Magadju'); ?>">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css?v=<?php echo file_exists(__DIR__.'/../dist/output.css') ? filemtime(__DIR__.'/../dist/output.css') : time(); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
        /* Couleur de synthèse (Sélection) - Mode Clair */
        ::selection {
            background-color: rgba(185, 28, 28, 0.2); /* Red-700 soft */
            color: #b91c1c;
        }
        /* Couleur de synthèse (Sélection) - Mode Sombre */
        .dark ::selection {
            background-color: rgba(251, 191, 36, 0.3); /* Amber-400 vivid */
            color: #fbbf24;
        }
    </style>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">

    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.json">
    <meta name="theme-color" content="#10b981">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo SITE_URL; ?>/sw.js')
                    .then(reg => {
                        console.log('SW Registered!', reg);
                        // Request push permission
                        if ('Notification' in window && Notification.permission !== 'denied') {
                            Notification.requestPermission().then(permission => {
                                if (permission === 'granted') {
                                    subscribeUserToPush(reg);
                                }
                            });
                        }
                    })
                    .catch(err => console.log('SW Reg Error:', err));
            });

            function subscribeUserToPush(reg) {
                reg.pushManager.getSubscription().then(sub => {
                    if (sub) return sub;
                    
                    // Note: En production, remplacez par votre clé VAPID publique
                    const applicationServerKey = 'BDO6_K_YmO7r2S8W6zG0S-Z3Y7uN8_E2_X6_U7_M_P_Y_V_A_P_I_D_K_E_Y_P_L_A_C_E_H_O_L_D_E_R';
                    
                    return reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: applicationServerKey
                    });
                }).then(sub => {
                    fetch('<?php echo SITE_URL; ?>/api/subscribe.php', {
                        method: 'POST',
                        body: JSON.stringify(sub),
                        headers: { 'Content-Type': 'application/json' }
                    });
                });
            }

            // PWA Installation Logic
            let deferredPrompt;
            const installBtn = document.getElementById('pwa-install-btn');

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67 and earlier from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later.
                deferredPrompt = e;
                // Update UI notify the user they can install the PWA
                if (installBtn) {
                    installBtn.classList.remove('hidden');
                    installBtn.classList.add('flex');
                }
            });

            if (installBtn) {
                installBtn.addEventListener('click', (e) => {
                    // Hide the app provided install promotion
                    installBtn.classList.add('hidden');
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
            }

            window.addEventListener('appinstalled', (evt) => {
                console.log('PWA was installed');
                if (installBtn) installBtn.classList.add('hidden');
            });
        }
    </script>

    <!-- Dark Mode Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-slate-900 dark:text-gray-100 transition-colors duration-300 pb-32 md:pb-0">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-slate-900 shadow-md sticky top-0 z-50 border-b border-gray-100 dark:border-white/5 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="<?php echo $router->generate('home'); ?>" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" alt="Logo DSM" class="h-12 w-12 sm:h-14 sm:w-14 rounded-full object-cover p-0.5 border-2 border-[rgb(var(--color-primary))]">
                    <div>
                        <div class="text-lg sm:text-xl font-bold text-[rgb(var(--color-dark))] dark:text-white transition-colors leading-tight">Dynamique Samy Magadju/Wema ni Hakiba ASBL</div>
                        <div class="hidden sm:block text-xs text-gray-600 dark:text-gray-400 transition-colors">Ensemble pour le développement intégral</div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo $router->generate('home'); ?>"
                        class="nav-link <?php echo ($current_page ?? '') === 'home' ? 'active' : ''; ?>">
                        Accueil
                    </a>
                    <a href="<?php echo $router->generate('about'); ?>"
                        class="nav-link <?php echo ($current_page ?? '') === 'about' ? 'active' : ''; ?>">
                        À propos
                    </a>
                    <a href="<?php echo $router->generate('news_list'); ?>"
                        class="nav-link <?php echo ($current_page ?? '') === 'news' ? 'active' : ''; ?>">
                        Actualités
                    </a>
                    <a href="<?php echo $router->generate('projects_list'); ?>"
                        class="nav-link <?php echo ($current_page ?? '') === 'projects' ? 'active' : ''; ?>">
                        Projets
                    </a>
                    <a href="<?php echo $router->generate('contact'); ?>"
                        class="btn-primary">
                        Contact
                    </a>

                    <!-- PWA Install Button (Desktop) -->
                    <button id="pwa-install-btn" class="hidden items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Installer
                    </button>
                    
                    <!-- Dark Mode Toggle (Desktop) -->
                    <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 transition-colors" aria-label="Toggle Dark Mode">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>

                    <!-- Login Icon (Desktop) -->
                    <a href="<?php echo SITE_URL; ?>/admin" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 transition-colors" title="Connexion Administration">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Navigation (Classic Bottom Bar) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 w-full h-16 bg-white dark:bg-slate-950 z-[100] border-t border-slate-200 dark:border-white/5 flex items-center justify-around transition-colors">
        <!-- Overlay Backdrop -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-slate-900/60 z-[100] opacity-0 pointer-events-none transition-opacity duration-300"></div>

        <!-- Actions Menu (Solid Panel) -->
        <div id="mobile-more-menu" class="fixed bottom-16 left-0 right-0 w-full bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-white/10 z-[101] p-4 flex flex-col gap-2 translate-y-full opacity-0 pointer-events-none transition-all duration-300">
            <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest px-2 mb-1">Actions</div>
            <a href="<?php echo $router->generate('contact'); ?>" class="flex items-center gap-4 p-4 bg-emerald-600 text-white active:bg-emerald-700 transition-colors">
                <div class="w-10 h-10 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-sm font-bold uppercase tracking-widest">Contactez-nous</span>
            </a>
            <div class="grid grid-cols-2 gap-px bg-slate-200 dark:bg-white/5">
                <button id="mobile-theme-toggle-v2" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 active:bg-slate-100 dark:active:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    <span class="text-[10px] font-bold uppercase">Thème</span>
                </button>
                <a href="<?php echo SITE_URL; ?>/admin" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 active:bg-slate-100 dark:active:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                    <span class="text-[10px] font-bold uppercase">Admin</span>
                </a>
            </div>
        </div>

        <!-- Home -->
        <a href="<?php echo $router->generate('home'); ?>" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
            <svg class="w-5 h-5 transition-colors <?php echo ($current_page ?? '') === 'home' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-400 group-active:text-gray-900 dark:group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo ($current_page ?? '') === 'home' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-500 dark:text-gray-400'; ?>">Accueil</span>
        </a>

        <!-- News -->
        <a href="<?php echo $router->generate('news_list'); ?>" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
            <svg class="w-5 h-5 transition-colors <?php echo ($current_page ?? '') === 'news' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-400 group-active:text-gray-900 dark:group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo ($current_page ?? '') === 'news' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-500 dark:text-gray-400'; ?>">Actualités</span>
        </a>

        <!-- Central Action (Plus) -->
        <button id="mobile-more-trigger" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
            <div class="w-10 h-10 bg-emerald-600 rounded-none flex items-center justify-center text-white active:bg-emerald-700 transition-colors">
                <svg id="trigger-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-tighter text-gray-500 dark:text-gray-400">Plus</span>
        </button>

        <!-- Projects -->
        <a href="<?php echo $router->generate('projects_list'); ?>" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
            <svg class="w-5 h-5 transition-colors <?php echo ($current_page ?? '') === 'projects' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-400 group-active:text-gray-900 dark:group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo ($current_page ?? '') === 'projects' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-500 dark:text-gray-400'; ?>">Projets</span>
        </a>

        <!-- About -->
        <a href="<?php echo $router->generate('about'); ?>" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
            <svg class="w-5 h-5 transition-colors <?php echo ($current_page ?? '') === 'about' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-400 group-active:text-gray-900 dark:group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo ($current_page ?? '') === 'about' ? 'text-emerald-600 dark:text-emerald-500' : 'text-gray-500 dark:text-gray-400'; ?>">À Propos</span>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark Mode Toggle Logic
            const themeToggleBtns = document.querySelectorAll('#theme-toggle, #mobile-theme-toggle-v2');
            themeToggleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                });
            });

            // Mobile "More" Menu Toggle (Raafig Style)
            const moreTrigger = document.getElementById('mobile-more-trigger');
            const moreMenu = document.getElementById('mobile-more-menu');
            const menuOverlay = document.getElementById('mobile-menu-overlay');
            const triggerIcon = document.getElementById('trigger-icon');

            if (moreTrigger && moreMenu) {
                let isOpen = false;

                const toggleMenu = (open) => {
                    isOpen = open;
                    if (isOpen) {
                        moreMenu.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                        menuOverlay.classList.remove('opacity-0', 'pointer-events-none');
                        triggerIcon.style.transform = 'rotate(45deg)';
                        document.body.style.overflow = 'hidden';
                    } else {
                        moreMenu.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                        menuOverlay.classList.add('opacity-0', 'pointer-events-none');
                        triggerIcon.style.transform = 'rotate(0deg)';
                        document.body.style.overflow = '';
                    }
                };

                moreTrigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleMenu(!isOpen);
                });

                // Close when clicking overlay or outside
                menuOverlay.addEventListener('click', () => toggleMenu(false));
                document.addEventListener('click', function(e) {
                    if (!moreMenu.contains(e.target) && isOpen) {
                        toggleMenu(false);
                    }
                });
            }
        });
    </script>
</body>