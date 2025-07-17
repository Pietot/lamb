-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 juil. 2025 à 14:05
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

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

CREATE TABLE `article` (
  `id_article` bigint(20) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `quantite_stock` int(11) DEFAULT NULL,
  `seuil_alerte` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `id_categorie` int(11) NOT NULL,
  `id_fournisseur_principal` int(11) DEFAULT NULL,
  `reference_fournisseur` varchar(100) DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `reference`, `description`, `nom`, `quantite_stock`, `seuil_alerte`, `date_creation`, `id_categorie`, `id_fournisseur_principal`, `reference_fournisseur`, `prix_achat`) VALUES
(1, 'fzfez', 'Jean slim en denim brut', 'Jean Slim', 120, 10, '2024-09-01 00:00:00', 1, 4, 'REF-5655646', 49.99),
(2, 'fzfezgre', 'Robe d\'été légère', 'Robe Floral Femme', 80, 5, '2024-07-15 00:00:00', 2, 3, 'REF-674123', 74.99),
(3, 'gezrgf', 'Ceinture cuir marron', 'Ceinture cuir', 5, 10, '2024-06-20 00:00:00', 3, 1, 'REF-123456', 29.99),
(4, 'zfds', 'T-shirt en coton bio', 'T-shirt Blanc Unisexe', 200, 400, '2024-08-01 00:00:00', 1, 4, 'REF-852741', 49.99),
(5, 'greq', 'Blazer cintré en laine', 'Blazer Femme', 60, 5, '2024-10-05 00:00:00', 2, 5, 'REF-741963', 69.69),
(6, 'vrth', 'Mini-sac en cuir vegan', 'Sac Bandoulière', 40, 5, '2024-11-20 00:00:00', 3, 3, 'REF-778899', 119.99),
(7, 'hsrtsht', 'Pantalon garçon 6-8 ans', 'Pantalon Enfant', 100, 10, '2025-01-10 00:00:00', 4, 1, 'REF-667667', 29.99),
(8, 'hrshst', 'Baskets montantes blanches', 'Baskets Montantes', 70, 10, '2025-02-15 00:00:00', 5, 5, 'REF-177013', 89.99),
(9, ' hrvyrv', 'Chapeau rond estival', 'Chapeau Melon', 5, 15, '2025-06-06 00:00:00', 3, 2, 'REF-752168', 19.99),
(10, 'ysrysrht', 'T-shirt stylisé thème Burgouz', 'T-Shirt Burgouz', 100, 20, '2025-06-07 00:00:00', 1, 1, 'REF-555555', 39.99),
(11, 'hgfehs', 'T-shirt stylisé thème Batman', 'T-Shirt Batman', 100, 20, '2025-06-08 00:00:00', 1, 2, 'REF-159753', 39.99),
(12, 'jutli', 'Chaussure de villes marron', 'Chaussure marron', 70, 10, '2025-06-15 00:00:00', 5, 4, 'REF-456852', 149.99),
(16, 'fffdffdsf', 'Montre connecté résistante a l\'eau', 'Montre Connecté', 100, 50, '2025-06-29 10:32:45', 3, 2, 'REF-954687', 94.99),
(17, 'fffdffdsfdf', 'dfdf', 'dfd', 15, 10, '2025-07-15 23:12:31', 2, 1, 'REF-65465465', 50.00);

-- --------------------------------------------------------

--
-- Structure de la table `article_lot`
--

CREATE TABLE `article_lot` (
  `id_article` bigint(20) NOT NULL,
  `id_lot` int(11) NOT NULL,
  `quantite_article` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article_lot`
--

INSERT INTO `article_lot` (`id_article`, `id_lot`, `quantite_article`) VALUES
(1, 1, '50'),
(1, 11, '1'),
(2, 2, '30'),
(4, 1, '60'),
(4, 12, '1'),
(5, 3, '20'),
(5, 11, '1'),
(6, 3, '15'),
(8, 3, '30'),
(8, 11, '1'),
(16, 4, '100');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`, `description`) VALUES
(1, 'Sports', 'Sport femme'),
(2, 'Femme', 'Vêtements femme'),
(3, 'Accessoires', 'Ceintures, sacs, etc.'),
(4, 'Enfant', 'Vêtements pour enfants'),
(5, 'Chaussures', 'Baskets, bottes, sandales');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `raison_sociale` varchar(100) NOT NULL,
  `nom_commercial` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `siret` varchar(14) DEFAULT NULL,
  `secteur_activite` varchar(100) DEFAULT NULL,
  `contact_principal` varchar(100) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `raison_sociale`, `nom_commercial`, `email`, `telephone`, `adresse`, `code_postal`, `ville`, `siret`, `secteur_activite`, `contact_principal`, `date_creation`) VALUES
