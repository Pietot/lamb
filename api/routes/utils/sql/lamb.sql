-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 05 juin 2025 à 14:55
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

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

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `description`, `nom`, `quantite_stock`, `seuil_alerte`, `date_creation`, `id_categorie`) VALUES
(1001, 'Jean slim en denim brut', 'Jean Slim Homme', 120, 10, '2024-09-01', 1),
(1002, 'Robe d\'été légère', 'Robe Floral Femme', 80, 5, '2024-07-15', 2),
(1003, 'Ceinture cuir marron', 'Ceinture Homme', 50, 10, '2024-06-20', 3),
(1004, 'T-shirt en coton bio', 'T-shirt Blanc Unisexe', 200, 20, '2024-08-01', 1),
(1005, 'Blazer cintré en laine', 'Blazer Femme', 60, 5, '2024-10-05', 2),
(1006, 'Mini-sac en cuir vegan', 'Sac Bandoulière', 40, 5, '2024-11-20', 3),
(1007, 'Pantalon garçon 6-8 ans', 'Pantalon Enfant', 100, 10, '2025-01-10', 4),
(1008, 'Baskets montantes blanches', 'Baskets Montantes', 70, 10, '2025-02-15', 5);

-- --------------------------------------------------------

--
-- Structure de la table `article_lot`
--

DROP TABLE IF EXISTS `article_lot`;
CREATE TABLE IF NOT EXISTS `article_lot` (
  `id_article` bigint NOT NULL,
  `id_lot` int NOT NULL,
  `quantite_article` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_article`,`id_lot`),
  KEY `id_lot` (`id_lot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `article_lot`
--

INSERT INTO `article_lot` (`id_article`, `id_lot`, `quantite_article`) VALUES
(1001, 1, '50'),
(1002, 2, '30'),
(1004, 1, '60'),
(1005, 3, '20'),
(1006, 3, '15'),
(1007, 4, '25'),
(1008, 3, '30');

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

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`, `description`) VALUES
(1, 'Homme', 'Vêtements homme'),
(2, 'Femme', 'Vêtements femme'),
(3, 'Accessoires', 'Ceintures, sacs, etc.'),
(4, 'Enfant', 'Vêtements pour enfants'),
(5, 'Chaussures', 'Baskets, bottes, sandales');

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

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `nom`, `prénom`, `email`, `telephone`, `adresse`) VALUES
(1, 'Durand', 'Claire', 'claire.durand@email.com', 12345, '15 rue Lafayette, Paris'),
(2, 'Martin', 'Julien', 'julien.martin@email.com', 23456, '42 avenue des Champs, Lyon'),
(3, 'Lemoine', 'Sarah', 'sarah.lemoine@email.com', 32767, '20 place Bellecour, Lyon'),
(4, 'Nguyen', 'Thierry', 'thierry.nguyen@email.com', 32767, '77 rue Nationale, Lille'),
(5, 'Bennani', 'Youssef', 'youssef.bennani@email.com', 32767, '98 rue de la Liberté, Marseille');

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

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `date_commande`, `montant_total`, `id_client`) VALUES
(1, '2025-05-10', 149.90, 1),
(2, '2025-05-12', 89.90, 2),
(3, '2025-05-14', 199.90, 3),
(4, '2025-05-15', 74.50, 4),
(5, '2025-05-16', 139.20, 5);

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

--
-- Déchargement des données de la table `commandelot`
--

INSERT INTO `commandelot` (`id_lot`, `id_commande`, `quantite`) VALUES
(1, 1, 2),
(1, 5, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `commande_utilisateur`
--

DROP TABLE IF EXISTS `commande_utilisateur`;
CREATE TABLE IF NOT EXISTS `commande_utilisateur` (
  `id_commande` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_commande`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_utilisateur`
--

INSERT INTO `commande_utilisateur` (`id_commande`, `id_utilisateur`) VALUES
(1, 1),
(2, 1),
(5, 1),
(3, 2),
(4, 2);

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

--
-- Déchargement des données de la table `lot`
--

INSERT INTO `lot` (`id_lot`, `nom`, `description`, `date_creation`, `quantite_stock`, `seuil_alerte`) VALUES
(1, 'Lot Printemps', 'Collection Printemps 2025', '2025-03-01', 200, 20),
(2, 'Lot Été', 'Nouveautés Été 2025', '2025-04-01', 150, 15),
(3, 'Lot Automne', 'Collection Automne 2025', '2025-05-01', 180, 25),
(4, 'Lot Enfant', 'Spécial Kids', '2025-04-20', 90, 10);

-- --------------------------------------------------------

--
-- Structure de la table `mouvement_stock`
--

DROP TABLE IF EXISTS `mouvement_stock`;
CREATE TABLE IF NOT EXISTS `mouvement_stock` (
  `id_mouvement` int NOT NULL,
  `quantite` int DEFAULT NULL,
  `date_mouvement` datetime DEFAULT NULL,
  `id_type` int NOT NULL,
  `id_article` bigint DEFAULT NULL,
  PRIMARY KEY (`id_mouvement`),
  KEY `id_type` (`id_type`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mouvement_stock`
--

INSERT INTO `mouvement_stock` (`id_mouvement`, `quantite`, `date_mouvement`, `id_type`, `id_article`) VALUES
(1, 50, '2025-04-10 10:00:00', 1, 1001),
(2, 10, '2025-05-10 12:00:00', 2, 1001),
(3, 60, '2025-05-01 09:00:00', 1, 1004),
(4, 5, '2025-05-14 10:30:00', 2, 1005),
(5, 10, '2025-05-15 14:00:00', 2, 1008),
(6, 25, '2025-05-12 11:00:00', 1, 1007),
(7, 8, '2025-05-16 15:45:00', 2, 1001);

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
-- Structure de la table `type_mouvement`
--

DROP TABLE IF EXISTS `type_mouvement`;
CREATE TABLE IF NOT EXISTS `type_mouvement` (
  `id_type` int NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `type_mouvement`
--

INSERT INTO `type_mouvement` (`id_type`, `type`) VALUES
(1, 'Entrée'),
(2, 'Sortie');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(30) NOT NULL,
  `pwd_hash` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `token_init` datetime DEFAULT NULL,
  `id_role` int NOT NULL,
  PRIMARY KEY (`id_utilisateur`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `login`, `pwd_hash`, `token`, `token_init`, `id_role`) VALUES
(1, 'Test', 'Test', 'test@test.com', 'test', '$2y$10$YMfhEoNm8tgkhfSm.kzGF.kezvI4p/SRqFp3VBR45KikqVUsKQc1y', '1fc56358b04075a4ffae124da13bf96a1f8bd5e9fd307fc213fed4ac23a0', '2025-06-05 16:32:01', 1),
(2, 'Laurent', 'Claire', 'claire.laurent@lamb.com', 'clairel', '$2y$10$dXJazd1Xvz1234examplehashedPWD', NULL, NULL, 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `Article_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`);

--
-- Contraintes pour la table `article_lot`
--
ALTER TABLE `article_lot`
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
-- Contraintes pour la table `commande_utilisateur`
--
ALTER TABLE `commande_utilisateur`
  ADD CONSTRAINT `Commande_Utilisateur_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `Commande_Utilisateur_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD CONSTRAINT `MouvementStock_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `type_mouvement` (`id_type`),
  ADD CONSTRAINT `MouvementStock_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `Utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
