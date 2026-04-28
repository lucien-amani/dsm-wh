<?php
$current_page = $current_page ?? '';
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 overflow-y-auto">
    <!-- Logo & Brand -->
    <div class="h-20 flex items-center px-8 border-b border-slate-100 dark:border-slate-800/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-emerald-500/20 transform hover:rotate-6 transition-transform overflow-hidden">
                <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none">DSM <span class="text-emerald-600">Admin</span></span>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1">Administration System</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-8 space-y-1.5">
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4 ml-4">Tableau de bord</p>
        
        <a href="<?php echo SITE_URL; ?>/admin/tableau-de-bord" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'dashboard' ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'dashboard' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <span class="font-bold text-sm">Dashboard</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/profil" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'profile' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'profile' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="font-bold text-sm">Mon Profil</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/parametres" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'settings' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'settings' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="font-bold text-sm">Paramètres</span>
        </a>

        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] pt-6 mb-4 ml-4">Contenu</p>

        <a href="<?php echo SITE_URL; ?>/admin/actualites" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo ($current_page === 'news' && !isset($_GET['type'])) ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo ($current_page === 'news' && !isset($_GET['type'])) ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
            <span class="font-bold text-sm">Actualités</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=tweet" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo (isset($_GET['type']) && $_GET['type'] === 'tweet') ? 'bg-[#1da1f2] text-white shadow-xl shadow-[#1da1f2]/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#1da1f2]'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo (isset($_GET['type']) && $_GET['type'] === 'tweet') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-[#1da1f2]/10 dark:group-hover:bg-[#1da1f2]/20'; ?>">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
            </div>
            <span class="font-bold text-sm">Lien X (Twitter)</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=linkedin" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo (isset($_GET['type']) && $_GET['type'] === 'linkedin') ? 'bg-[#0a66c2] text-white shadow-xl shadow-[#0a66c2]/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#0a66c2]'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo (isset($_GET['type']) && $_GET['type'] === 'linkedin') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-[#0a66c2]/10 dark:group-hover:bg-[#0a66c2]/20'; ?>">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
            </div>
            <span class="font-bold text-sm">Lien LinkedIn</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=youtube" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo (isset($_GET['type']) && $_GET['type'] === 'youtube') ? 'bg-[#ff0000] text-white shadow-xl shadow-[#ff0000]/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#ff0000]'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo (isset($_GET['type']) && $_GET['type'] === 'youtube') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-[#ff0000]/10 dark:group-hover:bg-[#ff0000]/20'; ?>">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </div>
            <span class="font-bold text-sm">Lien YouTube</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/projets" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'projects' ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'projects' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <span class="font-bold text-sm">Projets</span>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/commentaires" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'comments' ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'comments' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <span class="font-bold text-sm">Commentaires</span>
            <?php 
                $pdo_c = getDBConnection();
                $pending_count = $pdo_c->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
                if($pending_count > 0): 
            ?>
                <span class="ml-auto bg-amber-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full group-hover:scale-110 transition-transform"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/messages" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'messages' ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'messages' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="font-bold text-sm">Messages</span>
            <?php 
                $new_msgs = $pdo_c->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'Nouveau'")->fetchColumn();
                if($new_msgs > 0): 
            ?>
                <span class="ml-auto bg-blue-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full animate-pulse"><?php echo $new_msgs; ?></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo SITE_URL; ?>/admin/newsletter" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'newsletter' ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-emerald-600'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'newsletter' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="font-bold text-sm">Newsletter</span>
        </a>

        <div class="pt-8">
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4 ml-4">Paramètres</p>
            
            <!-- Dark Mode Toggle Widget -->
            <button id="theme-toggle" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-2xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover:text-amber-500 group-hover:bg-amber-50 dark:group-hover:bg-amber-900/20 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
                </div>
                <span class="font-bold text-sm">Mode Sombre</span>
            </button>

            <a href="<?php echo SITE_URL; ?>/" target="_blank"
               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-slate-500 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:text-blue-600 transition-all group mt-1">
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-100/50 dark:group-hover:bg-blue-900/30 transition-colors text-slate-400 group-hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <span class="font-bold text-sm">Voir le site</span>
            </a>

            <?php if ($_SESSION['admin_role'] === 'Superadmin' || $_SESSION['admin_role'] === 'Admin'): ?>
            <a href="<?php echo SITE_URL; ?>/admin/utilisateurs" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'editors' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600'; ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'editors' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20'; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="font-bold text-sm">Équipe (Éditeurs)</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/admin/logs" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-300 group <?php echo $current_page === 'logs' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600'; ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_page === 'logs' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20'; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="font-bold text-sm">Journaux (Logs)</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo SITE_URL; ?>/admin/deconnexion" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-all group mt-1">
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center group-hover:bg-rose-100 dark:group-hover:bg-rose-900/50 transition-colors text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H3"/></svg>
                </div>
                <span class="font-bold text-sm">Déconnexion</span>
            </a>
        </div>
    </nav>

    <!-- User Profile Bottom -->
    <div class="p-6 border-t border-slate-100 dark:border-slate-800">
        <a href="<?php echo SITE_URL; ?>/admin/profil" class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex items-center gap-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors group">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-black text-sm shrink-0 shadow-lg shadow-emerald-500/20 overflow-hidden">
                <?php 
                    $pdo_av = getDBConnection();
                    $stmt_av = $pdo_av->prepare("SELECT avatar FROM users WHERE id = :id");
                    $stmt_av->execute([':id' => $_SESSION['admin_id']]);
                    $av_user = $stmt_av->fetch();
                    if($av_user && $av_user['avatar']): 
                ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/avatars/<?php echo $av_user['avatar']; ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-black text-slate-900 dark:text-white truncate uppercase tracking-tighter"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest"><?php echo $_SESSION['admin_role']; ?></span>
            </div>
        </a>
    </div>
