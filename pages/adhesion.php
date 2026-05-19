<?php
// pages/adhesion.php

$current_page = 'adhesion';
$page_title = 'Demande d\'Adhésion Premium - DSM-WH ASBL';
$page_description = 'Rejoignez la DSM-WH ASBL via notre formulaire d\'adhésion moderne et sécurisé.';

include 'includes/header.php';
?>

<div class="bg-gray-100 dark:bg-slate-950 py-12 transition-colors min-h-screen">
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 shadow-2xl rounded-none md:rounded-[3rem] overflow-hidden border border-gray-200 dark:border-slate-800">
        
        <!-- Progress Bar -->
        <div class="bg-slate-50 dark:bg-slate-900/50 p-8 border-b border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between max-w-2xl mx-auto relative">
                <div class="absolute top-1/2 left-0 w-full h-1.5 bg-gray-200 dark:bg-slate-800 -translate-y-1/2 z-0 rounded-full"></div>
                <div id="progress-line" class="absolute top-1/2 left-0 w-0 h-1.5 bg-emerald-500 -translate-y-1/2 z-0 transition-all duration-700 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>

                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div id="step-dot-1" class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-black shadow-xl shadow-emerald-500/30 transition-all duration-500 transform rotate-3">1</div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600">Identité</span>
                </div>
                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div id="step-dot-2" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 text-gray-400 flex items-center justify-center font-black transition-all duration-500">2</div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Ancrage</span>
                </div>
                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div id="step-dot-3" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 text-gray-400 flex items-center justify-center font-black transition-all duration-500">3</div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Statut</span>
                </div>
            </div>
        </div>

        <form id="multi-step-adhesion" class="p-8 md:p-16">
            <input type="hidden" name="id" id="adhesion-id" value="">
            
            <!-- STEP 1: IDENTITÉ -->
            <div id="step-content-1" class="step-content space-y-12 animate-fade-in">
                <div class="flex items-center gap-6 mb-12">
                    <div class="w-16 h-16 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center text-emerald-600 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">Identité Civil</h2>
                        <p class="text-[10px] text-gray-400 uppercase tracking-[0.4em] mt-2 font-black">Informations personnelles de base</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" required autocomplete="family-name" class="input-field" placeholder="MAGADJU">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Post-nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="post_name" required autocomplete="additional-name" class="input-field" placeholder="IRAGI">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Prénom <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" required autocomplete="given-name" class="input-field" placeholder="Samy">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Lieu de naissance</label>
                        <input type="text" name="birth_place" autocomplete="birth-place" class="input-field" placeholder="Bukavu">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Date de naissance (JJ / MM / AAAA) <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" id="birth_day" placeholder="JJ" min="1" max="31" class="input-field px-4 text-center w-20">
                            <input type="number" id="birth_month" placeholder="MM" min="1" max="12" class="input-field px-4 text-center w-20">
                            <input type="number" id="birth_year" placeholder="AAAA" min="1900" max="<?php echo date('Y') - 18; ?>" class="input-field px-4 text-center flex-1">
                        </div>
                        <p id="date-error" class="hidden text-[9px] font-black text-rose-500 uppercase mt-2 tracking-widest">Vous devez avoir au moins 18 ans pour adhérer.</p>
                        <input type="hidden" name="birth_date" id="birth_date_hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group relative">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Nationalité</label>
                        <input type="hidden" name="nationality" value="Congolaise">
                        <button type="button" class="custom-select-btn input-field text-left flex justify-between items-center" data-target="nationality">
                            <span class="value">Congolaise</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="custom-select-options absolute w-full mt-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-2xl shadow-2xl z-50 opacity-0 pointer-events-none translate-y-2 transition-all duration-300">
                            <div class="p-2 space-y-1">
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Congolaise">Congolaise</button>
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Étrangère">Étrangère</button>
                            </div>
                        </div>
                    </div>
                    <div class="group relative">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">État Civil</label>
                        <input type="hidden" name="civil_status" id="civil_status_input" value="Célibataire">
                        <button type="button" class="custom-select-btn input-field text-left flex justify-between items-center" data-target="civil_status">
                            <span class="value">Célibataire</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="custom-select-options absolute w-full mt-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-2xl shadow-2xl z-50 opacity-0 pointer-events-none translate-y-2 transition-all duration-300">
                            <div class="p-2 space-y-1">
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Célibataire">Célibataire</button>
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Marié(e)">Marié(e)</button>
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Veuf(ve)">Veuf(ve)</button>
                                <button type="button" class="option-btn w-full text-left px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors" data-value="Divorcé(e)">Divorcé(e)</button>
                            </div>
                        </div>
                    </div>
                    <div class="group transition-all duration-500" id="children-field">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Enfants à charge</label>
                        <input type="number" name="children_count" value="0" min="0" class="input-field">
                    </div>
                </div>

                <div class="group">
                    <label class="label-field group-focus-within:text-emerald-500 transition-colors">Profession / Occupation actuelle</label>
                    <input type="text" name="profession" class="input-field" placeholder="Ingénieur, Étudiant, Commerçant...">
                </div>
            </div>

            <!-- STEP 2: ADRESSE -->
            <div id="step-content-2" class="step-content hidden space-y-12 animate-fade-in">
                <div class="flex items-center gap-6 mb-12">
                    <div class="w-16 h-16 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center text-emerald-600 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">Ancrage Géo</h2>
                        <p class="text-[10px] text-gray-400 uppercase tracking-[0.4em] mt-2 font-black">Où pouvons-nous vous trouver ?</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Province</label>
                        <input type="text" name="province" autocomplete="address-level1" class="input-field" placeholder="Sud-Kivu">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Ville / District</label>
                        <input type="text" name="city_district" autocomplete="address-level2" class="input-field" placeholder="Bukavu">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Commune / Territoire</label>
                        <input type="text" name="commune_territory" class="input-field" placeholder="Ibanda">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Quartier / Secteur</label>
                        <input type="text" name="quarter_secteur" class="input-field" placeholder="Ndendere">
                    </div>
                </div>

                <div class="group">
                    <label class="label-field group-focus-within:text-emerald-500 transition-colors">Adresse précise (Av., N°, Parcelle...)</label>
                    <textarea name="address_details" rows="2" class="textarea-field" placeholder="Votre adresse exacte..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Téléphone Principal <span class="text-rose-500">*</span></label>
                        <input type="tel" name="phone1" required autocomplete="tel" class="input-field" placeholder="+243 ...">
                    </div>
                    <div class="group">
                        <label class="label-field group-focus-within:text-emerald-500 transition-colors">Téléphone Secondaire</label>
                        <input type="tel" name="phone2" autocomplete="tel-extension" class="input-field" placeholder="+243 ...">
                    </div>
                </div>

                <div class="group">
                    <label class="label-field group-focus-within:text-emerald-500 transition-colors">Adresse E-mail</label>
                    <input type="email" name="email" autocomplete="email" class="input-field" placeholder="exemple@mail.com">
                </div>
            </div>

            <!-- STEP 3: STATUT & ENGAGEMENT -->
            <div id="step-content-3" class="step-content hidden space-y-12 animate-fade-in">
                <div class="flex items-center gap-6 mb-12">
                    <div class="w-16 h-16 bg-emerald-600 text-white rounded-[2rem] flex items-center justify-center shadow-xl shadow-emerald-600/30 rotate-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">Statut & Engagement</h2>
                        <p class="text-[10px] text-gray-400 uppercase tracking-[0.4em] mt-2 font-black">Finalisez votre adhésion officielle</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Left: Category & Fee -->
                    <div class="lg:col-span-7 space-y-10">
                        <div class="space-y-6">
                        <div class="space-y-6">
                            <label class="label-field text-center">Choisissez votre Statut de Membre</label>
                            <div class="flex flex-col md:flex-row gap-6">
                                <label class="flex-1 relative cursor-pointer group/btn">
                                    <input type="radio" name="member_category" value="Membre effectif" checked class="peer sr-only">
                                    <div class="h-full p-6 rounded-[2rem] border-2 border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/30 dark:peer-checked:bg-emerald-500/10 text-center flex flex-col items-center gap-3 hover:border-emerald-200">
                                        <div class="status-icon w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Membre Effectif</span>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Droit de vote</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex-1 relative cursor-pointer group/btn">
                                    <input type="radio" name="member_category" value="Membre adhérent" class="peer sr-only">
                                    <div class="h-full p-6 rounded-[2rem] border-2 border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/30 dark:peer-checked:bg-emerald-500/10 text-center flex flex-col items-center gap-3 hover:border-emerald-200">
                                        <div class="status-icon w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Membre Adhérent</span>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Soutien missions</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        </div>

                        <div class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border border-gray-100 dark:border-slate-800">
                            <label class="label-field mb-4">Frais d'Adhésion (Indiquez le montant versé)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-emerald-600 font-black text-lg">$</span>
                                <input type="text" name="membership_fee" class="input-field pl-12 text-lg" placeholder="0.00">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex gap-2">
                                    <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-lg text-[8px] font-black uppercase text-gray-400 border border-gray-100 dark:border-slate-700">USD</span>
                                    <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-lg text-[8px] font-black uppercase text-gray-400 border border-gray-100 dark:border-slate-700">CDF</span>
                                </div>
                            </div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-4 tracking-widest leading-relaxed">
                                Les frais d'adhésion servent au fonctionnement administratif et à la production de votre carte de membre.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Photo & Commitment -->
                    <div class="lg:col-span-5 space-y-10">
                        <div class="group">
                            <label class="label-field text-center mb-6">Identification Visuelle (Photo Passeport)</label>
                            <div class="relative w-48 h-64 mx-auto rounded-[2.5rem] overflow-hidden border-4 border-dashed border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-inner group/photo transition-all hover:border-emerald-500/50">
                                <input type="file" name="photo" id="photo-input" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                <div id="photo-preview" class="w-full h-full flex flex-col items-center justify-center p-6 transition-all group-hover/photo:scale-105 text-center">
                                    <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-emerald-600 mb-4 shadow-xl shadow-emerald-500/10">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <p class="text-[9px] font-black uppercase tracking-[0.1em] leading-relaxed text-gray-500">
                                        Ajouter votre photo
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-emerald-600 rounded-[3rem] text-white shadow-2xl shadow-emerald-600/30 relative overflow-hidden group/box">
                            <!-- Decorative background icon -->
                            <svg class="absolute -right-8 -bottom-8 w-40 h-40 text-white/10 rotate-12 transition-transform group-hover/box:scale-110 duration-700" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4">Serment d'Adhésion</h3>
                            <p class="text-[11px] font-bold leading-relaxed opacity-90 italic mb-8 relative z-10">
                                « Je déclare sur l'honneur que les informations fournies sont exactes, et je m'engage formellement à respecter les Statuts et le ROI de la DSM-WH ASBL. »
                            </p>
                            
                            <label class="flex items-center gap-4 cursor-pointer relative z-10 group/check">
                                <div class="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 transition-all group-hover/check:bg-white/40">
                                    <input type="checkbox" required class="peer hidden">
                                    <svg class="w-5 h-5 text-white scale-0 peer-checked:scale-100 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest">Je signe numériquement</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-20 flex items-center justify-between gap-8 pt-10 border-t border-gray-100 dark:border-slate-800">
                <button type="button" id="prev-btn" class="hidden px-10 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Précédent
                </button>
                <div class="flex-1"></div>
                <button type="button" id="next-btn" class="px-12 py-6 bg-emerald-600 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-emerald-600/30 hover:bg-emerald-700 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                    <span id="btn-text">Suivant</span>
                    <svg id="btn-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    <svg id="btn-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Final Message -->
        <div id="final-step" class="hidden p-32 text-center animate-fade-in bg-slate-50 dark:bg-slate-900/50">
            <div class="w-32 h-32 bg-emerald-500 text-white rounded-[3rem] flex items-center justify-center mx-auto mb-12 shadow-[0_20px_50px_rgba(16,185,129,0.4)] rotate-6">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-6 leading-none">Demande Soumise !</h2>
            <p class="text-gray-400 font-bold uppercase tracking-[0.2em] text-[10px] max-w-sm mx-auto leading-relaxed">
                Votre formulaire a été enregistré avec succès. Notre bureau administratif va maintenant examiner votre dossier.
            </p>
            <div class="mt-16 flex justify-center gap-6">
                <a href="<?php echo SITE_URL; ?>/" class="px-10 py-5 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-xl">Accueil</a>
                <button onclick="window.location.reload()" class="px-10 py-5 border-2 border-gray-100 dark:border-slate-800 text-gray-500 dark:text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-white dark:hover:bg-slate-800 transition-all">Nouveau dossier</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;

