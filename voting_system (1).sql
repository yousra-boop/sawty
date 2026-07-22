-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 juil. 2026 à 10:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `voting_system`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Ajouter_Admin` (IN `p_name` VARCHAR(255), IN `p_surname` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_login` VARCHAR(255))   BEGIN
    -- We just insert the name, surname, email, and login. 
    -- Password and is_temp handle themselves via table defaults!
    INSERT INTO Admins (admin_name, admin_surname, admin_email, admin_login, is_temp) 
    VALUES (p_name, p_surname, p_email, p_login, 1);
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_surname` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_login` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL DEFAULT '$2y$10$vieBMR0p86cEqFqsex8i4u4wHW4dvbHn2qrLg.xqPVZjmiePtWsDK',
  `is_temp` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id_admin`, `admin_name`, `admin_surname`, `admin_email`, `admin_login`, `admin_password`, `is_temp`) VALUES
(1, 'Super', 'Admin', 'admin@sawty.ma', 'admin', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 1),
(2, 'System', 'Admin', 'admin@sawty.ma', 'root_admin', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 1),
(4, 'test', 'Admin', 'admin@sawty.ma', 'admin_test', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 1),
(6, 'testing', 'Admin', 'admin@sawty.ma', 'admin_testing', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 1),
(7, 'Amine', 'Admin', 'amine@sawty.com', 'amine_admin', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 1),
(8, 'Amine', 'Admin', 'sawty@sawty.com', 'admin_admin', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 0),
(9, 'Omar', 'M', 'omar_m@sawtyadmin.com', 'OMadmin10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(10, 'Omar', 'M', 'omar_m@adminsawty.com', 'OMadmin100', '$2y$10$.JpAbBNb0RoobA/xv0sCpe.W3MiOYvVulZUd0r4.CPF1zVUvo5eS2', 0),
(11, 'Meryem', 'G', 'meryem.g@adminsawty.com', 'MGadmin100', '$2y$10$vOIS7kal6p5JVBseukaVgO0GyOKNPU3jTWqiwXWe0Gk2vw7Inkwyq', 0),
(12, 'Yousra', 'R', 'Yousra.r@adminsawty.com', 'YRadmin100', '$2y$10$vieBMR0p86cEqFqsex8i4u4wHW4dvbHn2qrLg.xqPVZjmiePtWsDK', 1);

-- --------------------------------------------------------

--
-- Structure de la table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `action`, `item_name`, `user_email`, `date_action`) VALUES
(1, 'INSERT', 'Test12', NULL, '2026-07-21 16:55:21'),
(2, 'INSERT', 'trigeerrrrr', NULL, '2026-07-21 17:10:33');

-- --------------------------------------------------------

--
-- Structure de la table `candidats`
--

CREATE TABLE `candidats` (
  `id_candidat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_election` int(11) NOT NULL,
  `c_bio` text NOT NULL,
  `c_photo` varchar(255) DEFAULT NULL,
  `c_video` varchar(255) DEFAULT NULL,
  `c_status` varchar(50) NOT NULL DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `candidats`
--

INSERT INTO `candidats` (`id_candidat`, `id_user`, `id_election`, `c_bio`, `c_photo`, `c_video`, `c_status`) VALUES
(2, 1, 1, 'test', 'uploads/1782742515_malikaRayan.jpg', NULL, 'approved'),
(3, 1, 1, 'Étudiante passionnée par l integration Web et engagée pour la vie étudiante de notre promotion.', 'uploads/salma_profile.png', NULL, 'En attente'),
(4, 2, 1, 'Prêt à défendre vos projets techniques et à simplifier la communication avec la direction.', 'uploads/amine_profile.png', NULL, 'En attente'),
(5, 12, 1, 'bonjour', 'uploads/1784508719_Logo.png', NULL, 'rejected'),
(6, 12, 23, 'hey', '1784660868_photo_Logo.png', '', 'approved'),
(7, 12, 24, 'Bonjour', '1784692667_photo_Gemini_Generated_Image_rndfierndfierndf.png', '', 'approved'),
(8, 13, 24, 'Apply', '1784695021_photo_Gemini_Generated_Image_78x6zy78x6zy78x6.png', '', 'approved'),
(9, 15, 24, 'HH', '1784695313_photo_Gemini_Generated_Image_78x6zy78x6zy78x6.png', '', 'approved'),
(10, 13, 24, 'BB', '1784695344_photo_Gemini_Generated_Image_rndfierndfierndf (1).png', '', 'approved'),
(11, 18, 26, 'Rigoureuse, à l\'écoute et profondément engagée dans la vie de notre établissement, je souhaite mettre mon dynamisme et mon sens des responsabilités au service de notre classe.\r\n\r\nEn tant que déléguée, mon objectif principal sera d\'assurer un lien fluide, constructif et transparent entre les étudiants, l\'équipe pédagogique et la direction. Je m\'engage à :\r\n\r\nReprésenter activement la voix de chacun avec objectivité et bienveillance lors des conseils de classe et des réunions officielles.\r\n\r\nFaciliter la communication et le partage d\'informations pour que chaque étudiant dispose des ressources nécessaires pour réussir.\r\n\r\nSoutenir un environnement d\'apprentissage solidaire, motivant et propice à la réussite collective.', '1784709180_photo_Gemini_Generated_Image_fun040fun040fun0.png', '', 'approved'),
(12, 18, 24, 'BIO ', '1784710190_photo_Gemini_Generated_Image_fun040fun040fun0.png', '', 'approved'),
(13, 18, 24, 'BIO', '', '', 'approved');

-- --------------------------------------------------------

--
-- Structure de la table `elections`
--

CREATE TABLE `elections` (
  `id_election` int(11) NOT NULL,
  `e_title` varchar(255) NOT NULL,
  `e_description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Fermé',
  `poster` varchar(255) DEFAULT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `elections`
