<?php
require_once 'config/database.php';

$current_page = 'about';
$page_title = 'À propos - DSM / Wema ni Hakiba ASBL';
$page_description = 'Découvrez Wema ni Hakiba ASBL, fondée à Bukavu en Janvier 2024 : sa vision, ses 5 engagements majeurs, le parcours de son fondateur Hon. Samy Magadju et son équipe multidisciplinaire.';

include 'includes/header.php';
?>

<!-- ===== ANIMATIONS & SVG STYLES ===== -->
<style>
/* ---- Reveal on scroll ---- */
.reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.7s cubic-bezier(.22,1,.36,1), transform 0.7s cubic-bezier(.22,1,.36,1); }
.reveal.from-left  { transform: translateX(-50px) translateY(0); }
.reveal.from-right { transform: translateX(50px)  translateY(0); }
.reveal.from-scale { transform: scale(0.88) translateY(0); }
.reveal.visible { opacity: 1 !important; transform: translateX(0) translateY(0) scale(1) !important; }

/* ---- Stagger delay helpers ---- */
.delay-100 { transition-delay: .10s !important; }
.delay-200 { transition-delay: .20s !important; }
.delay-300 { transition-delay: .30s !important; }
.delay-400 { transition-delay: .40s !important; }
.delay-500 { transition-delay: .50s !important; }

/* ---- Hero particles ---- */
@keyframes float-y { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-18px)} }
@keyframes float-x { 0%,100%{transform:translateX(0)} 50%{transform:translateX(14px)} }
@keyframes spin-slow { to { transform: rotate(360deg); } }
@keyframes pulse-ring { 0%{box-shadow:0 0 0 0 rgba(255,255,255,.4)} 70%{box-shadow:0 0 0 20px rgba(255,255,255,0)} 100%{box-shadow:0 0 0 0 rgba(255,255,255,0)} }
@keyframes draw-line { from{stroke-dashoffset:1000} to{stroke-dashoffset:0} }

.hero-particle { position:absolute; border-radius:50%; opacity:.18; pointer-events:none; }
.hero-particle.p1 { width:120px;height:120px; background:white; top:10%; left:5%; animation: float-y 6s ease-in-out infinite; }
.hero-particle.p2 { width:60px; height:60px;  background:white; top:60%; left:15%; animation: float-y 8s ease-in-out infinite 1s; }
.hero-particle.p3 { width:90px; height:90px;  background:white; top:20%; right:8%; animation: float-x 7s ease-in-out infinite .5s; }
.hero-particle.p4 { width:40px; height:40px;  background:white; bottom:20%; right:20%; animation: float-y 5s ease-in-out infinite 2s; }
.hero-particle.p5 { width:200px;height:200px; background:white; bottom:-60px; left:40%; animation: float-x 10s ease-in-out infinite; }

/* ---- Hero text entrance ---- */
@keyframes hero-in { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
.hero-title { animation: hero-in .9s cubic-bezier(.22,1,.36,1) both; }
.hero-sub   { animation: hero-in .9s cubic-bezier(.22,1,.36,1) .25s both; }
.hero-scroll-hint { animation: hero-in .9s cubic-bezier(.22,1,.36,1) .5s both; }
@keyframes bounce-arrow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(8px)} }
.bounce-arrow { animation: bounce-arrow 1.6s ease-in-out infinite; }

/* ---- SVG wave ---- */
.wave-svg { display:block; }

/* ---- Quote pulse border ---- */
@keyframes border-pulse { 0%,100%{border-color:rgb(var(--color-primary))} 50%{border-color:rgb(var(--color-primary-dark))} }
.quote-block { animation: border-pulse 3s ease-in-out infinite; }

/* ---- Pillar cards hover lift ---- */
.pillar-card { transition: transform .35s cubic-bezier(.22,1,.36,1), box-shadow .35s ease; }
.pillar-card:hover { transform: translateY(-8px); box-shadow: 0 24px 48px -12px rgba(var(--color-primary),.25); }

/* ---- Engagement rows hover ---- */
.engagement-row { transition: transform .3s ease, box-shadow .3s ease; }
.engagement-row:hover { transform: translateX(6px); }

