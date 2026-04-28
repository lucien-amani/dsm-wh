-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 28 avr. 2026 à 15:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- -- Création de la base de données
-- CREATE DATABASE IF NOT EXISTS `dsm_website` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `dsm_website`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dsm_website`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `user_id`, `action`, `ip_address`, `created_at`) VALUES
(20, 2, 'connexion', '::1', '2026-04-28 09:40:52'),
(21, 2, 'création article : Le derniere volontés de Mozart', '::1', '2026-04-28 09:58:13'),
(22, 2, 'connexion', '::1', '2026-04-28 10:16:15'),
(23, 2, 'modification article : Inauguration du nouveau cen', '::1', '2026-04-28 10:19:37'),
(24, 2, 'création projet : hjkhhhhhhhhhhhhhhhhh', '::1', '2026-04-28 10:30:27'),
(25, 2, 'connexion', '::1', '2026-04-28 10:42:37'),
(26, 2, 'modification article : Inauguration du nouveau cen', '::1', '2026-04-28 10:43:44'),
(27, 2, 'création projet : NJHNJKR', '::1', '2026-04-28 10:48:47'),
(28, 2, 'connexion', '::1', '2026-04-28 11:21:10'),
(29, 2, 'désactivation éditeur : Promesse BIBENTYO', '::1', '2026-04-28 11:32:59'),
(30, 2, 'activation éditeur : Promesse BIBENTYO', '::1', '2026-04-28 11:33:04'),
(31, 2, 'création éditeur : BUKAMU', '::1', '2026-04-28 11:42:03'),
(32, 2, 'deconnexion', '::1', '2026-04-28 11:43:57'),
(33, 2, 'connexion', '::1', '2026-04-28 11:44:19'),
(34, 2, 'modification éditeur : BUKAMU', '::1', '2026-04-28 11:44:59'),
(35, 2, 'deconnexion', '::1', '2026-04-28 11:45:11'),
(36, 4, 'connexion', '::1', '2026-04-28 11:45:25'),
(37, 4, 'deconnexion', '::1', '2026-04-28 11:57:28'),
(38, 4, 'connexion', '::1', '2026-04-28 11:57:40'),
(39, 4, 'deconnexion', '::1', '2026-04-28 12:02:15'),
(40, 2, 'connexion', '::1', '2026-04-28 12:02:29');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `news_id`, `user_name`, `content`, `status`, `created_at`, `likes`) VALUES
(13, 14, 'Lucien', 'Longue vie à la france afrique', 'approved', '2026-04-28 10:15:31', 1),
(14, 14, 'chikc', 'jjjjjjjjjjjjjjjjjjjjjjjj fjkkkkkkkkkkkkkk fjf', 'rejected', '2026-04-28 10:20:37', 0);

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Nouveau','Lu','Traité') DEFAULT 'Nouveau',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Lucien AMANI BAHOGWERHE', 'luciusamani@gmail.com', '0971425029', 'Demande de renseignements', 'Salut je suis Lucien', 'Nouveau', '2026-02-21 04:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `log_exports`
--

CREATE TABLE `log_exports` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `log_exports`
--

INSERT INTO `log_exports` (`id`, `filename`, `created_at`) VALUES
(1, 'logs_export_2026_04_28_11_50_40.json', '2026-04-28 09:50:40');

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `nanoid` varchar(12) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `content` text NOT NULL,
  `embed_code` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'Ir. Samy Magadju',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published` tinyint(1) DEFAULT 1,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `news`
--

