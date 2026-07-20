-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 20 juil. 2026 à 13:06
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
  `admin_password` varchar(255) NOT NULL,
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
(8, 'Amine', 'Admin', 'sawty@sawty.com', 'admin_admin', 'Erreur de mise à jour : SQLSTATE[42S22]: Column not found: 1054 Unknown column \'password\' in \'field list\'', 0);

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
(5, 12, 1, 'bonjour', 'uploads/1784508719_Logo.png', NULL, 'rejected');

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
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `elections`
--

INSERT INTO `elections` (`id_election`, `e_title`, `e_description`, `start_date`, `end_date`, `status`, `id_admin`) VALUES
(1, 'Délégués de Classe — DTS 2', 'Élection des représentants de la deuxième année développement informatique.', '2026-06-15 00:00:00', '2026-06-30 00:00:00', 'Actif', 1),
(2, 'Délégués de Classe — DTS 2', 'Élection officielle des représentants de la deuxième année développement informatique à IFIAG.', '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'Actif', 1),
(4, 'TestModifier..', '', '2026-07-27 01:29:00', '2026-08-01 01:29:00', 'Fermé', 8);

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
(1, 1, 2, '2026-07-20 00:50:37', 0, NULL);

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
('J130000000', 1),
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
(12, 'test', 'testeur', 'test@gmail.com', '0600000000', '$2y$10$DtHem52/Hxu9lV49nwxwKupSEdlC2KaMO.7VJp53t4ftE0Xe7rukK', 'AA00001', 'uploads/Logo.png');

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
(2, 1, '', '2026-07-20 00:50:37', 'AA00001');

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id_candidat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `elections`
--
ALTER TABLE `elections`
  MODIFY `id_election` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `envloppes`
--
ALTER TABLE `envloppes`
  MODIFY `id_envloppe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `votes_logs`
--
ALTER TABLE `votes_logs`
  MODIFY `id_vote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