/* ---- Timeline dot pulse ---- */
@keyframes dot-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(var(--color-primary),.5)} 50%{box-shadow:0 0 0 10px rgba(var(--color-primary),0)} }
.timeline-dot { animation: dot-pulse 2.5s ease-in-out infinite; }

/* ---- Team card icon spin-on-hover ---- */
.team-card:hover .team-icon { animation: spin-slow 1.5s linear infinite; }

/* ---- Partner card float ---- */
.partner-card { transition: transform .35s cubic-bezier(.22,1,.36,1), background .3s, box-shadow .35s; }
.partner-card:hover { transform: translateY(-10px); box-shadow: 0 32px 60px -16px rgba(0,0,0,.4); }

/* ---- Counter number ---- */
.counter-num { display:inline-block; transition: transform .3s; }
.counter-num:hover { transform: scale(1.15); }

/* ---- Section label badge bounce ---- */
@keyframes badge-in { from{opacity:0;transform:scale(.7)} to{opacity:1;transform:scale(1)} }
.badge-label.visible { animation: badge-in .5s cubic-bezier(.34,1.56,.64,1) both; }

/* ---- SVG line draw ---- */
.svg-draw { stroke-dasharray: 1000; stroke-dashoffset: 1000; transition: stroke-dashoffset 1.8s cubic-bezier(.22,1,.36,1); }
.svg-draw.visible { stroke-dashoffset: 0; }

/* ---- Smooth section transitions ---- */
section { overflow: hidden; }
</style>


<!-- Page Header -->
<section class="bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] text-white relative" style="padding: 5rem 0 0;">
    <!-- Floating particles -->
    <div class="hero-particle p1"></div>
    <div class="hero-particle p2"></div>
    <div class="hero-particle p3"></div>
    <div class="hero-particle p4"></div>
    <div class="hero-particle p5"></div>

    <!-- Rotating decorative SVG ring -->
    <svg class="absolute top-6 right-10 w-24 h-24 opacity-10" style="animation:spin-slow 18s linear infinite" viewBox="0 0 100 100" fill="none">
        <circle cx="50" cy="50" r="45" stroke="white" stroke-width="2" stroke-dasharray="8 6"/>
        <circle cx="50" cy="50" r="28" stroke="white" stroke-width="1.5" stroke-dasharray="4 4"/>
    </svg>
    <svg class="absolute bottom-16 left-8 w-16 h-16 opacity-10" style="animation:spin-slow 12s linear infinite reverse" viewBox="0 0 100 100" fill="none">
        <polygon points="50,5 95,75 5,75" stroke="white" stroke-width="3" fill="none"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 pb-16">
        <h1 class="text-4xl md:text-6xl font-bold mb-5 hero-title">À Propos</h1>
        <p class="text-xl text-white/90 max-w-3xl mx-auto hero-sub">
            Dynamique Samy Magadju / Wema ni Hakiba ASBL<br>
            <em class="font-light">— être debout, aux côtés de ceux qui en ont besoin</em>
        </p>
        <!-- Scroll hint -->
        <div class="mt-10 hero-scroll-hint">
            <svg class="w-8 h-8 mx-auto text-white/60 bounce-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    <!-- SVG Wave separator -->
    <div class="wave-svg -mb-1">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" class="w-full h-16 md:h-20">
            <path fill="white" fill-opacity="1" d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z" class="dark:fill-slate-900"/>
        </svg>
    </div>
</section>

