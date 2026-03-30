# Site Web - Ir. Samy Magadju

Site web officiel de l'Honorable Iragi Magadju Samy Bonheur, Ingénieur en Génie Électrique et élu de Bukavu.

## 🚀 Technologies Utilisées

- **Backend**: PHP 7.4+
- **Base de données**: MySQL
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript
- **Serveur local**: XAMPP

## 📋 Prérequis

- XAMPP (ou équivalent avec PHP 7.4+ et MySQL)
- Navigateur web moderne
- Node.js (pour Tailwind CSS)

## 🛠️ Installation

### 1. Base de données

1. Démarrez MySQL dans XAMPP
2. Importez le fichier `database.sql` dans phpMyAdmin :
   - Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
   - Créez une nouvelle base de données ou utilisez le fichier SQL qui le fera automatiquement
   - Importez `database.sql`

### 2. Configuration

1. Vérifiez les paramètres de connexion dans `config/database.php`
2. Assurez-vous que les informations correspondent à votre configuration MySQL

### 3. Tailwind CSS

Le projet utilise Tailwind CSS compilé localement. Un watcher est déjà en cours d'exécution.

Si vous devez le relancer :
```bash
npx tailwindcss -i ./src/input.css -o ./dist/output.css --watch
```

### 4. Permissions

Assurez-vous que le dossier `uploads/` a les permissions d'écriture :
```bash
chmod 755 uploads/
```

## 📁 Structure du Projet

```
dsm/
├── api/                    # Endpoints API
│   └── contact.php         # API formulaire de contact
├── assets/                 # Ressources statiques
│   └── js/
│       └── main.js         # JavaScript principal
├── config/                 # Configuration
│   └── database.php        # Configuration BDD
├── dist/                   # CSS compilé
│   └── output.css          # Tailwind CSS compilé
├── includes/               # Fichiers réutilisables
│   ├── header.php          # En-tête
│   └── footer.php          # Pied de page
├── src/                    # Sources
│   └── input.css           # Tailwind CSS source
├── uploads/                # Images uploadées
├── index.php               # Page d'accueil
├── about.php               # À propos
├── news.php                # Liste des actualités
├── news-detail.php         # Détail actualité
├── projects.php            # Liste des projets
├── project-detail.php      # Détail projet
├── contact.php             # Contact
├── database.sql            # Schéma de la base de données
└── logo_dsm.png            # Logo du site
```

## 🎨 Personnalisation

### Couleurs

Les couleurs principales sont définies dans `src/input.css` :
- **Vert principal** : `rgb(34, 139, 34)` - Inspiré du logo
- **Vert foncé** : `rgb(25, 105, 25)`
- **Gris foncé** : `rgb(33, 33, 33)`

### Contenu

Pour modifier le contenu :
1. **Actualités** : Ajoutez des entrées dans la table `news`
2. **Projets** : Ajoutez des entrées dans la table `projects`
3. **Informations biographiques** : Modifiez `about.php`

## 🔧 Fonctionnalités

- ✅ Page d'accueil avec présentation
- ✅ Section À propos détaillée
- ✅ Actualités avec pagination et filtres
- ✅ Projets avec filtres par statut
- ✅ Formulaire de contact fonctionnel
- ✅ Design responsive (mobile, tablette, desktop)
- ✅ Partage social (Facebook, Twitter, WhatsApp)
- ✅ Compteur de vues pour les actualités
- ✅ Articles similaires/projets similaires
- ✅ SEO optimisé

## 📱 Responsive Design

Le site est entièrement responsive et optimisé pour :
- 📱 Mobile (< 768px)
- 📱 Tablette (768px - 1024px)
- 💻 Desktop (> 1024px)

## 🌐 Navigation

- **Accueil** : Vue d'ensemble avec statistiques et dernières actualités
- **À propos** : Biographie, parcours, expérience et engagement
- **Actualités** : Blog style "Le Monde" avec articles
- **Projets** : Portfolio des réalisations
- **Contact** : Formulaire et coordonnées

## 📧 Contact

Pour toute question concernant le site :
- **Email** : magadjusamybonheur@gmail.com
- **Téléphone** : +243 993 859 330

## 📄 Licence

© 2026 Ir. Samy Magadju. Tous droits réservés.