(1, 'Durand SAS', 'Durand Distribution', 'contact@durand-sas.fr', '0156789012', '15 rue Lafayette', '75009', 'Paris', '12345678901234', 'Distribution textile', 'Claire Durand', '2025-06-28 00:00:00'),
(2, 'Martin & Cie', 'Martin Mode', 'info@martin-cie.fr', '0478901234', '42 avenue des Champs', '69003', 'Lyon', '23456789012345', 'Prêt-à-porter', 'Julien Martin', '2025-06-28 00:00:00'),
(3, 'Lemoine SARL', 'Boutique Lemoine', 'contact@lemoine-mode.fr', '0472345678', '20 place Bellecour', '69002', 'Lyon', '34567890123456', 'Mode femme', 'Sarah Lemoine', '2025-06-28 00:00:00'),
(4, 'Nguyen Import Export', 'Mode Asia', 'commercial@nguyen-import.fr', '0320456789', '77 rue Nationale', '59000', 'Lille', '45678901234567', 'Import textile', 'Thierry Nguyen', '2025-06-28 00:00:00'),
(5, 'Bennani Fashion Group', 'BFG Mode', 'contact@bfg-mode.fr', '0491234567', '98 rue de la Liberté', '13001', 'Marseille', '56789012345678', 'Grossiste mode', 'Youssef Bennani', '2025-06-28 00:00:00'),
(6, 'feur', 'FranceDeFils', 'fraistagadaa@gmail.com', '0698668296', '17 Rue Lortet', '69007', 'Lyon', '56446545646545', 'Textile', 'Cabrel', '2025-07-16 01:31:30');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `numero_commande` varchar(50) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `statut` varchar(11) NOT NULL,
  `montant_ht` decimal(10,2) DEFAULT NULL,
  `montant_ttc` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `numero_commande`, `id_client`, `date_commande`, `statut`, `montant_ht`, `montant_ttc`) VALUES
(1, 'CMD-20250120-YYMPDR', 1, '2025-05-10 00:00:00', 'expedie', 149.90, 0.00),
(2, 'CMD-202501201-YEMPDR', 2, '2025-05-12 00:00:00', 'expedie', 89.90, 0.00),
(3, 'CMD-202501201-YERPDR', 3, '2025-05-14 00:00:00', 'preparation', 199.90, 0.00),
(4, 'CMD-202501201-YERDRT', 4, '2025-05-15 00:00:00', 'preparation', 74.50, 0.00),
(5, 'CMD-202501201-RPDRT', 5, '2025-05-19 00:00:00', 'preparation', 139.20, 0.00),
(6, 'CMD-202501201-YERRT', 2, '2025-05-19 00:00:00', 'attente', 522.15, 0.00),
(7, 'CMD-202501201-YERPR', 5, '2025-01-20 00:00:00', 'preparation', 100.00, 0.00),
(8, 'CMD-202501201-RPDRT', 2, '2025-02-24 00:00:00', 'expedie', 149.96, 0.00),
(9, 'CMD-20250716-5-KONDO', 5, '2025-07-14 00:00:00', '1', 54800.00, 65760.00),
(10, 'CMD-20250716-6-CGXBV', 6, '2025-07-16 00:00:00', '2', 100.00, 120.00),
(11, 'CMD-20250716-4-HYMJZ', 4, '2025-07-15 00:00:00', '1', 100.00, 120.00);

-- --------------------------------------------------------

--
-- Structure de la table `commande_fournisseur`
--