<!-- ===== SECTION 1 : Signification & Historique de Wema ni Hakiba ===== -->
<section class="py-20 bg-white dark:bg-slate-900 transition-colors relative">
    <!-- SVG background decorator -->
    <svg class="absolute top-0 right-0 w-64 h-64 opacity-[0.03] pointer-events-none" viewBox="0 0 200 200" fill="none">
        <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="2" class="text-[rgb(var(--color-primary))]"/>
        <circle cx="100" cy="100" r="60" stroke="currentColor" stroke-width="1.5" class="text-[rgb(var(--color-primary))]"/>
        <circle cx="100" cy="100" r="30" stroke="currentColor" stroke-width="1" class="text-[rgb(var(--color-primary))]"/>
        <line x1="10" y1="100" x2="190" y2="100" stroke="currentColor" stroke-width="1" class="text-[rgb(var(--color-primary))]"/>
        <line x1="100" y1="10" x2="100" y2="190" stroke="currentColor" stroke-width="1" class="text-[rgb(var(--color-primary))]"/>
    </svg>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Signification du nom -->
        <div class="max-w-4xl mx-auto text-center mb-16 reveal">
            <span class="inline-block px-4 py-1 rounded-full bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] text-sm font-semibold mb-4 tracking-wide uppercase badge-label">Notre Identité</span>
            <h2 class="section-title inline-block mb-6">"Wema ni Hakiba"</h2>
            <div class="quote-block bg-gradient-to-r from-[rgb(var(--color-primary))]/5 to-[rgb(var(--color-primary-dark))]/10 dark:from-[rgb(var(--color-primary))]/10 dark:to-[rgb(var(--color-primary-dark))]/20 p-8 rounded-2xl border-l-4 border-[rgb(var(--color-primary))] text-left">
                <p class="text-2xl font-bold text-[rgb(var(--color-primary))] italic mb-4">
                    « Être debout, aux côtés de ceux qui en ont besoin. »
                </p>
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                    Ce nom n'est pas un simple label — c'est une <strong>promesse solennelle</strong> faite à la population de ne jamais détourner le regard, quelles que soient les circonstances. Il exprime l'engagement de rester présent, debout et solidaire, là où la communauté a besoin d'un soutien concret et d'une voix portée haut.
                </p>
            </div>
        </div>

        <!-- Historique -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
            <div class="reveal from-left">
                <span class="inline-block px-4 py-1 rounded-full bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] text-sm font-semibold mb-4 tracking-wide uppercase">Notre Histoire</span>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Née à Bukavu, en janvier 2024</h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                    L'association <strong>Wema ni Hakiba ASBL</strong> est née en <strong>janvier 2024 à Bukavu</strong> de la volonté de citoyens engagés, convaincus que le développement doit venir de la base. Face aux défis socioéconomiques persistants de l'Est de la RDC, ils ont choisi l'action collective plutôt que l'attente.
                </p>
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                    En refusant le fatalisme et en croyant en la capacité des communautés à se prendre en charge, ces citoyens ont forgé une structure qui place la <strong>dignité humaine</strong> et la <strong>solidarité active</strong> au cœur de chaque initiative.
                </p>
            </div>
            <div class="space-y-4 reveal from-right stagger-parent">
                <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 p-5 rounded-xl">
                    <div class="w-10 h-10 bg-[rgb(var(--color-primary))]/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white">Fondée en janvier 2024</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">À Bukavu, Sud-Kivu, République Démocratique du Congo</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 p-5 rounded-xl">
                    <div class="w-10 h-10 bg-[rgb(var(--color-primary))]/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white">Citoyens engagés</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Une coordination multidisciplinaire : Enseignants, Médecins, Juristes, Journalistes, Ingénieurs, ...etc </p>
                    </div>
                </div>
                <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 p-5 rounded-xl">
                    <div class="w-10 h-10 bg-[rgb(var(--color-primary))]/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white">Développement par la base</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Chaque action part des besoins réels des communautés, avec leurs membres comme acteurs principaux</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Les 3 Piliers de la Vision -->
        <div>
            <div class="text-center mb-12 reveal">
                <span class="inline-block px-4 py-1 rounded-full bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] text-sm font-semibold mb-4 tracking-wide uppercase badge-label">Notre Vision</span>
                <h2 class="section-title inline-block">Les 3 Piliers Fondateurs</h2>
                <p class="mt-4 text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Une vision claire, trois axes d'action pour transformer les communautés de l'intérieur.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-parent">
                <!-- Pilier 1 -->
                <div class="pillar-card relative overflow-hidden bg-gradient-to-br from-[rgb(var(--color-primary))]/5 to-[rgb(var(--color-primary-dark))]/10 dark:from-slate-800 dark:to-slate-750 p-8 rounded-2xl border border-[rgb(var(--color-primary))]/20 group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[rgb(var(--color-primary))]/5 rounded-full -translate-y-16 translate-x-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-[rgb(var(--color-primary))]/15 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-[rgb(var(--color-primary))] uppercase tracking-widest mb-2 block">Pilier 01</span>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Renforcement des liens communautaires</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Recréer la confiance entre citoyens et tisser des solidarités durables qui résistent aux crises. Parce que la communauté soudée est la première protection de chacun.
                        </p>
                    </div>
                </div>
                <!-- Pilier 2 -->
                <div class="relative overflow-hidden bg-gradient-to-br from-[rgb(var(--color-primary))]/5 to-[rgb(var(--color-primary-dark))]/10 dark:from-slate-800 dark:to-slate-750 p-8 rounded-2xl border border-[rgb(var(--color-primary))]/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[rgb(var(--color-primary))]/5 rounded-full -translate-y-16 translate-x-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-[rgb(var(--color-primary))]/15 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-[rgb(var(--color-primary))] uppercase tracking-widest mb-2 block">Pilier 02</span>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Cohésion des populations</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Rassembler par le dialogue et l'action commune pour apaiser les conflits et construire une paix sociale durable dans les zones les plus fragilisées.
                        </p>
                    </div>
                </div>
                <!-- Pilier 3 -->
                <div class="relative overflow-hidden bg-gradient-to-br from-[rgb(var(--color-primary))]/5 to-[rgb(var(--color-primary-dark))]/10 dark:from-slate-800 dark:to-slate-750 p-8 rounded-2xl border border-[rgb(var(--color-primary))]/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[rgb(var(--color-primary))]/5 rounded-full -translate-y-16 translate-x-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-[rgb(var(--color-primary))]/15 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-[rgb(var(--color-primary))] uppercase tracking-widest mb-2 block">Pilier 03</span>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Développement à la base</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Donner aux communautés les outils, les connaissances et les ressources pour être actrices de leur propre destin — et non de simples bénéficiaires.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ===== SECTION 2 : Nos 5 Engagements Majeurs ===== -->
