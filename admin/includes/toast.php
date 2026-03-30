<?php
// Toast & Layout Helper
?>
<!-- Toast Container -->
<div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3"></div>

<!-- Mobile Menu Button (Fixed Position) -->
<button onclick="toggleSidebar()" class="lg:hidden fixed top-6 left-6 z-[60] p-3 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
</button>

<!-- Confirm Modal -->
<div id="confirm-modal" class="fixed inset-0 z-[150] hidden flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm">
    <div class="glass-panel max-w-sm w-full p-8 rounded-[2.5rem] border border-white/20 shadow-2xl animate-modal-in">
        <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter text-center mb-2">Confirmation</h3>
        <p id="confirm-message" class="text-sm text-slate-500 text-center mb-8 font-medium leading-relaxed"></p>
        <div class="flex gap-4">
            <button onclick="closeConfirm(false)" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Annuler</button>
            <button onclick="closeConfirm(true)" class="flex-1 px-6 py-4 bg-rose-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-rose-600/20 hover:bg-rose-700 transition-all">Confirmer</button>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const colors = {
            success: 'bg-emerald-600 shadow-emerald-500/20',
            error: 'bg-rose-600 shadow-rose-500/20',
            info: 'bg-blue-600 shadow-blue-500/20'
        };

        toast.className = `${colors[type]} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-slide-in-toast transform transition-all duration-300 min-w-[300px] border border-white/10 backdrop-blur-md`;
        
        toast.innerHTML = `
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                ${type === 'success' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : 
                  type === 'error' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' :
                  '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'}
            </div>
            <div class="flex-1">
                <p class="text-sm font-black uppercase tracking-widest">${type}</p>
                <p class="text-xs font-bold opacity-90">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="p-1 hover:bg-white/10 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    let confirmCallback = null;

    function showConfirm(message, callback) {
        document.getElementById('confirm-message').innerText = message;
        document.getElementById('confirm-modal').classList.remove('hidden');
        document.getElementById('confirm-modal').classList.add('flex');
        confirmCallback = callback;
    }

    function closeConfirm(result) {
        document.getElementById('confirm-modal').classList.add('hidden');
        document.getElementById('confirm-modal').classList.remove('flex');
        if (result && confirmCallback) {
            confirmCallback();
        }
        confirmCallback = null;
    }

    // Capture URL messages
    const urlParamsSearch = new URLSearchParams(window.location.search);
    if (urlParamsSearch.has('msg')) {
        const msg = urlParamsSearch.get('msg');
        if (msg === 'created') showToast('Éclatant ! Élément créé avec succès.');
        if (msg === 'updated') showToast('Parfait ! Mise à jour effectuée.');
        if (msg === 'deleted') showToast('Supprimé ! Action confirmée.', 'error');
        if (msg === 'approved') showToast('Approuvé ! Le commentaire est en ligne.');
        if (msg === 'rejected') showToast('Rejeté ! Commentaire masqué.', 'info');
    }
</script>

<style>
    @keyframes slide-in-toast {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in-toast { animation: slide-in-toast 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

    @keyframes modal-in {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-modal-in { animation: modal-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
</style>
