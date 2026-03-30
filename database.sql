-- Base de données pour le site de Ir. Samy Magadju
CREATE DATABASE IF NOT EXISTS dsm_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dsm_website;

-- Table pour les actualités
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    author VARCHAR(100) DEFAULT 'Ir. Samy Magadju',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Table pour les projets
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    location VARCHAR(255),
    category VARCHAR(100),
    status ENUM(
        'En cours',
        'Terminé',
        'Planifié'
    ) DEFAULT 'En cours',
    start_date DATE,
    end_date DATE,
    budget VARCHAR(100),
    beneficiaries VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Table pour les messages de contact
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Nouveau', 'Lu', 'Traité') DEFAULT 'Nouveau',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insertion de données exemples pour les actualités
INSERT INTO
    news (
        title,
        slug,
        excerpt,
        content,
        image,
        category
    )
VALUES (
        'Élu en 2023 : Un engagement pour Bukavu et Bagira',
        'elu-2023-engagement-bukavu-bagira',
        'Iragi Magadju Samy Bonheur a été élu en 2023 pour représenter les citoyens de Bukavu, particulièrement la commune de Bagira. Une victoire qui marque le début d''une nouvelle ère de développement.',
        '<p>En 2023, les citoyens de Bukavu ont placé leur confiance en Iragi Magadju Samy Bonheur pour les représenter et porter leurs aspirations au niveau politique. Cette élection dans la commune de Bagira marque un tournant décisif dans la vie politique locale.</p><p>Fort de son expérience en tant qu''ingénieur en énergies renouvelables et de son engagement humanitaire, l''Honorable Samy Magadju apporte une vision nouvelle axée sur le développement durable et l''amélioration des conditions de vie des populations.</p><p>Son mandat se concentre sur plusieurs axes prioritaires : l''accès à l''énergie électrique, l''eau potable, l''éducation et le soutien aux personnes vulnérables.</p>',
        NULL,
        'Politique'
    ),
    (
        'Expertise en Énergies Renouvelables : Des Centrales Électriques à travers la RDC',
        'expertise-energies-renouvelables',
        'Avec une décennie d''expérience chez Nuru SARL et Weast Énergie Solaire, Ir. Samy Magadju a contribué à la construction de centrales électriques dans plusieurs provinces de la RDC.',
        '<p>L''expertise de Samy Magadju en énergies renouvelables s''est forgée au fil de nombreuses années d''expérience pratique. Travaillant pour des entreprises de renom comme Nuru SARL à Goma et Weast Énergie Solaire à Kinshasa, il a participé à la conception et à la mise en œuvre de projets énergétiques d''envergure.</p><p>Ses interventions ont touché plusieurs provinces : Lualaba, Tanganyika, Équateur, et bien sûr les deux Kivu. Ces projets ont permis d''apporter l''électricité à des milliers de foyers et d''entreprises, contribuant ainsi au développement économique de ces régions.</p><p>En tant qu''assistant d''université, il partage également son savoir avec la prochaine génération d''ingénieurs congolais, formant les leaders techniques de demain.</p>',
        NULL,
        'Énergie'
    ),
    (
        'Actions Humanitaires : Au Service des Plus Vulnérables',
        'actions-humanitaires-vulnerables',
        'Au-delà de ses fonctions politiques et professionnelles, Ir. Samy Magadju consacre une partie importante de son temps aux actions humanitaires en faveur des orphelins, veuves et personnes âgées.',
        '<p>L''engagement humanitaire de Samy Magadju ne se limite pas aux discours. Sur le terrain, il mène des actions concrètes pour améliorer le quotidien des personnes les plus vulnérables de la communauté.</p><p>Ses initiatives incluent le soutien aux orphelins par la prise en charge des frais scolaires, l''aide aux veuves et veufs pour démarrer des activités génératrices de revenus, et l''assistance aux personnes du troisième âge.</p><p>Cette dimension sociale de son action reflète sa conviction profonde que le développement ne peut être que global et inclusif, ne laissant personne de côté.</p>',
        NULL,
        'Social'
    );

-- Insertion de données exemples pour les projets
INSERT INTO
    projects (
        title,
        slug,
        description,
        content,
        image,
        location,
        category,
        status,
        beneficiaries
    )
VALUES (
        'Centrale Hydroélectrique de Lualaba',
        'centrale-hydroelectrique-lualaba',
        'Construction d''une centrale hydroélectrique pour alimenter les zones minières et résidentielles de Lualaba.',
        '<p>Le projet de centrale hydroélectrique de Lualaba représente un investissement majeur dans l''infrastructure énergétique de la province. Cette installation permettra de fournir une électricité stable et durable aux communautés locales et aux activités minières.</p><p>Les travaux ont inclus l''étude de faisabilité, la conception des installations, et la supervision de la construction. Le projet utilise les dernières technologies en matière d''énergies renouvelables pour maximiser l''efficacité tout en minimisant l''impact environnemental.</p><p>Une fois opérationnelle, cette centrale contribuera significativement au développement économique de la région en garantissant un approvisionnement énergétique fiable.</p>',
        NULL,
        'Lualaba',
        'Énergie',
        'Terminé',
        'Plus de 50 000 personnes'
    ),
    (
        'Forages d''Eau Potable au Sud-Kivu',
        'forages-eau-potable-sud-kivu',
        'Programme de forage de puits d''eau potable dans les zones rurales du Sud-Kivu pour améliorer l''accès à l''eau salubre.',
        '<p>L''accès à l''eau potable reste un défi majeur dans de nombreuses zones rurales du Sud-Kivu. Ce projet vise à répondre à cette problématique en forant des puits équipés de pompes dans les villages mal desservis.</p><p>Chaque forage est précédé d''une étude hydrogéologique pour garantir la qualité et la pérennité de la ressource. Les installations sont conçues pour être facilement entretenues par les communautés locales, assurant ainsi leur durabilité.</p><p>Ce programme s''inscrit dans une vision globale de développement sanitaire et de réduction des maladies hydriques.</p>',
        NULL,
        'Sud-Kivu',
        'Eau et Assainissement',
        'En cours',
        'Plus de 20 000 personnes'
    ),
    (
        'Installation Solaire pour Écoles Rurales',
        'installation-solaire-ecoles-rurales',
        'Équipement d''écoles rurales en panneaux solaires pour faciliter l''accès à l''éducation de qualité.',
        '<p>L''électrification des écoles rurales est essentielle pour améliorer la qualité de l''enseignement. Ce projet prévoit l''installation de systèmes solaires photovoltaïques dans une dizaine d''écoles à travers le Nord et le Sud-Kivu.</p><p>Ces installations permettront l''utilisation d''équipements pédagogiques modernes, l''éclairage des salles de classe pour les cours du soir, et l''accès à des ressources numériques.</p><p>Le projet inclut également la formation des enseignants et des techniciens locaux pour la maintenance des installations.</p>',
        NULL,
        'Nord-Kivu, Sud-Kivu',
        'Énergie & Éducation',
        'Planifié',
        '15 écoles, environ 5 000 élèves'
    );

-- Table pour les utilisateurs (administrateurs)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(150),
    role ENUM('Admin', 'Superadmin') DEFAULT 'Admin',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insertion d'un utilisateur par défaut (admin / admin123)
-- Hash généré via password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO
    users (
        username,
        password,
        full_name,
        email,
        role
    )
VALUES (
        'admin',
        '$2y$10$mC7pWDNfM8uRjD3L8G8Xp.H8x8K9j0.uH9Z0xV.xJ8X9D.O0W0W0W',
        'Administrateur',
        'admin@dsm-rdc.com',
        'Superadmin'
    );