<section class="py-20 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block px-4 py-1 rounded-full bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] text-sm font-semibold mb-4 tracking-wide uppercase">Nos Domaines d'Action</span>
            <h2 class="section-title inline-block">Nos 5 Engagements Majeurs</h2>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Cinq verbes d'action pour changer des vies — concrètement, durablement, ensemble.
            </p>
        </div>

        <div class="space-y-6">

            <!-- Engagement 1 : PROTÉGER -->
            <div class="engagement-row reveal group flex flex-col lg:flex-row gap-0 bg-white dark:bg-slate-800 rounded-2xl shadow-md dark:border dark:border-white/5">
                <div class="lg:w-2 bg-red-500 flex-shrink-0"></div>
                <div class="flex flex-col md:flex-row gap-6 p-8 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl font-black text-red-500 tracking-tight">PROTÉGER</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                            Défense des <strong>droits des femmes, des enfants et des personnes handicapées</strong> — les plus exposés aux abus. Accompagnement juridique et psychologique des victimes de violences pour qu'elles retrouvent dignité et perspectives d'avenir.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engagement 2 : NOURRIR -->
            <div class="engagement-row reveal delay-100 group flex flex-col lg:flex-row gap-0 bg-white dark:bg-slate-800 rounded-2xl shadow-md dark:border dark:border-white/5">
                <div class="lg:w-2 bg-amber-500 flex-shrink-0"></div>
                <div class="flex flex-col md:flex-row gap-6 p-8 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl font-black text-amber-500 tracking-tight">NOURRIR</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                            Lutte contre l'<strong>insécurité alimentaire</strong> en aidant les communautés vulnérables à produire par elles-mêmes. Nous croyons que la souveraineté alimentaire est le premier pas vers l'autonomie. Des jardins communautaires aux formations agricoles, chaque action remet la dignité alimentaire entre les mains des familles.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engagement 3 : ÉDUQUER -->
            <div class="engagement-row reveal delay-200 group flex flex-col lg:flex-row gap-0 bg-white dark:bg-slate-800 rounded-2xl shadow-md dark:border dark:border-white/5">
                <div class="lg:w-2 bg-blue-500 flex-shrink-0"></div>
                <div class="flex flex-col md:flex-row gap-6 p-8 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl font-black text-blue-500 tracking-tight">ÉDUQUER</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                            <strong>Formation technique des jeunes</strong> pour les armer contre la manipulation et le désœuvrement. Soutien à l'entrepreneuriat pour transformer les compétences en revenus. Éducation des <strong>personnes du troisième âge</strong> pour leur permettre de participer activement à la vie sociale et numérique de leur communauté.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engagement 4 : SOIGNER ET INFORMER -->
            <div class="engagement-row reveal delay-300 group flex flex-col lg:flex-row gap-0 bg-white dark:bg-slate-800 rounded-2xl shadow-md dark:border dark:border-white/5">
                <div class="lg:w-2 bg-teal-500 flex-shrink-0"></div>
                <div class="flex flex-col md:flex-row gap-6 p-8 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-teal-50 dark:bg-teal-900/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl font-black text-teal-500 tracking-tight">SOIGNER &amp; INFORMER</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                            Prévention du <strong>VIH/SIDA</strong> et éducation sur la santé de la reproduction dans les communautés les plus isolées. Informer, c'est déjà soigner : nos campagnes de sensibilisation brisent les tabous et donnent à chaque personne les connaissances nécessaires pour se protéger.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engagement 5 : PRÉSERVER -->
            <div class="engagement-row reveal delay-400 group flex flex-col lg:flex-row gap-0 bg-white dark:bg-slate-800 rounded-2xl shadow-md dark:border dark:border-white/5">
                <div class="lg:w-2 bg-green-600 flex-shrink-0"></div>
                <div class="flex flex-col md:flex-row gap-6 p-8 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl font-black text-green-600 tracking-tight">PRÉSERVER</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                            Protection de <strong>l'environnement et des ressources naturelles</strong> pour garantir la cohésion autour d'un patrimoine commun. Les conflits pour les ressources fragilisent les communautés ; nous travaillons à leur gestion pacifique et durable pour les générations à venir.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== SECTION 3 : Biographie du Fondateur ===== -->
