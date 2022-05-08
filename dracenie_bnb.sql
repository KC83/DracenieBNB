-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 08 mai 2022 à 11:20
-- Version du serveur : 10.4.21-MariaDB
-- Version de PHP : 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dracenie_bnb`
--

-- --------------------------------------------------------

--
-- Structure de la table `bien`
--

CREATE TABLE `bien` (
  `bienId` int(11) NOT NULL,
  `bienLibelle` varchar(255) NOT NULL,
  `bienLitAdulte` int(11) NOT NULL,
  `bienLitEnfant` int(11) NOT NULL,
  `bienCommission` decimal(10,2) NOT NULL,
  `bienActif` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bien`
--

INSERT INTO `bien` (`bienId`, `bienLibelle`, `bienLitAdulte`, `bienLitEnfant`, `bienCommission`, `bienActif`) VALUES
(1, 'Test 1', 4, 2, '25.00', 1),
(2, 'Test 2', 2, 0, '25.00', 1),
(3, 'Test 3', 5, 4, '15.00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `bien_tarif`
--

CREATE TABLE `bien_tarif` (
  `bienTarifId` int(11) NOT NULL,
  `bienId` int(11) NOT NULL,
  `tarifTypeId` int(11) NOT NULL,
  `periodeId` int(11) NOT NULL,
  `bienTarifMontant` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bien_tarif`
--

INSERT INTO `bien_tarif` (`bienTarifId`, `bienId`, `tarifTypeId`, `periodeId`, `bienTarifMontant`) VALUES
(1, 1, 1, 1, '150.50'),
(2, 1, 2, 1, '30.00'),
(3, 1, 1, 2, '143.00'),
(4, 1, 2, 2, '20.30'),
(5, 2, 1, 1, '54.00'),
(7, 3, 1, 1, '231.00'),
(8, 3, 2, 1, '156.00');

-- --------------------------------------------------------

--
-- Structure de la table `lien`
--

CREATE TABLE `lien` (
  `lienId` int(11) NOT NULL,
  `lienLibelle` varchar(255) NOT NULL,
  `lienDo` varchar(255) NOT NULL,
  `lienAction` varchar(255) NOT NULL,
  `lienType` varchar(255) DEFAULT NULL,
  `lienIcone` varchar(255) DEFAULT NULL,
  `lienOrdre` int(11) NOT NULL,
  `lienActif` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lien`
--

INSERT INTO `lien` (`lienId`, `lienLibelle`, `lienDo`, `lienAction`, `lienType`, `lienIcone`, `lienOrdre`, `lienActif`) VALUES
(1, 'Paramétrages', 'php/parametrage', 'fiche', NULL, 'fa fa-cogs', 100, 1),
(2, 'Locations', 'php/location', 'liste', NULL, 'fa fa-home', 10, 1),
(3, 'Planning des locations', 'php/location', 'planning', NULL, 'fa fa-calendar', 20, 1),
(4, 'Biens', 'php/parametrage', 'fiche', 'bien', NULL, 10, 1),
(5, 'Périodes', 'php/parametrage', 'fiche', 'periode', NULL, 10, 1),
(6, 'Sites de réservation', 'php/parametrage', 'fiche', 'site', NULL, 10, 1);

-- --------------------------------------------------------

--
-- Structure de la table `locataire`
--

CREATE TABLE `locataire` (
  `locataireId` int(11) NOT NULL,
  `locataireNom` varchar(255) NOT NULL,
  `locatairePrenom` varchar(255) NOT NULL,
  `locataireTelephone` varchar(255) DEFAULT NULL,
  `locataireEmail` varchar(255) DEFAULT NULL,
  `locatairePays` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `locataire`
--

INSERT INTO `locataire` (`locataireId`, `locataireNom`, `locatairePrenom`, `locataireTelephone`, `locataireEmail`, `locatairePays`) VALUES
(1, 'TEST A', 'BBBB', '0626167517', 'test@test.fr', 'FRANCE');

-- --------------------------------------------------------

--
-- Structure de la table `location`
--

CREATE TABLE `location` (
  `locationId` int(11) NOT NULL,
  `bienId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `locataireId` int(11) NOT NULL,
  `locationDateDebut` date NOT NULL,
  `locationDateFin` date NOT NULL,
  `locationNbAdulte` int(11) NOT NULL,
  `locationNbEnfant` int(11) NOT NULL,
  `locationCommentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `location`
--

INSERT INTO `location` (`locationId`, `bienId`, `siteId`, `locataireId`, `locationDateDebut`, `locationDateFin`, `locationNbAdulte`, `locationNbEnfant`, `locationCommentaire`) VALUES
(1, 1, 2, 1, '2022-04-18', '2022-04-24', 3, 1, 'test com\r\naa');

-- --------------------------------------------------------

--
-- Structure de la table `periode`
--

CREATE TABLE `periode` (
  `periodeId` int(11) NOT NULL,
  `periodeLibelle` varchar(255) NOT NULL,
  `periodeCouleur` varchar(255) NOT NULL,
  `periodeDateDebut` date NOT NULL,
  `periodeDateFin` date NOT NULL,
  `periodeActif` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `periode`
--

INSERT INTO `periode` (`periodeId`, `periodeLibelle`, `periodeCouleur`, `periodeDateDebut`, `periodeDateFin`, `periodeActif`) VALUES
(1, 'Jan - Avr', '#ffeb66', '2022-01-01', '2022-04-30', 1),
(2, 'Mai - Juin', '#a53131', '2022-05-01', '2022-06-30', 1),
(3, 'Haute saison', '#ff94d8', '2022-07-01', '2022-08-31', 1),
(4, 'Basse saison', '#43db85', '2022-09-01', '2022-12-16', 1),
(5, 'Hiver', '#367dbf', '2022-12-17', '2022-12-31', 1);

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

CREATE TABLE `site` (
  `siteId` int(11) NOT NULL,
  `siteLibelle` varchar(255) NOT NULL,
  `siteCouleur` varchar(255) NOT NULL,
  `siteCommission` decimal(10,2) DEFAULT 0.00,
  `siteActif` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `site`
--

INSERT INTO `site` (`siteId`, `siteLibelle`, `siteCouleur`, `siteCommission`, `siteActif`) VALUES
(1, 'Direct', '#57b2a7', NULL, 1),
(2, 'AirBnB', '#f49325', '3.60', 1),
(3, 'Booking', '#6ba4ff', '5.00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `tarif_type`
--

CREATE TABLE `tarif_type` (
  `tarifTypeId` int(11) NOT NULL,
  `tarifTypeLibelle` varchar(255) NOT NULL,
  `tarifTypeObjetTable` varchar(255) NOT NULL,
  `tarifTypeActif` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `tarif_type`
--

INSERT INTO `tarif_type` (`tarifTypeId`, `tarifTypeLibelle`, `tarifTypeObjetTable`, `tarifTypeActif`) VALUES
(1, 'Tarif à la nuit', 'bien', 1),
(2, 'Tarif du ménage', 'bien', 1),
(3, 'Prix de base', 'location', 1),
(4, 'Prix du ménage', 'location', 1),
(5, 'Frais du site', 'location', 1),
(6, 'Frais Dracénie BnB', 'location', 1),
(7, 'Frais supplémentaires', 'location', 1),
(8, 'Frais ménage pour Dracénie BnB', 'location', 1),
(9, 'Frais partenaires', 'location', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `utilisateurId` int(11) NOT NULL,
  `utilisateurPrenom` varchar(255) NOT NULL,
  `utilisateurNom` varchar(255) NOT NULL,
  `utilisateurLogin` varchar(255) NOT NULL,
  `utilisateurPassword` varchar(255) NOT NULL,
  `utilisateurActif` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateurId`, `utilisateurPrenom`, `utilisateurNom`, `utilisateurLogin`, `utilisateurPassword`, `utilisateurActif`) VALUES
(1, 'Kelly', 'Chiarotti', 'test', '$2y$10$EUcfRaQyQ18MniZiiC2J8O7n2wJRoA1GvoNv2VC5Z/3wts3Wjl25W', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bien`
--
ALTER TABLE `bien`
  ADD PRIMARY KEY (`bienId`);

--
-- Index pour la table `bien_tarif`
--
ALTER TABLE `bien_tarif`
  ADD PRIMARY KEY (`bienTarifId`),
  ADD KEY `bienId` (`bienId`),
  ADD KEY `tarifTypeId` (`tarifTypeId`),
  ADD KEY `periodeId` (`periodeId`);

--
-- Index pour la table `lien`
--
ALTER TABLE `lien`
  ADD PRIMARY KEY (`lienId`);

--
-- Index pour la table `locataire`
--
ALTER TABLE `locataire`
  ADD PRIMARY KEY (`locataireId`);

--
-- Index pour la table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`locationId`),
  ADD KEY `siteId` (`siteId`),
  ADD KEY `locataireId` (`locataireId`),
  ADD KEY `bienId` (`bienId`);

--
-- Index pour la table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`periodeId`);

--
-- Index pour la table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`siteId`);

--
-- Index pour la table `tarif_type`
--
ALTER TABLE `tarif_type`
  ADD PRIMARY KEY (`tarifTypeId`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`utilisateurId`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bien`
--
ALTER TABLE `bien`
  MODIFY `bienId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `bien_tarif`
--
ALTER TABLE `bien_tarif`
  MODIFY `bienTarifId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `lien`
--
ALTER TABLE `lien`
  MODIFY `lienId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `locataire`
--
ALTER TABLE `locataire`
  MODIFY `locataireId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `location`
--
ALTER TABLE `location`
  MODIFY `locationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `periode`
--
ALTER TABLE `periode`
  MODIFY `periodeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `site`
--
ALTER TABLE `site`
  MODIFY `siteId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `tarif_type`
--
ALTER TABLE `tarif_type`
  MODIFY `tarifTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `utilisateurId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
