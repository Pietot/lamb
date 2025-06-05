-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 juin 2025 à 18:30
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `lamb`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` bigint NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `quantite_stock` int DEFAULT NULL,
  `seuil_alerte` int DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_article`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `articlelot`
--

DROP TABLE IF EXISTS `articlelot`;
CREATE TABLE IF NOT EXISTS `articlelot` (
  `id_article` bigint NOT NULL,
  `id_lot` int NOT NULL,
  `quantite_article` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_article`,`id_lot`),
  KEY `id_lot` (`id_lot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` int NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prénom` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telephone` smallint DEFAULT NULL,
  `adresse` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int NOT NULL,
  `date_commande` date DEFAULT NULL,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `id_client` int NOT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commandelot`
--

DROP TABLE IF EXISTS `commandelot`;
CREATE TABLE IF NOT EXISTS `commandelot` (
  `id_lot` int NOT NULL,
  `id_commande` int NOT NULL,
  `quantite` int DEFAULT NULL,
  PRIMARY KEY (`id_lot`,`id_commande`),
  KEY `id_commande` (`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commande_employee`
--

DROP TABLE IF EXISTS `commande_employee`;
CREATE TABLE IF NOT EXISTS `commande_employee` (
  `id_commande` int NOT NULL,
  `id_employee` int NOT NULL,
  PRIMARY KEY (`id_commande`,`id_employee`),
  KEY `id_employee` (`id_employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `id_employee` int NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(30) NOT NULL,
  `pwd_hash` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `token_init` datetime DEFAULT NULL,
  `id_role` int NOT NULL,
  PRIMARY KEY (`id_employee`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `employee`
--

INSERT INTO `employee` (`id_employee`, `nom`, `prenom`, `email`, `login`, `pwd_hash`, `token`, `token_init`, `id_role`) VALUES
(1, 'Test', 'Test', 'test@test.com', 'test', '$2y$10$YMfhEoNm8tgkhfSm.kzGF.kezvI4p/SRqFp3VBR45KikqVUsKQc1y', '77feccf46ce04816339488e0eaf0049f4f690c2b5b18305f95d1e4d56e35', '2025-06-03 12:27:11', 1);

-- --------------------------------------------------------

--
-- Structure de la table `lot`
--

DROP TABLE IF EXISTS `lot`;
CREATE TABLE IF NOT EXISTS `lot` (
  `id_lot` int NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `quantite_stock` int DEFAULT NULL,
  `seuil_alerte` int DEFAULT NULL,
  PRIMARY KEY (`id_lot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mouvementstock`
--

DROP TABLE IF EXISTS `mouvementstock`;
CREATE TABLE IF NOT EXISTS `mouvementstock` (
  `id_mouvement` int NOT NULL,
  `quantite` int DEFAULT NULL,
  `date_mouvement` datetime DEFAULT NULL,
  `id_type` int NOT NULL,
  `id_article` bigint DEFAULT NULL,
  PRIMARY KEY (`id_mouvement`),
  KEY `id_type` (`id_type`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `role`) VALUES
(1, 'Test');

-- --------------------------------------------------------

--
-- Structure de la table `typemouvement`
--

DROP TABLE IF EXISTS `typemouvement`;
CREATE TABLE IF NOT EXISTS `typemouvement` (
  `id_type` int NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `Article_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`);

--
-- Contraintes pour la table `articlelot`
--
ALTER TABLE `articlelot`
  ADD CONSTRAINT `ArticleLot_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  ADD CONSTRAINT `ArticleLot_ibfk_2` FOREIGN KEY (`id_lot`) REFERENCES `lot` (`id_lot`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `Commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`);

--
-- Contraintes pour la table `commandelot`
--
ALTER TABLE `commandelot`
  ADD CONSTRAINT `CommandeLot_ibfk_1` FOREIGN KEY (`id_lot`) REFERENCES `lot` (`id_lot`),
  ADD CONSTRAINT `CommandeLot_ibfk_2` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`);

--
-- Contraintes pour la table `commande_employee`
--
ALTER TABLE `commande_employee`
  ADD CONSTRAINT `Commande_Employee_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `Commande_Employee_ibfk_2` FOREIGN KEY (`id_employee`) REFERENCES `employee` (`id_employee`);

--
-- Contraintes pour la table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `Employee_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);

--
-- Contraintes pour la table `mouvementstock`
--
ALTER TABLE `mouvementstock`
  ADD CONSTRAINT `MouvementStock_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `typemouvement` (`id_type`),
  ADD CONSTRAINT `MouvementStock_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