const form = document.getElementById('multi-step-adhesion');
const nextBtn = document.getElementById('next-btn');
const prevBtn = document.getElementById('prev-btn');
const btnText = document.getElementById('btn-text');
const btnIcon = document.getElementById('btn-icon');
const btnSpinner = document.getElementById('btn-spinner');
const adhesionIdInput = document.getElementById('adhesion-id');

// Custom Select Handling
document.querySelectorAll('.custom-select-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const options = btn.nextElementSibling;
        const isOpen = !options.classList.contains('opacity-0');
        
        // Close all other selects
        document.querySelectorAll('.custom-select-options').forEach(opt => {
            opt.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            opt.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
        });

        if (!isOpen) {
            options.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            btn.querySelector('svg').classList.add('rotate-180');
        }
    });
});

document.querySelectorAll('.option-btn').forEach(opt => {
    opt.addEventListener('click', (e) => {
        const val = opt.getAttribute('data-value');
        const container = opt.closest('.group');
        const input = container.querySelector('input[type="hidden"]');
        const display = container.querySelector('.value');
        
        input.value = val;
        display.textContent = val;
        
        // Conditional Logic for Children Field
        if (input.id === 'civil_status_input') {
            const childrenField = document.getElementById('children-field');
            if (val === 'Célibataire') {
                childrenField.style.opacity = '0';
                childrenField.style.pointerEvents = 'none';
                childrenField.style.transform = 'translateY(10px)';
                setTimeout(() => childrenField.classList.add('invisible'), 300);
            } else {
                childrenField.classList.remove('invisible');
                setTimeout(() => {
                    childrenField.style.opacity = '1';
                    childrenField.style.pointerEvents = 'auto';
                    childrenField.style.transform = 'translateY(0)';
                }, 10);
            }
        }

        // Close dropdown
        opt.closest('.custom-select-options').classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
        opt.closest('.custom-select-options').previousElementSibling.querySelector('svg').classList.remove('rotate-180');
    });
});

