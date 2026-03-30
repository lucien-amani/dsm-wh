<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo strip_tags($page_description ?? 'Dynamique Samy Magadju/Wema ni Hakiba - Ensemble pour le développement intégral du Sud-Kivu.'); ?>">
    <meta name="keywords" content="Samy Magadju, Wema ni Hakiba, Bukavu, Bagira, développement RDC, ASBL, politique Bukavu">
    <meta name="author" content="Dynamique Samy Magadju/Wema ni Hakiba">
    <title><?php echo $page_title ?? 'Dynamique Samy Magadju/Wema ni Hakiba - Ensemble pour le développement intégral'; ?></title>
    
    <!-- SEO Avancé -->
    <link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    
    <!-- Données Structurées JSON-LD (Organisation) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NGO",
      "name": "Dynamique Samy Magadju/Wema ni Hakiba",
      "url": "<?php echo SITE_URL; ?>",
      "logo": "<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg",
      "description": "ASBL engagée pour le développement intégral, les énergies renouvelables et le bien-être social au Sud-Kivu.",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Bukavu",
        "addressRegion": "Sud-Kivu",
        "addressCountry": "RDC"
      }
    }
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
      }
    }
    </script>
    <?php endif; ?>

    <!-- Meta Balises Open Graph / Réseaux Sociaux -->
    <meta property="og:type" content="<?php echo isset($is_article) ? 'article' : 'website'; ?>">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo strip_tags($page_title ?? 'Dynamique Samy Magadju/Wema ni Hakiba'); ?>">
    <meta property="og:description" content="<?php echo strip_tags($page_description ?? 'Dynamique Samy Magadju/Wema ni Hakiba - Ensemble pour le développement intégral.'); ?>">
    <meta property="og:image" content="<?php echo $page_image ?? SITE_URL . '/assets/logo/logo-dsm.jpg'; ?>">
    <meta property="og:site_name" content="Dynamique Samy Magadju/Wema ni Hakiba ASBL">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo strip_tags($page_title ?? 'Dynamique Samy Magadju/Wema ni Hakiba'); ?>">
    <meta name="twitter:description" content="<?php echo strip_tags($page_description ?? 'Dynamique Samy Magadju/Wema ni Hakiba - Ensemble pour le développement intégral.'); ?>">
    <meta name="twitter:image" content="<?php echo $page_image ?? SITE_URL . '/assets/logo/logo-dsm.jpg'; ?>">

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

    <!-- Dark Mode Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-slate-900 dark:text-gray-100 transition-colors duration-300">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-slate-900 shadow-md sticky top-0 z-50 border-b border-gray-100 dark:border-white/5 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>/" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" alt="Logo DSM" class="h-12 w-12 sm:h-14 sm:w-14 rounded-full object-cover p-0.5 border-2 border-[rgb(var(--color-primary))]">
                    <div>
                        <div class="text-lg sm:text-xl font-bold text-[rgb(var(--color-dark))] dark:text-white transition-colors leading-tight">Dynamique Samy Magadju/Wema ni Hakiba ASBL</div>
                        <div class="hidden sm:block text-xs text-gray-600 dark:text-gray-400 transition-colors">Ensemble pour le développement intégral</div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo SITE_URL; ?>/"
                        class="nav-link <?php echo ($current_page ?? '') === 'home' ? 'active' : ''; ?>">
                        Accueil
                    </a>
                    <a href="<?php echo SITE_URL; ?>/a-propos"
                        class="nav-link <?php echo ($current_page ?? '') === 'about' ? 'active' : ''; ?>">
                        À propos
                    </a>
                    <a href="<?php echo SITE_URL; ?>/actualites"
                        class="nav-link <?php echo ($current_page ?? '') === 'news' ? 'active' : ''; ?>">
                        Actualités
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projets"
                        class="nav-link <?php echo ($current_page ?? '') === 'projects' ? 'active' : ''; ?>">
                        Projets
                    </a>
                    <a href="<?php echo SITE_URL; ?>/contact"
                        class="btn-primary">
                        Contact
                    </a>
                    
                    <!-- Dark Mode Toggle (Desktop) -->
                    <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 transition-colors" aria-label="Toggle Dark Mode">
                        <!-- Sun Icon (Hidden in Dark Mode) -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon Icon (Hidden in Light Mode) -->
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden flex flex-col items-end justify-center gap-[5px] p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 shadow-sm transition-all group" style="width: 48px; height: 48px;" aria-label="Menu">
                    <div class="h-[2px] bg-gray-900 dark:bg-white rounded-full transition-all duration-300 group-hover:w-full" style="width: 100%;"></div>
                    <div class="h-[2px] bg-gray-900 dark:bg-white rounded-full transition-all duration-300 group-hover:w-full" style="width: 65%;"></div>
                    <div class="h-[2px] bg-gray-900 dark:bg-white rounded-full transition-all duration-300 group-hover:w-full" style="width: 40%;"></div>
                </button>
            </div>
        </div>

    <!-- Mobile Menu Container (Off-canvas) -->
    <div id="mobile-menu" class="fixed inset-0 z-[100] hidden overflow-hidden">
        <!-- Backdrop -->
        <div id="mobile-menu-backdrop" class="absolute inset-0 bg-slate-900/50 opacity-0 transition-opacity duration-300 ease-in-out pointer-events-none"></div>
        
        <!-- Menu Panel -->
        <div id="mobile-menu-panel" class="absolute top-0 right-0 w-[300px] h-full bg-white dark:bg-slate-900 shadow-2xl translate-x-full transition-transform duration-300 ease-in-out pointer-events-auto">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-white/5">
                    <span class="text-xl font-bold text-[rgb(var(--color-dark))] dark:text-white">Menu</span>
                    <button id="mobile-menu-close" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Links -->
                <div class="flex-1 overflow-y-auto py-6 px-6 space-y-4">
                    <a href="<?php echo SITE_URL; ?>/"
                        class="block py-2 text-lg font-medium text-gray-700 dark:text-gray-300 hover:text-[rgb(var(--color-primary))] transition-colors">
                        Accueil
                    </a>
                    <a href="<?php echo SITE_URL; ?>/a-propos"
                        class="block py-2 text-lg font-medium text-gray-700 dark:text-gray-300 hover:text-[rgb(var(--color-primary))] transition-colors">
                        À propos
                    </a>
                    <a href="<?php echo SITE_URL; ?>/actualites"
                        class="block py-2 text-lg font-medium text-gray-700 dark:text-gray-300 hover:text-[rgb(var(--color-primary))] transition-colors">
                        Actualités
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projets"
                        class="block py-2 text-lg font-medium text-gray-700 dark:text-gray-300 hover:text-[rgb(var(--color-primary))] transition-colors">
                        Projets
                    </a>
                    <a href="<?php echo SITE_URL; ?>/contact"
                        class="block py-2 text-lg font-bold text-[rgb(var(--color-primary))]">
                        Contact
                    </a>
                    
                    <div class="pt-6 border-t border-gray-100 dark:border-white/5 space-y-4">
                        <a href="<?php echo SITE_URL; ?>/admin/login.php" class="flex items-center gap-3 py-2 text-gray-700 dark:text-gray-300 font-medium hover:text-[rgb(var(--color-primary))] transition-colors w-full">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Connexion</span>
                        </a>

                        <button id="mobile-theme-toggle" class="flex items-center justify-between w-full py-2 text-gray-700 dark:text-gray-300 font-medium transition-colors">
                            <span>Thème</span>
                            <div class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800">
                                <!-- Sun Icon -->
                                <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <!-- Moon Icon -->
                                <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t border-gray-100 dark:border-white/5 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dynamique Samy Magadju/Wema ni Hakiba &copy; <?php echo date('Y'); ?></p>
                </div>
            </div>
        </div>
    </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu elements
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuPanel = document.getElementById('mobile-menu-panel');
            const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');

            if (mobileMenuButton && mobileMenuPanel) {
                function openMobileMenu() {
                    mobileMenu.classList.remove('hidden');
                    // Force reflow
                    void mobileMenu.offsetWidth;
                    mobileMenuBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                    mobileMenuBackdrop.classList.add('opacity-100', 'pointer-events-auto');
                    mobileMenuPanel.classList.remove('translate-x-full');
                    document.body.classList.add('overflow-hidden');
                }

                function closeMobileMenu() {
                    mobileMenuBackdrop.classList.remove('opacity-100', 'pointer-events-auto');
                    mobileMenuBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    mobileMenuPanel.classList.add('translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                    
                    setTimeout(() => {
                        if (mobileMenuPanel.classList.contains('translate-x-full')) {
                            mobileMenu.classList.add('hidden');
                        }
                    }, 300);
                }

                mobileMenuButton.addEventListener('click', openMobileMenu);
                if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
                if (mobileMenuBackdrop) mobileMenuBackdrop.addEventListener('click', closeMobileMenu);

                mobileMenuPanel.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', closeMobileMenu);
                });
            }

            // Dark Mode Toggle Logic
            const themeToggleBtns = document.querySelectorAll('#theme-toggle, #mobile-theme-toggle');
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
        });
    </script>