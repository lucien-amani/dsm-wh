<?php
$current_page = '404';
$page_title = 'Page non trouvée - Dynamique Samy Magadju/Wema ni Hakiba';
$page_description = 'La page que vous recherchez n\'existe pas ou a été déplacée.';
include 'includes/header.php';
?>

<main class="flex-1 flex items-center justify-center py-24 px-4 bg-gray-50 dark:bg-slate-900 transition-colors">
    <div class="max-w-xl w-full text-center space-y-8 animate-fade-in">
        <!-- Premium Illustration -->
        <div class="relative w-48 h-48 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-tr from-[rgb(var(--color-primary))] to-rose-500 rounded-full blur-3xl opacity-20 animate-pulse"></div>
            <div class="w-32 h-32 bg-white dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-white/5 flex items-center justify-center shadow-2xl relative transform hover:rotate-3 transition-transform duration-300">
                <span class="text-5xl font-black text-rose-600 dark:text-rose-500 font-serif">404</span>
            </div>
        </div>
        
        <div class="space-y-4">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tighter font-serif">Page Introuvable</h1>
            <p class="text-slate-500 dark:text-gray-400 font-medium leading-relaxed max-w-md mx-auto">
                La page que vous recherchez n'existe pas, a été déplacée ou est temporairement indisponible.
            </p>
        </div>
        
        <div class="pt-4 flex flex-wrap gap-4 justify-center">
            <a href="<?php echo SITE_URL; ?>/" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Retour à l'accueil
            </a>
            <a href="<?php echo SITE_URL; ?>/contact" class="px-8 py-4 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Nous contacter
            </a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
