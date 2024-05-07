-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 08, 2024 at 11:07 AM
-- Server version: 8.0.29
-- PHP Version: 8.1.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `virtuelle`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_commissions`
--

CREATE TABLE `account_commissions` (
  `id` char(36) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `solde` int DEFAULT NULL,
  `partenaire_id` char(36) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `account_commissions`
--

INSERT INTO `account_commissions` (`id`, `solde`, `partenaire_id`, `deleted`, `created_at`, `updated_at`) VALUES
('33f49edc-5fc0-49ea-a2bc-216f0bf4ffa6', 0, 'd510a5aa-bd98-4ab8-ba72-0d7c3506f6c7', 0, '2024-03-04 11:41:52', '2024-03-04 11:41:52'),
('9aa8ae02-473f-41f5-984f-78100c35b93f', 0, 'ddb55ffc-3c22-498c-800b-8ca568690339', 0, '2024-02-27 17:33:12', '2024-02-27 17:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `account_commission_operations`
--

CREATE TABLE `account_commission_operations` (
  `id` char(36) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `reference_bcb` varchar(255) NOT NULL,
  `reference_gtp` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `account_commission_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `account_distributions`
--

CREATE TABLE `account_distributions` (
  `id` char(36) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `partenaire_id` char(50) DEFAULT NULL,
  `solde` int DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `account_distributions`
--

INSERT INTO `account_distributions` (`id`, `partenaire_id`, `solde`, `deleted`, `updated_at`, `created_at`) VALUES
('0e287016-01d9-4f4c-8811-833fe723bf65', 'd510a5aa-bd98-4ab8-ba72-0d7c3506f6c7', 0, 0, '2024-03-04 11:41:52', '2024-03-04 11:41:52'),
('4964b882-a1ca-49a1-ab25-09425e32873b', 'ddb55ffc-3c22-498c-800b-8ca568690339', 0, 0, '2024-02-27 17:33:12', '2024-02-27 17:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `account_distribution_operations`
--

CREATE TABLE `account_distribution_operations` (
  `id` char(36) NOT NULL DEFAULT '',
  `reference_bcb` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `reference_gtp` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `user_id` char(50) DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `rechargement_partenaire_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `account_distribution_id` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `account_ventes`
--

CREATE TABLE `account_ventes` (
  `id` char(36) NOT NULL DEFAULT '',
  `partenaire_id` char(50) DEFAULT NULL,
  `solde` int DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `account_ventes`
--

INSERT INTO `account_ventes` (`id`, `partenaire_id`, `solde`, `deleted`, `created_at`, `updated_at`) VALUES
('29ef00b1-0669-4e20-9f05-c61db7578460', 'd510a5aa-bd98-4ab8-ba72-0d7c3506f6c7', 0, 0, '2024-03-04 11:41:52', '2024-03-04 11:41:52'),
('9ab15553-e84e-40fe-8b56-51e5ea7cef86', 'ddb55ffc-3c22-498c-800b-8ca568690339', 0, 0, '2024-02-27 17:33:12', '2024-02-27 17:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `account_vente_operations`
--

CREATE TABLE `account_vente_operations` (
  `id` char(36) NOT NULL DEFAULT '',
  `account_vente_id` char(50) DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `nombre` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `user_id` char(50) DEFAULT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  `vente_partenaire_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `api_partenaire_accounts`
--

CREATE TABLE `api_partenaire_accounts` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL DEFAULT '0',
  `balance` float DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `secret_api_key` varchar(255) DEFAULT NULL,
  `public_api_key` varchar(255) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `api_partenaire_fees`
--

CREATE TABLE `api_partenaire_fees` (
  `id` char(36) NOT NULL DEFAULT '',
  `api_partenaire_account_id` char(50) DEFAULT NULL,
  `type_fee` varchar(50) DEFAULT NULL,
  `beguin` int NOT NULL,
  `end` int DEFAULT NULL,
  `value` float DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `api_partenaire_transactions`
--

CREATE TABLE `api_partenaire_transactions` (
  `id` char(36) NOT NULL DEFAULT '',
  `api_partenaire_account_id` char(50) NOT NULL DEFAULT '0',
  `reference` varchar(50) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '0',
  `montant` float DEFAULT NULL,
  `frais` float DEFAULT NULL,
  `commission` float DEFAULT NULL,
  `solde_avant` float DEFAULT NULL,
  `solde_apres` float DEFAULT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `validate_id` varchar(50) DEFAULT NULL,
  `validate_time` datetime DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bcc_payments`
--

CREATE TABLE `bcc_payments` (
  `id` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `status` tinyint NOT NULL,
  `montant` int NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaires`
--

CREATE TABLE `beneficiaires` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_client_id` varchar(50) NOT NULL DEFAULT '0',
  `avatar` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaire_bcvs`
--

CREATE TABLE `beneficiaire_bcvs` (
  `id` char(36) NOT NULL DEFAULT '',
  `beneficiaire_id` varchar(50) NOT NULL DEFAULT '',
  `last_digits` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `customer_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `user_client_id` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaire_cards`
--

CREATE TABLE `beneficiaire_cards` (
  `id` char(36) NOT NULL DEFAULT '',
  `beneficiaire_id` varchar(50) DEFAULT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `last_digits` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaire_momos`
--

CREATE TABLE `beneficiaire_momos` (
  `id` char(36) NOT NULL DEFAULT '',
  `beneficiaire_id` varchar(50) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `compte_commissions`
--

CREATE TABLE `compte_commissions` (
  `id` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `solde` float NOT NULL DEFAULT '0',
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `compte_commissions`
--

INSERT INTO `compte_commissions` (`id`, `libelle`, `solde`, `deleted`, `created_at`, `updated_at`) VALUES
('02e9858b-0446-4c53-b163-9db0ee430820', 'UBA', 0, 0, '2024-02-15 19:18:39', '2024-02-26 12:39:44'),
('a8024a74-5fac-49a7-bae6-ff375ed9a883', 'ELG', 0, 0, '2024-02-15 19:18:21', '2024-02-26 12:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `compte_commission_operations`
--

CREATE TABLE `compte_commission_operations` (
  `id` varchar(255) NOT NULL,
  `compte_commission_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `type_operation` varchar(255) NOT NULL,
  `montant` int NOT NULL,
  `frais` int NOT NULL,
  `commission` int NOT NULL,
  `reference_gtp` varchar(255) NOT NULL,
  `status` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `compte_commission_operations`
--

INSERT INTO `compte_commission_operations` (`id`, `compte_commission_id`, `type_operation`, `montant`, `frais`, `commission`, `reference_gtp`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
('54085d53-53ad-4b56-aece-df754aea909c', '02e9858b-0446-4c53-b163-9db0ee430820', 'rechargement', 2000, 10, 4, '695872534', 0, 0, '2024-02-15 19:47:28', '2024-02-15 19:47:28'),
('60f68fc1-37b5-46d1-b481-68941e72746c', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'rechargement', 2000, 10, 6, '695872534', 0, 0, '2024-02-15 19:47:28', '2024-02-15 19:47:28'),
('b385daa4-cc7a-4d1f-9359-fb3182165c9f', '02e9858b-0446-4c53-b163-9db0ee430820', 'rechargement', 5000, 25, 10, '695873597', 0, 0, '2024-02-26 12:39:44', '2024-02-26 12:39:44'),
('c87afa6b-c2da-4c53-8e1c-3ee4515d9cae', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'rechargement', 5000, 25, 15, '695873597', 0, 0, '2024-02-26 12:39:44', '2024-02-26 12:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `compte_mouvements`
--

CREATE TABLE `compte_mouvements` (
  `id` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `solde` float NOT NULL DEFAULT '0',
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compte_mouvement_operations`
--

CREATE TABLE `compte_mouvement_operations` (
  `id` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `solde` float NOT NULL DEFAULT '0',
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `depots`
--

CREATE TABLE `depots` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_client_id` char(50) DEFAULT NULL,
  `user_card_id` int DEFAULT NULL,
  `user_partenaire_id` char(50) DEFAULT NULL,
  `partenaire_id` char(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `montant_recu` int DEFAULT NULL,
  `frais` int DEFAULT NULL,
  `reference_gtp` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending',
  `reasons` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `canceller_id` varchar(255) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `refunder_id` varchar(255) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refunded_reference` varchar(255) NOT NULL,
  `cancel_motif` text NOT NULL,
  `is_debited` tinyint DEFAULT NULL,
  `is_credited` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `frais`
--

CREATE TABLE `frais` (
  `id` char(36) NOT NULL DEFAULT '',
  `type_operation` varchar(50) DEFAULT NULL,
  `start` int DEFAULT NULL,
  `end` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `type_commission_partenaire` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `value_commission_partenaire` float NOT NULL DEFAULT '0',
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `frais`
--

INSERT INTO `frais` (`id`, `type_operation`, `start`, `end`, `type`, `value`, `type_commission_partenaire`, `value_commission_partenaire`, `deleted`, `created_at`, `updated_at`) VALUES
('18a782d8-d76e-42dd-8e32-ee6194551a8d', 'depot', 1, 2000000, 'pourcentage', 0.5, 'pourcentage', 20, 0, '2024-02-15 19:23:56', '2024-02-15 19:23:56'),
('a49776b8-f937-475d-9cff-657959585e9d', 'transfert', 1, 2000000, 'pourcentage', 0.5, 'pourcentage', 0, 0, '2024-02-15 19:22:58', '2024-02-15 19:22:58'),
('b1c85a4b-fc3e-4d6c-bf50-0afe93923e08', 'rechargement', 1, 2000000, 'pourcentage', 0.5, 'pourcentage', 0, 0, '2024-02-15 19:22:11', '2024-02-15 19:22:11'),
('f23aea61-ee82-42b6-a872-ef87ecbecb7e', 'retrait', 1, 2000000, 'pourcentage', 0.5, 'pourcentage', 20, 0, '2024-02-15 19:24:51', '2024-02-15 19:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `frai_compte_commissions`
--

CREATE TABLE `frai_compte_commissions` (
  `id` varchar(255) NOT NULL,
  `frai_id` varchar(255) NOT NULL,
  `compte_commission_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` int NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `frai_compte_commissions`
--

INSERT INTO `frai_compte_commissions` (`id`, `frai_id`, `compte_commission_id`, `type`, `value`, `deleted`, `created_at`, `updated_at`) VALUES
('0135468d-f3ee-4ed8-8af1-ab9ca470e85c', 'f23aea61-ee82-42b6-a872-ef87ecbecb7e', '02e9858b-0446-4c53-b163-9db0ee430820', 'pourcentage', 40, 0, '2024-02-15 19:24:51', '2024-02-15 19:24:51'),
('0df724fb-48fe-4a1a-b8ac-7536d70e5233', 'a49776b8-f937-475d-9cff-657959585e9d', '02e9858b-0446-4c53-b163-9db0ee430820', 'pourcentage', 40, 0, '2024-02-15 19:22:58', '2024-02-15 19:22:58'),
('6fbae034-4711-487d-a591-77df32e5afce', 'b1c85a4b-fc3e-4d6c-bf50-0afe93923e08', '02e9858b-0446-4c53-b163-9db0ee430820', 'pourcentage', 40, 0, '2024-02-15 19:22:11', '2024-02-15 19:22:11'),
('819dc18b-c922-45da-b2e8-994998137741', 'b1c85a4b-fc3e-4d6c-bf50-0afe93923e08', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'pourcentage', 60, 0, '2024-02-15 19:22:11', '2024-02-15 19:22:11'),
('941e45a6-80ba-4ef1-b0f8-cb6a60b1aa1e', 'a49776b8-f937-475d-9cff-657959585e9d', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'pourcentage', 60, 0, '2024-02-15 19:22:58', '2024-02-15 19:22:58'),
('9cae905a-97f6-4252-96af-8ff25af74f7a', '18a782d8-d76e-42dd-8e32-ee6194551a8d', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'pourcentage', 40, 0, '2024-02-15 19:23:56', '2024-02-15 19:23:56'),
('bc89093e-4953-4268-bcd5-f938504feee9', '18a782d8-d76e-42dd-8e32-ee6194551a8d', '02e9858b-0446-4c53-b163-9db0ee430820', 'pourcentage', 40, 0, '2024-02-15 19:23:56', '2024-02-15 19:23:56'),
('e8284c4e-a148-4c68-8399-40080363416a', 'f23aea61-ee82-42b6-a872-ef87ecbecb7e', 'a8024a74-5fac-49a7-bae6-ff375ed9a883', 'pourcentage', 40, 0, '2024-02-15 19:24:51', '2024-02-15 19:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `front_payments`
--

CREATE TABLE `front_payments` (
  `id` varchar(255) NOT NULL,
  `montant` double NOT NULL,
  `moyen_paiement` varchar(255) NOT NULL,
  `reference_paiement` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `user_client_id` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gammes`
--

CREATE TABLE `gammes` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(50) DEFAULT NULL,
  `prix` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `infos`
--

CREATE TABLE `infos` (
  `id` varchar(255) NOT NULL,
  `card_max` int NOT NULL,
  `card_price` int NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `infos`
--

INSERT INTO `infos` (`id`, `card_max`, `card_price`, `deleted`, `created_at`, `updated_at`) VALUES
('8a8b0616-9716-41c8-ace2-0c201375e434', 10, 50, 0, '2023-11-20 16:32:16', '2023-11-30 15:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `kkiapay_recharges`
--

CREATE TABLE `kkiapay_recharges` (
  `id` varchar(255) NOT NULL,
  `montant` int NOT NULL,
  `reference` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_clients`
--

CREATE TABLE `kyc_clients` (
  `id` char(36) NOT NULL DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `birthday` varchar(15) DEFAULT NULL,
  `departement` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `piece_type` int DEFAULT NULL,
  `piece_id` varchar(50) DEFAULT NULL,
  `piece_file` varchar(255) DEFAULT NULL,
  `partenaire_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `user_with_piece` varchar(255) DEFAULT NULL,
  `job` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `salary` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `agreement` tinyint DEFAULT NULL,
  `user_partenaire_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `user_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `kyc_clients`
--

INSERT INTO `kyc_clients` (`id`, `name`, `lastname`, `email`, `telephone`, `birthday`, `departement`, `city`, `country`, `address`, `piece_type`, `piece_id`, `piece_file`, `partenaire_id`, `deleted`, `created_at`, `updated_at`, `user_with_piece`, `job`, `salary`, `agreement`, `user_partenaire_id`, `user_id`) VALUES
('11aee9e4-31e3-4df7-9760-d13af6a3bda8', 'GBEVE', 'Aurens Exaucee', 'aurensahd@gmail.com', '263 62617848', '01-JAN-1970', 'LI', 'Cotonou', 'Benin', 'Cadjehoun, haie vive', 2, '1234567855', NULL, NULL, 0, '2024-02-15 16:44:12', '2024-02-29 11:03:11', '/storage/pieces/user_with_pieces/2MOCpCltqSERQmGqEvU0qEelyS9jaQvP9hgh52ob.jpg', 'Informaticien', '200000', 1, '', ''),
('15429978-6c9c-4736-85fa-3defd75dfa88', NULL, NULL, NULL, '229 41358941', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 16:55:41', '2024-02-15 16:55:41', NULL, NULL, NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `limits`
--

CREATE TABLE `limits` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_partenaire_id` varchar(50) NOT NULL DEFAULT '0',
  `type_operation` varchar(50) DEFAULT NULL,
  `partenaire_id` int DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mouchards`
--

CREATE TABLE `mouchards` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `user_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mouchard_partenaires`
--

CREATE TABLE `mouchard_partenaires` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `user_partenaire_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `partenaires`
--
-- Error reading structure for table virtuelle.partenaires: #1812 - Tablespace is missing for table virtuelle/partenaires.
-- Error reading data for table virtuelle.partenaires: #1812 - Tablespace is missing for table virtuelle/partenaires.

-- --------------------------------------------------------

--
-- Table structure for table `partner_all_wallets`
--

CREATE TABLE `partner_all_wallets` (
  `id` varchar(255) NOT NULL,
  `solde` int NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `partner_all_wallets`
--

INSERT INTO `partner_all_wallets` (`id`, `solde`, `deleted`, `created_at`, `updated_at`) VALUES
('050b790b-b792-4cd9-84cd-e8a934539f54', 500000, 0, '2024-01-22 15:57:03', '2024-02-09 15:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `partner_all_wallet_details`
--

CREATE TABLE `partner_all_wallet_details` (
  `id` varchar(255) NOT NULL,
  `partenaire_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `libelle` varchar(255) NOT NULL,
  `sens` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_wallets`
--

CREATE TABLE `partner_wallets` (
  `id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `phone_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `customer_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `last_digits` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint NOT NULL,
  `partenaire_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `partner_wallets`
--

INSERT INTO `partner_wallets` (`id`, `type`, `phone`, `phone_code`, `customer_id`, `last_digits`, `deleted`, `partenaire_id`, `created_at`, `updated_at`) VALUES
('005a2b7d-48d6-4d4d-a35f-88848a9da09e', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:03', '2024-01-23 22:59:03'),
('0385b2d5-0c34-415a-8868-f4d1bd303a6b', 'momo', '61000000', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:44:26', '2023-12-18 17:22:20'),
('0c578945-9de1-45f1-a0d1-06819843651c', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:07', '2024-01-23 22:59:07'),
('254329dc-8abc-40d8-a8f9-a7cfd3a6b762', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:24:37', '2024-01-23 22:30:26'),
('3c13bbd7-50be-45df-917e-23418a5a4aef', 'bmo', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 10:51:57', '2024-01-23 22:36:20'),
('48734b37-f2d1-469e-a816-a37f0743d9bf', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:24:59', '2024-01-23 22:37:36'),
('4bd9341e-fcae-4262-b948-b003cdb273bf', 'flooz', '61000000', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:53:11', '2023-12-18 16:53:11'),
('51c16d52-3b24-47f8-9700-ab055a6fa17c', 'momo', '90391589', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-24 08:42:31', '2024-01-24 08:42:31'),
('5aff5849-cbd3-40d8-8c1e-153e7a184725', 'momo', '61000000', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:20:25', '2023-12-18 17:35:53'),
('60dc0fcb-0195-40a8-a47a-f78f3fa3c1b4', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:08', '2024-01-23 22:59:08'),
('754ebc2a-0e78-40d3-91b5-9507b655b9b4', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:59:41', '2023-12-18 17:36:09'),
('83015e06-0085-4494-a373-e89e2fd37160', 'bmo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 09:37:36', '2024-01-23 09:37:36'),
('83c8c1d0-95c2-43ce-88c6-6fdc292b8b50', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:06', '2024-01-23 22:59:06'),
('87e4dfb7-e1d6-429b-ae2b-7ccdad6d5626', 'visa', NULL, NULL, '1234567', '1234', 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:12:49', '2024-01-23 12:12:49'),
('8b75bb9e-b5c1-4876-94d3-64678bbbfb26', 'bmo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 10:52:04', '2024-01-23 10:52:04'),
('8bbcff51-97d1-41ff-a4ca-db3d8b75d3e9', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:13', '2024-01-23 22:59:13'),
('93df0b9a-02fe-48a0-a8a2-f5b884328d15', 'bmo', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 10:52:01', '2024-01-23 22:41:01'),
('a1c43806-27ca-4df2-ba55-aea324edd007', 'momo', '61000000', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:45:14', '2023-12-18 20:37:47'),
('b84f1262-4836-41f0-8ac0-1b8d5ccd5c97', 'flooz', '61000000', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:48:39', '2023-12-18 17:36:21'),
('c009cbcc-2729-498c-a968-ee4b66720491', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:24:57', '2024-01-23 22:38:01'),
('e382902f-364c-45a4-b318-a40f4f034bf9', 'momo', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:11', '2024-01-24 08:41:33'),
('ea5a98b8-8076-43e9-8be9-131f32551a73', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 17:01:35', '2024-01-23 22:37:55'),
('ead67b1a-49a9-420f-bb50-59a381d22691', 'bmo', '62617848', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2023-12-18 16:57:20', '2024-01-23 22:40:53'),
('edc883ee-c6ff-4b92-ace8-de172580392e', 'momo', '96029830', '229', NULL, NULL, 0, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 22:59:10', '2024-01-23 22:59:10'),
('efde6cd5-d7f7-462e-80e8-13bc3f1d7d26', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:24:58', '2024-01-23 22:40:44'),
('f7a4dccb-41cd-4f03-8c9d-5d1bb4fbf5b8', 'bcv', '96029830', '229', NULL, NULL, 1, 'c42949b1-dd22-4ca6-bdcc-a0a0cb333d3c', '2024-01-23 12:24:59', '2024-01-23 22:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `partner_wallet_deposits`
--

CREATE TABLE `partner_wallet_deposits` (
  `id` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `partenaire_id` varchar(255) NOT NULL,
  `wallet_id` varchar(255) NOT NULL,
  `montant` int NOT NULL,
  `status` varchar(255) NOT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `is_debited` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `partner_wallet_withdraws`
--

CREATE TABLE `partner_wallet_withdraws` (
  `id` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `partenaire_id` varchar(255) NOT NULL,
  `wallet_id` varchar(255) NOT NULL,
  `montant` int NOT NULL,
  `status` varchar(255) NOT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `reference_bcb` varchar(255) NOT NULL,
  `reference_operateur` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `reference_gtp_credit` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_questions`
--

CREATE TABLE `password_reset_questions` (
  `id` varchar(255) NOT NULL,
  `libelle` mediumtext NOT NULL,
  `status` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_reset_questions`
--

INSERT INTO `password_reset_questions` (`id`, `libelle`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
('0053db0b-06cf-4246-8d1c-1e91ec968ad3', 'Quel est le prénom de votre cousin préféré ?', 1, 0, '2024-02-13 16:35:04', '2024-02-13 16:35:04'),
('0c170dcd-f3bf-4a80-86d7-42c1c12c1b86', 'Dans quelle ville êtes-vous né(e) ?', 1, 0, '2024-02-13 16:29:19', '2024-02-13 16:29:19'),
('0ed91a8a-813d-4c02-bdeb-0b55d69cd0c0', 'test de l\'ajout', 1, 1, '2024-02-13 21:05:18', '2024-02-13 21:13:12'),
('4f664f98-ca86-4222-8be1-9b1609577a95', 'Quel est le nom de votre école primaire ?', 1, 0, '2024-02-13 16:29:19', '2024-02-13 16:29:19'),
('91572f51-7eaf-40df-b030-08918fab760e', 'Quel est le nom de jeune fille de votre mère ?', 1, 0, '2024-02-13 21:36:54', '2024-02-13 21:36:54'),
('96e5bd3d-593f-48ff-b7ae-4051cd646f56', 'Quel est le prénom de votre père ?', 1, 0, '2024-02-13 16:32:44', '2024-02-13 21:11:52'),
('df2e3da3-1dbe-4310-9525-3a7595618d88', 'Quel est votre age en 2024 ?', 1, 0, '2024-02-13 16:32:44', '2024-02-13 21:10:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_question_clients`
--

CREATE TABLE `password_reset_question_clients` (
  `id` varchar(255) NOT NULL,
  `user_client_id` varchar(255) NOT NULL,
  `password_reset_question` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `answer` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_reset_question_clients`
--

INSERT INTO `password_reset_question_clients` (`id`, `user_client_id`, `password_reset_question`, `answer`, `deleted`, `created_at`, `updated_at`) VALUES
('0c184075-2486-435a-9457-8a7543c879b2', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'Dans quelle ville êtes-vous né(e) ?', 'Cotonou', 0, '2024-02-15 16:47:40', '2024-02-15 16:47:40'),
('1ca30a2b-852e-4b9b-a494-1dcf3e093096', '0c170dcd-f3bf-4a80-86d7-42c1c12c1b86', 'Quel est le prénom de votre cousin préféré ?', 'Cousin', 0, '2024-02-13 19:16:02', '2024-02-13 19:16:02'),
('388756e9-ea4b-4c38-ae91-bcc91cc196ef', '0c170dcd-f3bf-4a80-86d7-42c1c12c1b86', 'Dans quelle ville êtes-vous né(e) ?', 'Ville', 0, '2024-02-13 19:16:02', '2024-02-13 19:16:02'),
('4d327825-462f-4246-b54e-6dc385c8dc17', 'c6194b6e-bd44-46af-b2f2-d910427a33ff', 'Quel est le prénom de votre cousin préféré ?', 'Cousin', 0, '2024-02-13 19:38:57', '2024-02-13 19:38:57'),
('5b4fb06d-3487-4865-9ec4-9b8943ad2785', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'Quel est le nom de jeune fille de votre mère ?', 'Béatrice', 0, '2024-02-15 16:47:40', '2024-02-15 16:47:40'),
('7eabe232-e237-46ba-b4ce-4c8765898925', 'c6194b6e-bd44-46af-b2f2-d910427a33ff', 'Dans quelle ville êtes-vous né(e) ?', 'Ville', 0, '2024-02-13 19:38:57', '2024-02-13 19:38:57'),
('a0f5fd70-20b5-45e5-8a41-b076d7fbc839', '0c170dcd-f3bf-4a80-86d7-42c1c12c1b86', 'Quel est le nom de votre école primaire ?', 'Ecole primaire', 0, '2024-02-13 19:16:02', '2024-02-13 19:16:02'),
('b71edb20-2be4-44be-9766-742ca7138ce9', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'Quel est le prénom de votre cousin préféré ?', 'Paterne', 0, '2024-02-15 16:47:40', '2024-02-15 16:47:40'),
('dcc02720-c0dd-4c6d-bbad-b8a9a837dcba', 'c6194b6e-bd44-46af-b2f2-d910427a33ff', 'Quel est le nom de votre école primaire ?', 'Ecole primaire', 0, '2024-02-13 19:38:57', '2024-02-13 19:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `route` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `libelle`, `type`, `route`, `deleted`, `created_at`, `updated_at`) VALUES
('083aa8c6-e287-4bcf-b322-55370081818d', 'Rechercher transactions clients', 'admin', 'admin.search.transaction.client', 0, '2024-03-05 12:47:42', '2024-03-05 12:47:42'),
('085109ed-f394-445f-a20f-632cf0d57d51', 'Reinitialiser le mot de passe d\'un utlisateur partenaire', 'admin', 'admin.partenaire.user.reset.password', 0, '2024-03-05 11:43:31', '2024-03-05 11:43:31'),
('0e377d1f-6b22-4bef-b8d6-76100669555e', 'Voir le rapport des transactions des clients', 'admin', 'admin.rapport.transaction.client', 0, '2024-03-05 11:05:49', '2024-03-05 11:05:49'),
('1159891d-af75-4263-9bf7-010bae031445', 'Voir les operations partenaires en attentes', 'admin', 'admin.partenaire.operations.attentes', 0, '2024-03-04 21:08:29', '2024-03-04 21:08:29'),
('117185a7-9e38-4f6f-b6c7-d62c7a913c46', 'Supprimer un utilisateur de partenaire', 'admin', 'admin.partenaire.user.delete', 0, '2024-03-05 11:40:25', '2024-03-05 11:40:25'),
('137b6b5c-2b15-4403-b4bd-22fc62f1039c', 'Modifier ses informations de profil', 'admin', 'admin.profile.informations.edit', 0, '2024-03-05 12:41:34', '2024-03-05 12:41:34'),
('13aa13bb-ff4a-4171-ab9f-ace290c47b96', 'Rechercher achat de carte', 'admin', 'admin.search.achat.carte', 0, '2024-03-05 12:50:03', '2024-03-05 12:50:03'),
('1572ccdc-0f33-4e6e-8017-3ef5c67a4ef2', 'Supprimer des questions de reinitialisation', 'admin', 'admin.question.delete', 0, '2024-03-05 11:27:31', '2024-03-05 11:27:31'),
('16d40aae-5d95-4f12-bc3e-33257fa9f290', 'Modifier les informations d\'un utilisateur partenaire', 'admin', 'admin.partenaire.user.edit', 0, '2024-03-05 11:31:40', '2024-03-05 11:31:40'),
('1d01175d-01b3-459c-ab86-2988d1cdbdca', 'Modifier les infos d\'un TPE', 'admin', 'admin.tpe.edit', 0, '2024-03-05 11:11:39', '2024-03-05 11:11:39'),
('203d6102-3c6d-45c0-8ab3-f23ace2e31f5', 'Voir les operations partenaires remboursees', 'admin', 'admin.partenaire.operations.remboursees', 0, '2024-03-04 21:10:02', '2024-03-04 21:10:02'),
('2aaac617-65eb-4109-be79-02b4e2b9fd68', 'Supprimer une permission', 'admin', 'admin.permissions.delete', 0, '2024-03-04 17:05:27', '2024-03-04 17:05:27'),
('2beeb0b3-0bba-4aaf-bdb8-01b26f2a0f26', 'Finaliser une operation cliente', 'admin', 'admin.client.operations.attentes.complete', 0, '2024-03-04 16:52:20', '2024-03-04 16:52:20'),
('2e84bef9-ea58-43a1-be66-99ce5cc94d90', 'Telecharger etat transactions partenaires', 'admin', 'admin.download.transaction.partenaire', 0, '2024-03-05 12:49:27', '2024-03-05 12:49:27'),
('30253264-1f0f-4be3-8658-f13e71006ffc', 'Desactiver un utlisateur de partenaire', 'admin', 'admin.partenaire.user.desactivation', 0, '2024-03-05 11:41:58', '2024-03-05 11:41:58'),
('3075dabf-be9d-4303-a6c0-fc3538cc6110', 'Gerer l\'application cliente', 'admin', 'admin.app.client', 0, '2024-02-23 15:56:56', '2024-02-23 15:56:56'),
('3099901f-13d9-4971-9933-56f88c3f8a8d', 'Rembourser les informations clients en attentes', 'admin', 'admin.partenaire.operations.attentes.refund', 0, '2024-03-04 21:15:34', '2024-03-04 21:15:34'),
('31a5312b-2058-45a6-ad48-87ffd35ceb58', 'Voir les operations clients remboursées', 'admin', 'admin.client.operations.remboursees', 0, '2024-03-04 16:57:43', '2024-03-04 16:57:43'),
('3c4f446f-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des partenaires', 'admin', 'admin.partenaires', 0, '2022-11-17 18:16:14', '2022-11-17 18:16:15'),
('3c4f4bbf-7fbd-11ee-a7db-fa163e0972ee', 'Modifier un role', 'admin', 'admin.roles.edit', 0, '2022-11-18 16:04:22', '2022-11-18 16:04:26'),
('3c4f4df3-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer un role', 'admin', 'admin.roles.delete', 0, '2022-11-18 16:04:22', '2022-11-18 16:04:26'),
('3c4f4efe-7fbd-11ee-a7db-fa163e0972ee', 'Editer le kyc', 'admin', 'admin.edit.kyc', 0, '2022-11-21 17:40:41', '2022-11-21 17:40:42'),
('3c4f4ff2-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des retraits en attente de validation partenaire', 'admin', 'admin.retraits.unvalidate', 0, '2022-12-22 16:07:14', '2022-12-22 16:07:16'),
('3c4f7e15-7fbd-11ee-a7db-fa163e0972ee', 'Validation de retrait', 'admin', 'admin.retrait.validate', 0, '2022-12-22 16:07:25', '2022-12-22 16:07:26'),
('3c4f8074-7fbd-11ee-a7db-fa163e0972ee', 'Telechargement du retrait', 'admin', 'admin.retrait.download', 0, '2022-12-22 16:07:33', '2022-12-22 16:07:34'),
('3c4f818b-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des depots rejetes', 'admin', 'admin.depots.rejetes', 0, '2022-12-22 16:07:42', '2022-12-22 16:07:43'),
('3c4f8286-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des depots en attente de validation partenaire', 'admin', 'admin.depots.unvalidate', 0, '2022-12-22 16:07:51', '2022-12-22 16:07:52'),
('3c4f8455-7fbd-11ee-a7db-fa163e0972ee', 'Annulation de depot', 'admin', 'admin.depot.cancel', 0, '2022-12-22 16:08:01', '2022-12-22 16:08:03'),
('3c4f8547-7fbd-11ee-a7db-fa163e0972ee', 'Validation du depot', 'admin', 'admin.depot.validate', 0, '2022-12-22 16:11:26', '2022-12-22 16:11:27'),
('3c4f8634-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un partenaire', 'admin', 'admin.partenaire.add', 0, '2022-11-17 18:16:16', '2022-11-17 18:16:16'),
('3c4f87f8-7fbd-11ee-a7db-fa163e0972ee', 'Telechargement du depot', 'admin', 'admin.depot.download', 0, '2022-12-22 16:17:04', '2022-12-22 16:17:05'),
('3c4f8901-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des restrictions', 'admin', 'admin.restrictions', 0, '2022-12-22 16:17:30', '2022-12-22 16:18:54'),
('3c4f8ac3-7fbd-11ee-a7db-fa163e0972ee', 'Ajout de restrictions', 'admin', 'admin.restriction.add', 0, '2022-12-22 16:17:36', '2022-12-22 16:18:54'),
('3c4f8bb9-7fbd-11ee-a7db-fa163e0972ee', 'Modifier les restrictions', 'admin', 'admin.restriction.edit', 0, '2022-12-22 16:17:41', '2022-12-22 16:18:55'),
('3c50a886-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer les restrictions', 'admin', 'admin.restriction.delete', 0, '2022-12-22 16:17:46', '2022-12-22 16:18:56'),
('3c519e1d-7fbd-11ee-a7db-fa163e0972ee', 'Activer une restriction', 'admin', 'admin.restriction.activation', 0, '2022-12-22 16:18:02', '2022-12-22 16:18:57'),
('3c51a129-7fbd-11ee-a7db-fa163e0972ee', 'Desactiver une restriction', 'admin', 'admin.restriction.desactivation', 0, '2022-12-22 16:18:09', '2022-12-22 16:18:57'),
('3c51a410-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des limites d\'operation', 'admin', 'admin.limits', 0, '2022-12-22 16:18:13', '2022-12-22 16:18:58'),
('3c51c7f5-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter une limite d\'operation', 'admin', 'admin.limit.add', 0, '2022-12-22 16:18:17', '2022-12-22 16:19:00'),
('3c51fa86-7fbd-11ee-a7db-fa163e0972ee', 'Modifier une limite d\'operaion', 'admin', 'admin.limit.edit', 0, '2022-12-22 16:18:20', '2022-12-22 16:18:59'),
('3c51fc3f-7fbd-11ee-a7db-fa163e0972ee', 'Modifier les partenaires', 'admin', 'admin.partenaire.edit', 0, '2022-11-17 18:16:17', '2022-11-17 18:16:17'),
('3c51fcf0-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer une limite d\'operaion', 'admin', 'admin.limit.delete', 0, '2022-12-22 16:18:24', '2022-12-22 16:19:00'),
('3c51fdad-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des roles', 'admin', 'admin.roles', 0, '2022-12-22 16:18:27', '2022-12-22 16:19:01'),
('3c51ff0c-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter les roles', 'admin', 'admin.roles.add', 0, '2022-12-22 16:18:41', '2022-12-22 16:19:01'),
('3c51ffaf-7fbd-11ee-a7db-fa163e0972ee', 'Modifier les roles', 'admin', 'admin.roles.edit', 0, '2022-12-22 16:18:46', '2022-12-22 16:19:02'),
('3c52004a-7fbd-11ee-a7db-fa163e0972ee', 'Suprimer les roles', 'admin', 'admin.roles.delete', 0, '2022-12-22 16:18:50', '2022-12-22 16:19:03'),
('3c5200e5-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des retraits rejetes', 'admin', 'admin.retraits.rejetes', 0, '2022-12-26 15:22:25', '2022-12-26 15:22:26'),
('3c520237-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un depot', 'admin', 'admin.depots.new', 0, '2022-12-26 16:20:17', '2022-12-26 16:20:18'),
('3c520359-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un retrait', 'admin', 'admin.retraits.new', 0, '2022-12-26 16:20:17', '2022-12-26 16:20:19'),
('3c5205a5-7fbd-11ee-a7db-fa163e0972ee', 'Voir le compte commission', 'admin', 'admin.commissions', 0, '2023-03-23 12:23:18', '2023-03-23 12:23:18'),
('3c520671-7fbd-11ee-a7db-fa163e0972ee', 'Voir le compte distribution', 'admin', 'admin.distributions', 0, NULL, NULL),
('3c520717-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer les partenaires', 'admin', 'admin.partenaire.delete', 0, '2022-11-17 18:16:18', '2022-11-17 18:16:19'),
('3c5207b7-7fbd-11ee-a7db-fa163e0972ee', 'Transferer du compte commission ver le compte distribution', 'admin', 'admin.transfert.commission.distribution', 0, NULL, NULL),
('3c52090d-7fbd-11ee-a7db-fa163e0972ee', 'Retirer du compte commission', 'admin', 'admin.retrait.commission', 0, NULL, NULL),
('3c5209ad-7fbd-11ee-a7db-fa163e0972ee', 'Retirer du compte distribution', 'admin', 'admin.retrait.distribution', 0, NULL, NULL),
('3c520a4b-7fbd-11ee-a7db-fa163e0972ee', 'Voir les clients en attente de validation', 'admin', 'admin.clients.attentes', 0, '2023-03-21 16:33:20', '2023-03-21 16:33:20'),
('3c520ceb-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter commissions', 'admin', 'admin.commissions', 0, '2023-03-23 13:26:32', '2023-03-23 13:26:33'),
('3c520d9d-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter restrictions', 'admin', 'admin.restrictions', 0, '2023-03-23 13:27:22', '2023-03-23 13:27:24'),
('3c520ed2-7fbd-11ee-a7db-fa163e0972ee', 'Voir les details des partenaires', 'admin', 'admin.partenaire.details', 0, '2022-11-17 18:16:19', '2022-11-17 18:16:20'),
('3c521019-7fbd-11ee-a7db-fa163e0972ee', 'Annuler les retraits d\'un partenaire', 'admin', 'admin.partenaire.cancel.retrait', 0, '2022-11-16 18:16:21', '2022-11-17 18:16:22'),
('3c5210ba-7fbd-11ee-a7db-fa163e0972ee', 'Annuler les depots d\'un partenaire', 'admin', 'admin.partenaire.cancel.depot', 0, '2022-11-17 18:16:23', '2022-11-17 18:16:24'),
('3c521152-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des utilisateurs', 'admin', 'admin.users', 0, '2022-11-17 18:16:27', '2022-11-17 18:16:26'),
('3c5211ed-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un utilisateur', 'admin', 'admin.user.add', 0, '2022-11-17 18:16:25', '2022-11-17 18:16:27'),
('3c521335-7fbd-11ee-a7db-fa163e0972ee', 'Modifier un utilisateur', 'admin', 'admin.user.edit', 0, '2022-11-17 18:16:28', '2022-11-17 18:16:29'),
('3c5213d8-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des clients', 'admin', 'admin.clients', 0, '2022-11-17 18:15:59', '2022-11-17 18:16:02'),
('3c52168e-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer un utilisateur', 'admin', 'admin.user.delete', 0, '2022-11-17 18:16:29', '2022-11-17 18:16:30'),
('3c521748-7fbd-11ee-a7db-fa163e0972ee', 'Activer un utilisateur', 'admin', 'admin.user.activation', 0, '2022-11-17 18:16:31', '2022-11-17 18:16:32'),
('3c5217f2-7fbd-11ee-a7db-fa163e0972ee', 'Desactiver un utilisateur', 'admin', 'admin.user.desactivation', 0, '2022-11-17 18:16:31', '2022-11-17 18:16:31'),
('3c521a63-7fbd-11ee-a7db-fa163e0972ee', 'Voir les details des utilisateurs', 'admin', 'admin.user.details', 0, '2022-11-17 18:16:33', '2022-11-17 18:16:34'),
('3c521b68-7fbd-11ee-a7db-fa163e0972ee', 'Reinitialiser le mot de passe des utilisateurs', 'admin', 'admin.user.reset.password', 0, '2022-11-17 18:16:34', '2022-11-17 18:16:35'),
('3c521dd5-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des gammes de cartes', 'admin', 'admin.gammes', 0, '2022-11-17 18:16:35', '2022-11-17 18:16:36'),
('3c521e91-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter une gamme de carte', 'admin', 'admin.gamme.add', 0, '2022-11-17 18:16:37', '2022-11-17 18:16:37'),
('3c521f30-7fbd-11ee-a7db-fa163e0972ee', 'Modifier une gamme de carte', 'admin', 'admin.gamme.edit', 0, '2022-11-17 18:16:38', '2022-11-17 18:16:38'),
('3c521fc7-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer une gamme de carte', 'admin', 'admin.gamme.delete', 0, '2022-11-17 18:16:44', '2022-11-17 18:16:45'),
('3c52718a-7fbd-11ee-a7db-fa163e0972ee', 'Activer une gamme de carte', 'admin', 'admin.gamme.activation', 0, '2022-11-17 18:16:45', '2022-11-17 18:16:46'),
('3c5272ab-7fbd-11ee-a7db-fa163e0972ee', 'Modifier les clients', 'admin', 'admin.client.edit', 0, '2022-11-17 18:16:01', '2022-11-17 18:16:03'),
('3c5275b1-7fbd-11ee-a7db-fa163e0972ee', 'Désactiver une gamme de carte', 'admin', 'admin.gamme.desactivation', 0, '2022-11-17 18:16:46', '2022-11-17 18:16:47'),
('3c527669-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des frais de retraits', 'admin', 'admin.frais', 0, '2022-11-17 18:16:47', '2022-11-17 18:16:48'),
('3c52771d-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un frais', 'admin', 'admin.frais.add', 0, '2022-11-17 18:16:48', '2022-11-17 18:16:49'),
('3c527874-7fbd-11ee-a7db-fa163e0972ee', 'Modifier un frais', 'admin', 'admin.frais.edit', 0, '2022-11-17 18:16:49', '2022-11-17 18:16:50'),
('3c52791c-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer un frais', 'admin', 'admin.frais.delete', 0, '2022-11-17 18:16:50', '2022-11-17 18:16:51'),
('3c5279b4-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des cartes physiques disponibles', 'admin', 'admin.carte.physiques', 0, '2022-11-17 18:16:54', '2022-11-17 18:16:54'),
('3c527a53-7fbd-11ee-a7db-fa163e0972ee', 'Stocker des cartes physiques', 'admin', 'admin.carte.physiques.add', 0, '2022-11-17 18:16:53', '2022-11-17 18:16:53'),
('3c527b9c-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte physiques en attentes', 'admin', 'admin.vente.physiques.attentes', 0, '2022-11-17 18:16:52', '2022-11-17 18:16:52'),
('3c527c44-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer les ventes de carte physiques', 'admin', 'admin.vente.physiques.delete', 0, '2022-11-17 18:16:55', '2022-11-17 18:16:55'),
('3c527cdc-7fbd-11ee-a7db-fa163e0972ee', 'Valider les ventes de carte physiques en attente', 'admin', 'admin.vente.physiques.attentes.validation', 0, '2022-11-17 18:16:56', '2022-11-17 18:16:56'),
('3c527d79-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer des comptes clients', 'admin', 'admin.client.delete', 0, '2022-11-17 18:15:59', '2022-11-17 18:16:04'),
('3c527ebc-7fbd-11ee-a7db-fa163e0972ee', 'Rejeter les ventes de carte physiques en attente', 'admin', 'admin.vente.physiques.attentes.rejet', 0, '2022-11-17 18:16:57', '2022-11-17 18:16:58'),
('3c527f6d-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte physiques finalies', 'admin', 'admin.vente.physiques.finalises', 0, '2022-11-17 18:16:59', '2022-11-17 18:16:59'),
('3c52800d-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte physiques en rejete', 'admin', 'admin.vente.physiques.rejetes', 0, '2022-11-17 18:16:58', '2022-11-17 18:17:00'),
('3c5280a7-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte virtuelle en attentes', 'admin', 'admin.vente.virtuelles.attentes', 0, '2022-11-17 18:17:05', '2022-11-17 18:17:00'),
('3c52813c-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer les ventes de carte virtuelles', 'admin', 'admin.vente.virtuelles.delete', 0, '2022-11-17 18:17:02', '2022-11-17 18:17:01'),
('3c5349f0-7fbd-11ee-a7db-fa163e0972ee', 'Valider les ventes de carte virtuelles en attente', 'admin', 'admin.vente.virtuelles.attentes.validation', 0, '2022-11-17 18:17:02', '2022-11-17 18:17:01'),
('3c534b07-7fbd-11ee-a7db-fa163e0972ee', 'Rejeter les ventes de carte virtuelles en attente', 'admin', 'admin.vente.virtuelles.attentes.rejet', 0, '2022-11-17 18:17:07', '2022-11-17 18:17:07'),
('3c534c65-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte virtuelles finalises', 'admin', 'admin.vente.virtuelles.finalises', 0, '2022-11-17 18:17:09', '2022-11-17 18:17:09'),
('3c534d74-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des ventes de carte virtuelles en rejete', 'admin', 'admin.vente.virtuelles.rejetes', 0, '2022-11-17 18:17:11', '2022-11-17 18:17:08'),
('3c534e1c-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des recharges en attentes', 'admin', 'admin.rechargements.attente', 0, '2022-11-17 18:17:10', '2022-11-17 18:17:14'),
('3c534ec2-7fbd-11ee-a7db-fa163e0972ee', 'Reinitialiser le mot de passe des clients', 'admin', 'admin.client.reset.password', 0, '2022-11-17 18:16:07', '2022-11-17 18:16:08'),
('3c535019-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer les recharges en attentes', 'admin', 'admin.rechargement.attentes.delete', 0, '2022-11-17 18:17:12', '2022-11-17 18:17:14'),
('3c5350bf-7fbd-11ee-a7db-fa163e0972ee', 'Valider les recharges en attentes', 'admin', 'admin.rechargement.attentes.validation', 0, '2022-11-15 18:17:12', '2022-11-17 18:17:13'),
('3c5354c8-7fbd-11ee-a7db-fa163e0972ee', 'Rejeter les recharges en attentes', 'admin', 'admin.rechargement.attentes.rejet', 0, '2022-11-17 18:17:34', '2022-11-17 18:17:36'),
('3c535592-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des rechargements finalises', 'admin', 'admin.rechargement.finalises', 0, '2022-11-17 18:17:35', '2022-11-17 18:17:35'),
('3c5356fb-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des rechargements rejetes', 'admin', 'admin.rechargement.rejetes', 0, '2022-11-17 18:46:31', '2022-11-17 18:17:36'),
('3c5358d8-7fbd-11ee-a7db-fa163e0972ee', 'Activer des comptes clients', 'admin', 'admin.client.activation', 0, '2022-11-17 18:16:09', '2022-11-17 18:16:09'),
('3c535b64-7fbd-11ee-a7db-fa163e0972ee', 'Désactiver des comptes clients', 'admin', 'admin.client.desactivation', 0, '2022-11-17 18:16:10', '2022-11-17 18:16:11'),
('3c535c1a-7fbd-11ee-a7db-fa163e0972ee', 'Voir les retaits en attente de validation client', 'admin', 'admin.retraits', 0, '2022-11-17 18:53:07', '2022-11-17 18:53:06'),
('3c536015-7fbd-11ee-a7db-fa163e0972ee', 'Voir les retraits finalisés', 'admin', 'admin.retraits.finalises', 0, '2022-11-17 18:53:04', '2022-11-17 18:53:05'),
('3c5360cb-7fbd-11ee-a7db-fa163e0972ee', 'Voir les details des comptes clients', 'admin', 'admin.client.details', 0, '2022-11-17 18:16:11', '2022-11-17 18:16:12'),
('3c536411-7fbd-11ee-a7db-fa163e0972ee', 'Faire une operation de retrait', 'admin', 'admin.retrait.create', 0, '2022-11-17 18:53:24', '2022-11-17 18:53:27'),
('3c5364e0-7fbd-11ee-a7db-fa163e0972ee', 'Annuler une operation de retrait', 'admin', 'admin.retrait.cancel', 0, '2022-11-17 18:53:28', '2022-11-17 18:53:27'),
('3c5368a6-7fbd-11ee-a7db-fa163e0972ee', 'Voir les depots en attentes de validation client', 'admin', 'admin.depots', 0, '2022-11-17 18:53:28', '2022-11-17 18:53:31'),
('3c536976-7fbd-11ee-a7db-fa163e0972ee', 'Voir les depots finalisés', 'admin', 'admin.depots.finalises', 0, '2022-11-17 18:53:29', '2022-11-17 18:53:29'),
('3c536a18-7fbd-11ee-a7db-fa163e0972ee', 'Faire une operation de depot', 'admin', 'admin.depot.create', 0, '2022-11-17 18:53:33', '2022-11-17 18:53:31'),
('3c536ab7-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des utilisateurs', 'admin', 'admin.users', 0, '2022-11-17 18:53:38', '2022-11-17 18:53:39'),
('3c53c052-7fbd-11ee-a7db-fa163e0972ee', 'Editer les KYC clients', 'admin', 'admin.kyc.edit', 0, '2022-11-17 18:16:13', '2022-11-17 18:16:14'),
('3c53c1b2-7fbd-11ee-a7db-fa163e0972ee', 'Details des utilisateurs partenaires', 'admin', 'admin.user.details', 0, '2022-11-17 18:53:40', '2022-11-17 18:53:40'),
('3c53c328-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un utilisateur partenaire', 'admin', 'admin.user.add', 0, '2022-11-17 18:53:41', '2022-11-17 18:53:42'),
('3c53c3d5-7fbd-11ee-a7db-fa163e0972ee', 'Modifier un utilisateur partenaire', 'admin', 'admin.user.edit', 0, '2022-11-17 18:53:42', '2022-11-17 18:53:43'),
('3c53c479-7fbd-11ee-a7db-fa163e0972ee', 'Supprimer un utilisateur partenaire', 'admin', 'admin.user.delete', 0, '2022-11-17 18:53:43', '2022-11-17 18:53:44'),
('3c53c526-7fbd-11ee-a7db-fa163e0972ee', 'Reinitialiser le mot de passe d\'un utilisateur partenaire', 'admin', 'admin.user.reset.password', 0, '2022-11-17 18:53:45', '2022-11-17 18:53:46'),
('3c53ca87-7fbd-11ee-a7db-fa163e0972ee', 'Activer un utilisateur partenaire', 'admin', 'admin.user.activation', 0, '2022-11-17 18:53:47', '2022-11-17 18:53:48'),
('3c53cb4c-7fbd-11ee-a7db-fa163e0972ee', 'Desactiver un utilisateur partenaire', 'admin', 'admin.user.desactivation', 0, '2022-11-17 18:53:49', '2022-11-17 18:53:49'),
('3c53cbfe-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un compte client', 'admin', 'admin.client.add', 0, '2022-11-18 10:19:04', '2022-11-18 10:19:11'),
('3c53cd6c-7fbd-11ee-a7db-fa163e0972ee', 'Voir la liste des roles', 'admin', 'admin.roles', 0, '2022-11-18 16:04:24', '2022-11-18 16:04:25'),
('3c53ce24-7fbd-11ee-a7db-fa163e0972ee', 'Ajouter un role', 'admin', 'admin.roles.add', 0, '2022-11-18 16:04:23', '2022-11-18 16:04:26'),
('439a21c2-6da2-442b-8b0b-4234413acb22', 'Desactiver un service', 'admin', 'admin.service.desactivate', 0, '2024-03-05 11:26:29', '2024-03-05 11:26:29'),
('475a48e8-d9a8-4d9e-8821-052734ed45e9', 'Voir les operations clients en attente', 'admin', 'admin.client.operations.attentes', 0, '2024-03-04 16:49:05', '2024-03-04 16:49:05'),
('484b78f2-2d0d-4325-9643-1a4a82a8ee5c', 'Voir les services de l\'application admin', 'admin', 'admin.app.admin', 0, '2024-03-05 13:48:20', '2024-03-05 13:48:20'),
('51abb1e9-d2b6-4fbc-a329-9d3c44575a24', 'Voir les rechargement en attentes', 'admin', 'admin.partenaire.recharge.attentes', 0, '2024-03-04 18:52:57', '2024-03-04 18:52:57'),
('53bdafeb-03cb-4ffe-a9cd-8777f6764890', 'Modifier les restrictions', 'admin', 'admin.restrictions.edit', 0, '2024-03-04 17:17:38', '2024-03-04 17:17:38'),
('542ef9f7-d09b-4084-b371-3200bcf633b5', 'Supprimer un TPE', 'admin', 'admin.tpe.delete', 0, '2024-03-05 11:12:07', '2024-03-05 11:12:07'),
('5bfd4611-f990-428d-a4bf-39824ff808e8', 'Voir les comptes d\'un partenaire', 'admin', 'admin.partenaire.compte', 0, '2024-03-04 17:01:03', '2024-03-04 17:01:03'),
('5c387211-3555-43fa-a95f-847cb4d22514', 'Voir les comptes de commission', 'admin', 'admin.compte.commission', 0, '2024-03-04 17:02:47', '2024-03-04 17:02:47'),
('5df98d81-d09f-4796-90c8-d951fcab6e9a', 'Activé un TPE', 'admin', 'admin.tpe.activation', 0, '2024-03-05 11:12:48', '2024-03-05 11:12:48'),
('618a6799-0168-475e-97fd-1b26434aa65c', 'Voir la liste des TPE', 'admin', 'admin.tpes', 0, '2024-03-05 11:10:04', '2024-03-05 11:10:04'),
('623996c5-0b05-4a6e-b363-468cd2801aea', 'Initié un rechargement partenaire', 'admin', 'admin.partenaire.recharge.init', 0, '2024-03-05 10:56:12', '2024-03-05 10:56:12'),
('64f58284-9bdb-49ff-967f-adf67cb0502b', 'Ajouter un service a l\'application partenaire', 'admin', 'admin.service.partenaire.add', 0, '2024-03-05 11:25:00', '2024-03-05 11:25:00'),
('67b4f929-819f-42e5-abde-ff7a7bef2878', 'Ajouter un utilisateur a un partenaire', 'admin', 'admin.partenaire.user.add', 0, '2024-03-04 17:01:27', '2024-03-04 17:01:27'),
('69fecec0-08ad-4e08-89a1-c024866109f6', 'Voir les operations clients annulées', 'admin', 'admin.client.operations.annulees', 0, '2024-03-04 16:57:57', '2024-03-04 16:57:57'),
('6dc9a393-a073-4c84-95bb-d60f22bfd4f7', 'Voir le compte de mouvements des partenaires', 'admin', 'admin.compte.all.partner', 0, '2024-03-05 12:08:43', '2024-03-05 12:08:43'),
('745882f7-7a35-4e6f-8f7f-16b504ee68fb', 'Annuler une operation cliente', 'admin', 'admin.client.operations.attentes.cancel', 0, '2024-03-04 16:51:29', '2024-03-04 16:51:29'),
('76efdc47-00f4-492b-9c20-24b11a1edf71', 'Ajouter des questions de reinitialisation', 'admin', 'admin.question.add', 0, '2024-03-05 11:27:08', '2024-03-05 11:27:08'),
('7daf4fac-30b5-47b0-b264-d0381d0c1363', 'Ajouter une permmission', 'admin', 'admin.permissions.add', 0, '2024-03-04 17:04:38', '2024-03-04 17:04:38'),
('7e0c2942-2417-4389-8dd1-14c9d8e96f2e', 'Telecharger le revelvé d\'un client', 'admin', 'admin.download.client.revele', 0, '2024-03-04 16:59:27', '2024-03-04 16:59:27'),
('7f35bd3b-c67a-4991-a28f-3496209790e6', 'Rechercher transactions partenaires', 'admin', 'admin.search.transaction.partenaire', 0, '2024-03-05 12:48:42', '2024-03-05 12:48:42'),
('80823292-3bb0-4c27-b91a-fa627fcaf132', 'Voir les restrictions', 'admin', 'admin.restrictions.add', 0, '2024-03-04 17:17:17', '2024-03-04 17:17:17'),
('86448168-ca6d-4a52-91cd-3a482ecec307', 'Annuler les operations partenaires en attente', 'admin', 'admin.partenaire.operations.attentes.cancel', 0, '2024-03-04 21:14:41', '2024-03-04 21:14:41'),
('869941ac-cfda-422a-b607-842f05719fbb', 'Finaliser les operations partenaires en attente', 'admin', 'admin.partenaire.operations.attentes.complete', 0, '2024-03-04 21:16:15', '2024-03-04 21:16:15'),
('8bbec6c4-203c-4f53-9c3c-c05c3c2665ab', 'Ajouter un service a l\'application cliente', 'admin', 'admin.service.client.add', 0, '2024-03-05 11:23:58', '2024-03-05 11:23:58'),
('8e1be5e7-bee3-4acc-a5ef-52a774236a72', 'Administrateur partenaire', 'partner', NULL, 0, '2024-01-25 15:07:56', '2024-01-25 15:07:56'),
('9495ddcd-fe5e-4bd7-bd0f-bef44491f5fb', 'Activer un utlisateur de partenaire', 'admin', 'admin.partenaire.user.activation', 0, '2024-03-05 11:41:44', '2024-03-05 11:41:44'),
('950c4f20-2771-46c4-a5bc-67848495da4f', 'Validation de compte client', 'admin', 'admin.client.validation', 0, '2024-03-04 16:46:59', '2024-03-04 16:46:59'),
('9fcc42be-cdc5-4b6b-80f5-c4aab9956a5a', 'Voir les details d\'un utlisateur partenaire', 'admin', 'admin.partenaire.user.details', 0, '2024-03-05 11:42:40', '2024-03-05 11:42:40'),
('a09902bb-643c-43c1-9911-c6276e068878', 'Changer son mot de passe', 'admin', 'admin.profile.password.change', 0, '2024-03-05 12:45:21', '2024-03-05 12:45:21'),
('a101873e-cd13-4110-a933-4f7b6017cdf6', 'Ajouter un service a l\'application admin', 'admin', 'admin.service.admin.add', 0, '2024-03-05 13:49:15', '2024-03-05 13:49:15'),
('a43eb440-924d-41a2-a9ff-31866d8fcbea', 'Desactivation d\'un utilisateur', 'admin', 'admin.user.desactivation', 0, '2024-01-03 17:10:52', '2024-01-03 17:10:52'),
('a5ec077f-9fb8-4695-99c0-52ffa802afc3', 'Ajouter un nouveau TPE', 'admin', 'admin.tpe.add', 0, '2024-03-05 11:10:51', '2024-03-05 11:10:51'),
('a79a5a65-f72e-4924-b280-5154b5588a5c', 'Voir les comptes de commissions des entités', 'admin', 'admin.compte.commission.detail', 0, '2024-03-05 12:10:17', '2024-03-05 12:10:17'),
('ab776ad0-665e-490e-bbc2-bf72bef0d84c', 'Voir les rapports d\'achats de cartes', 'admin', 'admin.rapport.achat.carte', 0, '2024-03-05 11:08:30', '2024-03-05 11:08:30'),
('abb89077-88c0-4ce9-a72a-40b3448c3459', 'Activer un service', 'admin', 'admin.service.activate', 0, '2024-03-05 11:26:01', '2024-03-05 11:26:01'),
('aef3a472-f13f-4806-9fae-7fc290ddf60e', 'Voir les operations partenaires en annulees', 'admin', 'admin.partenaire.operations.annulees', 0, '2024-03-04 21:11:33', '2024-03-04 21:11:33'),
('b19e0748-01ab-43ae-a067-eb5fe3c0f84b', 'Desactivé un TPE', 'admin', 'admin.tpe.desactivation', 0, '2024-03-05 11:16:23', '2024-03-05 11:16:23'),
('b1c00ada-121b-4000-b415-35566fcacb50', 'Voir la liste des permissions', 'admin', 'admin.permissions', 0, '2024-03-04 17:04:17', '2024-03-04 17:04:17'),
('b1dbd614-6eff-407e-9e60-0e0ddb925960', 'Voir les operations partenaires finalisees', 'admin', 'admin.partenaire.operations.finalisees', 0, '2024-03-04 21:09:16', '2024-03-04 21:09:16'),
('b1f647dd-bf3a-4dc4-9013-28ea71556e88', 'Telecharger etat transactions clients', 'admin', 'admin.download.transaction.client', 0, '2024-03-05 12:48:15', '2024-03-05 12:48:15'),
('b4a40259-b15e-4f06-97ed-9e638104e53f', 'Voir les transferts admin', 'admin', 'admin.transfert', 0, '2024-03-05 12:21:45', '2024-03-05 12:21:45'),
('b6c4b11b-1dc0-4364-9bf0-b34f72a662a0', 'Telecharger etat achat de carte', 'admin', 'admin.download.achat.carte', 0, '2024-03-05 12:50:30', '2024-03-05 12:50:30'),
('b7f29894-bb24-4f14-a9a5-5f30c72e7480', 'Voir les promotions', 'admin', 'admin.promotions', 0, '2024-03-05 12:32:56', '2024-03-05 12:32:56'),
('bc37cd97-8189-4afe-bc72-c638b389af0a', 'Voir les paiements en attente', 'admin', 'admin.client.paiements', 0, '2024-03-04 16:58:59', '2024-03-04 16:58:59'),
('bc4d14ea-223e-495a-8a89-2a618acf9803', 'Voir la liste des comptes clients rejetes', 'admin', 'admin.clients.rejetes', 0, '2024-03-04 16:44:29', '2024-03-04 16:44:29'),
('c0175d8e-db8d-4b07-a5cd-e1e9bc2c783b', 'Voir les soldes de tous les comptes de mouvements', 'admin', 'admin.compte.solde', 0, '2024-03-05 12:07:15', '2024-03-05 12:07:15'),
('c5e3ee14-651e-44bf-94b7-1241ccf35de8', 'Valider un rechargement partenaire', 'admin', 'admin.partenaire.valide.recharge', 0, '2024-03-05 10:59:28', '2024-03-05 10:59:28'),
('cbcb0f96-be84-4d92-8942-ff3685236674', 'Suppprimer un service', 'admin', 'admin.service.delete', 0, '2024-03-05 11:25:35', '2024-03-05 11:25:35'),
('ce9de1bd-6656-437d-843d-74baea6cb2fb', 'Gerer l\'application partenaire', 'admin', 'admin.app.partenaire', 0, '2024-02-23 15:57:56', '2024-02-23 15:57:56'),
('d1b8ba80-c0c3-4cf6-80bb-226f0f71c8da', 'Activer une restrictions', 'admin', 'admin.restrictions.activate', 0, '2024-03-04 17:18:35', '2024-03-04 17:18:35'),
('d919960c-2a8d-47a8-874d-b182d7e09e22', 'Ajouter un compte de commission', 'admin', 'admin.compte.commission.add', 0, '2024-03-04 17:03:05', '2024-03-04 17:03:05'),
('d9f4bc14-5fa4-49c0-b29b-3f802e80b6dc', 'Supprimer une restriction', 'admin', 'admin.restrictions.desactivate', 0, '2024-03-04 17:18:54', '2024-03-04 17:18:54'),
('d9fd07c4-672c-4fb8-96d4-8486b02c2dc2', 'Afficher son profile', 'admin', 'admin.profile', 0, '2024-03-05 12:41:04', '2024-03-05 12:41:04'),
('db387a2e-60bc-41c0-ae8d-c4c91724c88c', 'Rembourser une operation cliente', 'admin', 'admin.client.operations.attentes.refund', 0, '2024-03-04 16:51:56', '2024-03-04 16:51:56'),
('e17e0f10-bfad-4f6e-ab44-1f3911fd8cff', 'Voir les operations clients en finalisees', 'admin', 'admin.client.operations.finalisees', 0, '2024-03-04 16:58:16', '2024-03-04 16:58:16'),
('e7213150-a55b-4931-8886-31e16e132c16', 'Recharger le compte de mouvement des partenaires', 'admin', 'admin.compte.all.partner.recharge', 0, '2024-03-05 12:24:37', '2024-03-05 12:24:37'),
('e8523ad0-b8bb-495b-bfbf-269dbdc2ada5', 'Modifier une permission', 'admin', 'admin.permissions.edit', 0, '2024-03-04 17:05:02', '2024-03-04 17:05:02'),
('ea1c6155-8ad6-47da-9ae2-06ee8b8016e2', 'Effectuer un transfert admin', 'admin', 'admin.transfert.add', 0, '2024-03-05 12:22:36', '2024-03-05 12:22:36'),
('f1bc8fab-2d38-489d-9958-47a13434eed6', 'Voir  les transactions partenaires en attentes', 'admin', 'admin.rapport.transaction.partenaire', 0, '2024-03-05 11:09:24', '2024-03-05 11:09:24'),
('f32fac27-ab58-43a7-ae21-b90910f3388a', 'Supprimer les restrictions', 'admin', 'admin.restrictions.delete', 0, '2024-03-04 17:18:02', '2024-03-04 17:18:02'),
('f7c385f4-1562-4e32-bb0f-2c5f95b4580b', 'Rejeter un compte client', 'admin', 'admin.client.rejet', 0, '2024-03-04 16:48:25', '2024-03-04 16:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` varchar(255) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `type_gain_client` varchar(255) NOT NULL,
  `gain_client` varchar(255) NOT NULL,
  `type_gain_partenaire` varchar(255) NOT NULL,
  `gain_partenaire` varchar(255) NOT NULL,
  `status` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rechargement_partenaires`
--

CREATE TABLE `rechargement_partenaires` (
  `id` char(36) NOT NULL DEFAULT '',
  `partenaire_id` char(50) DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recharges`
--

CREATE TABLE `recharges` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_client_id` char(50) DEFAULT NULL,
  `user_card_id` char(50) DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `moyen_paiement` varchar(50) DEFAULT NULL,
  `reference_operateur` varchar(50) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT 'pending',
  `reasons` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `is_debited` tinyint DEFAULT NULL,
  `is_credited` tinyint DEFAULT NULL,
  `motif_rejet` mediumtext,
  `frais` int DEFAULT NULL,
  `montant_recu` int DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `canceller_id` char(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `refunder_id` char(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refunded_reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `reference_gtp` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recharges`
--

INSERT INTO `recharges` (`id`, `user_client_id`, `user_card_id`, `montant`, `moyen_paiement`, `reference_operateur`, `status`, `reasons`, `is_debited`, `is_credited`, `motif_rejet`, `frais`, `montant_recu`, `solde_avant`, `solde_apres`, `canceller_id`, `refunder_id`, `cancelled_at`, `refunded_at`, `refunded_reference`, `reference_gtp`, `deleted`, `created_at`, `updated_at`) VALUES
('4ef2a816-58f5-4435-8078-20c53f7adc4e', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 5000, 'bmo', '78481708951109', 'completed', NULL, 1, 1, NULL, 25, 4975, 4423, 9398, NULL, NULL, NULL, NULL, NULL, '695873597', 0, '2024-02-26 12:39:29', '2024-02-26 12:39:33'),
('59ca62bd-fc7e-470d-938a-9f4f7983dfa0', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 5000, 'momo', '4wSwFZoh_', 'completed', NULL, 1, 1, NULL, 25, 4975, 4975, 9950, NULL, NULL, NULL, NULL, NULL, '695872530', 0, '2024-02-15 19:26:39', '2024-02-15 19:26:43'),
('5a7bb1b6-85aa-4ccf-8b2c-cba7cfd0e09e', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'a176c5e9-a5d1-45c4-9111-397ccf9ca551', 2000, 'momo', 'ACGd-ZVHH', 'completed', NULL, 1, 1, NULL, 10, 1990, 9950, 11940, NULL, NULL, NULL, NULL, NULL, '695872534', 0, '2024-02-15 19:47:15', '2024-02-15 19:47:17'),
('6a6d4351-8092-4574-9333-8efd411fe3f6', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 2000, 'bmo', '78481708951264', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-26 12:41:29', '2024-02-26 12:41:29'),
('7e784e5e-3c2c-4983-9eea-ddc33286b8d9', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 5000, 'momo', 'Om9kAvymg', 'completed', NULL, 1, 1, NULL, 25, 4975, 0, 4975, NULL, NULL, NULL, NULL, NULL, '695872526', 0, '2024-02-15 19:15:58', '2024-02-15 19:16:01'),
('aced81c0-b1ab-4591-bb20-f855b83656ab', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 1000, 'momo', 'xWifbBrip', 'failed', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 20:17:01', '2024-02-15 20:17:02'),
('d5cf2b00-25a0-432d-af92-c03d900c15b0', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 5000, 'bmo', '78481708949104', 'pending', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-26 12:07:11', '2024-02-26 12:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `restrictions`
--

CREATE TABLE `restrictions` (
  `id` char(36) NOT NULL DEFAULT '',
  `type_operation` varchar(50) DEFAULT NULL,
  `type_restriction` varchar(50) DEFAULT NULL,
  `type_acteur` varchar(50) DEFAULT NULL,
  `valeur` int DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `etat` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `restrictions`
--

INSERT INTO `restrictions` (`id`, `type_operation`, `type_restriction`, `type_acteur`, `valeur`, `periode`, `etat`, `deleted`, `created_at`, `updated_at`) VALUES
('64795f0a-2057-43bd-939e-f380aef6ce3d', 'transfert', 'montant', 'client', 1000000, 'day', 1, 0, '2023-11-29 14:55:46', '2023-11-29 14:55:46'),
('d36ed508-b9cc-48ca-83e1-dd3b0aa4af2d', 'retrait', 'montant', 'client', 1000000, 'day', 1, 0, '2023-11-29 14:52:39', '2023-11-29 14:55:05'),
('fc378718-ac14-4417-a7ab-06ead1c2e4e0', 'depot', 'montant', 'client', 1000000, 'day', 1, 0, '2023-11-29 14:54:48', '2023-11-29 14:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `restriction_agences`
--

CREATE TABLE `restriction_agences` (
  `id` char(36) NOT NULL DEFAULT '',
  `partenaire_id` char(50) DEFAULT NULL,
  `user_partenaire_id` char(50) DEFAULT NULL,
  `createur_id` char(50) DEFAULT NULL,
  `type_operation` varchar(50) DEFAULT NULL,
  `type_restriction` varchar(50) DEFAULT NULL,
  `valeur` int DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `etat` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `retraits`
--

CREATE TABLE `retraits` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_client_id` varchar(50) DEFAULT NULL,
  `user_partenaire_id` varchar(50) DEFAULT NULL,
  `partenaire_id` varchar(50) DEFAULT NULL,
  `user_card_id` varchar(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `frais` int DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reasons` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `cancel_motif` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `reference_gtp` varchar(50) DEFAULT NULL,
  `is_debited` tinyint DEFAULT NULL,
  `is_credited` tinyint DEFAULT NULL,
  `canceller_id` varchar(255) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `refunder_id` varchar(255) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refunded_reference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` char(36) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `libelle`, `type`, `deleted`, `created_at`, `updated_at`) VALUES
('091cedd7-1884-49ed-a806-7452f649cb35', 'Administrateur', 'admin', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('44667efb-f74a-4e63-949e-c75ca349f781', 'Administrateur partenaire', 'partner', 0, '2024-01-25 16:05:26', '2024-01-25 16:05:26'),
('50b99b48-dfa0-4f4e-b383-04c00ffa2816', 'audi', 'admin', 1, '2024-01-25 15:24:37', '2024-01-25 15:34:49'),
('5b75c472-ee15-4b48-9749-326a1aa21d55', 'Intermediaire', 'admin', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', 'Controle', 'admin', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('f85469ae-c80c-4900-9b32-f4ecfe193306', 'Administrateur partenaire', 'partner', 1, '2024-01-25 15:11:52', '2024-01-25 15:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` char(36) NOT NULL DEFAULT '',
  `role_id` char(50) DEFAULT NULL,
  `permission_id` char(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `deleted`, `created_at`, `updated_at`) VALUES
('0053db0b-06cf-4246-8d1c-1e91ec968ad3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f7e15-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('00ed8dc9-6a36-4090-b749-ac5cc2f547a2', '091cedd7-1884-49ed-a806-7452f649cb35', '5c387211-3555-43fa-a95f-847cb4d22514', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('00f846ca-1609-43fb-9808-4aeab6eff62a', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5200e5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('0183f33c-d7ea-4abc-84f0-90a376fa232f', '091cedd7-1884-49ed-a806-7452f649cb35', '475a48e8-d9a8-4d9e-8821-052734ed45e9', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('0231851b-884a-4701-8386-b4edf46c5d11', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52800d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('02a7ef8a-4e8c-4969-ae42-ddb9af2a5234', '091cedd7-1884-49ed-a806-7452f649cb35', '618a6799-0168-475e-97fd-1b26434aa65c', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('03f4c6f8-6bd0-4ebf-b648-705bab00a587', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c535592-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('060fa98b-baf1-448e-9166-8317c9f7d71a', '091cedd7-1884-49ed-a806-7452f649cb35', '869941ac-cfda-422a-b607-842f05719fbb', 0, '2024-03-05 10:49:23', '2024-03-05 10:49:23'),
('061802fc-b66a-4b69-ac82-002aebc46cc2', '091cedd7-1884-49ed-a806-7452f649cb35', '80823292-3bb0-4c27-b91a-fa627fcaf132', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('0637a1b5-215a-47a5-b04d-3cb9b5842cf7', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f4ff2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('072a5b15-52d1-4d99-8812-06ea95f00479', '091cedd7-1884-49ed-a806-7452f649cb35', 'a5ec077f-9fb8-4695-99c0-52ffa802afc3', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('07a976e2-646e-4d65-930e-0eee15ee97f5', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5211ed-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('0827114a-987a-4982-94f1-754497ebc388', '091cedd7-1884-49ed-a806-7452f649cb35', '3c534ec2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('087fdfc7-7014-47b0-b855-20da0dfcc6b0', '091cedd7-1884-49ed-a806-7452f649cb35', '7e0c2942-2417-4389-8dd1-14c9d8e96f2e', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('08e85e59-bdba-457c-886d-9132a7babf18', '091cedd7-1884-49ed-a806-7452f649cb35', 'a79a5a65-f72e-4924-b280-5154b5588a5c', 0, '2024-03-05 12:15:22', '2024-03-05 12:15:22'),
('0900eee9-03e4-48aa-b8b2-30f444d48d67', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5279b4-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('09081c17-2f5f-4203-8216-88037e8abb0d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52004a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('0a4809fa-545d-40ab-80cd-bff363518d32', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c052-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('0b214e07-ec2e-4031-aaec-53aa0e1758de', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f818b-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('0b6083b2-55e4-40cb-9c70-cecbeae5b7a9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c1b2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('0d7b7751-5dce-4b39-8c45-8ff6e8b3e42a', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51ffaf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('0ea2e5cc-8fd7-414e-bd85-10c7175e2213', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f4df3-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('0f974e87-c630-4be5-9154-c4e9fc2f83b0', '091cedd7-1884-49ed-a806-7452f649cb35', '9fcc42be-cdc5-4b6b-80f5-c4aab9956a5a', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('0fd5af66-6884-4d63-8257-3b99a534d0a4', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8455-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('1009b549-7010-4ebc-bf57-8e82f2ab2956', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5368a6-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('1023b7fb-b61f-4c91-b5d0-30101e3d5628', '091cedd7-1884-49ed-a806-7452f649cb35', '53bdafeb-03cb-4ffe-a9cd-8777f6764890', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('1274a043-c475-4ec7-aa94-3674fc8a2ce3', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520671-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('13829801-5d03-43e6-a7a7-d2f864506f02', '091cedd7-1884-49ed-a806-7452f649cb35', '3c536a18-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('13afb8e3-37ea-4461-9d3e-bc2c0ed0bcab', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527a53-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('13cf64a7-d5ab-4e5e-b047-11bc56f9a01a', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8ac3-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('141d1f55-e27b-419a-b035-653292f07454', '091cedd7-1884-49ed-a806-7452f649cb35', 'b1f647dd-bf3a-4dc4-9013-28ea71556e88', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('14fa0306-b53f-46ca-ad56-ffae45703ff1', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520237-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('15490b55-f059-4c3c-b6b4-09e31cf37609', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53cb4c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('1639dd4d-35f3-4d28-b66f-33738720919d', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52791c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('174f5bb0-0922-4a4c-8a5b-3062ea7d5568', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51a410-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('1802c7cb-1c18-4445-b0a1-f5fb178afcc2', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521dd5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('1d779b77-8d9d-49a9-bbf1-cdb28b698b8e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c534e1c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('1eea8321-8dca-44c1-9866-96dbf22d4418', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8547-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('20b5d39b-a4de-4369-804a-bcf538935e02', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5213d8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('20c8fbc6-be26-46dc-8d60-f0dc0a84c765', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c052-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('21b38288-dd3a-4b43-9c9e-c1b994989b41', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5354c8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('226587d5-9f5d-4697-b30a-b66d22bb6c8a', '091cedd7-1884-49ed-a806-7452f649cb35', '64f58284-9bdb-49ff-967f-adf67cb0502b', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('22d8e7c0-611f-47df-8435-1231847d04fb', '091cedd7-1884-49ed-a806-7452f649cb35', '3099901f-13d9-4971-9933-56f88c3f8a8d', 0, '2024-03-05 10:49:22', '2024-03-05 10:49:22'),
('24105b75-d178-4fee-a4f7-46bd7753a1a5', '091cedd7-1884-49ed-a806-7452f649cb35', '7daf4fac-30b5-47b0-b264-d0381d0c1363', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('24e25186-a747-47f0-a5e0-224de21c98c5', '091cedd7-1884-49ed-a806-7452f649cb35', '13aa13bb-ff4a-4171-ab9f-ace290c47b96', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('258d7e97-2c40-4980-a699-cc1d2fe239ec', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53cbfe-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('26464d58-2bdd-4c8d-af5b-cd991972e8a9', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c534ec2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('26e431f2-563f-4391-8f7a-053bc07db2d6', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51fdad-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('26ff5ee5-17ef-4367-979a-93031b9f7206', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521f30-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('27646523-70bf-4a40-8d02-f6c85686307a', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5272ab-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('27c64921-3dbc-4f06-ab93-48a2d18071f6', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51fc3f-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('27d834f8-a2ad-43a5-9a0f-52e1da172917', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c536411-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('282a0a32-0827-4b9f-ba65-5c12f2900d01', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5209ad-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('28da1352-e5e6-4902-bf78-998f6dc3f397', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5360cb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('2ab71192-76b8-4887-bc1a-6cce5d7fb067', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521b68-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('2b9174d5-c2d5-4cd1-beea-558bb152f0ca', '091cedd7-1884-49ed-a806-7452f649cb35', '3c536976-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('2bef7af3-f27b-43b0-b832-5d7a022c0062', '091cedd7-1884-49ed-a806-7452f649cb35', '51abb1e9-d2b6-4fbc-a329-9d3c44575a24', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('2c665eaf-aa4d-4286-92e2-7611ae8f09c0', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520237-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('2cb0707a-cb4b-4f27-92c0-8ccdfbe8ee1e', '091cedd7-1884-49ed-a806-7452f649cb35', 'b1dbd614-6eff-407e-9e60-0e0ddb925960', 0, '2024-03-05 10:49:23', '2024-03-05 10:49:23'),
('2cd3b97f-1d4e-474e-aac7-eb0b66973e52', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8286-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('2cd5a08c-ae51-448b-bb36-b45471d75dd6', '091cedd7-1884-49ed-a806-7452f649cb35', '7f35bd3b-c67a-4991-a28f-3496209790e6', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('2e810b01-7423-45e7-b33b-e2a307f048a9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5209ad-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('2f7c5d5c-a8ba-4680-b656-8a6737c3757b', '091cedd7-1884-49ed-a806-7452f649cb35', '3c535592-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('31d06f13-ade9-4d0d-bd7a-8083a274961d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c534b07-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('32024d2e-a521-4aee-8863-202340e22886', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8286-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('3211a9e4-2ba2-497a-b3e0-3c78c2e82580', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c479-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('346c92d2-d962-4515-a362-078040004c7c', '091cedd7-1884-49ed-a806-7452f649cb35', '5bfd4611-f990-428d-a4bf-39824ff808e8', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('348b9b63-dc82-437e-aea4-1d263df72afa', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51ffaf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('360c3175-0364-4de9-bda6-bb0cac22a0a4', '091cedd7-1884-49ed-a806-7452f649cb35', '3c50a886-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('371d143b-8e39-4865-81d8-f05932f83285', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521152-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('37ddad28-a151-46d8-ab7f-4b4ba8e90ab9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51fcf0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('38243a31-c4a2-418f-91ad-2f10bbc02164', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c536015-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('39364d3b-5956-4a91-9bfc-503c342940e5', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f4ff2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('39dae343-3f08-447e-bfd6-87b4754396f2', '091cedd7-1884-49ed-a806-7452f649cb35', 'cbcb0f96-be84-4d92-8942-ff3685236674', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('3b912393-a8bc-42f5-944e-eefd90451855', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521a63-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('3d4ad01a-0b97-485a-8780-7d7911a30782', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5356fb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('3d68331e-5413-4d59-a201-0a918cc9c67c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520359-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('3d77df19-442b-466a-b2c7-5329690d3a89', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520a4b-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('3e7a058d-8fb3-4a70-b867-f65e7b61065b', '091cedd7-1884-49ed-a806-7452f649cb35', '76efdc47-00f4-492b-9c20-24b11a1edf71', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('3e836fad-c9b1-4035-8db8-8990586dde73', '091cedd7-1884-49ed-a806-7452f649cb35', '623996c5-0b05-4a6e-b363-468cd2801aea', 0, '2024-03-05 11:01:38', '2024-03-05 11:01:38'),
('3f0a45f2-2f10-4449-816a-748b98c711a9', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5368a6-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('40c2fc0b-de97-49f3-9d74-fdfdc18776a3', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8901-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('40c75416-9643-41cf-9c09-2c5dd130f667', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527d79-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('40dc9968-736d-4cf5-aaa3-00c3acd63267', '091cedd7-1884-49ed-a806-7452f649cb35', '86448168-ca6d-4a52-91cd-3a482ecec307', 0, '2024-03-05 10:49:23', '2024-03-05 10:49:23'),
('41852ab6-8b07-4281-a4bf-d45b7bbda2a6', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8074-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('41c4b6d7-53e8-4654-8dcc-e30e55e50fb5', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5364e0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('437e6e86-9dd8-43b6-9d65-5a0aeaaa19cb', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c519e1d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('4620c985-08db-4541-a874-ba5cfc299f2b', '091cedd7-1884-49ed-a806-7452f649cb35', '3c536411-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('4a3464f2-b892-4366-ad6d-738713fabf26', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5205a5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('4d45aacd-9085-46c1-b591-edb54d24e8e8', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5358d8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('4d45b402-528d-403f-8fb7-fb0d86016ef9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c535019-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('4d462d96-a171-4033-8d03-b83920b55ff2', '091cedd7-1884-49ed-a806-7452f649cb35', 'b4a40259-b15e-4f06-97ed-9e638104e53f', 0, '2024-03-05 12:32:01', '2024-03-05 12:32:01'),
('5067bad3-cc56-4c7d-8fda-342bf32ac339', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5217f2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('51856007-e3ac-4099-b31b-1d5282587664', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527c44-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('51fb45d8-8af7-4b95-974d-e8fc110e3bfc', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527c44-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('52c8b4dd-da17-43fa-a971-cf880b3cb61f', '091cedd7-1884-49ed-a806-7452f649cb35', 'a09902bb-643c-43c1-9911-c6276e068878', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('533ae77a-6d04-4e6e-9f2f-d7b2aaf75f17', '091cedd7-1884-49ed-a806-7452f649cb35', '3075dabf-be9d-4303-a6c0-fc3538cc6110', 0, '2024-02-23 15:58:16', '2024-02-23 15:58:16'),
('5344ff2a-4afb-4573-b650-b5d507db57d1', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c536ab7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('53caa25b-3f71-4f74-9660-07991dad2f18', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f4bbf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('554d1f77-b840-4407-a754-3b66819867c1', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51c7f5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('55514a11-ea93-4e46-9c42-b2b9bc7ad641', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520d9d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('5582c0e7-ef30-4a33-982d-b69dad14c407', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521019-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('558a56e6-d07a-430d-9970-a414a4823831', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c536976-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('565c5fa7-1b76-4fdf-bbdb-ee1f81977deb', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520717-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('56f38c12-142f-4dd6-8e2c-35d29def09db', '091cedd7-1884-49ed-a806-7452f649cb35', 'e17e0f10-bfad-4f6e-ab44-1f3911fd8cff', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('57c2cb48-4588-4e13-a0c9-9187b2c30951', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f87f8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('57faee5d-9e02-46b7-9491-e67ad8a64b9c', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52168e-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('585f2c42-efe1-433e-83a5-0970e407e465', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8bb9-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('5863cc35-d431-49fd-9f7e-764aa37c72db', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527874-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('5866e192-7507-4b13-b685-a4cfe969f782', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5207b7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('59be85bf-4e9c-48b7-ba01-35512d30c32a', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521fc7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('5c1de96f-bf21-4c21-bf7a-ccaae37cfaee', '091cedd7-1884-49ed-a806-7452f649cb35', '2beeb0b3-0bba-4aaf-bdb8-01b26f2a0f26', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('5c448691-8081-4e0f-8b4e-75235166fcfa', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5210ba-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('5e530b7f-5a62-4938-ae42-1add66e56cf9', '091cedd7-1884-49ed-a806-7452f649cb35', 'bc4d14ea-223e-495a-8a89-2a618acf9803', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('5ecce097-deba-453b-a248-bf387d188689', '091cedd7-1884-49ed-a806-7452f649cb35', '950c4f20-2771-46c4-a5bc-67848495da4f', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('5f8b7c7d-0582-454b-ab72-8517480921a4', '091cedd7-1884-49ed-a806-7452f649cb35', 'f7c385f4-1562-4e32-bb0f-2c5f95b4580b', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('5f9aed50-1055-4d7f-901b-bd6f28914d3e', '091cedd7-1884-49ed-a806-7452f649cb35', '8bbec6c4-203c-4f53-9c3c-c05c3c2665ab', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('60ac8cea-5460-4df0-a9cb-4fe5b455b45d', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527cdc-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('60b1eab4-5851-4590-a7ce-49ee40eb2235', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520ed2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('60ccf42b-0e2c-4021-a7a2-ab6778157b7e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c526-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('623f4b91-517a-4464-ba92-65c9df28aca2', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5213d8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('64a99e9b-da63-435b-b7a7-039dd63fdeb8', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c534c65-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('6631ee88-2b05-494d-bf1f-3a19051f9c83', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52813c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('6753b0bf-b1b6-4c21-8b2e-67c83282303d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c536ab7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('69c1e8aa-079b-4ae4-9a47-fc713f551434', '091cedd7-1884-49ed-a806-7452f649cb35', '439a21c2-6da2-442b-8b0b-4234413acb22', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('69de3342-b018-4b00-bd89-73b6b6ac207f', '091cedd7-1884-49ed-a806-7452f649cb35', 'e7213150-a55b-4931-8886-31e16e132c16', 0, '2024-03-05 12:32:01', '2024-03-05 12:32:01'),
('69e77450-5943-4286-8509-da2c31aa3f8e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521e91-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('6afaa95b-eb18-4e40-91c6-160f362d1b76', '091cedd7-1884-49ed-a806-7452f649cb35', '5df98d81-d09f-4796-90c8-d951fcab6e9a', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('6b290810-0ffe-4a1f-bb9c-595e32c7d199', '091cedd7-1884-49ed-a806-7452f649cb35', '3c534d74-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('6c37aed9-afd4-481b-ab02-641ec10b6374', '091cedd7-1884-49ed-a806-7452f649cb35', '137b6b5c-2b15-4403-b4bd-22fc62f1039c', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('6cea4d8c-15f3-46c8-bac4-e85be0a203b3', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f446f-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('6d5a7bc5-5390-443a-9a5a-011dc2d68973', '091cedd7-1884-49ed-a806-7452f649cb35', '1d01175d-01b3-459c-ab86-2988d1cdbdca', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('6dbded31-23dc-42cc-9395-18724eb46f4c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52718a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('6fcf555f-9801-499c-9200-03a8ba6b6618', '091cedd7-1884-49ed-a806-7452f649cb35', 'c5e3ee14-651e-44bf-94b7-1241ccf35de8', 0, '2024-03-05 11:01:38', '2024-03-05 11:01:38'),
('7040618e-5e25-4faf-8dbd-4a572e029b3f', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527f6d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('7130e2f3-b537-430b-9d33-717aa7628ffc', '091cedd7-1884-49ed-a806-7452f649cb35', '67b4f929-819f-42e5-abde-ff7a7bef2878', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('72413c69-3df5-488f-b6eb-41a55e805507', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52800d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('74cab1b0-c6f1-4d54-9bd9-f175d7087bf2', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5280a7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('7549aa23-7789-44a8-9436-802d3299d18b', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c328-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('77bf2fb5-6561-4f43-ba33-83f236338631', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520d9d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('77d4827c-d953-4e44-92af-7bd9a4c6c654', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521748-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('786521c9-4012-4848-af45-0c40fa8a3438', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520ed2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('788359df-3191-414e-8383-bdb14423c839', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527b9c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('792f0a24-4c04-4c35-89a2-6b65e8f643d7', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c534d74-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('7a248326-e006-4b30-b396-cc3a8071dc22', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53cd6c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('7a2c01be-0b55-405a-b2df-729feb50fd44', '091cedd7-1884-49ed-a806-7452f649cb35', 'd1b8ba80-c0c3-4cf6-80bb-226f0f71c8da', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('7a5ce705-8c0e-44ac-970e-64be28d1bbe7', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f4bbf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('7ae92701-6903-4daf-83be-443f983f17cd', '091cedd7-1884-49ed-a806-7452f649cb35', '16d40aae-5d95-4f12-bc3e-33257fa9f290', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('7b18d4e2-ca0e-4199-a769-c5568c23fbc3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520717-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('7e35eeab-5d44-452a-9ce4-a11c69d8b650', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51ff0c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('7f9a79b9-e65c-4aaa-8426-a8983e905068', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527f6d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('7fa5c1b4-dd6c-4b68-82b1-7e6d34ec1263', '44667efb-f74a-4e63-949e-c75ca349f781', '8e1be5e7-bee3-4acc-a5ef-52a774236a72', 0, '2024-01-25 16:05:26', '2024-01-25 16:05:26'),
('8186b74e-6b08-4806-961a-e7a7ef15a49c', '091cedd7-1884-49ed-a806-7452f649cb35', '2aaac617-65eb-4109-be79-02b4e2b9fd68', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('81f43912-6689-4454-b323-ef7ade022d61', '091cedd7-1884-49ed-a806-7452f649cb35', 'b1c00ada-121b-4000-b415-35566fcacb50', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('8378eb96-7e7e-4a3c-b29f-5c75b297f346', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52791c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('83b1699d-9eff-4c78-8259-016505fa041a', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c3d5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('840ca151-76be-43a3-be5b-cb2222146ea5', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f4efe-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('84573d4e-d474-4398-8630-b50686a75c98', '091cedd7-1884-49ed-a806-7452f649cb35', '31a5312b-2058-45a6-ad48-87ffd35ceb58', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('845dee82-e3fb-41c6-b270-4162225c3dfb', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521f30-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('84797f38-996b-4df0-ab06-ced122a9c8e3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521fc7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('84b66fd3-937d-43c0-93e2-e8202849a02d', '091cedd7-1884-49ed-a806-7452f649cb35', 'd919960c-2a8d-47a8-874d-b182d7e09e22', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('84f546e2-77e0-41e6-8a34-1143e81511f0', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5275b1-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('8635bb72-1136-46f2-97f6-de1d9772800c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5279b4-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('870b9e54-4891-4c05-8d48-d75f05ce5d46', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5200e5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('874ee67b-1baf-4998-aa6d-45ddfe7feb56', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521335-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('88278ac7-4290-4f49-a728-925c03b04a7d', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8bb9-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('887dde74-8eb7-4b93-8cd4-98c56bc820ab', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51fc3f-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('8b11c70f-7f12-449d-a675-aef849d3e85f', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c526-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('8b710958-e3b1-4791-8b8c-65127bfef24d', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51fa86-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('8c146c3e-156c-464f-ade2-07881ea1f844', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53cbfe-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('8dd45c89-ed4d-4fa0-be67-24e46e300c27', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f87f8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('8de5c56c-1826-4fcd-9726-5d3399679c0d', '091cedd7-1884-49ed-a806-7452f649cb35', 'c0175d8e-db8d-4b07-a5cd-e1e9bc2c783b', 0, '2024-03-05 12:15:22', '2024-03-05 12:15:22'),
('8fc20ae1-d1e9-459b-ae1c-f636f83f9446', '091cedd7-1884-49ed-a806-7452f649cb35', 'aef3a472-f13f-4806-9fae-7fc290ddf60e', 0, '2024-03-05 10:49:23', '2024-03-05 10:49:23'),
('8fcd7c64-13b8-40e5-a544-d502fdf5e558', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5349f0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('8fd269b7-8424-4eae-84a9-2e2cee765713', '091cedd7-1884-49ed-a806-7452f649cb35', '745882f7-7a35-4e6f-8f7f-16b504ee68fb', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('9033b6fd-b5aa-4ba0-9e08-74fca13c4692', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5210ba-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('908b29f1-58cf-4715-b460-8d491e83295d', '091cedd7-1884-49ed-a806-7452f649cb35', '203d6102-3c6d-45c0-8ab3-f23ace2e31f5', 0, '2024-03-05 10:49:22', '2024-03-05 10:49:22'),
('90a7077c-a961-4980-93ca-b6e5b94096f3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c479-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('91a8316c-80ed-40a4-8071-9bad1700b0eb', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520ceb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('93bda337-1a72-4788-899b-24b29bfa651b', '091cedd7-1884-49ed-a806-7452f649cb35', '9495ddcd-fe5e-4bd7-bd0f-bef44491f5fb', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('945bdf50-62d8-48d3-8bf7-115258ed23a5', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c534b07-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('94d1ecb2-6075-4eee-b9c4-06d417df2398', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53ce24-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('952972a5-bf9e-47bf-8e4d-c3a3bd8e669b', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c535b64-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('953b25be-166c-4b56-95ef-b155f31addeb', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527669-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('9591ef35-00c1-4501-b73c-90c416e4895e', '091cedd7-1884-49ed-a806-7452f649cb35', 'f1bc8fab-2d38-489d-9958-47a13434eed6', 0, '2024-03-05 11:19:11', '2024-03-05 11:19:11'),
('960f9734-4ca9-48bf-876c-ccd2a88e9db9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5364e0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('96480e99-d9e5-4347-88e6-82a26a56c4f7', '091cedd7-1884-49ed-a806-7452f649cb35', 'b7f29894-bb24-4f14-a9a5-5f30c72e7480', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('982fa677-2ecb-4fe8-b3dc-14ea8e470516', '091cedd7-1884-49ed-a806-7452f649cb35', 'b6c4b11b-1dc0-4364-9bf0-b34f72a662a0', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('98a28c97-ac01-4797-9b45-f728d9d4c8aa', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c535c1a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('98f29a25-0a43-451c-a908-cd229daed5a0', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5207b7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('9d9a9f3c-e7cb-4ced-b9e8-040682c10f8d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52090d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('9e2a0dc7-33fd-447b-aa81-a22bf7a4b882', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8901-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('9ef30f0f-5dc8-4cbe-85e2-2f237358cb6e', '091cedd7-1884-49ed-a806-7452f649cb35', 'db387a2e-60bc-41c0-ae8d-c4c91724c88c', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('9fd0431e-3f33-4085-bdee-cc700641f83d', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c3d5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a08716d3-684b-438d-8f9c-e586ced49683', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53ca87-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a0d30403-cf6d-4803-8e04-23371a8c2cf9', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f4efe-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('a0e51bd6-1129-4c75-899d-b0f22a685c09', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521019-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('a14aaeed-2520-4da7-b135-b8ff8f1e3703', '091cedd7-1884-49ed-a806-7452f649cb35', '3c534e1c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('a245c6b7-86bc-4c0e-921c-89056f5c5ed9', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521dd5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a3d4bd44-11ee-4174-b7bf-5982798c3ca6', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5217f2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('a453ea0f-fdfe-492c-8813-912c621ca0e8', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51ff0c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a48453b8-9e84-4243-9f65-f9296bb809c3', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f818b-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('a485e26c-4d38-4184-84e2-d95c4e78d90b', '091cedd7-1884-49ed-a806-7452f649cb35', '117185a7-9e38-4f6f-b6c7-d62c7a913c46', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('a49231f7-181b-401e-ab7d-c5cb3fda57c4', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51fcf0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a5185d80-830d-45f2-ab15-dbb09ebc7ddc', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5272ab-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a52c8ce2-7427-427e-91c1-4efabe090e84', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52813c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('a57af6dd-7095-4678-a29a-df3fe8cbdade', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520359-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('a66cb4ee-f0d4-4338-aec3-53dd69f9695f', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5280a7-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a6c4169e-aa84-435f-8f60-5cb3e6d3a195', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8074-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('a7608372-1469-4c8c-82ee-29d38eaa90f6', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5205a5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a788b75f-883d-4b1b-9464-6a8a2ab28a6c', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51a129-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('a8135784-fd34-4e73-a3de-0c35be0ef502', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52004a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('a89421bb-e2b2-4b40-9753-34a496fccc29', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52771d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('a965ca01-c706-4576-b386-8c125e919057', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8ac3-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('ae6f4bdd-c62b-412a-a447-52d2a6dcde00', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5350bf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('af93c5b0-7694-48ba-a90a-cf0cbdea3829', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51a129-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b058bc6d-8d47-4c25-8a9d-6ac976a60157', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521335-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('b1b1e1f7-c154-4e73-8924-e3c5c68c5d28', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521e91-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('b2b16aba-49f6-4547-a120-8c824dcc3a5e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53cb4c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b2c2c2c8-394c-457d-89e2-c044bf2eef46', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51fdad-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b30bd5fe-a2a5-470d-a1b0-4ef0becadeba', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51fa86-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('b389842d-1d86-4b9c-beb6-3518e084a38d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f4bbf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('b38e4206-2c8f-496e-b0b2-548badc60caf', '091cedd7-1884-49ed-a806-7452f649cb35', '3c519e1d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('b5af3f2a-a9cf-4e17-86df-1b2dec6ca27e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53c1b2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b60900e6-610c-44de-9c5b-d96c1d11b172', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5360cb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('b62829af-ec77-44e6-987d-ec710f65767c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52090d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b672a493-fd6f-41a4-b3fc-57172ab2b61b', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f4efe-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('b7488203-711f-4366-bf86-68b9951ecb1c', '091cedd7-1884-49ed-a806-7452f649cb35', '3c52718a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('b7ebc87e-d40b-4baa-92a8-1e111aec1551', '091cedd7-1884-49ed-a806-7452f649cb35', 'd9f4bc14-5fa4-49c0-b29b-3f802e80b6dc', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('b84f943c-7b8c-4d27-83b5-465b9fa934f0', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f446f-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('b89ed9a8-49a6-4854-833c-62654bc71b01', '091cedd7-1884-49ed-a806-7452f649cb35', '3c536015-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('b9968a77-de74-4a32-9433-5cf90326f203', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520671-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('b9cee73b-dab2-459c-8b81-92233c6412ea', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c520a4b-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('bb46bb90-5cf2-4e05-9a0c-0e491e33d025', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c521152-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('be1601a4-5934-45c1-8f92-7019bc128e4e', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f818b-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('c262dd60-64f5-42ea-b00c-b11244af09ff', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f4ff2-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('c3940c2f-ca30-4c1c-8fb5-61f12b3f7f14', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521a63-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('c4a42c16-ad12-426f-9441-ab3120cc1df5', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527ebc-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('c4e36bda-0e74-44ed-ab88-22c4af83eb4e', '091cedd7-1884-49ed-a806-7452f649cb35', '6dc9a393-a073-4c84-95bb-d60f22bfd4f7', 0, '2024-03-05 12:15:22', '2024-03-05 12:15:22'),
('c506fd35-4b24-4a5f-8b29-8648932fb950', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5354c8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('c5dafbb2-e033-44d9-bde3-c76eed62a41f', '091cedd7-1884-49ed-a806-7452f649cb35', 'abb89077-88c0-4ce9-a72a-40b3448c3459', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('c7707108-9afa-4e44-945e-6e7a3a1f7e74', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8634-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('c7c24fae-2da8-4ca6-8039-3ede1ddad59d', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f8286-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('c8c5b055-8291-4686-8655-323748e0d532', '091cedd7-1884-49ed-a806-7452f649cb35', '3c51a410-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('c9e3f0a9-12fd-4404-8541-f6552083c46b', '091cedd7-1884-49ed-a806-7452f649cb35', 'a43eb440-924d-41a2-a9ff-31866d8fcbea', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('c9efa921-2195-4b36-a227-b66f1a38cffe', '091cedd7-1884-49ed-a806-7452f649cb35', '2e84bef9-ea58-43a1-be66-99ce5cc94d90', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('cab7886f-6178-4c4e-a33b-abfb37044804', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52168e-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('cb1ef6b3-2917-41ad-8d8a-5c1f2c9b604b', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c5275b1-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('cde50cf5-1905-4bde-89d6-40a81cf1782f', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53ca87-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('ce3d105c-0904-498a-940a-79d84beb120c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f8547-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('d0966aac-8c52-4362-9ac4-6e5d40ba8882', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f8074-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('d0b119ef-9ca2-4746-afdd-bb72fc352970', '091cedd7-1884-49ed-a806-7452f649cb35', '1572ccdc-0f33-4e6e-8017-3ef5c67a4ef2', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('d0dbbe20-9251-4de3-b57d-885a81f65aca', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c4f446f-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:27', '2024-01-25 15:36:27'),
('d14036b2-74e5-406f-8a73-39b53fe94a54', '091cedd7-1884-49ed-a806-7452f649cb35', 'ce9de1bd-6656-437d-843d-74baea6cb2fb', 0, '2024-02-23 15:58:16', '2024-02-23 15:58:16'),
('d1ae0b1f-fafb-4fe5-a5a3-98f6c180eca5', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f7e15-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('d1c1a806-faad-45f7-9bfe-ae6a77f2bc32', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527a53-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('d2393bd6-59bc-48a0-93a3-f24ed124a5f3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c535019-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('d29042ee-c244-4804-9e64-2a8b58a530ac', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5349f0-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('d43e2ce6-88ef-41f9-af19-73ef4d4fde26', '091cedd7-1884-49ed-a806-7452f649cb35', 'e8523ad0-b8bb-495b-bfbf-269dbdc2ada5', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('d5a5c369-a62f-47e9-bb47-3bfb91f04519', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527ebc-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('d5e15667-579b-4cc0-ad60-5acc2756cffc', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f7e15-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('d62385e2-3a89-48f7-b77d-ae719d38a7cb', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5356fb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('d7856720-6af9-40b1-99ee-7be737aedc95', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521748-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('d8c6f33b-0fbc-4540-824b-5589456a7889', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8634-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('da1003fd-d38a-43b0-8eff-f981a8ee34ca', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527d79-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('da4645d0-f989-4bb8-8df2-ec82d939e2ba', '091cedd7-1884-49ed-a806-7452f649cb35', '0e377d1f-6b22-4bef-b8d6-76100669555e', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('daa81c29-1adc-4460-9baa-baba5cd2a055', '091cedd7-1884-49ed-a806-7452f649cb35', 'b19e0748-01ab-43ae-a067-eb5fe3c0f84b', 0, '2024-03-05 11:19:11', '2024-03-05 11:19:11'),
('dd74a9fd-1623-458d-8367-63339eead2d9', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c50a886-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('df55369e-233a-48ce-8e86-fc62646fce8c', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f8455-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('df8bf2ff-92ea-43d3-89d2-6fefed0bb065', '091cedd7-1884-49ed-a806-7452f649cb35', 'ab776ad0-665e-490e-bbc2-bf72bef0d84c', 0, '2024-03-05 11:19:11', '2024-03-05 11:19:11'),
('e0a77135-c334-4503-bdc9-d4445e86b8b1', '091cedd7-1884-49ed-a806-7452f649cb35', 'bc37cd97-8189-4afe-bc72-c638b389af0a', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('e16ad70b-491f-4028-99e3-9e85875b09e3', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527cdc-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('e21140e5-05fc-47f3-8265-1dd35cc54c9f', '091cedd7-1884-49ed-a806-7452f649cb35', '3c535c1a-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('e4365700-cc69-4459-ab04-97fa4f189912', '091cedd7-1884-49ed-a806-7452f649cb35', '542ef9f7-d09b-4084-b371-3200bcf633b5', 0, '2024-03-05 11:19:10', '2024-03-05 11:19:10'),
('e458a994-1e78-4aff-b702-b2b08ed465ce', '091cedd7-1884-49ed-a806-7452f649cb35', 'ea1c6155-8ad6-47da-9ae2-06ee8b8016e2', 0, '2024-03-05 12:32:01', '2024-03-05 12:32:01'),
('e544b418-3e4c-44a1-8d00-0d0de77673f3', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c536a18-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('e6fb4ff9-d640-41e9-b07a-953a450464e6', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c51c7f5-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('e81ef6e8-9344-4adf-8c9f-01afaf0674b3', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f8455-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33'),
('e8e1fb7f-cde2-440f-880c-a2d5dd2eb74e', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527b9c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('e9524552-9925-46cd-aec1-47ac2d4d965e', '091cedd7-1884-49ed-a806-7452f649cb35', 'd9fd07c4-672c-4fb8-96d4-8486b02c2dc2', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55'),
('e962e50a-6f2e-41a1-9140-d4890247546b', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5211ed-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('ea4cd5a2-008d-4d3c-83db-fc01a27ad041', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c53ce24-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('ea51f990-63aa-466b-8103-21ea3cbb81f1', '091cedd7-1884-49ed-a806-7452f649cb35', '3c534c65-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('eb0a60a0-ae5e-4a87-b41a-bb67034ed263', '091cedd7-1884-49ed-a806-7452f649cb35', '30253264-1f0f-4be3-8658-f13e71006ffc', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('eb692f92-352b-48cd-968d-bf0d720dd6fd', '091cedd7-1884-49ed-a806-7452f649cb35', '3c4f4df3-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:42', '2024-01-04 10:07:42'),
('eccd382e-b247-46cb-87d8-a5df05ba2827', '091cedd7-1884-49ed-a806-7452f649cb35', '3c521b68-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('eceb5e85-9c3a-4ae6-9175-6de36001f0c4', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c527874-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('ed75f42f-b7e8-4dae-b093-2040f27dcc5a', '091cedd7-1884-49ed-a806-7452f649cb35', '085109ed-f394-445f-a20f-632cf0d57d51', 0, '2024-03-05 12:00:39', '2024-03-05 12:00:39'),
('eee550ad-4328-4eb2-992f-5d5228388a33', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53c328-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('eef66121-b2c5-4ea3-ae9b-96f86fe32160', '091cedd7-1884-49ed-a806-7452f649cb35', '3c535b64-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('ef365948-d3bf-451c-861c-ff9bfec51715', '091cedd7-1884-49ed-a806-7452f649cb35', '3c527669-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:44', '2024-01-04 10:07:44'),
('f060bc6c-ee90-401e-adc1-e6b1b4d587cf', '5b75c472-ee15-4b48-9749-326a1aa21d55', 'a43eb440-924d-41a2-a9ff-31866d8fcbea', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('f0fdd39f-60cc-4b1b-bcca-70339ba8abb7', '091cedd7-1884-49ed-a806-7452f649cb35', '69fecec0-08ad-4e08-89a1-c024866109f6', 0, '2024-03-04 17:06:04', '2024-03-04 17:06:04'),
('f14882f3-7ebb-400d-861a-9799e9cd0f4c', '5b75c472-ee15-4b48-9749-326a1aa21d55', '3c52771d-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 15:36:28', '2024-01-25 15:36:28'),
('f191c982-bbff-4629-aa40-7a014a8c07fa', '091cedd7-1884-49ed-a806-7452f649cb35', '083aa8c6-e287-4bcf-b322-55370081818d', 0, '2024-03-05 13:28:55', '2024-03-05 13:28:55');
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `deleted`, `created_at`, `updated_at`) VALUES
('f4f45eb8-455a-484f-b160-f15cd35b5763', '091cedd7-1884-49ed-a806-7452f649cb35', '3c520ceb-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:43', '2024-01-04 10:07:43'),
('f56c0e5c-6025-4232-a2cd-56b18b99c75f', '091cedd7-1884-49ed-a806-7452f649cb35', 'f32fac27-ab58-43a7-ae21-b90910f3388a', 0, '2024-03-04 18:53:59', '2024-03-04 18:53:59'),
('f5cbf118-cf64-48d1-8e4a-1d1eab25cb7d', '091cedd7-1884-49ed-a806-7452f649cb35', '3c53cd6c-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('f699fa6d-cd11-4d61-9cac-8c586b394f88', '091cedd7-1884-49ed-a806-7452f649cb35', '1159891d-af75-4263-9bf7-010bae031445', 0, '2024-03-05 10:49:22', '2024-03-05 10:49:22'),
('f712988f-1c47-414f-9788-abf64a46e58f', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5358d8-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('f770c3d4-2bfb-46f9-b907-d8445bf8b90e', '091cedd7-1884-49ed-a806-7452f649cb35', '3c5350bf-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-04 10:07:45', '2024-01-04 10:07:45'),
('fb723154-55d2-4c0d-b4ae-6d7a087bc098', 'bd7f7ac8-4e76-42a5-aa00-5ee14f6b7f86', '3c4f4df3-7fbd-11ee-a7db-fa163e0972ee', 0, '2024-01-25 13:00:33', '2024-01-25 13:00:33');


--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `slug`, `type`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
('07e764a3-0eda-448d-8528-72dfd5bceee9', 'transfer-to-bcv', 'client', 1, 0, '2023-11-30 20:22:49', '2024-02-19 15:17:37'),
('0c0bf0ab-a215-4eec-a8d3-d5fc13ca80a6', 'link-bmo-cashout-mean', 'partenaire', 0, 0, '2023-11-23 14:53:35', '2023-11-23 14:53:35'),
('0d6c865d-98cb-4bec-b75c-87fbbf8e38db', 'cashin-from-bmo', 'partenaire', 0, 0, '2023-11-23 14:59:03', '2023-11-23 14:59:03'),
('2a247c89-10a8-4ac2-8edb-e403c7bb9e75', 'withdraw-to-partner', 'client', 1, 0, '2023-12-05 18:48:54', '2024-02-15 19:53:22'),
('2ab0b419-8dc3-4aad-9b81-73e2445a7113', 'recharge-card-from-bmo', 'client', 1, 0, '2023-11-16 16:40:28', '2024-02-13 14:10:34'),
('31660599-c14e-47fa-a34f-b70fa2b29451', 'cashout-to-atm', 'partenaire', 0, 0, '2023-11-23 14:57:58', '2023-11-23 14:57:58'),
('4477bc92-55dd-427b-ab59-d5a15957d342', 'cashin-from-visa', 'partenaire', 0, 0, '2023-11-23 14:59:32', '2023-11-23 14:59:32'),
('4b66a959-1e16-4145-bb6e-f9759680d6ba', 'cashin-from-momo', 'partenaire', 0, 0, '2023-11-23 14:59:12', '2023-11-23 14:59:12'),
('4cc9acd2-8054-4940-8f04-641108fe54e4', 'link-momo-cashout-mean', 'partenaire', 0, 0, '2023-11-23 14:53:46', '2023-11-23 14:53:46'),
('55ab57b8-df18-435a-b920-04edb1b521bd', 'client-card-withdraw', 'partenaire', 1, 0, '2023-11-23 14:59:53', '2023-11-23 14:59:53'),
('651b9650-57a1-4016-88ad-b7d946ea5979', 'recharge-card-from-visa', 'client', 1, 0, '2023-11-16 16:41:52', '2024-02-15 16:48:56'),
('68da75bd-b853-4eee-92d2-7aa18953336b', 'transfer-to-visa', 'client', 1, 0, '2023-11-16 16:44:04', '2024-02-15 19:53:13'),
('6b6d1a8a-0d09-4e2b-95d3-14371ebc0099', 'link-bank-cashout-mean', 'partenaire', 0, 0, '2023-11-23 14:56:48', '2023-11-23 14:56:48'),
('6e64a6d3-f69e-44d7-8a21-ea26116e66ff', 'withdraw-to-atm', 'client', 0, 0, '2023-11-16 16:42:57', '2024-02-15 19:53:19'),
('78373ccc-c79f-4ed8-8325-acd78a53b302', 'withdraw-to-bmo', 'client', 1, 0, '2023-11-16 16:42:33', '2024-02-15 19:53:14'),
('7ef0d12b-6406-4937-bcc1-9fff2397a7f9', 'cashin-from-bank', 'partenaire', 0, 0, '2023-11-23 14:59:44', '2023-11-23 14:59:44'),
('84a82659-928c-4d38-ba98-c57a55d354c8', 'recharge-card-from-mobile-money', 'client', 1, 0, '2023-11-16 16:41:44', '2024-02-15 16:48:54'),
('8952419d-5dc4-423e-b687-459dd087b3dc', 'withdraw-to-mobile-money', 'client', 1, 0, '2023-11-16 16:42:44', '2024-02-15 19:53:17'),
('8e2ff954-1c2c-4ccf-970e-3b7c87a37acc', 'cashout-to-visa', 'partenaire', 0, 0, '2023-11-23 14:57:45', '2023-11-23 14:57:45'),
('a5c946f0-af7d-4879-bd72-6de9a55a3ecc', 'transfer-to-mastercard', 'client', 1, 0, '2023-11-16 16:44:35', '2024-02-15 16:49:05'),
('b087b939-d5aa-4d53-a62a-390bfdea77da', 'buy-card', 'client', 1, 0, '2023-11-16 16:38:45', '2024-02-13 13:49:20'),
('b5caa19c-4df9-4b77-bbe1-3e0ae3d5f845', 'client-card-recharge', 'partenaire', 0, 0, '2023-11-23 15:00:03', '2023-11-23 15:00:03'),
('b884a3f2-bf2a-49e0-b41d-543f5e695d77', 'cashout-to-bmo', 'partenaire', 0, 0, '2023-11-23 14:57:20', '2023-11-23 14:57:20'),
('cc83cc35-48ed-4988-96c7-aaa95298fad9', 'link-visa-cashout-mean', 'partenaire', 0, 0, '2023-11-23 14:54:57', '2023-11-23 14:54:57'),
('d04b4241-ac28-4dae-acae-6bee2a554bfc', 'cashout-to-momo', 'partenaire', 0, 0, '2023-11-23 14:57:35', '2023-11-23 14:57:35'),
('d6b8bd31-73e6-4ce0-8f41-6f46c6c2c769', 'link-card', 'client', 1, 0, '2023-11-16 16:40:18', '2024-02-13 13:49:38'),
('e5200db1-a571-4ceb-9a0e-82f7381bebfd', 'recharge-card-from-mastercard', 'client', 1, 0, '2023-11-16 16:42:02', '2024-02-13 15:08:05'),
('e9e27495-eeb6-4caa-b432-f62cba579b20', 'transfer-to-bmo', 'client', 1, 0, '2023-11-16 16:43:20', '2024-02-15 16:48:59'),
('f0d28b1f-36e1-499f-b27f-969facd68bea', 'cashout-to-bank', 'partenaire', 0, 0, '2023-11-23 14:58:17', '2023-11-23 14:58:17'),
('f116bbbb-8d29-441d-b293-d9d23fec29df', 'transfer-to-mobile-money', 'client', 1, 0, '2023-11-16 16:43:31', '2024-02-15 19:53:11'),
('f87138ca-798f-463a-a821-3500a2f194db', 'transfer-to-linked-bank', 'client', 1, 0, '2023-11-16 16:45:12', '2024-02-15 16:49:02');

-- --------------------------------------------------------

--
-- Table structure for table `tpes`
--

CREATE TABLE `tpes` (
  `id` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `partenaire_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tpes`
--

INSERT INTO `tpes` (`id`, `code`, `partenaire_id`, `status`, `type`, `deleted`, `created_at`, `updated_at`) VALUES
('52138f84-45f5-45d7-9f5d-eaf5d1c00a43', '789654110', 'ddb55ffc-3c22-498c-800b-8ca568690339', 'off', 'telpo', 0, '2024-02-27 18:01:59', '2024-02-28 10:24:55');

-- --------------------------------------------------------

--
-- Table structure for table `tpe_locations`
--

CREATE TABLE `tpe_locations` (
  `id` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `lng` float NOT NULL,
  `lat` float NOT NULL,
  `description` text NOT NULL,
  `partenaire_id` varchar(255) NOT NULL,
  `user_partenaire_id` varchar(255) NOT NULL,
  `tpe_id` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Table structure for table `transfert_admins`
--

CREATE TABLE `transfert_admins` (
  `id` varchar(255) NOT NULL,
  `compte` varchar(255) NOT NULL,
  `program` int NOT NULL,
  `sens` varchar(255) NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `last_digits` varchar(255) NOT NULL,
  `montant` float NOT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transfert_admins`
--

INSERT INTO `transfert_admins` (`id`, `compte`, `program`, `sens`, `customer_id`, `last_digits`, `montant`, `deleted`, `created_at`, `updated_at`) VALUES
('4431b8e3-07f0-4606-b127-b68a7461d494', '11700037', 66, 'credit', '11700045', '1720', 1000, 0, '2024-02-22 19:09:37', '2024-02-22 19:09:37'),
('7181fe30-b4c3-4e60-83a0-c860cb5e90a8', '11700037', 36, 'credit', '11700045', '1720', 1000, 0, '2024-02-22 21:03:02', '2024-02-22 21:03:02'),
('ff101fa6-d02f-4e22-8209-735d55cdb7e5', '11700037', 36, 'credit', '11700045', '1720', 1000, 0, '2024-02-22 21:04:11', '2024-02-22 21:04:11');

--
-- Table structure for table `transfert_outs`
--

CREATE TABLE `transfert_outs` (
  `id` char(36) NOT NULL DEFAULT '',
  `moyen_paiement` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `user_client_id` char(50) DEFAULT NULL,
  `user_card_id` char(50) DEFAULT NULL,
  `receveur_id` char(50) DEFAULT NULL,
  `receveur_card_id` varchar(255) DEFAULT NULL,
  `receveur_telephone` varchar(50) DEFAULT NULL,
  `receveur_customer_id` varchar(50) DEFAULT NULL,
  `receveur_last_digits` varchar(50) DEFAULT NULL,
  `montant` int DEFAULT NULL,
  `frais` double DEFAULT NULL,
  `montant_recu` double DEFAULT NULL,
  `reference_gtp_debit` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `reference_gtp_credit` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `reference_operateur` varchar(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `solde_avant` int DEFAULT NULL,
  `solde_apres` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `is_credited` tinyint NOT NULL DEFAULT '0',
  `status` varchar(255) DEFAULT 'pending',
  `reasons` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_debited` tinyint NOT NULL DEFAULT '0',
  `cancel_motif` mediumtext,
  `cancelled_at` datetime DEFAULT NULL,
  `canceller_id` varchar(255) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refunder_id` varchar(255) DEFAULT NULL,
  `refunded_reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `transfert_outs`
--

INSERT INTO `transfert_outs` (`id`, `moyen_paiement`, `user_client_id`, `user_card_id`, `receveur_id`, `receveur_card_id`, `receveur_telephone`, `receveur_customer_id`, `receveur_last_digits`, `montant`, `frais`, `montant_recu`, `reference_gtp_debit`, `reference_gtp_credit`, `reference_operateur`, `libelle`, `solde_avant`, `solde_apres`, `name`, `lastname`, `is_credited`, `status`, `reasons`, `deleted`, `created_at`, `updated_at`, `is_debited`, `cancel_motif`, `cancelled_at`, `canceller_id`, `refunded_at`, `refunder_id`, `refunded_reference`) VALUES
('47e6ce13-8852-4860-ad35-acdb599db30b', 'bmo', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', NULL, NULL, '22962617848', NULL, NULL, 2000, 10, 1990, '695873599', NULL, 'OPAC2024022614083621770005', 'Transfert BMO de 2000 vers le numero 22962617848.', 9398, 7388, 'GBEVE', 'Aurens', 1, 'completed', NULL, 0, '2024-02-26 14:08:35', '2024-02-26 14:08:36', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('92496330-30b4-406b-9e18-5ec72770f4c4', 'momo', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', NULL, NULL, NULL, NULL, NULL, 1500, 7.5, NULL, '695872542', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'pending', NULL, 0, '2024-02-15 20:12:34', '2024-02-15 20:12:34', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('beb02f63-5d93-4cf2-aa52-6261ce6fd085', 'momo', 'adecd340-6c72-4b29-8097-1a2e81caee68', '1ac31813-1d41-4f08-8824-e060e04d8ab5', NULL, NULL, NULL, NULL, NULL, 2000, 10, NULL, '695872540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'pending', NULL, 0, '2024-02-15 20:09:55', '2024-02-15 20:09:55', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('f44be3ce-7005-4311-88d4-7a2e85bdd2b3', 'momo', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'a176c5e9-a5d1-45c4-9111-397ccf9ca551', NULL, NULL, NULL, NULL, NULL, 500, 2.5, NULL, '695872544', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'pending', NULL, 0, '2024-02-15 20:26:16', '2024-02-15 20:26:16', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('f9ef9068-554e-47e3-a76f-3c489807ee27', 'momo', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'a176c5e9-a5d1-45c4-9111-397ccf9ca551', NULL, NULL, NULL, NULL, NULL, 500, 2.5, NULL, '695872546', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'pending', NULL, 0, '2024-02-15 20:29:25', '2024-02-15 20:29:25', 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL DEFAULT '',
  `lastname` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `role_id` char(50) DEFAULT NULL,
  `last_connexion` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lastname`, `name`, `username`, `password`, `deleted`, `status`, `role_id`, `last_connexion`, `created_at`, `updated_at`) VALUES
('0f41a557-a26b-4abc-9f1e-34903bb72963', 'Hervé', 'DANDJINOU', 'dherve', '$2y$10$W/awFfRMRChIHtKrUbQo3up2.fVn6q4XP4ojR4IxjBMEUygmdUMba', 0, 0, '091cedd7-1884-49ed-a806-7452f649cb35', '01-Dec-2023 19:06:10', '2023-11-28 11:29:45', '2024-01-03 12:00:14'),
('3d3aef66-7fbd-11ee-a7db-fa163e0972ee', 'Aurens', 'GBEVE', 'gaurens', '$2y$10$XVdPNQ2C0TBPN7m72iqxguL/ubhBEL2BypeYr3nMvtXCo25BY9Oky', 0, 1, '091cedd7-1884-49ed-a806-7452f649cb35', '27-Dec-2023 10:27:38', '2022-11-09 15:38:43', '2023-12-27 10:27:38'),
('3d3af269-7fbd-11ee-a7db-fa163e0972ee', 'Evans', 'GBEVE', 'gevans', '$2y$10$fYsVcVk19n/.EP7dSUHduOGPT6CfSOkyUHuJwAFNoS56zYnvxSe6m', 1, 1, '091cedd7-1884-49ed-a806-7452f649cb35', NULL, '2022-11-11 12:40:43', '2023-08-29 20:48:16'),
('3d3af34d-7fbd-11ee-a7db-fa163e0972ee', 'Jean', 'SINGBO', 'sjean', '$2y$10$d0V9ELzhYEAD0wpY4wLuT.n/uXjVYmAWegV/D8et5eqr2osQAw95u', 1, 1, '091cedd7-1884-49ed-a806-7452f649cb35', NULL, '2022-12-02 13:30:16', '2024-01-03 11:59:56'),
('3e8b8836-7573-48ac-a81c-ebfe9d494ab0', 'Cedar', 'KPOIZOUN', 'kcedar', '$2y$10$VTYFsUZ46EiM99S7bFCGnOh/vpWNIUiMqBjDr3prG0XrOJ8Gvum8.', 0, 1, '091cedd7-1884-49ed-a806-7452f649cb35', '05-Dec-2023 15:45:28', '2023-11-21 12:24:49', '2023-12-05 15:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_cards`
--

CREATE TABLE `user_cards` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_client_id` char(50) DEFAULT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  `customer_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `last_digits` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_first` tinyint DEFAULT NULL,
  `is_buy` tinyint DEFAULT '0',
  `reference` varchar(50) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_cards`
--

INSERT INTO `user_cards` (`id`, `user_client_id`, `libelle`, `customer_id`, `last_digits`, `type`, `is_first`, `is_buy`, `reference`, `deleted`, `created_at`, `updated_at`) VALUES
('1ac31813-1d41-4f08-8824-e060e04d8ab5', 'adecd340-6c72-4b29-8097-1a2e81caee68', NULL, '}§§¬u', ' ª', 'bmo', 1, 1, NULL, 0, '2024-02-15 18:22:58', '2024-02-15 18:22:58'),
('a176c5e9-a5d1-45c4-9111-397ccf9ca551', 'adecd340-6c72-4b29-8097-1a2e81caee68', NULL, '}§§¬v', ' ¯', 'kkiapay', 0, 1, NULL, 0, '2024-02-15 18:51:04', '2024-02-15 18:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_card_buys`
--

CREATE TABLE `user_card_buys` (
  `id` varchar(255) NOT NULL,
  `user_card_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `user_client_id` varchar(255) NOT NULL,
  `moyen_paiement` varchar(255) NOT NULL,
  `reference_paiement` varchar(255) NOT NULL,
  `montant` float DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reasons` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `is_debited` tinyint NOT NULL DEFAULT '0',
  `cancel_motif` mediumtext,
  `cancelled_at` datetime DEFAULT NULL,
  `canceller_id` varchar(255) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refunder_id` varchar(255) DEFAULT NULL,
  `refunded_reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_card_buys`
--

INSERT INTO `user_card_buys` (`id`, `user_card_id`, `user_client_id`, `moyen_paiement`, `reference_paiement`, `montant`, `status`, `reasons`, `is_debited`, `cancel_motif`, `cancelled_at`, `canceller_id`, `refunded_at`, `refunder_id`, `refunded_reference`, `deleted`, `created_at`, `updated_at`) VALUES
('26a38edc-40f1-47d1-8e5f-7557d656f344', '1ac31813-1d41-4f08-8824-e060e04d8ab5', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'bmo', '78481708021348', 50, 'completed', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:22:56', '2024-02-15 18:22:58'),
('59b5db6d-dcc0-4b6d-b79f-2b0772a33f53', NULL, 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', 'Tbzs_nls7', 50, 'failed', '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:44:13', '2024-02-15 18:44:14'),
('6b31a272-7d82-4ee7-b166-84d04f0c9a28', NULL, 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', 'xtOAXEWma', 50, 'pending', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:50:16', '2024-02-15 18:50:16'),
('8a5c970d-4b85-4b8e-96f1-34b5c862c12a', NULL, 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', '-ta5u6U-_', 50, 'pending', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:47:45', '2024-02-15 18:47:45'),
('9473afaf-ae99-45a3-b1e8-4674ae554b28', NULL, 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', 'eif0q4d0_', 50, 'pending', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:54:47', '2024-02-15 18:54:47'),
('b3bc13df-3ee9-480b-9bb5-649dd0d31780', 'a176c5e9-a5d1-45c4-9111-397ccf9ca551', 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', '04DwxN3Ez', 50, 'completed', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:51:02', '2024-02-15 18:51:04'),
('de5d02f8-dd15-487b-add7-aa5fcf485130', NULL, 'adecd340-6c72-4b29-8097-1a2e81caee68', 'kkiapay', '-HZiUqn4R', 50, 'pending', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-02-15 18:46:23', '2024-02-15 18:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_clients`
--

CREATE TABLE `user_clients` (
  `id` char(36) NOT NULL DEFAULT '',
  `lastname` varchar(100) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `last` varchar(10) DEFAULT NULL,
  `username` varchar(15) DEFAULT NULL,
  `password` mediumtext,
  `status` tinyint DEFAULT NULL,
  `phone_code` int DEFAULT NULL,
  `phone` int DEFAULT NULL,
  `double_authentification` tinyint DEFAULT NULL,
  `sms` tinyint DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_connexion` varchar(50) DEFAULT NULL,
  `verification` tinyint DEFAULT '0',
  `user_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `verification_step_one` tinyint DEFAULT '0',
  `verification_step_two` tinyint DEFAULT '0',
  `verification_step_three` tinyint DEFAULT '0',
  `kyc_client_id` char(50) DEFAULT NULL,
  `code_otp` int DEFAULT NULL,
  `pin` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `motif_rejet` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `is_rejected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_clients`
--

INSERT INTO `user_clients` (`id`, `lastname`, `name`, `code`, `last`, `username`, `password`, `status`, `phone_code`, `phone`, `double_authentification`, `sms`, `deleted`, `created_at`, `updated_at`, `last_connexion`, `verification`, `user_id`, `verification_step_one`, `verification_step_two`, `verification_step_three`, `kyc_client_id`, `code_otp`, `pin`, `motif_rejet`, `is_rejected`) VALUES
('adecd340-6c72-4b29-8097-1a2e81caee68', 'GBEVE', 'Aurens Exauce', NULL, NULL, '22962617848', '$2y$10$OuvzzvuB3gx0KpKw40Dyae46rNDdlKVnzQa/MbIh8VgDgFhti.Xd6', 1, 229, 62617848, 0, 0, 0, '2024-02-15 16:44:12', '2024-02-28 14:00:42', '2024-02-28 14:00:42', 1, NULL, 1, 1, 1, '11aee9e4-31e3-4df7-9760-d13af6a3bda8', NULL, '}©®', NULL, NULL),
('f4cb350d-35df-43ae-86b6-cb338b7200c4', NULL, NULL, NULL, NULL, '22941358941', '$2y$10$y3kFNRz.N23arWhiyYQBwuCNxOp0tJ.02/D2M66RJYaM55NbijzD6', 1, 229, 41358941, 0, 0, 0, '2024-02-15 16:55:41', '2024-02-15 16:55:41', NULL, 0, NULL, 1, 0, 0, '15429978-6c9c-4736-85fa-3defd75dfa88', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_partenaires`
--

CREATE TABLE `user_partenaires` (
  `id` char(36) NOT NULL DEFAULT '',
  `lastname` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `partenaire_id` char(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `status` bit(1) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `pin` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `role_id` char(50) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `lastconnexion` varchar(50) DEFAULT NULL,
  `promo_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_partenaires`
--

INSERT INTO `user_partenaires` (`id`, `lastname`, `name`, `partenaire_id`, `username`, `status`, `password`, `pin`, `role_id`, `deleted`, `created_at`, `updated_at`, `lastconnexion`, `promo_code`) VALUES
('776c20f5-d13f-405f-9a11-2901684d14e3', 'Aurens', 'GBEVE', 'd510a5aa-bd98-4ab8-ba72-0d7c3506f6c7', 'gaurens', b'1', '$2y$10$IY44ToUoW5c89mSNiVwQ6uuD1MehXyLdmEy63d1pWeg8i943fW8LG', NULL, '44667efb-f74a-4e63-949e-c75ca349f781', 0, '2024-03-04 11:41:52', '2024-03-04 11:41:52', NULL, NULL),
('c67a0921-e718-4589-b453-b60bd103e79b', 'Aurens', 'GBEVE', 'ddb55ffc-3c22-498c-800b-8ca568690339', 'gaurens', b'1', '$2y$10$Fb3N9TbfI7VTX1OreZ.lbezrZl3kOE1ErtE2OxuBkur3UB/WH.oV6', NULL, '44667efb-f74a-4e63-949e-c75ca349f781', 0, '2024-02-27 17:33:12', '2024-02-27 17:33:12', NULL, NULL);


--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_commissions`
--
ALTER TABLE `account_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_commission_operations`
--
ALTER TABLE `account_commission_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_distributions`
--
ALTER TABLE `account_distributions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_distribution_operations`
--
ALTER TABLE `account_distribution_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_ventes`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_vente_operations`
--
ALTER TABLE `account_vente_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_partenaire_accounts`
--
ALTER TABLE `api_partenaire_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_partenaire_fees`
--
ALTER TABLE `api_partenaire_fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_partenaire_transactions`
--
ALTER TABLE `api_partenaire_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bcc_payments`
--
ALTER TABLE `bcc_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaires`
--
ALTER TABLE `beneficiaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaire_bcvs`
--
ALTER TABLE `beneficiaire_bcvs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaire_cards`
--
ALTER TABLE `beneficiaire_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaire_momos`
--
ALTER TABLE `beneficiaire_momos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carte_physiques`
--
ALTER TABLE `carte_physiques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carte_virtuelles`
--
ALTER TABLE `carte_virtuelles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `compte_commissions`
--
ALTER TABLE `compte_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `compte_mouvements`
--
ALTER TABLE `compte_mouvements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `compte_mouvement_operations`
--
ALTER TABLE `compte_mouvement_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `depots`
--
ALTER TABLE `depots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frais`
--
ALTER TABLE `frais`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frai_compte_commissions`
--
ALTER TABLE `frai_compte_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_payments`
--
ALTER TABLE `front_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gammes`
--
ALTER TABLE `gammes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infos`
--
ALTER TABLE `infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kkiapay_recharges`
--
ALTER TABLE `kkiapay_recharges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_clients`
--
ALTER TABLE `kyc_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mouchards`
--
ALTER TABLE `mouchards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mouchard_partenaires`
--
ALTER TABLE `mouchard_partenaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partner_all_wallets`
--
ALTER TABLE `partner_all_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partner_wallets`
--
ALTER TABLE `partner_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partner_wallet_withdraws`
--
ALTER TABLE `partner_wallet_withdraws`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_questions`
--
ALTER TABLE `password_reset_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_question_clients`
--
ALTER TABLE `password_reset_question_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rechargement_partenaires`
--
ALTER TABLE `rechargement_partenaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recharges`
--
ALTER TABLE `recharges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restrictions`
--
ALTER TABLE `restrictions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restriction_agences`
--
ALTER TABLE `restriction_agences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `retraits`
--
ALTER TABLE `retraits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `self_retraits`
--
ALTER TABLE `self_retraits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tpes`
--
ALTER TABLE `tpes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tpe_locations`
--
ALTER TABLE `tpe_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfert_admins`
--
ALTER TABLE `transfert_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfert_outs`
--
ALTER TABLE `transfert_outs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_card_buys`
--
ALTER TABLE `user_card_buys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_clients`
--
ALTER TABLE `user_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_partenaires`
--
ALTER TABLE `user_partenaires`
  ADD PRIMARY KEY (`id`);