<header class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50 transition-all">
    <div class="flex items-center gap-4 min-w-0">
        <div class="min-w-0">
            <h1 class="text-lg lg:text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate leading-tight"><?php echo $header_title ?? 'Administration'; ?></h1>
            <p class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1 truncate"><?php echo $header_subtitle ?? 'Système DSM'; ?></p>
        </div>
    </div>

    <!-- Middle Actions (Page Specific) -->
    <?php if (isset($header_actions)): ?>
    <div class="hidden xl:flex items-center gap-2 mx-4">
        <?php echo $header_actions; ?>
    </div>
    <?php endif; ?>
    
    <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
        <div class="flex items-center gap-1.5 lg:gap-3 bg-slate-100/50 dark:bg-slate-800/50 p-1.5 rounded-2xl border border-slate-200/50 dark:border-slate-700/50">
            <!-- Theme Toggle -->
            <button id="theme-toggle-header" class="w-9 h-9 lg:w-11 lg:h-11 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-amber-500 transition-all shadow-sm" title="Changer le thème">
                <svg id="theme-toggle-dark-icon-header" class="hidden w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon-header" class="hidden w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
            </button>

            <!-- Visit Site -->
            <a href="<?php echo SITE_URL; ?>/" target="_blank" class="w-9 h-9 lg:w-11 lg:h-11 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-blue-600 transition-all shadow-sm" title="Voir le site">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
        </div>

        <!-- Date Widget -->
        <div class="hidden md:flex bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 items-center gap-2 pr-4 shadow-sm h-11 lg:h-13">
            <div class="w-7 h-7 lg:w-9 lg:h-9 bg-emerald-600 rounded-lg lg:rounded-xl flex items-center justify-center text-white font-bold text-[10px] lg:text-sm flex-shrink-0">
                <?php echo date('d'); ?>
            </div>
            <div class="flex flex-col leading-none">
                <span class="text-[7px] lg:text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('F'); ?></span>
                <span class="text-[10px] lg:text-xs font-black text-slate-900 dark:text-white"><?php echo date('Y'); ?></span>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkIconHeader = document.getElementById('theme-toggle-dark-icon-header');
        const lightIconHeader = document.getElementById('theme-toggle-light-icon-header');

        if (document.documentElement.classList.contains('dark')) {
            if (lightIconHeader) lightIconHeader.classList.remove('hidden');
        } else {
            if (darkIconHeader) darkIconHeader.classList.remove('hidden');
        }

        const themeToggleBtnHeader = document.getElementById('theme-toggle-header');
        if (themeToggleBtnHeader) {
            themeToggleBtnHeader.addEventListener('click', function() {
                darkIconHeader.classList.toggle('hidden');
                lightIconHeader.classList.toggle('hidden');

                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });
        }
    });
</script>
