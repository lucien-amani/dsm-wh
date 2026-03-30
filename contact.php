<?php
require_once 'config/database.php';

$current_page = 'contact';
$page_title = 'Contact - Ir. Samy Magadju';
$page_description = 'Contactez l\'Ir. Samy Magadju pour vos questions, projets ou collaborations';

include 'includes/header.php';
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 100%; width: 100%; z-index: 1; }
    .leaflet-container { font-family: 'Inter', sans-serif; }
</style>

<!-- Page Header -->
<section class="bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Contactez-nous</h1>
        <p class="text-xl text-white/90 max-w-3xl mx-auto">
            Une question ? Un projet ? N'hésitez pas à nous contacter
        </p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Formulaire de contact -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Envoyez-nous un message</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                    Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.
                </p>

                <form id="contact-form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                            <input type="text" id="name" name="name" required
                                   class="input-field"
                                   placeholder="Votre nom">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Téléphone
                            </label>
                            <input type="tel" id="phone" name="phone"
                                   class="input-field"
                                   placeholder="+243 XXX XXX XXX">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               class="input-field"
                               placeholder="votre.email@example.com">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                            Sujet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" required
                               class="input-field"
                               placeholder="Objet de votre message">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" required rows="6"
                                  class="textarea-field"
                                  placeholder="Votre message..."></textarea>
                    </div>

                    <div id="form-message" class="hidden"></div>

                    <button type="submit" class="btn-primary w-full md:w-auto">
                        <span id="submit-text">Envoyer le message</span>
                        <span id="submit-loading" class="hidden">Envoi en cours...</span>
                    </button>
                </form>
            </div>

            <!-- Informations de contact -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Coordonnées</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                        Vous pouvez également nous joindre directement via les coordonnées ci-dessous.
                    </p>
                </div>

                <!-- Téléphone -->
                <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-xl transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[rgb(var(--color-primary))]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Téléphones</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                <a href="tel:+243993859330" class="hover:text-[rgb(var(--color-primary))] transition-colors">
                                    +243 993 859 330
                                </a>
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                <a href="tel:+243822798258" class="hover:text-[rgb(var(--color-primary))] transition-colors">
                                    +243 822 798 258
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-xl transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[rgb(var(--color-primary))]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Email</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                <a href="mailto:magadjusamybonheur@gmail.com" class="hover:text-[rgb(var(--color-primary))] transition-colors">
                                    magadjusamybonheur@gmail.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-xl transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[rgb(var(--color-primary))]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Adresse</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Bukavu, Sud-Kivu<br>
                                République Démocratique du Congo
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Horaires -->
                <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-xl transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[rgb(var(--color-primary))]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Disponibilité</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Lundi - Vendredi: 8h00 - 17h00<br>
                                Samedi: 9h00 - 13h00<br>
                                Dimanche: Fermé
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-16 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="aspect-video bg-gray-200 dark:bg-slate-800 rounded-xl overflow-hidden shadow-lg">
            <div id="map"></div>
        </div>
    </div>
</section>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.getElementById('contact-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const formMessage = document.getElementById('form-message');
    
    // Désactiver le bouton
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Récupérer les données du formulaire
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/contact.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            formMessage.className = 'p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg';
            formMessage.textContent = result.message;
            formMessage.classList.remove('hidden');
            e.target.reset();
        } else {
            formMessage.className = 'p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg';
            formMessage.textContent = result.message || 'Une erreur est survenue. Veuillez réessayer.';
            formMessage.classList.remove('hidden');
        }
    } catch (error) {
        formMessage.className = 'p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg';
        formMessage.textContent = 'Une erreur est survenue. Veuillez réessayer.';
        formMessage.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Masquer le message après 5 secondes
        setTimeout(() => {
            formMessage.classList.add('hidden');
        }, 5000);
    }
});

// Initialize Leaflet Map
document.addEventListener('DOMContentLoaded', function() {
    // Bukavu coordinates
    const bukavuCoords = [-2.5089, 28.8608];
    
    const map = L.map('map').setView(bukavuCoords, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Custom marker icon (optional, using default for now but styled)
    const marker = L.marker(bukavuCoords).addTo(map);
    marker.bindPopup("<div class='p-2'><b>Ir. Samy Magadju</b><br>Bukavu, Sud-Kivu, RDC</div>").openPopup();
});
</script>

<?php include 'includes/footer.php'; ?>