<section class="py-20 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="reveal from-left">
                <div class="aspect-square rounded-2xl overflow-hidden shadow-xl" style="transition: box-shadow .4s ease">
                    <img src="<?php echo SITE_URL; ?>/uploads/profil.jpg" alt="Hon. Samy Magadju" class="w-full h-full object-cover" style="transition: transform .6s cubic-bezier(.22,1,.36,1)" onmouseenter="this.style.transform='scale(1.04)'" onmouseleave="this.style.transform='scale(1)'">
                </div>
            </div>
            <div class="space-y-6 reveal from-right">
                <div>
                    <span class="inline-block px-4 py-1 rounded-full bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] text-sm font-semibold mb-4 tracking-wide uppercase">Le Fondateur</span>
                    <h2 class="section-title">Iragi Magadju Samy Bonheur</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mt-2">Hon. &amp; Ingénieur Électricien</p>
                </div>
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                    Né le <strong>22 juillet 1986</strong> à Kalehe, Sud-Kivu, Samy Magadju est un 
                    Ingénieur en génie Électrique avec plus de 15ans d’experience professionnel  
                    notamment chez <strong>NURU SARL</strong>,<strong>Weast Énergie Solaire&Eau</strong>,
                    <strong>CEI SARL</strong>,<strong>BEC Sarl</strong>,... 
                    avec contribution exceptionnelle pour la conception,dimensionnement et
                     réalisation des centrales hydroélectriques et solaires photovoltaïques 
                     en Afrique Subsaharienne et particulièrement en République démocratique 
                     du Congo dans les provinces  du Sud-Kivu,Nord kivu,Maniema,Kinshasa,Kasai 
                     Orientale,Lualaba,Haut Lomami,Tanganyika,Haut Katanga,Ituri,Tshopo,...
                </p>
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                    Marié et père de cinq enfants, il incarne une génération qui <strong>refuse le fatalisme</strong> et croit en une nation qui se construit <em>« par et pour sa jeunesse »</em>. Son engagement politique naît de cette conviction : les compétences techniques doivent être mises au service de la communauté, pas seulement du marché.
                </p>
                <div class="bg-[rgb(var(--color-primary))]/5 dark:bg-[rgb(var(--color-primary))]/10 p-6 rounded-xl border-l-4 border-[rgb(var(--color-primary))]">
                    <p class="text-lg italic text-gray-700 dark:text-gray-300">
                        "L'action politique n'est pas une fin en soi, mais un levier pour améliorer le quotidien de nos concitoyens et bâtir un avenir meilleur pour tous."
                    </p>
                    <p class="mt-3 text-sm font-semibold text-[rgb(var(--color-primary))]">— Hon. Samy Magadju, Fondateur</p>
                </div>
            </div>
        </div>

        <!-- Parcours professionnel -->
        <div class="mt-16 max-w-5xl mx-auto space-y-8">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white text-center mb-8">Parcours Professionnel & Académique</h3>

            <!-- Nuru SARL -->
            <div class="relative pl-8 border-l-4 border-[rgb(var(--color-primary))] reveal">
                <div class="timeline-dot absolute -left-3 top-0 w-6 h-6 rounded-full bg-[rgb(var(--color-primary))]"></div>
                <div class="bg-gray-50 dark:bg-slate-800 p-8 rounded-xl">
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">Nuru SARL</h4>
                        <span class="px-4 py-1 bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] rounded-full text-sm font-semibold">Goma</span>
                    </div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4"><strong>Ingénieur en énergies renouvelables</strong></p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Participation active à la conception et à la mise en œuvre de projets énergétiques d'envergure, contribuant à l'électrification de nombreuses zones dans l'Est de la RDC. Expertise particulière dans les centrales hydroélectriques et les installations solaires — un travail concret, au service de communautés trop longtemps privées d'électricité.
                    </p>
                </div>
            </div>

            <!-- West Énergie Solaire -->
            <div class="relative pl-8 border-l-4 border-[rgb(var(--color-primary))] reveal">
                <div class="timeline-dot absolute -left-3 top-0 w-6 h-6 rounded-full bg-[rgb(var(--color-primary))]"></div>
                <div class="bg-gray-50 dark:bg-slate-800 p-8 rounded-xl">
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">West Énergie Solaire</h4>
                        <span class="px-4 py-1 bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] rounded-full text-sm font-semibold">Kinshasa</span>
                    </div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4"><strong>Ingénieur spécialisé en énergie solaire</strong></p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Développement et supervision de projets d'électrification par énergie solaire à travers plusieurs provinces de la RDC (Lualaba, Tanganyika, Équateur, Nord-Kivu, Sud-Kivu). Contribution significative au développement des infrastructures énergétiques nationales — preuve que les ressources du pays peuvent bâtir son avenir.
                    </p>
                </div>
            </div>

            <!-- Formation -->
            <div class="relative pl-8 border-l-4 border-[rgb(var(--color-primary))] reveal">
                <div class="timeline-dot absolute -left-3 top-0 w-6 h-6 rounded-full bg-[rgb(var(--color-primary))]"></div>
                <div class="bg-gray-50 dark:bg-slate-800 p-8 rounded-xl">
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">ISTA Goma</h4>
                        <span class="px-4 py-1 bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] rounded-full text-sm font-semibold">Formation universitaire</span>
                    </div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4"><strong>Ingénieur en Génie Électrique — Énergies Renouvelables</strong></p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Diplômé en génie électrique, spécialisé en énergies renouvelables. Actuellement en préparation d'une maîtrise pour approfondir son expertise. Également <strong>assistant d'université</strong>, il transmet son savoir à la future génération d'ingénieurs congolais.
                    </p>
                </div>
            </div>

            <!-- Engagement Politique -->
            <div class="relative pl-8 border-l-4 border-[rgb(var(--color-primary))] reveal">
                <div class="timeline-dot absolute -left-3 top-0 w-6 h-6 rounded-full bg-[rgb(var(--color-primary))]"></div>
                <div class="bg-gray-50 dark:bg-slate-800 p-8 rounded-xl">
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">Député — Bukavu, Commune de Bagira</h4>
                        <span class="px-4 py-1 bg-[rgb(var(--color-primary))]/10 text-[rgb(var(--color-primary))] rounded-full text-sm font-semibold">Élu 2023</span>
                    </div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4"><strong>Représentant du peuple</strong></p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        En 2023, les citoyens de Bukavu — particulièrement ceux de la commune de Bagira — ont placé leur confiance en lui. Cette élection marque l'aboutissement d'un engagement de longue date et le début d'une ère ancrée dans l'accès à l'électricité, à l'eau potable, à l'éducation de qualité et à la bonne gouvernance.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-50 dark:bg-slate-950 transition-colors relative overflow-hidden">
    <!-- Animated background SVG -->
    <svg class="absolute inset-0 w-full h-full opacity-[0.03] pointer-events-none" preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 200" fill="none">
        <path d="M0,100 Q200,20 400,100 T800,100" stroke="currentColor" stroke-width="2" class="text-[rgb(var(--color-primary))] svg-draw"/>
        <path d="M0,140 Q200,60 400,140 T800,140" stroke="currentColor" stroke-width="1" class="text-[rgb(var(--color-primary))] svg-draw" style="transition-delay:.3s"/>
    </svg>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 stagger-parent">
            <div class="text-center p-6 rounded-xl bg-white dark:bg-slate-800 hover:bg-[rgb(var(--color-primary))]/5 transition-colors shadow-sm">
                <div class="text-4xl font-bold text-[rgb(var(--color-primary))] mb-2">
                    <span class="counter-num" data-target="10" data-suffix="+">10+</span>
                </div>
                <div class="text-gray-600 dark:text-gray-400 font-medium">Années d'expérience</div>
            </div>
            <div class="text-center p-6 rounded-xl bg-white dark:bg-slate-800 hover:bg-[rgb(var(--color-primary))]/5 transition-colors shadow-sm">
                <div class="text-4xl font-bold text-[rgb(var(--color-primary))] mb-2">
                    <span class="counter-num" data-target="2024" data-suffix="">2024</span>
                </div>
                <div class="text-gray-600 dark:text-gray-400 font-medium">Année de création</div>
            </div>
            <div class="text-center p-6 rounded-xl bg-white dark:bg-slate-800 hover:bg-[rgb(var(--color-primary))]/5 transition-colors shadow-sm">
                <div class="text-4xl font-bold text-[rgb(var(--color-primary))] mb-2">
                    <span class="counter-num" data-target="5" data-suffix="">5</span>
                </div>
                <div class="text-gray-600 dark:text-gray-400 font-medium">Engagements majeurs</div>
            </div>
            <div class="text-center p-6 rounded-xl bg-white dark:bg-slate-800 hover:bg-[rgb(var(--color-primary))]/5 transition-colors shadow-sm">
                <div class="text-4xl font-bold text-[rgb(var(--color-primary))] mb-2">
                    <span class="counter-num" data-target="3" data-suffix="">3</span>
                </div>
                <div class="text-gray-600 dark:text-gray-400 font-medium">Piliers de vision</div>
            </div>
        </div>
    </div>
