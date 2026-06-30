-- ============================================================
-- Urgences SN — Base de données MySQL
-- Généré depuis les migrations Laravel
-- ============================================================

CREATE DATABASE IF NOT EXISTS `urgences_sn`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `urgences_sn`;

-- ============================================================
-- Table : users (Laravel par défaut)
-- ============================================================
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : password_reset_tokens
-- ============================================================
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : sessions
-- ============================================================
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` TEXT NULL DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : cache
-- ============================================================
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : jobs
-- ============================================================
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL DEFAULT NULL,
    `cancelled_at` INT NULL DEFAULT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : structures
-- ============================================================
CREATE TABLE `structures` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `sigle` VARCHAR(255) NULL DEFAULT NULL,
    `type` ENUM('pompiers','samu','police','gendarmerie','marine','protection_civile','autre') NOT NULL,
    `region` VARCHAR(255) NULL DEFAULT NULL,
    `adresse` VARCHAR(255) NULL DEFAULT NULL,
    `telephone` VARCHAR(255) NULL DEFAULT NULL,
    `email` VARCHAR(255) NULL DEFAULT NULL,
    `responsable_nom` VARCHAR(255) NULL DEFAULT NULL,
    `responsable_titre` VARCHAR(255) NULL DEFAULT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : agents
-- ============================================================
CREATE TABLE `agents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `identifiant` VARCHAR(255) NOT NULL UNIQUE,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','pompier','samu') NOT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `token` VARCHAR(255) NULL DEFAULT NULL,
    `structure_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT `agents_structure_id_foreign` FOREIGN KEY (`structure_id`) REFERENCES `structures` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : incidents
-- ============================================================
CREATE TABLE `incidents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type_urgence` ENUM('incendie','accident','medical','autre') NOT NULL,
    `latitude` DECIMAL(10,7) NULL DEFAULT NULL,
    `longitude` DECIMAL(10,7) NULL DEFAULT NULL,
    `adresse` VARCHAR(255) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `nom_citoyen` VARCHAR(255) NULL DEFAULT NULL,
    `telephone_citoyen` VARCHAR(255) NULL DEFAULT NULL,
    `statut` ENUM('en_attente','pris_en_charge','en_route','sur_place','termine') NOT NULL DEFAULT 'en_attente',
    `agent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT `incidents_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : victimes
-- ============================================================
CREATE TABLE `victimes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `incident_id` BIGINT UNSIGNED NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) NOT NULL,
    `age` INT NULL DEFAULT NULL,
    `sexe` ENUM('homme','femme','inconnu') NOT NULL DEFAULT 'inconnu',
    `telephone` VARCHAR(255) NULL DEFAULT NULL,
    `groupe_sanguin` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-','inconnu') NOT NULL DEFAULT 'inconnu',
    `etat` ENUM('leger','grave','critique','decede','inconnu') NOT NULL DEFAULT 'inconnu',
    `observations` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT `victimes_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Données initiales (Seeder)
-- ============================================================

INSERT INTO `structures` (`nom`, `sigle`, `type`, `region`, `actif`, `created_at`, `updated_at`) VALUES
('Sapeurs-Pompiers de Dakar', 'SPD',  'pompiers',          'Dakar', 1, NOW(), NOW()),
('SAMU National',             'SAMU', 'samu',              'Dakar', 1, NOW(), NOW()),
('Police Nationale',          'PN',   'police',            'Dakar', 1, NOW(), NOW()),
('Gendarmerie Nationale',     'GN',   'gendarmerie',       'Dakar', 1, NOW(), NOW()),
('Marine Nationale',          'MN',   'marine',            'Dakar', 1, NOW(), NOW()),
('Protection Civile',         'PC',   'protection_civile', 'Dakar', 1, NOW(), NOW());

-- Agents (mots de passe hashés avec bcrypt — valeur : admin123, pompier123, samu123)
INSERT INTO `agents` (`identifiant`, `mot_de_passe`, `nom`, `prenom`, `role`, `actif`, `structure_id`, `created_at`, `updated_at`) VALUES
('admin',    '$2y$12$fSulrU69o94qoSs6ERlKZO2eusGBReny.Z/BkR4ITep6/1Nkxf8jy', '', '', 'admin',   1, NULL, NOW(), NOW()),
('pompier1', '$2y$12$/rw4MdXCPpHSML1qsNbg4eW8SQrvGPq1sp6PjqlWR8ym.tfh9arMq', '', '', 'pompier', 1, 1,    NOW(), NOW()),
('pompier2', '$2y$12$/rw4MdXCPpHSML1qsNbg4eW8SQrvGPq1sp6PjqlWR8ym.tfh9arMq', '', '', 'pompier', 1, 1,    NOW(), NOW()),
('samu1',    '$2y$12$8s5g5/LWvZLJb.9.eb2ziegdcSSb6uel9j1xafanTdUDlTpabIN9K', '', '', 'samu',    1, 2,    NOW(), NOW()),
('samu2',    '$2y$12$8s5g5/LWvZLJb.9.eb2ziegdcSSb6uel9j1xafanTdUDlTpabIN9K', '', '', 'samu',    1, 2,    NOW(), NOW());
