<?php
require_once 'config/database.php';
$page_title = "Politique des Cookies - Dynamique Samy Magadju";
$page_description = "Apprenez-en plus sur la manière dont nous utilisons les cookies pour améliorer votre expérience sur le site de la Dynamique Samy Magadju.";
include 'includes/header.php';
?>

<!-- Hero Header -->
<section class="relative py-24 bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900 overflow-hidden border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0 100 C 20 0 50 0 100 100" fill="none" stroke="currentColor" stroke-width="0.5" />
        </svg>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
        <span class="inline-block px-4 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-full mb-6">Confidentialité & Transparence</span>
        <h1 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6 font-serif leading-none">
            Politique des <span class="text-blue-600 italic">Cookies</span>
        </h1>
        <p class="text-xl text-slate-500 dark:text-slate-400 font-serif leading-relaxed italic max-w-2xl mx-auto">
            "Nous attachons une importance capitale à la protection de votre vie privée lors de votre navigation sur nos plateformes numériques."
        </p>
    </div>
</section>

<section class="py-20 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="space-y-20 animate-fade-in">
            <!-- 1. Qu'est-ce qu'un cookie ? -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">01</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-4 font-serif border-b-4 border-blue-600 inline-block pb-1">Qu'est-ce qu'un cookie ?</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-serif">
                        Un cookie est un petit fichier texte déposé sur votre ordinateur, mobile ou tablette lors de la visite d'un site. Imaginé comme une "mémoire de navigation", il permet au site de mémoriser vos actions et préférences (nom d'utilisateur, langue, taille de police, mode sombre) pendant un temps donné.
                    </p>
                </div>
            </div>

            <!-- 2. Comment utilisons-nous les cookies ? -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">02</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-4 font-serif border-b-4 border-blue-600 inline-block pb-1">Pourquoi les utilisons-nous ?</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                        <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2 uppercase tracking-wide text-sm">Préférences</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic">Mémorise vos réglages personnalisés comme le mode sombre ou l'affichage des articles.</p>
                        </div>
                        <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2 uppercase tracking-wide text-sm">Analyses</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic">Nous aide à comprendre quelles actualités et quels projets passionnent le plus notre communauté.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Gestion des cookies & Tutoriels -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">03</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-4 font-serif border-b-4 border-blue-600 inline-block pb-1">Maîtrisez vos cookies</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed font-serif">
                        Vous avez le contrôle total. Vous pouvez configurer votre navigateur pour accepter, refuser ou être averti lors du dépôt d'un cookie. Voici comment faire sur les principaux navigateurs :
                    </p>

                    <!-- Bouton de gestion directe -->
                    <div class="mb-12 p-8 bg-blue-600 rounded-[2.5rem] text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden group">
                        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 blur-3xl rounded-full group-hover:bg-white/20 transition-colors"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="text-center md:text-left">
                                <h3 class="text-xl font-black uppercase tracking-tight mb-2">Configurez vos préférences ici</h3>
                                <p class="text-blue-100 text-sm font-medium italic">Vous pouvez modifier vos choix en un clic sans passer par les réglages de votre navigateur.</p>
                            </div>
                            <button onclick="document.getElementById('cookie-settings-btn').click()" class="px-8 py-4 bg-white text-blue-600 font-black rounded-2xl hover:bg-slate-100 transition-all transform active:scale-95 uppercase tracking-widest text-xs shadow-xl shadow-black/10">
                                Ouvrir les paramètres
                            </button>
                        </div>
                    </div>

                    <!-- Interface de Tutoriels Animée -->
                    <div class="mt-8 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] p-4 sm:p-8 border border-slate-100 dark:border-slate-800">
                        <!-- Navigation des Onglets -->
                        <div class="flex flex-wrap justify-center gap-4 mb-10">
                            <button onclick="switchTab('chrome')" class="browser-tab active" id="tab-chrome">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 2c5.523 0 10 4.477 10 10 0 1.29-.245 2.52-.69 3.65l-6.85-6.85c-.18-.18-.43-.28-.69-.28h-3c-.55 0-1 .45-1 1v2h-2c-.55 0-1 .45-1 1v3c0 .55-.45 1-1 1h-2v3.31c-2.45-1.55-4-4.24-4-7.31s1.55-5.76 4-7.31V4c0-.55.45-1 1-1h1zm2.5 13c.28 0 .5-.22.5-.5V13h-4v1.5c0 .28.22.5.5.5h3z"/></svg>
                                <span>Chrome</span>
                            </button>
                            <button onclick="switchTab('firefox')" class="browser-tab" id="tab-firefox">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.344 1.1s-1.848.432-3.336 1.704c-1.488 1.248-1.584 2.856-1.584 2.856s.216-.408.84-.744c.624-.312 1.488-.312 1.488-.312s-.408.84-.792 1.848c-.384 1.008.384 1.632.384 1.632s-.192-.624.168-1.296c.36-.672.912-.912 1.488-1.032.576-.12 1.2.144 1.344 1.128s-.072 2.376-1.488 3.552c-1.416 1.176-2.568 1.32-3.336.168l1.416-.24c-.48-.96-1.272-.936-2.112-.648-.84.288-1.728 1.032-1.872 2.232-.144 1.2.432 2.064 1.392 2.376l-1.44-.048s-.528-.48-.672-.984c-.144-.504-.048-1.032.144-1.512-.48.552-.864 1.776-.36 2.88.504 1.104.936 2.208.576 3.768-.36 1.56-1.296 2.544-2.856 3.12-1.56.576-3.264.456-4.656-.432-1.392-.888-2.256-2.52-2.328-4.32-.072-1.8 1.056-3.816 2.856-4.824 1.8-1.008 3.84-.96 3.84-.96s-.696-.048-1.224.288c-.528.336-.984.792-1.224 1.464l.168-.144s.312-1.152 1.248-1.68c.936-.528 2.088-.552 2.088-.552s-1.056-.504-2.28-.408c-1.224.096-2.328.792-3.048 1.896-.72 1.104-1.008 2.472-.888 3.744.12 1.272.768 2.28 1.68 2.904-.648-.816-.96-1.92-.816-3.024.144-1.104.696-2.016 1.44-2.592.744-.576 1.512-.672 1.512-.672s-.48-.288-.984-.192c-.504.096-.984.456-1.152 1.032l-.12-.024s.192-1.632 1.488-2.328c1.296-.696 2.328-.168 2.328-.168s-2.04-.648-3.336 1.608c0 0 .912-1.512 2.76-1.848.336-.072.72-.12 1.08-.12.336 0 .648.024.96.072-.864-.384-1.824-.48-2.784-.12-.96.36-1.656 1.152-1.824 2.112-.168.96.12 1.848.648 2.52-.408-.624-.552-1.392-.384-2.112.168-.72.648-1.272 1.224-.576l-.024.168s-1.512 2.04.552 4.104c1.176 1.176 2.856 1.728 4.416 1.512 1.56-.216 2.904-.96 3.744-2.136.84-1.176 1.128-2.568.864-3.84a7.712 7.712 0 00-1.8-3.528 7.35 7.35 0 00-3.312-2.16c1.128-.072 2.136.216 2.856.84.72.624 1.128 1.488 1.176 2.472.048-.792-.312-1.536-.888-2.016-.576-.48-1.296-.696-2.04-.696.888-.384 1.848-.48 2.784-.12.936.36 1.632 1.104 1.968 2.016-.36-.936-.984-1.68-1.848-2.136-.864-.456-1.8-.576-2.736-.336.912-.528 1.992-.72 3.024-.456 1.032.264 1.944.888 2.448 1.8.192.36.336.744.432 1.152-.072-.744-.336-1.44-.768-2.04s-1.032-1.104-1.752-1.44c.48-.096.96-.12 1.416-.072.48.048.912.192 1.32.432a5.4 5.4 0 011.08 1.032c-.144-.768-.504-1.416-.96-1.896-.456-.48-1.056-.792-1.68-.864-.624-.072-1.224.048-1.752.336 1.272-.696 2.856-.456 4.08.648.024.024.072.048.096.072a3.812 3.812 0 01.384-.576z"/></svg>
                                <span>Firefox</span>
                            </button>
                            <button onclick="switchTab('safari')" class="browser-tab" id="tab-safari">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 1.25.19 2.45.54 3.58l3.6-3.6c-.09-.32-.14-.65-.14-.98 0-2.21 1.79-4 4-4 .33 0 .66.05.98.14l3.6-3.6C11.45.19 10.25 0 9 0c-4.97 0-9 4.03-9 9 0 4.97 4.03 9 9 9 1.25 0 2.45-.19 3.58-.54l-3.6-3.6c.32.09.65.14.98.14 2.21 0 4-1.79 4-4 0-.33-.05-.66-.14-.98l3.6-3.6c.35 1.13.54 2.33.54 3.58 0 6.63-5.37 12-12 12-6.63 0-12-5.37-12-12S5.37 0 12 0z"/></svg>
                                <span>Safari</span>
                            </button>
                            <button onclick="switchTab('edge')" class="browser-tab" id="tab-edge">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 2c1.373 0 2.668.277 3.854.773L12.5 6h-1c-.828 0-1.5.672-1.5 1.5v2H8c-.552 0-1 .448-1 1v2H5c-.552 0-1 .448-1 1v4H2.43c-1.488-1.543-2.43-3.66-2.43-5.999 0-5.523 4.477-10 10-10zm2.5 13c1.38 0 2.5-1.12 2.5-2.5S15.88 10 14.5 10s-2.5 1.12-2.5 2.5 1.12 2.5 2.5 2.5z"/></svg>
                                <span>Edge</span>
                            </button>
                        </div>

                        <!-- Contenu des Onglets -->
                        <div class="relative min-h-[350px]">
                            <!-- Chrome Content -->
                            <div class="browser-content active animate-in" id="content-chrome">
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 2c5.523 0 10 4.477 10 10 0 1.29-.245 2.52-.69 3.65l-6.85-6.85c-.18-.18-.43-.28-.69-.28h-3c-.55 0-1 .45-1 1v2h-2c-.55 0-1 .45-1 1v3c0 .55-.45 1-1 1h-2v3.31c-2.45-1.55-4-4.24-4-7.31s1.55-5.76 4-7.31V4c0-.55.45-1 1-1h1zm2.5 13c.28 0 .5-.22.5-.5V13h-4v1.5c0 .28.22.5.5.5h3z"/></svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight font-serif">Google Chrome</h3>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="step group">
                                            <span class="step-num">1</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Ouvrez le menu <span class="browser-ui">...</span> situé dans le coin supérieur droit.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">2</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Naviguez vers <span class="browser-ui">Paramètres</span> > <span class="browser-ui">Confidentialité et sécurité</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">3</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Cliquez sur <span class="browser-ui">Cookies et autres données des sites</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">4</span>
                                            <div class="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-800/50">
                                                <p class="text-xs font-black uppercase text-blue-600 dark:text-blue-400 mb-2">Option Recommandée</p>
                                                <p class="font-serif italic text-slate-600 dark:text-slate-300">Cochez "Bloquer les cookies tiers en mode navigation privée" pour un équilibre parfait.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Firefox Content -->
                            <div class="browser-content animate-in" id="content-firefox">
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M19.344 1.1s-1.848.432-3.336 1.704c-1.488 1.248-1.584 2.856-1.584 2.856s.216-.408.84-.744c.624-.312 1.488-.312 1.488-.312s-.408.84-.792 1.848c-.384 1.008.384 1.632.384 1.632s-.192-.624.168-1.296c.36-.672.912-.912 1.488-1.032.576-.12 1.2.144 1.344 1.128s-.072 2.376-1.488 3.552c-1.416 1.176-2.568 1.32-3.336.168l1.416-.24c-.48-.96-1.272-.936-2.112-.648-.84.288-1.728 1.032-1.872 2.232-.144 1.2.432 2.064 1.392 2.376l-1.44-.048s-.528-.48-.672-.984c-.144-.504-.048-1.032.144-1.512-.48.552-.864 1.776-.36 2.88.504 1.104.936 2.208.576 3.768-.36 1.56-1.296 2.544-2.856 3.12-1.56.576-3.264.456-4.656-.432-1.392-.888-2.256-2.52-2.328-4.32-.072-1.8 1.056-3.816 2.856-4.824 1.8-1.008 3.84-.96 3.84-.96s-.696-.048-1.224.288c-.528.336-.984.792-1.224 1.464l.168-.144s.312-1.152 1.248-1.68c.936-.528 2.088-.552 2.088-.552s-1.056-.504-2.28-.408c-1.224.096-2.328.792-3.048 1.896-.72 1.104-1.008 2.472-.888 3.744.12 1.272.768 2.28 1.68 2.904-.648-.816-.96-1.92-.816-3.024.144-1.104.696-2.016 1.44-2.592.744-.576 1.512-.672 1.512-.672s-.48-.288-.984-.192c-.504.096-.984.456-1.152 1.032l-.12-.024s.192-1.632 1.488-2.328c1.296-.696 2.328-.168 2.328-.168s-2.04-.648-3.336 1.608c0 0 .912-1.512 2.76-1.848.336-.072.72-.12 1.08-.12.336 0 .648.024.96.072-.864-.384-1.824-.48-2.784-.12-.96.36-1.656 1.152-1.824 2.112-.168.96.12 1.848.648 2.52-.408-.624-.552-1.392-.384-2.112.168-.72.648-1.272 1.224-.576l-.024.168s-1.512 2.04.552 4.104c1.176 1.176 2.856 1.728 4.416 1.512 1.56-.216 2.904-.96 3.744-2.136.84-1.176 1.128-2.568.864-3.84a7.712 7.712 0 00-1.8-3.528 7.35 7.35 0 00-3.312-2.16c1.128-.072 2.136.216 2.856.84.72.624 1.128 1.488 1.176 2.472.048-.792-.312-1.536-.888-2.016-.576-.48-1.296-.696-2.04-.696.888-.384 1.848-.48 2.784-.12.936.36 1.632 1.104 1.968 2.016-.36-.936-.984-1.68-1.848-2.136-.864-.456-1.8-.576-2.736-.336.912-.528 1.992-.72 3.024-.456 1.032.264 1.944.888 2.448 1.8.192.36.336.744.432 1.152-.072-.744-.336-1.44-.768-2.04s-1.032-1.104-1.752-1.44c.48-.096.96-.12 1.416-.072.48.048.912.192 1.32.432a5.4 5.4 0 011.08 1.032c-.144-.768-.504-1.416-.96-1.896-.456-.48-1.056-.792-1.68-.864-.624-.072-1.224.048-1.752.336 1.272-.696 2.856-.456 4.08.648.024.024.072.048.096.072a3.812 3.812 0 01.384-.576z"/></svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight font-serif">Mozilla Firefox</h3>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="step group">
                                            <span class="step-num">1</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Ouvrez le menu <span class="browser-ui">≡</span> et cliquez sur <span class="browser-ui">Paramètres</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">2</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Sélectionnez l'onglet <span class="browser-ui">Vie privée et sécurité</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">3</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Sous <span class="browser-ui">Cookies et données de sites</span>, gérez vos exceptions ou effacez tout.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Safari Content -->
                            <div class="browser-content animate-in" id="content-safari">
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/20 text-sky-600 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 1.25.19 2.45.54 3.58l3.6-3.6c-.09-.32-.14-.65-.14-.98 0-2.21 1.79-4 4-4 .33 0 .66.05.98.14l3.6-3.6C11.45.19 10.25 0 9 0c-4.97 0-9 4.03-9 9 0 4.97 4.03 9 9 9 1.25 0 2.45-.19 3.58-.54l-3.6-3.6c.32.09.65.14.98.14 2.21 0 4-1.79 4-4 0-.33-.05-.66-.14-.98l3.6-3.6c.35 1.13.54 2.33.54 3.58 0 6.63-5.37 12-12 12-6.63 0-12-5.37-12-12S5.37 0 12 0z"/></svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight font-serif">Apple Safari</h3>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="step group">
                                            <span class="step-num">1</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Allez dans le menu <span class="browser-ui">Safari</span> > <span class="browser-ui">Réglages...</span></p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">2</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Cliquez sur le bouclier <span class="browser-ui">Confidentialité</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">3</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Cochez ou décochez <span class="browser-ui">Bloquer tous les cookies</span> selon votre besoin.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edge Content -->
                            <div class="browser-content animate-in" id="content-edge">
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 2c1.373 0 2.668.277 3.854.773L12.5 6h-1c-.828 0-1.5.672-1.5 1.5v2H8c-.552 0-1 .448-1 1v2H5c-.552 0-1 .448-1 1v4H2.43c-1.488-1.543-2.43-3.66-2.43-5.999 0-5.523 4.477-10 10-10zm2.5 13c1.38 0 2.5-1.12 2.5-2.5S15.88 10 14.5 10s-2.5 1.12-2.5 2.5 1.12 2.5 2.5 2.5z"/></svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight font-serif">Microsoft Edge</h3>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="step group">
                                            <span class="step-num">1</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Cliquez sur <span class="browser-ui">...</span> puis <span class="browser-ui">Paramètres</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">2</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Menu <span class="browser-ui">Cookies et autorisations de site</span>.</p>
                                        </div>
                                        <div class="step group">
                                            <span class="step-num">3</span>
                                            <p class="font-serif italic text-slate-600 dark:text-slate-300">Gérez le niveau de protection (Basique, Équilibré ou Strict).</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        /* Styles spécifiques pour l'interface animée */
                        .browser-tab {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            padding: 0.75rem 1.75rem;
                            border-radius: 1.25rem;
                            background: transparent;
                            border: 2px solid transparent;
                            color: #94a3b8;
                            font-size: 0.9rem;
                            font-weight: 800;
                            text-transform: uppercase;
                            letter-spacing: 0.1em;
                            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                        }
                        .dark .browser-tab { color: #475569; }
                        
                        .browser-tab.active {
                            background: white;
                            border-color: #3b82f6;
                            color: #1e293b;
                            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.15);
                            transform: translateY(-2px);
                        }
                        .dark .browser-tab.active {
                            background: #1e293b;
                            border-color: #3b82f6;
                            color: white;
                        }
                        
                        .browser-tab:hover:not(.active) {
                            background: rgba(255,255,255,0.5);
                            color: #3b82f6;
                        }
                        .dark .browser-tab:hover:not(.active) {
                            background: rgba(30,41,59,0.5);
                        }

                        .browser-content {
                            display: none;
                            opacity: 0;
                            transform: translateY(20px);
                            transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
                        }
                        
                        .browser-content.active {
                            display: block;
                        }
                        
                        .browser-content.animate-in {
                            opacity: 1;
                            transform: translateY(0);
                        }

                        .step {
                            display: flex;
                            align-items: flex-start;
                            gap: 1.5rem;
                            padding: 1rem;
                            border-radius: 1.5rem;
                            transition: background 0.3s;
                        }
                        .step:hover { background: rgba(59, 130, 246, 0.03); }

                        .step-num {
                            width: 2.5rem;
                            height: 2.5rem;
                            border-radius: 0.75rem;
                            background: #f1f5f9;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: 900;
                            color: #3b82f6;
                            font-size: 1rem;
                            flex-shrink: 0;
                        }
                        .dark .step-num { background: #1e293b; }

                        .browser-ui {
                            display: inline-block;
                            padding: 0.1rem 0.5rem;
                            background: #f8fafc;
                            border: 1px solid #e2e8f0;
                            border-radius: 4px;
                            font-family: sans-serif;
                            font-style: normal;
                            font-weight: 700;
                            font-size: 0.8rem;
                            color: #1a6fb5;
                        }
                        .dark .browser-ui { background: #0f172a; border-color: #1e293b; }
                    </style>

                    <script>
                        function switchTab(browserId) {
                            // 1. Désactiver tous les onglets
                            document.querySelectorAll('.browser-tab').forEach(t => t.classList.remove('active'));
                            // 2. Cacher tous les contenus
                            document.querySelectorAll('.browser-content').forEach(c => {
                                c.classList.remove('active', 'animate-in');
                            });
                            
                            // 3. Activer l'onglet sélectionné
                            document.getElementById('tab-' + browserId).classList.add('active');
                            
                            // 4. Afficher le contenu avec animation
                            const content = document.getElementById('content-' + browserId);
                            content.classList.add('active');
                            
                            // Petit délai pour déclencher l'animation d'entrée
                            setTimeout(() => {
                                content.classList.add('animate-in');
                            }, 50);
                        }
                        
                        // Déclencher le premier au chargement
                        window.addEventListener('DOMContentLoaded', () => {
                            switchTab('chrome');
                        });
                    </script>
                </div>
            </div>
            
            <!-- Table des cookies -->
            <div class="mt-20 border-t border-slate-100 dark:border-slate-800 pt-10">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6 font-serif">Tableau de nos Cookies</h3>
                <div class="overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-800">
                    <table class="w-full text-left border-collapse bg-white dark:bg-slate-900">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fonction</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Durée</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase rounded">Technique</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-serif italic">Mémorise votre session admin et vos préférences de thème (clair/sombre).</td>
                                <td class="px-6 py-4 text-xs text-slate-400 font-bold text-right">30 jours</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[9px] font-black uppercase rounded">Analytique</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-serif italic">Compte le nombre de visites sur nos actualités et projets.</td>
                                <td class="px-6 py-4 text-xs text-slate-400 font-bold text-right">Session</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