// Close selects on outside click
document.addEventListener('click', () => {
    document.querySelectorAll('.custom-select-options').forEach(opt => {
        opt.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
        opt.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
    });
});

// Initial State for Children Field
window.addEventListener('load', () => {
    const childrenField = document.getElementById('children-field');
    childrenField.style.opacity = '0';
    childrenField.style.pointerEvents = 'none';
    childrenField.style.transform = 'translateY(10px)';
    childrenField.classList.add('invisible');
});

function updateUI() {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById(`step-content-${currentStep}`).classList.remove('hidden');

    for (let i = 1; i <= totalSteps; i++) {
        const dot = document.getElementById(`step-dot-${i}`);
        const span = dot.nextElementSibling;
        if (i < currentStep) {
            dot.classList.remove('bg-white', 'dark:bg-slate-800', 'border-gray-200', 'dark:border-slate-700', 'text-gray-400');
            dot.classList.add('bg-emerald-500', 'text-white', 'border-emerald-500');
            dot.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
            span.classList.add('text-emerald-600');
            span.classList.remove('text-gray-400');
        } else if (i === currentStep) {
            dot.classList.add('bg-emerald-500', 'text-white', 'border-emerald-500');
            dot.classList.remove('bg-white', 'dark:bg-slate-800', 'border-gray-200', 'dark:border-slate-700', 'text-gray-400');
            dot.innerHTML = i;
            span.classList.add('text-emerald-600');
            span.classList.remove('text-gray-400');
        } else {
            dot.classList.add('bg-white', 'dark:bg-slate-800', 'border-gray-200', 'dark:border-slate-700', 'text-gray-400');
            dot.classList.remove('bg-emerald-500', 'text-white', 'border-emerald-500');
            dot.innerHTML = i;
            span.classList.remove('text-emerald-600');
            span.classList.add('text-gray-400');
        }
    }

    const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progress-line').style.width = `${progressPercent}%`;

    prevBtn.classList.toggle('hidden', currentStep === 1);
    btnText.textContent = currentStep === totalSteps ? 'Finaliser le dossier' : 'Étape suivante';
    btnIcon.classList.toggle('hidden', currentStep === totalSteps);
}