INSERT INTO `news` (`id`, `nanoid`, `author_id`, `title`, `slug`, `excerpt`, `content`, `embed_code`, `image`, `category`, `author`, `views`, `created_at`, `updated_at`, `published`, `likes`) VALUES
(13, 'EORIGtnQV4', 2, 'Le derniere volontés de Mozart', 'le-derniere-volont-es-de-mozart', 'HOLA', '<h1 class=\"fig-headline fig-pagination__hidden\">Guerre en Ukraine : un nouveau mus&eacute;e &agrave; Pyongyang expose des chars et des blind&eacute;s am&eacute;ricains, allemands et fran&ccedil;ais</h1>\r\n<p>Il est loin le temps o&ugrave; la Russie gardait le silence autour de la pr&eacute;sence de soldats nord-cor&eacute;ens engag&eacute;s dans la&nbsp;<a href=\"http://www.lefigaro.fr/international/dans-la-region-de-koursk-le-front-ukrainien-s-effondre-sous-les-assauts-russes-20250310\" target=\"_blank\" rel=\"noopener\" data-fig-type=\"NewsFlash\" data-gtm-custom-categorie=\"navigation\" data-gtm-custom-action=\"crossclick\" data-gtm-custom-label=\"Contextuel\" data-gtm-event=\"customEventSPE\" data-fig-domain=\"LEFIGARO\" data-mrf-link=\"http://www.lefigaro.fr/international/dans-la-region-de-koursk-le-front-ukrainien-s-effondre-sous-les-assauts-russes-20250310\">bataille de Koursk</a>. &Agrave; l&rsquo;&eacute;t&eacute; 2024, les Ukrainiens avaient r&eacute;ussi une perc&eacute;e spectaculaire dans cet oblast russe frontalier de l&rsquo;Ukraine. &Agrave; l&rsquo;automne, les Russes contre-attaquaient, avec l&rsquo;appui de&nbsp;<a href=\"http://www.lefigaro.fr/international/pourquoi-des-milliers-de-soldats-nord-coreens-ne-changeraient-pas-le-cours-de-la-guerre-en-ukraine-20241025\" target=\"_blank\" rel=\"noopener\" data-fig-type=\"Article\" data-gtm-custom-categorie=\"navigation\" data-gtm-custom-action=\"crossclick\" data-gtm-custom-label=\"Contextuel\" data-gtm-event=\"customEventSPE\" data-fig-domain=\"LEFIGARO\" data-mrf-link=\"http://www.lefigaro.fr/international/pourquoi-des-milliers-de-soldats-nord-coreens-ne-changeraient-pas-le-cours-de-la-guerre-en-ukraine-20241025\">quatre brigades nord-cor&eacute;ennes</a>, ce qui sera officialis&eacute; d&eacute;but 2025 par Pyongyang et Moscou, la bataille de Koursk s&rsquo;achevant en avril. Depuis, le r&eacute;gime ermite vante les hauts faits de ses soldats, passant sous silence les pertes &eacute;lev&eacute;es rapport&eacute;es par les services de renseignement occidentaux - 2000 morts sur 12.000, selon S&eacute;oul. Qu&rsquo;importe, pour Pyongyang, le symbole est ailleurs : pour la premi&egrave;re fois de son histoire, la R&eacute;publique de Cor&eacute;e a men&eacute; une op&eacute;ration militaire ext&eacute;rieure &agrave; l&rsquo;&eacute;tranger, qui plus est en Europe, &agrave; pr&egrave;s de 7000 km de ses fronti&egrave;res.</p>', '', NULL, 'Economie', 'Ir. Samy Magadju', 0, '2026-04-28 09:58:13', '2026-04-28 11:17:53', 1, 0),
(14, 'k9IdiR3xjq', 2, 'Inauguration du nouveau centre communautaire à Bagira', 'inauguration-du-nouveau-centre-communautaire-a-bagira', 'Un nouveau centre polyvalent a été inauguré ce matin à Bagira, offrant des services essentiels à la population locale.', '<p>Le projet, soutenu par la Dynamique Samy Magadju, vise &agrave; renforcer les capacit&eacute;s locales et &agrave; offrir un espace de rencontre et de formation pour les jeunes du quartier.</p>', '', '69f08f60b407a.jpg', 'Social', 'Ir. Samy Magadju', 18, '2026-04-28 10:08:11', '2026-04-28 13:19:11', 1, 4),
(15, 'A1tbqgc1n3', 2, 'Lutte contre l\'érosion : lancement des travaux de drainage', 'lutte-contre-erosion-lancement-travaux-drainage', 'Face aux récentes inondations, des travaux d\'urgence ont débuté pour sécuriser les zones à risque dans la commune de Bukavu.', 'Ces travaux de drainage sont essentiels pour prévenir les glissements de terrain et protéger les habitations des citoyens pendant la saison des pluies.', NULL, NULL, 'Environnement', 'Ir. Samy Magadju', 7, '2026-04-28 10:08:11', '2026-04-28 13:24:39', 1, 1),
(16, 'gu2xEaNiAi', 2, 'Formation sur les énergies renouvelables pour les ingénieurs locaux', 'formation-energies-renouvelables-ingenieurs-locaux', 'Une série d\'ateliers a débuté pour former les techniciens du Sud-Kivu aux nouvelles technologies solaires et hydroélectriques.', 'L\'objectif est de créer une main-d\'œuvre qualifiée capable de maintenir et de développer les infrastructures énergétiques de la région.', NULL, NULL, 'Énergie', 'Ir. Samy Magadju', 1, '2026-04-28 10:08:11', '2026-04-28 13:24:31', 1, 0),
(17, 'drsxQFgECq', 2, 'Plaidoyer pour une meilleure gestion de l\'eau potable', 'plaidoyer-meilleure-gestion-eau-potable', 'L\'Honorable Samy Magadju a porté la voix des sinistrés auprès des autorités compétentes pour résoudre la crise de l\'eau.', 'Il est impératif que chaque foyer ait accès à une eau propre et abordable. La Dynamique continue de surveiller de près l\'avancement des promesses gouvernementales.', NULL, NULL, 'Politique', 'Ir. Samy Magadju', 2, '2026-04-28 10:08:11', '2026-04-28 13:24:23', 1, 1),
(18, 'ZeTnUjYLcW', 2, 'Soutien aux micro-entrepreneurs du Sud-Kivu', 'soutien-micro-entrepreneurs-sud-kivu', 'Un nouveau programme de micro-crédit a été lancé pour aider les petits commerçants à relancer leurs activités.', 'Ce programme met l\'accent sur l\'autonomisation des femmes et des jeunes entrepreneurs, moteurs essentiels de l\'économie locale.', NULL, NULL, 'Economie', 'Ir. Samy Magadju', 2, '2026-04-28 10:08:11', '2026-04-28 11:17:54', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `status`, `created_at`) VALUES
(1, 'luciusamani@gmail.com', 'active', '2026-02-21 05:04:54'),
(2, 'amanibisimwa03@gmail.com', 'active', '2026-02-21 05:11:25'),
(3, 'amanitechfusion@gmail.com', 'active', '2026-02-21 05:15:07'),
(4, 'magadjusamybonheur@gmail.com', 'active', '2026-02-24 04:39:45'),
(5, 'promessebibentyo80@gmail.com', 'active', '2026-02-24 04:41:03'),
(6, 'teacher@gmail.com', 'active', '2026-02-24 08:45:40'),
(7, 'johnmoka2024@gmail.com', 'active', '2026-03-05 07:10:53');

