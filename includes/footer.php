    <!-- Footer (Dark, no gradients) -->
    <footer class="bg-slate-900 relative overflow-hidden mt-auto">
        <!-- Toast Container for public side -->
        <div id="toast-container" class="fixed top-6 right-6 z-[300] flex flex-col gap-3 pointer-events-none"></div>

        <!-- Top separator line -->
        <div class="absolute top-0 left-0 right-0 h-px bg-white/5"></div>

        <!-- ---- Main footer content ---- -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-10 relative z-10">

            <!-- Top block: Brand -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 mb-14 pb-8 border-b border-white/5">
                <!-- Brand identity -->
                <div class="max-w-md">
                    <div class="flex items-center gap-3 mb-3 group">
                        <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" alt="Logo DSM" class="w-10 h-10 rounded-xl object-cover border border-[rgb(var(--color-primary))]/30 flex-shrink-0 p-0.5 bg-white/5">
                        <div>
                            <p class="text-white font-black text-xl leading-tight tracking-wide uppercase">DSM / <span class="text-[rgb(var(--color-primary))]">Wema ni Hakiba ASBL</span></p>
                            <p class="text-slate-500 text-[10px] uppercase tracking-[0.25em] mt-0.5 font-semibold">Association Sans But Lucratif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4-column grid (Space optimized for mobile) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-16 md:gap-y-12">

                <!-- Col 1: Newsletter -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-6 py-1 border-b-2 border-[rgb(var(--color-primary))]/20 inline-block">
                        Newsletter
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        Recevez nos actualités, projets et événements directement dans votre boîte mail.
                    </p>
                    <form id="newsletter-form" class="space-y-2">
                        <input type="email" id="newsletter-email" name="email" required
                               placeholder="votre@email.com"
                               class="w-full bg-white/5 border border-white/10 text-white placeholder-slate-500 px-4 py-2.5 text-sm rounded-xl focus:outline-none focus:border-[rgb(var(--color-primary))] focus:bg-white/10 transition-all">
                        <button type="submit"
                                class="w-full bg-[rgb(var(--color-primary))] text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-[rgb(var(--color-primary-dark))] transition-all active:scale-95 flex items-center justify-center gap-2 group">
                            <span>S'abonner</span>
                            <svg id="newsletter-btn-icon" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                            <svg id="newsletter-btn-loading" class="w-4 h-4 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>

                    <!-- Socials (Optimized) -->
                    <div class="mt-8">
                        <p class="text-slate-500 text-[10px] uppercase tracking-[0.2em] mb-4 font-bold">Nous suivre</p>
                        <div class="flex items-center gap-3">
                            <?php
                            $socials = [
                                [
                                    'name' => 'Facebook',
                                    'aria' => 'Facebook',
                                    'href' => '#',
                                    'color' => 'hover:bg-[#1877F2] hover:border-[#1877F2]',
                                    'svg' => '<path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>'
                                ],
                                [
                                    'name' => 'Twitter',
                                    'aria' => 'Twitter / X',
                                    'href' => '#',
                                    'color' => 'hover:bg-[#1DA1F2] hover:border-[#1DA1F2]',
                                    'svg' => '<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>'
                                ],
                                [
                                    'name' => 'LinkedIn',
                                    'aria' => 'LinkedIn',
                                    'href' => '#',
                                    'color' => 'hover:bg-[#0A66C2] hover:border-[#0A66C2]',
                                    'svg' => '<path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>'
                                ],
                                [
                                    'name' => 'YouTube',
                                    'aria' => 'YouTube',
                                    'href' => '#',
                                    'color' => 'hover:bg-[#FF0000] hover:border-[#FF0000]',
                                    'svg' => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.073 0 12 0 12s0 3.927.501 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.377.55 9.377.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.927 24 12 24 12s0-3.927-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>'
                                ],
                                [
                                    'name' => 'Instagram',
                                    'aria' => 'Instagram',
                                    'href' => '#',
                                    'color' => 'hover:bg-gradient-to-tr hover:from-[#FCAF45] hover:via-[#E1306C] hover:to-[#C13584] hover:border-transparent',
                                    'svg' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>'
                                ],
                            ];

                            foreach ($socials as $social):
                            ?>
                            <a href="<?php echo $social['href']; ?>" 
                               aria-label="<?php echo $social['aria']; ?>"
                               title="<?php echo $social['name']; ?>"
                               class="w-10 h-10 rounded-xl border border-white/10 flex items-center justify-center text-slate-400 <?php echo $social['color']; ?> hover:text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-black/20 group relative overflow-hidden">
                                <span class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                    <?php echo $social['svg']; ?>
                                </svg>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-6 py-1 border-b-2 border-[rgb(var(--color-primary))]/20 inline-block">
                        Navigation
                    </h3>
                    <ul class="space-y-3">
                        <?php
                        $nav_links = [
                            ['route' => 'home', 'label' => 'Accueil', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'about', 'label' => 'À Propos', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                            ['route' => 'news_list', 'label' => 'Actualités & Blog', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                            ['route' => 'projects_list', 'label' => 'Projets réalisés', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['route' => 'contact', 'label' => 'Contact', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ];
                        foreach ($nav_links as $link): ?>
                        <li>
                            <a href="<?php echo $router->generate($link['route']); ?>"
                               class="group flex items-center gap-2.5 text-slate-400 hover:text-white transition-colors text-sm">
                                <svg class="w-3.5 h-3.5 text-[rgb(var(--color-primary))]/50 group-hover:text-[rgb(var(--color-primary))] transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $link['icon']; ?>"/>
                                </svg>
                                <span class="group-hover:translate-x-0.5 transition-transform duration-200 inline-block"><?php echo $link['label']; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Col 3: Nos Engagements -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-6 py-1 border-b-2 border-[rgb(var(--color-primary))]/20 inline-block">
                        Nos Engagements
                    </h3>
                    <ul class="space-y-3">
                        <li class="group flex items-center gap-2.5 text-slate-400 hover:text-red-400 transition-colors text-sm cursor-default">
                            <span class="w-2 h-2 rounded-full bg-red-500/60 flex-shrink-0 group-hover:scale-125 transition-transform"></span>
                            Protéger les droits des vulnérables
                        </li>
                        <li class="group flex items-center gap-2.5 text-slate-400 hover:text-amber-400 transition-colors text-sm cursor-default">
                            <span class="w-2 h-2 rounded-full bg-amber-500/60 flex-shrink-0 group-hover:scale-125 transition-transform"></span>
                            Lutter contre l'insécurité alimentaire
                        </li>
                        <li class="group flex items-center gap-2.5 text-slate-400 hover:text-blue-400 transition-colors text-sm cursor-default">
                            <span class="w-2 h-2 rounded-full bg-blue-500/60 flex-shrink-0 group-hover:scale-125 transition-transform"></span>
                            Former et éduquer la jeunesse
                        </li>
                        <li class="group flex items-center gap-2.5 text-slate-400 hover:text-teal-400 transition-colors text-sm cursor-default">
                            <span class="w-2 h-2 rounded-full bg-teal-500/60 flex-shrink-0 group-hover:scale-125 transition-transform"></span>
                            Prévention VIH/SIDA & santé reproductive
                        </li>
                        <li class="group flex items-center gap-2.5 text-slate-400 hover:text-green-400 transition-colors text-sm cursor-default">
                            <span class="w-2 h-2 rounded-full bg-green-600/60 flex-shrink-0 group-hover:scale-125 transition-transform"></span>
                            Préserver l'environnement et les ressources
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Contact -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-6 py-1 border-b-2 border-[rgb(var(--color-primary))]/20 inline-block">
                        Contact
                    </h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="mailto:magadjusamybonheur@gmail.com"
                               class="group flex items-start gap-3 text-slate-400 hover:text-white transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/8 flex items-center justify-center flex-shrink-0 group-hover:bg-[rgb(var(--color-primary))]/20 group-hover:border-[rgb(var(--color-primary))]/30 transition-all">
                                    <svg class="w-3.5 h-3.5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm break-all">magadjusamybonheur@gmail.com</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:+243993859330"
                               class="group flex items-center gap-3 text-slate-400 hover:text-white transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/8 flex items-center justify-center flex-shrink-0 group-hover:bg-[rgb(var(--color-primary))]/20 group-hover:border-[rgb(var(--color-primary))]/30 transition-all">
                                    <svg class="w-3.5 h-3.5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="text-sm">+243 993 859 330</span>
                            </a>
                        </li>
                        <li>
                            <div class="group flex items-center gap-3 text-slate-400">
                                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/8 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm">Bukavu, Sud-Kivu — RDC</span>
                            </div>
                        </li>
                    </ul>
                </div>

            </div><!-- /grid -->

            <!-- ---- Bottom bar (Cleaned) ---- -->
            <div class="mt-14 pt-6 border-t border-white/5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                    <!-- Copyright -->
                    <div class="flex items-center gap-2 text-slate-500 text-xs font-medium">
                        <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" alt="Logo DSM" class="w-5 h-5 rounded-full object-cover border border-white/10">
                        &copy; <?php echo date('Y'); ?>
                        <span class="text-white font-bold">DSM / Wema ni Hakiba ASBL</span>
                        — Tous droits réservés.
                    </div>

                    <!-- Legal links -->
                    <div class="flex items-center gap-1 flex-wrap justify-center">
                        <?php
                        $legal = [
                            ['route' => 'privacy', 'label' => 'Confidentialité'],
                            ['route' => 'cookies', 'label' => 'Cookies'],
                            ['route' => 'mentions-legales', 'label' => 'Mentions légales'],
                        ];
                        foreach ($legal as $i => $item): ?>
                        <?php if ($i > 0): ?><span class="text-slate-700 text-xs">·</span><?php endif; ?>
                        <a href="<?php echo $router->generate($item['route']); ?>"
                           class="text-slate-500 text-xs hover:text-[rgb(var(--color-primary))] transition-colors px-1">
                            <?php echo $item['label']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </div><!-- /max-w -->
    </footer>



    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- Newsletter Handler ---
            const nForm = document.getElementById('newsletter-form');
            if (nForm) {
                nForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const input = document.getElementById('newsletter-email');
                    const icon = document.getElementById('newsletter-btn-icon');
                    const loading = document.getElementById('newsletter-btn-loading');
                    
                    if (icon) icon.classList.add('hidden');
                    if (loading) loading.classList.remove('hidden');
                    nForm.classList.add('pointer-events-none', 'opacity-50');

                    try {
                        const response = await fetch('<?php echo SITE_URL; ?>/api/newsletter.php', { 
                            method: 'POST', 
                            body: new FormData(nForm) 
                        });
                        const res = await response.json();
                        
                        if (loading) loading.classList.add('hidden');
                        if (icon) icon.classList.remove('hidden');
                        nForm.classList.remove('pointer-events-none', 'opacity-50');

                        if (res.success) {
                            showToast(res.message, 'success');
                            input.value = '';
                        } else {
                            showToast(res.message, 'error');
                        }
                    } catch (err) {
                        if (loading) loading.classList.add('hidden');
                        if (icon) icon.classList.remove('hidden');
                        nForm.classList.remove('pointer-events-none', 'opacity-50');
                        showToast("Une erreur est survenue.", 'error');
                    }
                });
            }
        });
    </script>
    <!-- Cookie Consent Banner -->
    <div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[500] p-4 transform translate-y-full transition-transform duration-500 ease-in-out">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[rgb(var(--color-primary))]" fill="currentColor" viewBox="0 0 24 24"><path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A11.59 11.59 0 0 0 12 2c-5.856 0-10.74 4.312-11.498 9.936a1 1 0 0 0 .562 1.036c3.232 1.457 5.61 4.417 6.342 7.848.1.474.52.812 1.004.812h.016a5.5 5.5 0 0 1 5.37-4.14 5.503 5.503 0 0 1 5.484 5.068 1.003 1.003 0 0 0 1.258.872A11.593 11.593 0 0 0 22 12c0-.323-.016-.643-.046-.958a1.004 1.004 0 0 0-.356-.978zM4.5 11a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zM10 8.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm2.5 7.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm4 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1.5-6.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/></svg>
                        Respect de votre vie privée
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Nous utilisons des cookies pour optimiser votre expérience, analyser le trafic et personnaliser les contenus. 
                        Vous pouvez accepter tous les cookies, les refuser ou personnaliser vos préférences.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <button onclick="acceptAllCookies()" class="flex-1 md:flex-none px-6 py-3 bg-[rgb(var(--color-primary))] text-white rounded-xl text-sm font-bold hover:bg-[rgb(var(--color-primary-dark))] transition-all whitespace-nowrap">
                        Tout accepter
                    </button>
                    <button onclick="refuseAllCookies()" class="flex-1 md:flex-none px-6 py-3 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold hover:bg-gray-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap">
                        Tout refuser
                    </button>
                    <button onclick="toggleCookieConfig()" class="w-full md:w-auto px-6 py-3 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                        Paramétrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cookie Config Modal -->
    <div id="cookie-modal" class="fixed inset-0 z-[600] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl border border-white/10 overflow-hidden animate-slide-up">
            <div class="p-8 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">Préférences Cookies</h3>
                <button onclick="toggleCookieConfig()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
                </button>
            </div>
            <div class="p-8 space-y-6 max-h-[60vh] overflow-y-auto">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 dark:text-white mb-1">Essentiels</p>
                        <p class="text-xs text-gray-500">Nécessaires au bon fonctionnement du site. Ne peuvent pas être désactivés.</p>
                    </div>
                    <div class="w-12 h-6 bg-[rgb(var(--color-primary))] rounded-full relative opacity-50 cursor-not-allowed">
                        <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full"></div>
                    </div>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 dark:text-white mb-1">Analytiques</p>
                        <p class="text-xs text-gray-500">Nous permettent de comprendre comment vous utilisez le site pour l'améliorer.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="cookie-analytics" class="sr-only peer" checked>
                        <div class="w-12 h-6 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[rgb(var(--color-primary))]"></div>
                    </label>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 dark:text-white mb-1">Marketing</p>
                        <p class="text-xs text-gray-500">Utilisés pour vous proposer des contenus et publicités adaptés à vos intérêts.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="cookie-marketing" class="sr-only peer">
                        <div class="w-12 h-6 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[rgb(var(--color-primary))]"></div>
                    </label>
                </div>
            </div>
            <div class="p-8 bg-gray-50 dark:bg-slate-800/50 flex gap-4">
                <button onclick="saveCookiePreferences()" class="flex-1 bg-[rgb(var(--color-primary))] text-white py-4 rounded-2xl font-bold hover:bg-[rgb(var(--color-primary-dark))] transition-all active:scale-95">
                    Enregistrer mes choix
                </button>
            </div>
        </div>
    </div>

    <script>
        function acceptAllCookies() {
            localStorage.setItem('cookieConsent', 'all');
            hideCookieBanner();
        }

        function refuseAllCookies() {
            localStorage.setItem('cookieConsent', 'none');
            hideCookieBanner();
        }

        function saveCookiePreferences() {
            const analytics = document.getElementById('cookie-analytics').checked;
            const marketing = document.getElementById('cookie-marketing').checked;
            localStorage.setItem('cookieConsent', JSON.stringify({ analytics, marketing }));
            toggleCookieConfig();
            hideCookieBanner();
        }

        function hideCookieBanner() {
            const banner = document.getElementById('cookie-banner');
            if (banner) banner.classList.add('translate-y-full');
        }

        function toggleCookieConfig() {
            const modal = document.getElementById('cookie-modal');
            if (modal) {
                if (modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        }

        // Afficher la bannière si pas de consentement
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('cookieConsent')) {
                setTimeout(() => {
                    const banner = document.getElementById('cookie-banner');
                    if (banner) banner.classList.remove('translate-y-full');
                }, 1000);
            }
        });
    </script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