</section>


        <!-- Crédit Rédaction (Optimized) -->
        <div class="mt-20 pt-10 border-t border-gray-100 dark:border-white/5">
            <div class="max-w-3xl mx-auto bg-white dark:bg-slate-900/50 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 border border-slate-100 dark:border-white/10 shadow-xl shadow-slate-200/40 dark:shadow-none relative overflow-hidden group">
                <!-- Subtle background glow -->
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-[rgb(var(--color-primary))]/5 rounded-full blur-3xl group-hover:bg-[rgb(var(--color-primary))]/10 transition-colors duration-500"></div>

                <div class="w-14 h-14 bg-gradient-to-tr from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[rgb(var(--color-primary))]/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                
                <div class="text-center sm:text-left relative z-10">
                    <p class="text-[10px] font-black text-[rgb(var(--color-primary))] uppercase tracking-[0.2em] mb-3">Note de rédaction</p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed italic text-sm sm:text-base" 
                        <strong class="text-gray-900 dark:text-white not-italic font-bold text-base">Promesse T. Bibentyo</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 5 : Devenir Partenaire — Appel à l'Action ===== -->
<section class="py-20 bg-gradient-to-br from-slate-900 via-[rgb(var(--color-primary-dark))]/90 to-[rgb(var(--color-primary))]/80 text-white relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-48 -translate-y-48"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-48 translate-y-48"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block px-4 py-1 rounded-full bg-white/20 text-white text-sm font-semibold mb-4 tracking-wide uppercase">Rejoignez-Nous</span>
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Devenir Partenaire</h2>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">
                Wema ni Hakiba ne peut pas être debout seule. Chaque partenariat est une main tendue à la communauté entière.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 stagger-parent">

            <!-- Institutions Publiques -->
            <div class="partner-card bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Institutions Publiques</h3>
                <p class="text-white/80 leading-relaxed mb-4">
                    Mairies, ministères, services déconcentrés de l'État — nous souhaitons être un <strong>relais de terrain efficace</strong>, complémentaire à l'action publique, pour augmenter l'impact des politiques sociales.
                </p>
                <div class="pt-4 border-t border-white/20">
                    <p class="text-sm text-white/60 font-medium">→ Protocoles de collaboration, co-organisation d'événements, données terrain</p>
                </div>
            </div>

            <!-- Partenaires Techniques et Financiers -->
            <div class="partner-card bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Partenaires Techniques &amp; Financiers</h3>
                <p class="text-white/80 leading-relaxed mb-4">
                    ONG nationales et internationales, bailleurs de fonds, entreprises RSE — rejoignez une structure locale rigoureuse portée par des professionnels. Vos ressources sont transformées en <strong>impact mesurable sur le terrain</strong>.
                </p>
                <div class="pt-4 border-t border-white/20">
                    <p class="text-sm text-white/60 font-medium">→ Financement de projets, appui technique, formations, matériel</p>
                </div>
            </div>

            <!-- Citoyens -->
            <div class="partner-card bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Citoyens</h3>
                <p class="text-white/80 leading-relaxed mb-4">
                    Vous n'avez pas besoin de beaucoup pour changer les choses. En devenant <strong>bénévole, donateur</strong> ou simple relais de notre message, vous êtes déjà debout aux côtés de ceux qui en ont besoin.
                </p>
                <div class="pt-4 border-t border-white/20">
                    <p class="text-sm text-white/60 font-medium">→ Bénévolat, don ponctuel ou mensuel, partage sur les réseaux</p>
                </div>
            </div>

        </div>

        <div class="text-center">
            <a href="<?php echo $router->generate('contact'); ?>" id="btn-devenir-partenaire" class="inline-block px-10 py-4 bg-white text-[rgb(var(--color-primary))] font-bold rounded-xl hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl text-lg">
                Nous contacter pour un partenariat
            </a>
        </div>
    </div>