-- --------------------------------------------------------

--
-- Structure de la table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `news_categories`
--

INSERT INTO `news_categories` (`id`, `name`) VALUES
(1, 'Actualités'),
(4, 'Communiqués'),
(11, 'Economie'),
(8, 'Énergie'),
(3, 'Évènements'),
(10, 'LinkedIn'),
(7, 'Politique'),
(2, 'Projets'),
(6, 'Social'),
(5, 'Technologie'),
(9, 'X (Twitter)');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `nanoid` varchar(12) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `embed_code` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('En cours','Terminé','Planifié') DEFAULT 'En cours',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `beneficiaries` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `nanoid`, `author_id`, `title`, `slug`, `description`, `content`, `embed_code`, `image`, `location`, `category`, `status`, `start_date`, `end_date`, `budget`, `beneficiaries`, `created_at`, `updated_at`, `published`) VALUES
(5, 'a8RVSQBs2U', 2, 'hjkhhhhhhhhhhhhhhhhh', 'hjkhhhhhhhhhhhhhhhhh', 'KDYYR ', '<p>jjirufioion ed</p>', '', '69f08c43185f8.png', 'Bukavu', 'Développement Local', 'En cours', NULL, NULL, NULL, '7090', '2026-04-28 10:30:27', '2026-04-28 11:17:55', 1),
(6, 'NFy4twVncP', 2, 'NJHNJKR', 'brouillon-auto-28-04-2026-12-48-26', '', '<p>GHVNHH&nbsp; DJDIL D</p>', NULL, NULL, '', NULL, 'Terminé', NULL, NULL, NULL, '', '2026-04-28 10:48:26', '2026-04-28 11:17:55', 0),
(7, 'zKkmmePz4a', 2, 'NJHNJKR', 'njhnjkr', 'HGUYDD', '<p>GHVNHH&nbsp; DJDIL D</p>', '', '69f0908f2eee3.png', 'KASHA', '', 'Terminé', NULL, NULL, NULL, '1002', '2026-04-28 10:48:47', '2026-04-28 11:17:55', 1);

