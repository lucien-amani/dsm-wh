<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Technique - Dynamique Samy Magadju/Wema ni Hakiba</title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>/dist/output.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>/assets/logo/logo-dsm.jpg">
    
    <!-- Dark Mode Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full min-h-screen flex flex-col bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-gray-100 transition-colors duration-300">

    <!-- Header / Nav Minimaliste -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img src="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>/assets/logo/logo-dsm.jpg" alt="Logo DSM" class="h-10 w-10 rounded-full object-cover p-0.5 border border-emerald-500">
            <span class="text-sm font-black uppercase tracking-tighter text-slate-900 dark:text-white font-outfit">DSM <span class="text-emerald-600">ASBL</span></span>
        </div>
        
        <!-- Dark Mode Toggle -->
        <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 transition-colors" aria-label="Toggle Dark Mode">
            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center space-y-10 animate-fade-in">
            
            <!-- Illustration Animée -->
            <div class="relative w-48 h-48 mx-auto flex items-center justify-center">
                <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500 to-amber-500 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                <div class="w-32 h-32 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-gray-100 dark:border-white/5 flex items-center justify-center shadow-2xl relative">
                    <svg class="w-16 h-16 text-emerald-600 dark:text-emerald-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Textes -->
            <div class="space-y-4">
                <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white uppercase tracking-tighter font-serif">Maintenance Technique</h1>
                <p class="text-lg text-slate-500 dark:text-gray-400 font-medium max-w-xl mx-auto leading-relaxed">
                    Le site internet de la Dynamique Samy Magadju subit actuellement des opérations de maintenance. Nos serveurs seront de retour dans quelques instants.
                </p>
            </div>

            <!-- Diagnostics Accordion for admin -->
            <div class="max-w-md mx-auto bg-white dark:bg-slate-800/40 border border-gray-100 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm">
                <button onclick="toggleDiagnostics()" class="w-full px-6 py-4 flex items-center justify-between text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                    <span>Informations de connexion</span>
                    <svg id="chevron-icon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="diagnostics-panel" class="hidden px-6 pb-6 text-left border-t border-gray-100 dark:border-white/5 pt-4">
                    <p class="text-[11px] font-bold text-rose-500 uppercase tracking-widest mb-2 font-outfit">Erreur détectée :</p>
                    <p class="text-xs font-mono text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl overflow-x-auto border border-gray-100 dark:border-white/5">
                        PDOException: Connexion à la base de données momentanément indisponible.<br>
                        Veuillez vérifier les configurations dans le fichier de variables d'environnement (.env).
                    </p>
                </div>
            </div>

            <!-- Actions de secours -->
            <div class="flex flex-wrap gap-4 justify-center pt-2">
                <button onclick="window.location.reload()" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"/></svg>
                    Actualiser la page
                </button>
                <a href="mailto:contact@dsm-samy.com" class="px-8 py-4 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contacter le support
                </a>
            </div>

        </div>
    </main>

    <!-- Footer minimaliste -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-gray-200/50 dark:border-white/5 text-center text-xs text-gray-400">
        <p class="font-bold">&copy; <?php echo date('Y'); ?> Dynamique Samy Magadju/Wema ni Hakiba ASBL. Tous droits réservés.</p>
    </footer>

    <script>
        // Dark Mode Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            });
        }

        // Accordion toggle
        function toggleDiagnostics() {
            const panel = document.getElementById('diagnostics-panel');
            const icon = document.getElementById('chevron-icon');
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                panel.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>
