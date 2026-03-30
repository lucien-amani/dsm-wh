<?php
require_once 'config/database.php';
$page_title = "Politique de Confidentialité - Dynamique Samy Magadju";
$page_description = "Votre vie privée est notre priorité. Découvrez comment la Dynamique Samy Magadju protège et gère vos données personnelles.";
include 'includes/header.php';
?>

<!-- Hero Header -->
<section class="relative py-24 bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900 overflow-hidden border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <rect width="100" height="100" fill="none" stroke="currentColor" stroke-width="0.1" />
            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="0.1" />
        </svg>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
        <span class="inline-block px-4 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-full mb-6">Sécurité & Éthique</span>
        <h1 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6 font-serif leading-none">
            Politique de <span class="text-emerald-600 italic">Confidentialité</span>
        </h1>
        <p class="text-xl text-slate-500 dark:text-slate-400 font-serif leading-relaxed italic max-w-2xl mx-auto">
            "Nous nous engageons à traiter vos informations avec le plus grand respect et conformément aux standards internationaux de protection."
        </p>
    </div>
</section>

<section class="py-20 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="space-y-24 animate-fade-in">
            <!-- 1. Introduction -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">01</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-emerald-600 inline-block pb-1">Introduction</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-serif">
                        La Dynamique Samy Magadju / Wema ni Hakiba accorde une importance capitale à la protection de l'intimité de ses membres et des visiteurs de son portail web. Cette politique détaille nos engagements concernant la collecte et le traitement de vos informations.
                    </p>
                </div>
            </div>

            <!-- 2. Collecte des données -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">02</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-emerald-600 inline-block pb-1">Collecte des données</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-serif mb-8">
                        Nous ne collectons que les informations strictement nécessaires à la fourniture de nos services :
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-300">Identité (Nom, Prénom)</span>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-300">Email & Contact</span>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-300">Logs de Navigation</span>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.016z"/></svg>
                            </div>
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-300">Consentements</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Utilisation & Protection -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">03</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-emerald-600 inline-block pb-1">Utilisation & Sécurité</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed font-serif">
                        Les données sont utilisées exclusivement pour répondre à vos demandes via nos formulaires et pour optimiser les performances de notre site. 
                    </p>
                    <div class="bg-slate-950 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <h4 class="text-emerald-400 font-black uppercase tracking-widest text-xs mb-4">Notre forteresse numérique</h4>
                            <p class="text-xl font-serif leading-relaxed italic mb-6">"Toutes les transmissions de données sont chiffrées (SSL/HTTPS) et stockées sur des serveurs sécurisés."</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-widest text-emerald-500">Protection Activée</span>
                            </div>
                        </div>
                        <!-- Décoration abstraite -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-600/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    </div>
                </div>
            </div>

            <!-- 4. Vos Droits -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">04</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-emerald-600 inline-block pb-1">Vos Droits</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed font-serif">
                        Vous disposez d'un droit total sur vos données : Accès, Rectification, Suppression et Opposition.
                    </p>
                    <div class="p-8 border-2 border-dashed border-emerald-100 dark:border-emerald-900/30 rounded-[2rem] text-center">
                        <p class="text-slate-500 dark:text-slate-400 font-serif italic mb-6">Pour toute demande concernant vos données personnelles, contactez-nous directement :</p>
                        <a href="mailto:magadjusamybonheur@gmail.com" class="inline-flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-widest text-xs rounded-full transition-all hover:scale-105 shadow-xl shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Exercer mes droits
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