-- --------------------------------------------------------

--
-- Structure de la table `project_categories`
--

CREATE TABLE `project_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project_categories`
--

INSERT INTO `project_categories` (`id`, `name`) VALUES
(6, 'culte'),
(5, 'Développement Local'),
(2, 'Eau & Assainissement'),
(7, 'Eau et Assainissement'),
(4, 'Éducation'),
(8, 'Énergie & Éducation'),
(1, 'Énergie Renouvelable'),
(3, 'Environnement');

-- --------------------------------------------------------

--
-- Structure de la table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` int(11) NOT NULL,
  `endpoint` text NOT NULL,
  `p256dh` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `key_value`, `updated_at`) VALUES
(1, 'mail_from_name', 'DYNAMIQUE SAMY MAGADJU/Wema ni Akiba A.S.B.L', '2026-03-05 07:33:04');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Superadmin','Editor') DEFAULT 'Editor',
  `status` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `avatar`, `role`, `status`, `last_login`, `created_at`) VALUES
(2, 'lucienamani', '$2y$10$nnxP5jQydciqOVszltWHSeU/VO0DTRQu//XfMEUxZXtcawXjWB10i', 'Lucien Amani', 'luciusamani@gmail.com', '0848382440', 'avatar_2_1772696809.png', 'Superadmin', 1, '2026-04-28 14:02:29', '2026-03-03 00:36:49'),
(3, 'promesse', '$2y$10$7e5MHjewmU0BRdlZISHKeemtXvX1CMtWF9oLZEjH9VGlLBX6LTdZa', 'Promesse BIBENTYO', 'promessebibentyo80@gmail.com', '+243971426799', NULL, 'Editor', 1, '2026-03-05 12:58:24', '2026-03-04 21:45:14'),
(4, 'bukamu', '$2y$10$zShm41uBfrb1jSd0i8rT1OZxnBnOTsK3u5JdkWxJJIOue7tmHOA2m', 'BUKAMU', 'teacher@gmail.com', '+243971426798', NULL, 'Editor', 1, '2026-04-28 13:57:39', '2026-04-28 11:42:03');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `log_exports`
--
ALTER TABLE `log_exports`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `idx_news_nanoid` (`nanoid`),
  ADD KEY `fk_news_author` (`author_id`);

--
-- Index pour la table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `idx_projects_nanoid` (`nanoid`);

--
-- Index pour la table `project_categories`
--
ALTER TABLE `project_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `log_exports`
--
ALTER TABLE `log_exports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `project_categories`
--
ALTER TABLE `project_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