</aside>

<!-- Admin Navigation (Classic Bottom Bar) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 w-full h-16 bg-slate-950 z-[100] border-t border-white/5 flex items-center justify-around">
    <!-- Dashboard -->
    <a href="<?php echo SITE_URL; ?>/admin/tableau-de-bord" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
        <svg class="w-5 h-5 transition-colors <?php echo $current_page === 'dashboard' ? 'text-emerald-500' : 'text-gray-400 group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo $current_page === 'dashboard' ? 'text-emerald-500' : 'text-gray-500'; ?>">Dashboard</span>
    </a>

    <!-- News -->
    <a href="<?php echo SITE_URL; ?>/admin/actualites" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
        <svg class="w-5 h-5 transition-colors <?php echo $current_page === 'news' ? 'text-emerald-500' : 'text-gray-400 group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo $current_page === 'news' ? 'text-emerald-500' : 'text-gray-500'; ?>">Actualités</span>
    </a>

    <!-- Central Action (Projets) -->
    <a href="<?php echo SITE_URL; ?>/admin/projets" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
        <div class="w-10 h-10 bg-emerald-600 rounded-none flex items-center justify-center text-white active:bg-emerald-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="text-[9px] font-bold uppercase tracking-tighter text-gray-500">Projets</span>
    </a>

    <!-- Messages -->
    <a href="<?php echo SITE_URL; ?>/admin/messages" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
        <div class="relative">
            <svg class="w-5 h-5 transition-colors <?php echo $current_page === 'messages' ? 'text-emerald-500' : 'text-gray-400 group-active:text-gray-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <?php if($new_msgs > 0): ?><span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-rose-600 rounded-full"></span><?php endif; ?>
        </div>
        <span class="text-[9px] font-bold uppercase tracking-tighter <?php echo $current_page === 'messages' ? 'text-emerald-500' : 'text-gray-500'; ?>">Messages</span>
    </a>

    <!-- Menu Trigger -->
    <button onclick="toggleSidebar()" class="flex-1 flex flex-col items-center justify-center gap-0.5 group">
        <svg class="w-5 h-5 text-gray-400 group-active:text-gray-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
        <span class="text-[9px] font-bold uppercase tracking-tighter text-gray-500">Menu</span>
    </button>
</div>

<!-- Overlays -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-[40] bg-slate-950/60 backdrop-blur-md hidden"></div>

<!-- Theme & Sidebar JS -->
<script>
    // Theme Switcher Logic
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        if (lightIcon) lightIcon.classList.remove('hidden');
    } else {
        document.documentElement.classList.remove('dark');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        if (darkIcon) darkIcon.classList.remove('hidden');
    }

    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            document.getElementById('theme-toggle-dark-icon').classList.toggle('hidden');
            document.getElementById('theme-toggle-light-icon').classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    }

    // Mobile Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    }
</script>