CREATE TABLE `commande_fournisseur` (
  `id_commande_fournisseur` int(11) NOT NULL,
  `numero_commande` varchar(50) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `date_commande` datetime NOT NULL DEFAULT current_timestamp(),
  `date_livraison_prevue` date DEFAULT NULL,
  `statut` enum('brouillon','envoyee','confirmee','livraison_partielle','livree','annulee') DEFAULT 'brouillon',
  `montant_ht` decimal(10,2) DEFAULT 0.00,
  `montant_ttc` decimal(10,2) DEFAULT 0.00,
  `id_utilisateur` int(11) DEFAULT NULL COMMENT 'Utilisateur qui a créé la commande',
  `notes` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_fournisseur`
--

INSERT INTO `commande_fournisseur` (`id_commande_fournisseur`, `numero_commande`, `id_fournisseur`, `date_commande`, `date_livraison_prevue`, `statut`, `montant_ht`, `montant_ttc`, `id_utilisateur`, `notes`, `date_creation`, `date_modification`) VALUES
(5, 'CF-2025-001', 1, '2025-05-01 10:00:00', '2025-05-06', 'confirmee', 2500.00, 3000.00, 1, 'Collection été - Urgent', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(6, 'CF-2025-002', 2, '2025-05-03 14:30:00', '2025-05-08', 'livraison_partielle', 1800.00, 2160.00, 2, 'Accessoires printemps', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(7, 'CF-2025-003', 3, '2025-05-05 09:00:00', '2025-05-12', 'envoyee', 4200.00, 5040.00, 1, 'Grosse commande mensuelle', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(8, 'CF-2025-004', 4, '2025-05-10 11:00:00', '2025-05-15', 'brouillon', 3500.00, 4200.00, 3, 'En attente de validation', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(9, 'CF-2025-005', 5, '2025-04-20 10:00:00', '2025-04-30', 'livree', 2800.00, 3360.00, 2, 'Collection hiver - Livré', '2025-06-28 18:02:58', '2025-06-28 18:02:58');

-- --------------------------------------------------------

--
-- Structure de la table `commande_lot`
--

CREATE TABLE `commande_lot` (
  `id_lot` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_lot`
--

INSERT INTO `commande_lot` (`id_lot`, `id_commande`, `quantite`) VALUES
(1, 1, 2),
(1, 5, 1),
(2, 2, 1),
(3, 3, 1),
(3, 9, 546),
(3, 11, 1),
(4, 4, 2),
(11, 9, 2),
(12, 10, 1);

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

CREATE TABLE `fournisseur` (
  `id_fournisseur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `contact_nom` varchar(100) DEFAULT NULL,
  `contact_prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `site_web` varchar(255) DEFAULT NULL,
  `conditions_paiement` varchar(100) DEFAULT NULL,
  `delai_livraison` int(11) DEFAULT NULL COMMENT 'Délai en jours',
  `note_qualite` decimal(3,2) DEFAULT NULL COMMENT 'Note sur 5',
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fournisseur`
--

INSERT INTO `fournisseur` (`id_fournisseur`, `nom`, `contact_nom`, `contact_prenom`, `email`, `telephone`, `adresse`, `ville`, `code_postal`, `pays`, `site_web`, `conditions_paiement`, `delai_livraison`, `note_qualite`, `actif`, `date_creation`, `date_modification`) VALUES
(1, 'Chapeau Melon SAS', 'Cailloux', 'Pierre', 'contact@Chapeau-Melon-SAS.fr', '0123456789', '10 rue de l\'Industrie', 'Paris', '75001', 'France', NULL, NULL, 5, 4.50, 1, '2025-06-28 17:50:47', '2025-06-29 10:35:57'),
(2, 'Chic&Choc', 'Fleur', 'Marguerite', 'contact@bernard-fr.com', '0145678901', '42 rue du Commerce', 'Lyon', '69001', 'France', NULL, NULL, 3, 4.80, 1, '2025-06-28 17:50:47', '2025-06-29 10:36:28'),
(3, 'Martin & Cie', 'Martin', 'Cie', 'info@martin-cie.fr', '0234567890', '15 avenue des Affaires', 'Marseille', '13001', 'France', NULL, NULL, 7, 4.20, 1, '2025-06-28 17:50:47', '2025-06-29 10:36:37'),
(4, 'XavHabille', 'Dupont de L\'habillage', 'Xavier', 'contact@dupont-sas.fr', '0156789012', '25 boulevard Commercial', 'Lille', '59000', 'France', NULL, NULL, 4, 4.60, 1, '2025-06-28 17:50:47', '2025-06-29 10:37:09'),
(5, 'Fashion Import', 'Leclerc', 'Thomas', 'import@fashion.fr', '0167890123', '8 place du Textile', 'Toulouse', '31000', 'France', NULL, NULL, 10, 3.90, 1, '2025-06-28 17:50:47', '2025-06-28 17:50:47');

-- --------------------------------------------------------

--
-- Structure de la table `livraison`
--

CREATE TABLE `livraison` (
  `id_livraison` int(11) NOT NULL,
  `numero_livraison` varchar(50) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `id_commande_fournisseur` int(11) DEFAULT NULL,
  `date_livraison` datetime NOT NULL DEFAULT current_timestamp(),
  `numero_bon_livraison` varchar(100) DEFAULT NULL,
  `statut` enum('en_attente','en_cours','completee','probleme') DEFAULT 'en_attente',
  `id_utilisateur_reception` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livraison`
--

INSERT INTO `livraison` (`id_livraison`, `numero_livraison`, `id_fournisseur`, `id_commande_fournisseur`, `date_livraison`, `numero_bon_livraison`, `statut`, `id_utilisateur_reception`, `notes`, `date_creation`, `date_modification`) VALUES
(1, 'LIV-2025-001', 2, NULL, '2025-05-04 09:30:00', 'BL-BF-2025-123', 'completee', 1, 'Livraison partielle - Manque les baskets', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(2, 'LIV-2025-002', 1, NULL, '2025-05-06 14:00:00', 'BL-DS-2025-456', 'en_attente', NULL, 'Prévue cet après-midi', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(3, 'LIV-2025-003', 5, 9, '2025-04-28 10:00:00', 'BL-FI-2025-789', 'completee', 2, 'Collection hiver complète', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(4, 'LIV-2025-004', 3, NULL, '2025-05-08 00:00:00', NULL, 'en_attente', NULL, 'Livraison hors commande - Stock supplémentaire', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(5, 'LIV-2025-005', 4, NULL, '2025-05-03 16:00:00', 'BL-DP-2025-321', 'en_attente', 1, 'Articles endommagés - En attente de retour', '2025-06-28 18:02:58', '2025-06-29 09:49:19'),
(6, 'LIV-2025-006', 2, NULL, '2025-05-07 00:00:00', NULL, 'en_attente', NULL, 'Nouvelle collection accessoires', '2025-06-28 18:02:58', '2025-06-28 18:02:58'),
(16, 'LIV-2025-07-16-1-UAGGQ', 1, NULL, '2025-07-11 19:52:00', 'BL-1752688348915', 'completee', 1, 'Aucune note', '2025-07-16 17:52:28', '2025-07-16 20:11:38');

-- --------------------------------------------------------

--
-- Structure de la table `lot`
--

CREATE TABLE `lot` (
  `id_lot` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `quantite_stock` int(11) DEFAULT NULL,
  `seuil_alerte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lot`
--

INSERT INTO `lot` (`id_lot`, `nom`, `description`, `date_creation`, `quantite_stock`, `seuil_alerte`) VALUES
(1, 'Lot Printemps', 'Collection Printemps 2025', '2025-03-01', 200, 20),
(2, 'Lot Été', 'Nouveautés Été 2025', '2025-04-01', 150, 15),
(3, 'Lot Automne', 'Collection Automne 2025', '2025-05-01', 180, 25),
(4, 'Lot Enfantin', 'Spécial Kids', '2025-07-15', 90, 10),
(10, 'Lot test', 'test', '2025-07-15', 15, 10),
(11, 'lot feur', 'feur test', '2025-07-15', 65, 10),
(12, 'lot feurer', 'feurer', '2025-07-15', 56, 10);

-- --------------------------------------------------------

--
-- Structure de la table `mouvement_stock`
--

CREATE TABLE `mouvement_stock` (
  `id_mouvement` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  `date_mouvement` datetime DEFAULT NULL,
  `id_type` int(11) NOT NULL,
  `id_article` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `mouvement_stock`
--

INSERT INTO `mouvement_stock` (`id_mouvement`, `quantite`, `date_mouvement`, `id_type`, `id_article`) VALUES
(1, 50, '2025-04-10 10:00:00', 1, 1),
(2, 10, '2025-05-10 12:00:00', 2, 1),
(3, 60, '2025-05-01 09:00:00', 1, 4),
(4, 5, '2025-05-14 10:30:00', 2, 5),
(5, 10, '2025-05-15 14:00:00', 2, 8),
(6, 25, '2025-05-12 11:00:00', 1, 7),
(7, 8, '2025-05-16 15:45:00', 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `role`) VALUES
(1, 'Administrateur'),
(2, 'Commercial'),
(3, 'Préparateur');

-- --------------------------------------------------------

--
-- Structure de la table `type_mouvement`
--

CREATE TABLE `type_mouvement` (
  `id_type` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `login` varchar(30) NOT NULL,
  `pwd_hash` varchar(72) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `login`, `pwd_hash`, `last_login`, `id_role`) VALUES
(1, 'Test', 'Test', 'test@test.com', 'test', '$2y$10$YMfhEoNm8tgkhfSm.kzGF.kezvI4p/SRqFp3VBR45KikqVUsKQc1y', '2025-07-17 13:47:40', 1),
(2, 'Laurent', 'Claire', 'claire.laurent@lamb.com', 'clairel', '$2y$10$dXJazd1Xvz1234examplehashedPWD', NULL, 2),
(3, 'Fabrice', 'Fief', 'fabrice.fief@lamb.com', 'Fabgaming', '$2y$10$dXJazd1Xvz1234examplehashedPWD', NULL, 3),
(5, 'Gui', 'Guizma', 'Guizma@lamb.com', 'guizma', '$2y$10$YMfhEoNm8tgkhfSm.kzGF.kezvI4p/SRqFp3VBR45KikqVUsKQc1y', '2025-07-05 20:14:32', 3),
(6, 'feur', 'feur', 'prepa@test.com', 'feur', '$2y$10$upJMBmEhD40QgsfmEVp1QuTVLEzKFKRxavRyGILn6C11q6Jz87rSm', '2025-07-17 13:50:32', 3);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_categorie` (`id_categorie`),
  ADD KEY `fk_article_fournisseur` (`id_fournisseur_principal`);

--
-- Index pour la table `article_lot`
--
ALTER TABLE `article_lot`
  ADD PRIMARY KEY (`id_article`,`id_lot`),
  ADD KEY `id_lot` (`id_lot`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `commande_fournisseur`
--
ALTER TABLE `commande_fournisseur`
  ADD PRIMARY KEY (`id_commande_fournisseur`),
  ADD UNIQUE KEY `unique_numero_commande` (`numero_commande`),
  ADD KEY `fk_commande_fournisseur` (`id_fournisseur`),
  ADD KEY `fk_commande_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_commande_fournisseur_statut` (`statut`),
  ADD KEY `idx_commande_fournisseur_date` (`date_commande`);

--
-- Index pour la table `commande_lot`
--
ALTER TABLE `commande_lot`
  ADD PRIMARY KEY (`id_lot`,`id_commande`),
  ADD KEY `id_commande` (`id_commande`);

--
-- Index pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD PRIMARY KEY (`id_fournisseur`),
  ADD KEY `idx_fournisseur_actif` (`actif`),
  ADD KEY `idx_fournisseur_nom` (`nom`);

--
-- Index pour la table `livraison`
--
ALTER TABLE `livraison`
  ADD PRIMARY KEY (`id_livraison`),
  ADD UNIQUE KEY `unique_numero_livraison` (`numero_livraison`),
  ADD KEY `fk_livraison_fournisseur` (`id_fournisseur`),
  ADD KEY `fk_livraison_commande` (`id_commande_fournisseur`),
  ADD KEY `fk_livraison_utilisateur` (`id_utilisateur_reception`),
  ADD KEY `idx_livraison_statut` (`statut`),
  ADD KEY `idx_livraison_date` (`date_livraison`);

--
-- Index pour la table `lot`
--
ALTER TABLE `lot`
  ADD PRIMARY KEY (`id_lot`);

--
-- Index pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD PRIMARY KEY (`id_mouvement`),
  ADD KEY `id_type` (`id_type`),
  ADD KEY `id_article` (`id_article`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Index pour la table `type_mouvement`
--
ALTER TABLE `type_mouvement`
  ADD PRIMARY KEY (`id_type`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id_article` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `article_lot`
--
ALTER TABLE `article_lot`
  MODIFY `id_article` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `commande_fournisseur`
--
ALTER TABLE `commande_fournisseur`
  MODIFY `id_commande_fournisseur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  MODIFY `id_fournisseur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `livraison`
--
ALTER TABLE `livraison`
  MODIFY `id_livraison` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `lot`
--
ALTER TABLE `lot`
  MODIFY `id_lot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  MODIFY `id_mouvement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `type_mouvement`
--
ALTER TABLE `type_mouvement`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `Article_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`),
  ADD CONSTRAINT `fk_article_fournisseur` FOREIGN KEY (`id_fournisseur_principal`) REFERENCES `fournisseur` (`id_fournisseur`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Contraintes pour la table `commande_fournisseur`
--
ALTER TABLE `commande_fournisseur`
  ADD CONSTRAINT `fk_commande_fournisseur` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_lot`
--
ALTER TABLE `commande_lot`
  ADD CONSTRAINT `Commande_Lot_ibfk_1` FOREIGN KEY (`id_lot`) REFERENCES `lot` (`id_lot`),
  ADD CONSTRAINT `Commande_Lot_ibfk_2` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`);

--
-- Contraintes pour la table `livraison`
--
ALTER TABLE `livraison`
  ADD CONSTRAINT `fk_livraison_commande` FOREIGN KEY (`id_commande_fournisseur`) REFERENCES `commande_fournisseur` (`id_commande_fournisseur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_livraison_fournisseur` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_livraison_utilisateur` FOREIGN KEY (`id_utilisateur_reception`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

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
