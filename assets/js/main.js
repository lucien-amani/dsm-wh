// Main JavaScript file for DSM website

// Mobile menu toggle (déjà géré dans header.php, mais on garde ici pour référence)
document.addEventListener('DOMContentLoaded', function () {
    // Smooth scroll pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Animation au scroll (fade in)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les éléments avec la classe 'observe-fade'
    document.querySelectorAll('.observe-fade').forEach(el => {
        observer.observe(el);
    });

    // Fermer le menu mobile lors du clic sur un lien
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');

    if (mobileMenu && mobileMenuButton) {
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }

    // Sticky header avec shadow au scroll
    const nav = document.querySelector('nav');
    if (nav) {
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }

            lastScroll = currentScroll;
        });
    }
});

// Toast Notification System
window.showToast = function (message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-6 right-6 z-[300] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto flex items-center gap-4 px-6 py-4 rounded-[1.5rem] shadow-2xl transition-all duration-500 transform translate-x-12 opacity-0 border border-white/10 backdrop-blur-2xl relative overflow-hidden group min-w-[320px]';

    // Theme configuration
    const themes = {
        success: {
            bg: 'bg-emerald-600/90',
            iconBg: 'bg-emerald-500',
            shadow: 'shadow-emerald-500/20',
            label: 'Félicitations',
            icon: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
        },
        error: {
            bg: 'bg-rose-600/90',
            iconBg: 'bg-rose-500',
            shadow: 'shadow-rose-500/20',
            label: 'Erreur',
            icon: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'
        },
        info: {
            bg: 'bg-amber-500/90',
            iconBg: 'bg-amber-400',
            shadow: 'shadow-amber-500/20',
            label: 'Information',
            icon: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        },
        warning: {
            bg: 'bg-orange-500/90',
            iconBg: 'bg-orange-400',
            shadow: 'shadow-orange-500/20',
            label: 'Attention',
            icon: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        }
    };

    const theme = themes[type] || themes.info;

    toast.classList.add(theme.bg, theme.shadow);

    toast.innerHTML = `
        <div class="w-10 h-10 rounded-xl ${theme.iconBg} flex items-center justify-center shrink-0 shadow-lg border border-white/20">
            ${theme.icon}
        </div>
        <div class="flex-1 pr-4">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/60 mb-0.5">${theme.label}</p>
            <p class="text-sm font-bold text-white leading-tight">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-white/40 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <!-- Progress bar -->
        <div class="absolute bottom-0 left-0 h-1 bg-white/30 w-full">
            <div class="h-full bg-white/50 w-full animate-progress" style="animation: progress 5s linear forwards"></div>
        </div>
        <style>
            @keyframes progress { from { width: 100%; } to { width: 0%; } }
        </style>
    `;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-12', 'opacity-0');
    });

    // Auto remove
    const timeout = setTimeout(() => {
        toast.classList.add('translate-x-12', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 5000);

    // Pause on hover
    toast.addEventListener('mouseenter', () => {
        const bar = toast.querySelector('.animate-progress');
        if (bar) bar.style.animationPlayState = 'paused';
        clearTimeout(timeout);
    });
};

function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.showToast('Copié dans le presse-papier !', 'success');
    }).catch(err => {
        console.error('Erreur lors de la copie : ', err);
    });
}
