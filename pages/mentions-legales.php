<?php
require_once 'config/database.php';
$page_title = "Mentions Légales - Dynamique Samy Magadju";
$page_description = "Consultez les informations légales concernant l'édition et l'hébergement du site officiel de la Dynamique Samy Magadju.";
include 'includes/header.php';
?>

<!-- Hero Header -->
<section class="relative py-24 bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900 overflow-hidden border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0 0 L100 100 M100 0 L0 100" stroke="currentColor" stroke-width="0.1" />
        </svg>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
        <span class="inline-block px-4 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-full mb-6">Cadre Juridique</span>
        <h1 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6 font-serif leading-none">
            Mentions <span class="text-slate-500 italic">Légales</span>
        </h1>
        <p class="text-xl text-slate-500 dark:text-slate-400 font-serif leading-relaxed italic max-w-2xl mx-auto">
            "La transparence est le fondement de la confiance entre notre organisation et ses partenaires numériques."
        </p>
    </div>
</section>

<section class="py-20 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="space-y-24 animate-fade-in">
            <!-- 1. Éditeur du site -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">01</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-slate-900 dark:border-white inline-block pb-1">Éditeur du site</h2>
                    <div class="p-8 bg-slate-50 dark:bg-slate-800 rounded-[2.5rem] border border-slate-100 dark:border-slate-700">
                        <p class="text-lg text-slate-800 dark:text-slate-200 font-serif leading-relaxed mb-4">
                            Le site internet <span class="text-blue-600 font-bold"><?php echo SITE_URL; ?></span> est la propriété exclusive de :
                        </p>
                        <div class="space-y-2 text-slate-600 dark:text-slate-400 font-serif italic">
                            <p class="font-bold text-slate-900 dark:text-white not-italic">La Dynamique Samy Magadju Bonheur / Wema ni Hakiba ASBL</p>
                            <p>Sous le patronage de l'Ir. Samy Magadju Bonheur</p>
                            <p>Bukavu, Sud-Kivu, République Démocratique du Congo</p>
                            <div class="pt-4 flex flex-col gap-2 shadow-sm border-t border-slate-200 dark:border-slate-700 mt-4 pt-4">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Contact Officiel</span>
                                <a href="tel:+243993859330" class="text-slate-900 dark:text-white font-bold hover:text-blue-600 transition-colors">+243 993 859 330</a>
                                <a href="mailto:magadjusamybonheur@gmail.com" class="text-slate-900 dark:text-white font-bold hover:text-blue-600 transition-colors">magadjusamybonheur@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hébergement -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">02</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-slate-900 dark:border-white inline-block pb-1">Hébergement</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-serif">
                        Ce site est actuellement hébergé à titre de développement et de démonstration :
                    </p>
                    <ul class="mt-4 space-y-2 text-slate-600 dark:text-slate-400 font-serif italic">
                        <li><span class="font-bold text-slate-900 dark:text-white">Environnement :</span> Localhost / Station de travail Lucien Amani</li>
                        <li><span class="font-bold text-slate-900 dark:text-white">Localisation :</span> Los Angeles (via tunneling) / Bukavu (Source)</li>
                        <li><span class="font-bold text-slate-900 dark:text-white">Type :</span> Site vitrine dynamique PHP/MySQL</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Propriété Intellectuelle -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">03</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-slate-900 dark:border-white inline-block pb-1">Propriété Intellectuelle</h2>
                    <div class="relative p-8 bg-slate-950 rounded-[2.5rem] text-white">
                        <p class="text-xl font-serif leading-relaxed italic">
                            "L'ensemble des contenus (textes, images, projets, graphismes, logo) présents sur ce site sont la propriété exclusive de l'Ir. Samy Magadju Bonheur."
                        </p>
                        <p class="mt-6 text-sm text-slate-400 font-serif leading-relaxed">
                            Toute reproduction, distribution ou modification, même partielle, est strictement interdite sans un accord écrit préalable de la direction de la Dynamique.
                        </p>
                        <div class="absolute top-0 right-0 p-8">
                            <svg class="w-12 h-12 text-slate-800" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Responsabilité -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                <div class="md:col-span-1">
                    <span class="text-4xl font-black text-slate-200 dark:text-slate-800 font-serif">04</span>
                </div>
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6 font-serif border-b-4 border-slate-900 dark:border-white inline-block pb-1">Limitation de Responsabilité</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-serif">
                        L'éditeur s'efforce de fournir des informations précises et à jour. Cependant, il ne peut être tenu responsable des éventuelles omissions ou erreurs dans le contenu, ni des dommages résultant de l'utilisation du site ou de l'impossibilité d'y accéder.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