</section>

<!-- ===== ANIMATION SCRIPTS ===== -->
<script>
(function(){
  // --- Intersection Observer: reveal on scroll ---
  const revealEls = document.querySelectorAll('.reveal');
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if(e.isIntersecting) { e.target.classList.add('visible'); }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  revealEls.forEach(el => revealObs.observe(el));

  // --- Badge label reveal ---
  document.querySelectorAll('.badge-label').forEach(el => revealObs.observe(el));

  // --- SVG draw lines ---
  document.querySelectorAll('.svg-draw').forEach(el => revealObs.observe(el));

  // --- Counter animation ---
  function animateCounter(el, target, suffix, duration) {
    let start = 0, step = target / (duration / 16);
    const tick = () => {
      start = Math.min(start + step, target);
      el.textContent = Math.floor(start) + suffix;
      if(start < target) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }
  const counterObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if(e.isIntersecting && !e.target.dataset.counted) {
        e.target.dataset.counted = '1';
        const raw   = e.target.dataset.target || '0';
        const suffix = e.target.dataset.suffix || '';
        animateCounter(e.target, parseInt(raw), suffix, 1200);
      }
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('.counter-num[data-target]').forEach(el => counterObs.observe(el));

  // --- Stagger children inside .stagger-parent ---
  document.querySelectorAll('.stagger-parent').forEach(parent => {
    [...parent.children].forEach((child, i) => {
      child.classList.add('reveal');
      child.style.transitionDelay = (i * 0.12) + 's';
    });
  });
  // Re-observe staggered children
  document.querySelectorAll('.stagger-parent .reveal').forEach(el => revealObs.observe(el));
})();
</script>

<?php include 'includes/footer.php'; ?>