async function saveStep(finalize = false) {
    // Combine Date fields if in Step 1
    if (currentStep === 1) {
        const d = document.getElementById('birth_day').value;
        const m = document.getElementById('birth_month').value;
        const y = document.getElementById('birth_year').value;
        if (d && m && y) {
            // YYYY-MM-DD
            document.getElementById('birth_date_hidden').value = `${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
        }
    }

    const formData = new FormData(form);
    formData.append('step', currentStep);
    if (finalize) formData.append('finalize', '1');

    nextBtn.disabled = true;
    btnSpinner.classList.remove('hidden');
    btnIcon.classList.add('hidden');

    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/adhesion_save.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            adhesionIdInput.value = result.id;
            return true;
        } else {
            alert("Erreur: " + result.message);
            return false;
        }
    } catch (e) {
        alert("Erreur de connexion.");
        return false;
    } finally {
        nextBtn.disabled = false;
        btnSpinner.classList.add('hidden');
        if (currentStep < totalSteps) btnIcon.classList.remove('hidden');
    }
}

nextBtn.addEventListener('click', async () => {
    const currentContainer = document.getElementById(`step-content-${currentStep}`);
    const inputs = currentContainer.querySelectorAll('input[required], textarea[required]');
    let valid = true;
    inputs.forEach(input => {
        if (!input.value) {
            input.classList.add('border-rose-500', 'bg-rose-50');
            valid = false;
        } else {
            input.classList.remove('border-rose-500', 'bg-rose-50');
        }
    });

    if (!valid) return;

    // Additional Date Validation (18+)
    if (currentStep === 1) {
        const y = parseInt(document.getElementById('birth_year').value);
        const m = parseInt(document.getElementById('birth_month').value);
        const d = parseInt(document.getElementById('birth_day').value);
        const errorMsg = document.getElementById('date-error');
        
        if (y && m && d) {
            const birthDate = new Date(y, m - 1, d);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m_diff = today.getMonth() - birthDate.getMonth();
            if (m_diff < 0 || (m_diff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            if (age < 18) {
                errorMsg.classList.remove('hidden');
                document.getElementById('birth_year').classList.add('border-rose-500');
                return;
            } else {
                errorMsg.classList.add('hidden');
                document.getElementById('birth_year').classList.remove('border-rose-500');
            }
        }
    }

    const saved = await saveStep(currentStep === totalSteps);
    if (saved) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            form.classList.add('hidden');
            document.getElementById('final-step').classList.remove('hidden');
            document.querySelector('.bg-slate-50.dark\\:bg-slate-900\\/50').scrollIntoView({ behavior: 'smooth' });
            clearLocal();
        }
    }
});

prevBtn.addEventListener('click', () => {
    if (currentStep > 1) {
        currentStep--;
        updateUI();
    }
});

document.getElementById('photo-input').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-none transition-transform duration-700">`;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

// --- Smart Auto-fill & Local Persistence ---
const STORAGE_KEY = 'dsm_adhesion_draft';

function saveToLocal() {
    const data = {};
    const formData = new FormData(form);
    formData.forEach((value, key) => {
        if (!(value instanceof File)) data[key] = value;
    });
    // Add custom fields
    data.birth_day = document.getElementById('birth_day').value;
    data.birth_month = document.getElementById('birth_month').value;
    data.birth_year = document.getElementById('birth_year').value;
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function loadFromLocal() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;
    
    const data = JSON.parse(saved);
    Object.keys(data).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            if (input.type === 'radio') {
                if (input.value === data[key]) input.checked = true;
            } else {
                input.value = data[key];
            }
        }
    });

    // Special fields JJ/MM/AAAA
    if (data.birth_day) document.getElementById('birth_day').value = data.birth_day;
    if (data.birth_month) document.getElementById('birth_month').value = data.birth_month;
    if (data.birth_year) document.getElementById('birth_year').value = data.birth_year;

    // Trigger UI updates (like custom selects)
    document.querySelectorAll('.custom-select-btn').forEach(btn => {
        const target = btn.getAttribute('data-target');
        const hiddenInput = document.querySelector(`input[name="${target}"]`);
        if (hiddenInput && hiddenInput.value) {
            btn.querySelector('.value').textContent = hiddenInput.value;
            // Trigger conditional logic for civil status
            if (target === 'civil_status' && hiddenInput.value !== 'Célibataire') {
                const childrenField = document.getElementById('children-field');
                childrenField.classList.remove('invisible');
                childrenField.style.opacity = '1';
                childrenField.style.pointerEvents = 'auto';
                childrenField.style.transform = 'translateY(0)';
            }
        }
    });
}

// Listen to all inputs for auto-save
form.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('input', saveToLocal);
});

// Clear local storage on final success
function clearLocal() {
    localStorage.removeItem(STORAGE_KEY);
}

// Initialize
window.addEventListener('load', loadFromLocal);

updateUI();
</script>

<style>
.label-field {
    @apply block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2.5;
}
.input-field {
    @apply w-full px-6 py-5 bg-gray-50/50 dark:bg-slate-800/50 border-2 border-gray-100 dark:border-slate-700/50 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all dark:text-white placeholder:text-gray-300 dark:placeholder:text-slate-600;
}
.textarea-field {
    @apply w-full px-6 py-5 bg-gray-50/50 dark:bg-slate-800/50 border-2 border-gray-100 dark:border-slate-700/50 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all dark:text-white placeholder:text-gray-300 dark:placeholder:text-slate-600;
}

/* Custom Checked Styles for status cards */
.peer:checked + div .status-icon {
    @apply bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 !important;
}
.peer:checked + div .status-dot {
    @apply scale-100 !important;
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

/* Hide date icon but keep functionality */
input[type="date"]::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}
</style>

<?php include 'includes/footer.php'; ?>