--

INSERT INTO `elections` (`id_election`, `e_title`, `e_description`, `start_date`, `end_date`, `status`, `poster`, `id_admin`) VALUES
(1, 'Délégués de Classe — DTS 2', 'Élection des représentants de la deuxième année développement informatique.', '2026-06-15 00:00:00', '2026-06-30 00:00:00', 'Actif', NULL, 1),
(2, 'Délégués de Classe — DTS 2', 'Élection officielle des représentants de la deuxième année développement informatique à IFIAG.', '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'Actif', NULL, 1),
(4, 'TestModifier..', '', '2026-07-27 01:29:00', '2026-08-01 01:29:00', 'Fermé', NULL, 8),
(6, 'Test12', '', '2026-07-23 16:55:00', '2026-07-28 16:55:00', 'Fermé', NULL, 10),
(7, 'trigeerrrrr', '', '2026-08-07 17:10:00', '2026-08-08 17:10:00', 'Fermé', NULL, 10),
(23, 'Fermé', 'hh', '2026-07-26 18:05:00', '2026-07-28 18:05:00', 'closed', '2e425ee5dcdb9e739d01f7e9dedab961.png', 10),
(24, 'Élection du Président du Conseil de la Vie Lycéenne - Lycée à Rabat', 'Le Conseil de la Vie Lycéenne (CVL) représente la voix des étudiants au sein du lycée. Il est en charge de proposer des idées, de faire remonter les préoccupations des élèves et de participer activement à l\'organisation des activités culturelles, environnementales et sportive de l\'établissement.\r\n\r\nEn tant que candidat(e), cette section présente votre campagne et vos engagements : améliorer le quotidien au lycée, porter vos projets et renforcer l\'esprit de solidarité entre tous les camarades.\r\n\r\nJour du vote : Le 12 octobre 2026.', '2026-09-14 01:00:00', '2026-09-28 01:00:00', 'active', 'dfd8c1d3b32fa9f48a5143e2a28a9184.png', 10),
(25, 'Élection du Bureau du Club Modèle des Nations Unies (MUN)', 'Le bureau du Club MUN gère les simulations de débats diplomatiques, coordonne les sessions d\'entraînement à la prise de parole et sélectionne les délégués pour les conférences nationales et internationales.\r\n\r\nEn tant que candidat(e), votre campagne s\'engage à développer l\'éloquence des membres, à organiser des simulations de haut niveau et à renforcer l\'ouverture sur les enjeux géopolitiques mondiaux.\r\n\r\nJour du vote : Le 14 octobre 2026.', '2026-10-01 01:00:00', '2026-10-12 01:00:00', 'active', 'ca951cc611be989e7fb80d81be39a760.png', 10),
(26, 'Titre de l\'élection : Élection des Délégués de Classe - Lycée à Rabat', 'Les délégués de classe sont les représentants officiels de leurs camarades lors des conseils de classe et assurent la liaison directe avec l\'administration et le corps professoral.\r\n\r\nEn tant que candidat(e), votre campagne met en avant votre écoute, votre sens des responsabilités et votre promesse d\'intervenir efficacement pour résoudre les problématiques de la classe.\r\n\r\nJour du vote : Le 16 octobre 2026.', '2026-09-18 01:00:00', '2026-10-16 01:00:00', 'active', 'c778ea1a1823e193527fa0bbbd61d5ca.png', 10);

-- --------------------------------------------------------

--
-- Structure de la table `envloppes`
--

CREATE TABLE `envloppes` (
  `id_envloppe` int(11) NOT NULL,
  `id_election` int(11) NOT NULL,
  `id_candidat` int(11) DEFAULT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_protest` tinyint(1) NOT NULL DEFAULT 0,
  `protest_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `envloppes`
--

INSERT INTO `envloppes` (`id_envloppe`, `id_election`, `id_candidat`, `voted_at`, `user_protest`, `protest_reason`) VALUES
(1, 1, 2, '2026-07-20 00:50:37', 0, NULL),
(2, 23, 6, '2026-07-21 20:45:33', 0, ''),
(3, 24, NULL, '2026-07-22 06:11:09', 1, 'Je veux un candidat qui peut garantir le respect de temps de la prière!');

-- --------------------------------------------------------

--
-- Structure de la table `listesblanches`
--

CREATE TABLE `listesblanches` (
  `identifiant` varchar(255) NOT NULL,
  `id_election` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `listesblanches`
--

INSERT INTO `listesblanches` (`identifiant`, `id_election`) VALUES
('A123', 23),
('A123', 24),
('A123', 25),
('A123', 26),
('B123', 23),
('B123', 24),
('B123', 25),
('B123', 26),
('C123', 23),
('C123', 24),
('C123', 25),
('C123', 26),
('D123', 23),
('D123', 24),
('D123', 25),
('D123', 26),
('E123', 23),
('E123', 24),
('E123', 25),
('E123', 26),
('J130000000', 1),
('J1818', 24),
('J1818', 26),
('K130024567', 1),
('MASSAR12345', 1);

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profile`
--

INSERT INTO `profile` (`id`, `user_email`, `content`, `created_at`) VALUES
(1, 'salma1@gmail.com', 'Hello world!', '2026-07-15 01:32:35'),
(2, 'salma1@gmail.com', 'Hello', '2026-07-15 01:37:56');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_surname` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_phone` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `national_id` varchar(150) NOT NULL,
  `user_avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `user_name`, `user_surname`, `user_email`, `user_phone`, `user_password`, `national_id`, `user_avatar`) VALUES
(1, 'Malika', 'Rayan', 'malika@example.com', '0611223344', 'hashed_password_here', 'G723941', NULL),
(2, '', 'Salma Alaoui', 'salma1@gmail.com', '0600000000', '$2y$10$YkJ6tHK/V2rSizXdDFmuO.JbFkpAlMPKtASl8NWrBY/Zm19dzsVUC', 'K130000000', 'avatar_salma1_1784083045.jpg'),
(3, 'Salma', 'Alaoui', 'salma.alaoui@student.ma', '0655443322', 'hashed_pass_1', 'MASSAR12345', NULL),
(4, 'Amine', 'Tazi', 'amine.tazi@student.ma', '0677889900', 'hashed_pass_2', 'MASSAR67890', NULL),
(5, '', 'marwa', 'marwa@gmail.com', '0600000000', '$2y$10$dtMB09JjTKNkNgXATlXTsen9c.vQtNWDZt4x.wVma9KQMwLl3Limm', 'J130000000', 'avatar_marwa_1784088710.jpg'),
(6, '', 'Ilyass Alaoui', 'ilyass@gmail.com', '0600000000', '$2y$10$kCSCAOxWyEhrKPBNyEtsCuvNFsdZRgv.pSoZpyh2BJ0w3.dUfE8Ba', 'J1200000', NULL),
(12, 'test', 'testeur', 'test@gmail.com', '0600000000', '$2y$10$DtHem52/Hxu9lV49nwxwKupSEdlC2KaMO.7VJp53t4ftE0Xe7rukK', 'AA00001', 'uploads/Logo.png'),
(13, 'Manal', 'Amine', 'ManalAmine@s.com', '0678000000', '$2y$10$1lzxtyVDkylAQaAXZGTsbObpwbu5b5pThZBNWViBfP.FEifg2zhem', 'B123', 'uploads/Gemini_Generated_Image_rndfierndfierndf (1).png'),
(15, 'Hamza', 'Foullani', 'Hamza@g.com', '069999793', '$2y$10$geaXf9I694U65UFYKOK6turIhZD.sfTgiaogbk3CXEIQXYiB.68tm', 'C123', 'uploads/Gemini_Generated_Image_78x6zy78x6zy78x6.png'),
(17, 'Zakaria', 'Samlali', 'zak@g.com', '0678000000', '$2y$10$Tw5hMMlDO5WH2YBDgHznSOIBCNjD7I9CtDZNNj3h9ZbXfY3EqwhwK', 'D123', 'uploads/Gemini_Generated_Image_78x6zy78x6zy78x6.png'),
(18, 'Yousra', 'Rachd', 'yr@gmail.com', '0765454545', '$2y$10$Wbrrlugggd3B6ou6osb6E.gTmdrF3yRbWKidnhY61n228nYJ2lDru', 'J1818', 'uploads/Gemini_Generated_Image_fun040fun040fun0.png');

-- --------------------------------------------------------

--
-- Structure de la table `votes_logs`
--

CREATE TABLE `votes_logs` (
  `id_vote` int(11) NOT NULL,
  `id_election` int(11) NOT NULL,
  `vote_token` varchar(255) NOT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `national_id` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `votes_logs`
--

INSERT INTO `votes_logs` (`id_vote`, `id_election`, `vote_token`, `voted_at`, `national_id`) VALUES
(2, 1, '', '2026-07-20 00:50:37', 'AA00001'),
(5, 23, '9492f6606ae3ba5900b032331a291fde', '2026-07-21 20:45:33', 'A123'),
(6, 24, 'c0bfeefcf89a05903a0a702a341c97bd', '2026-07-22 06:11:09', 'D123');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `admin_login` (`admin_login`),
  ADD UNIQUE KEY `admin_login_2` (`admin_login`);

--
-- Index pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id_candidat`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_election` (`id_election`);

--
-- Index pour la table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id_election`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Index pour la table `envloppes`
--
ALTER TABLE `envloppes`
  ADD PRIMARY KEY (`id_envloppe`),
  ADD KEY `id_election` (`id_election`),
  ADD KEY `id_candidat` (`id_candidat`);

--
-- Index pour la table `listesblanches`
--
ALTER TABLE `listesblanches`
  ADD PRIMARY KEY (`identifiant`,`id_election`),
  ADD KEY `id_election` (`id_election`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `national_id` (`national_id`);

--
-- Index pour la table `votes_logs`
--
ALTER TABLE `votes_logs`
  ADD PRIMARY KEY (`id_vote`),
  ADD UNIQUE KEY `vote_token` (`vote_token`),
  ADD UNIQUE KEY `unique_voter_per_election` (`id_election`,`national_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id_candidat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `elections`
--
ALTER TABLE `elections`
  MODIFY `id_election` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `envloppes`
--
ALTER TABLE `envloppes`
  MODIFY `id_envloppe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `votes_logs`
--
ALTER TABLE `votes_logs`
  MODIFY `id_vote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD CONSTRAINT `candidats_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `candidats_ibfk_2` FOREIGN KEY (`id_election`) REFERENCES `elections` (`id_election`);

--
-- Contraintes pour la table `elections`
--
ALTER TABLE `elections`
  ADD CONSTRAINT `elections_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`);

--
-- Contraintes pour la table `envloppes`
--
ALTER TABLE `envloppes`
  ADD CONSTRAINT `envloppes_ibfk_1` FOREIGN KEY (`id_election`) REFERENCES `elections` (`id_election`),
  ADD CONSTRAINT `envloppes_ibfk_2` FOREIGN KEY (`id_candidat`) REFERENCES `candidats` (`id_candidat`);

--
-- Contraintes pour la table `listesblanches`
--
ALTER TABLE `listesblanches`
  ADD CONSTRAINT `listesblanches_ibfk_1` FOREIGN KEY (`id_election`) REFERENCES `elections` (`id_election`);

--
-- Contraintes pour la table `votes_logs`
--
ALTER TABLE `votes_logs`
  ADD CONSTRAINT `votes_logs_ibfk_1` FOREIGN KEY (`id_election`) REFERENCES `elections` (`id_election`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
