-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 04:37 AM
-- Server version: 8.4.2
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_neptune`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Created well production reading for ARJUNA-01', 'App\\Models\\Production\\WellProduction', NULL, 7, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/wells/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-07T06:52:42.433305Z\", \"attributes\": {\"id\": 7, \"remarks\": \"eddf\", \"well_id\": 1, \"vessel_id\": 1, \"created_at\": \"2025-09-07 06:52:41\", \"flow_hours\": 2, \"updated_at\": \"2025-09-07 06:52:41\", \"api_gravity\": 4, \"recorded_by\": 1, \"bs_w_percent\": 33, \"oil_rate_bph\": 54, \"gas_rate_mscfh\": 43, \"water_rate_bph\": 45, \"choke_size_64th\": 34, \"reading_datetime\": \"2025-09-07 06:51:10\", \"wellhead_pressure_psi\": 34, \"wellhead_temperature_f\": 43}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"eddf\", \"well_id\": 1, \"flow_hours\": 2, \"api_gravity\": 4, \"bs_w_percent\": 33, \"oil_rate_bph\": 54, \"gas_rate_mscfh\": 43, \"water_rate_bph\": 45, \"choke_size_64th\": 34, \"reading_datetime\": \"2025-09-07T06:51:10.295Z\", \"wellhead_pressure_psi\": 34, \"wellhead_temperature_f\": 43}}', NULL, '2025-09-06 23:52:42', '2025-09-06 23:52:42'),
(2, 'default', 'activity.gas_sales_metering.created', 'App\\Models\\Production\\GasSalesMetering', NULL, 3, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/sales-gas/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-07T21:43:37.558417Z\", \"attributes\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07 21:43:37\", \"updated_at\": \"2025-09-07 21:43:37\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"recorded_by\": null, \"reading_time\": \"2025-09-07T21:28:06.285Z\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}}', NULL, '2025-09-07 14:43:37', '2025-09-07 14:43:37'),
(3, 'default', 'activity.gas_sales_metering.updated', 'App\\Models\\Production\\GasSalesMetering', NULL, 3, 'App\\Models\\User', 1, '{\"old\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07T21:43:37.000000Z\", \"updated_at\": \"2025-09-07T21:43:37.000000Z\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07T21:28:06.000000Z\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"url\": \"https://neptune.local/api/production/sales-gas/3/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-08T02:27:00.968809Z\", \"attributes\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07 21:43:37\", \"updated_at\": \"2025-09-07 21:43:37\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"changes_count\": 0}', NULL, '2025-09-07 19:27:00', '2025-09-07 19:27:00'),
(4, 'default', 'activity.gas_sales_metering.created', 'App\\Models\\Production\\GasSalesMetering', NULL, 4, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/sales-gas/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-08T04:01:06.845490Z\", \"attributes\": {\"id\": 4, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"wqrwq\", \"created_at\": \"2025-09-08 04:01:06\", \"updated_at\": \"2025-09-08 04:01:06\", \"recorded_by\": 1, \"reading_time\": \"2025-09-08 03:59:55\", \"export_temp_f\": 213, \"flowrate_mmscfd\": 21, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 24, \"specific_gravity\": 21, \"variance_percent\": 41.67, \"total_volume_mmscf\": 212, \"co2_content_percent\": 32, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 34, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": null, \"buyer_name\": \"wqrwq\", \"reading_time\": \"2025-09-08T03:59:55.646Z\", \"export_temp_f\": 213, \"flowrate_mmscfd\": 21, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 24, \"specific_gravity\": 21, \"variance_percent\": 41.67, \"total_volume_mmscf\": 212, \"co2_content_percent\": 32, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 34, \"heating_value_btu_scf\": 21}}', NULL, '2025-09-07 21:01:06', '2025-09-07 21:01:06'),
(5, 'default', 'activity.well.updated', 'App\\Models\\Master\\Well', NULL, 9, 'App\\Models\\User', 1, '{\"old\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13T14:48:51.000000Z\", \"updated_at\": \"2025-09-13T14:48:51.000000Z\", \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": 21}, \"url\": \"https://neptune.local/api/master/wells/9/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-14T05:01:50.648510Z\", \"attributes\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13 14:48:51\", \"updated_at\": \"2025-09-13 14:48:51\", \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": \"21.00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": 21}, \"changes_count\": 0}', NULL, '2025-09-13 22:01:50', '2025-09-13 22:01:50'),
(6, 'default', 'activity.well.updated', 'App\\Models\\Master\\Well', NULL, 9, 'App\\Models\\User', 1, '{\"old\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13T14:48:51.000000Z\", \"updated_at\": \"2025-09-14T05:02:17.000000Z\", \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": 21}, \"url\": \"https://neptune.local/api/master/wells/9/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-14T05:02:17.549176Z\", \"attributes\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13 14:48:51\", \"updated_at\": \"2025-09-14 05:02:17\", \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": \"21.00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": 30.2}, \"changes_count\": 0}', NULL, '2025-09-13 22:02:17', '2025-09-13 22:02:17'),
(7, 'default', 'Created equipment record for wq', 'App\\Models\\Master\\Equipment', NULL, 10, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/master/equipment/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-15T17:05:33.670473Z\", \"attributes\": {\"id\": 10, \"tag\": \"21\", \"code\": \"2131\", \"name\": \"wq\", \"type\": \"Compressor\", \"model\": \"21\", \"category\": \"Utilities\", \"vessel_id\": 13, \"created_at\": \"2025-09-15 17:05:33\", \"updated_at\": \"2025-09-15 17:05:33\", \"is_critical\": true, \"manufacturer\": \"21\", \"serial_number\": \"wq\", \"installation_date\": \"2025-09-11 00:00:00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"tag\": \"21\", \"code\": \"2131\", \"name\": \"wq\", \"type\": \"Compressor\", \"model\": \"21\", \"status\": \"stopped\", \"category\": \"Utilities\", \"vessel_id\": 13, \"is_critical\": true, \"manufacturer\": \"21\", \"sub_category\": null, \"serial_number\": \"wq\", \"installation_date\": \"2025-09-11\"}}', NULL, '2025-09-15 10:05:33', '2025-09-15 10:05:33'),
(8, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 1, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:03:48.325817Z\", \"attributes\": {\"id\": 1, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:03:48\", \"updated_at\": \"2025-10-06 13:03:48\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 06:03:48', '2025-10-06 06:03:48'),
(9, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 2, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:03:56.014029Z\", \"attributes\": {\"id\": 2, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:03:56\", \"updated_at\": \"2025-10-06 13:03:56\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 06:03:56', '2025-10-06 06:03:56'),
(10, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 3, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:05:41.115434Z\", \"attributes\": {\"id\": 3, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:05:41\", \"updated_at\": \"2025-10-06 13:05:41\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 06:05:41', '2025-10-06 06:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `chemicals`
--

CREATE TABLE `chemicals` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `trade_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `type` enum('TEG','Methanol','Biocide','Corrosion Inhibitor','Scale Inhibitor','Demulsifier','Defoamer','H2S Scavenger','Oxygen Scavenger','Pour Point Depressant','Wax Inhibitor','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `unit` enum('Liters','Gallons','Barrels','Kg','Tons','Drums','IBC') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Liters',
  `spesific_gravity` decimal(6,4) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `chemicals`
--

INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CH-TEG', 'Triethylene Glycol (Lean)', 'TEG Lean', 'TEG', 'Liters', 1.1250, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(2, 'CH-BIO', 'Biocide (Glutaraldehyde)', 'BIO GA', 'Biocide', 'Liters', 1.0500, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(3, 'CH-CI', 'Corrosion Inhibitor (Film Form)', 'CI-FF', 'Corrosion Inhibitor', 'Liters', 0.9500, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(4, 'CH-DEM', 'Demulsifier', 'Demul-X', 'Other', 'Liters', 0.8800, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(5, 'CH-ANTIFOAM', 'Anti-Foam', 'AF-1000', 'Other', 'Liters', 0.9800, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(6, 'CH-MEOH', 'Methanol', 'MeOH', 'Methanol', 'Liters', 0.7918, 1, '2025-09-18 05:42:47', '2025-09-18 05:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `chemical_operations`
--

CREATE TABLE `chemical_operations` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `operation_date` date NOT NULL,
  `chemical_id` bigint UNSIGNED NOT NULL,
  `day_tank_level_cm` decimal(6,2) DEFAULT NULL,
  `day_tank_volume_l` decimal(8,2) DEFAULT NULL,
  `topup_drum` decimal(5,1) DEFAULT NULL,
  `topup_litres` decimal(8,2) DEFAULT NULL,
  `consumption_drum` decimal(5,1) DEFAULT NULL,
  `consumption_litres` decimal(8,2) DEFAULT NULL,
  `received_drum` decimal(5,1) DEFAULT NULL,
  `received_litres` decimal(8,2) DEFAULT NULL,
  `stock_drum` decimal(5,1) DEFAULT NULL,
  `stock_litres` decimal(8,2) DEFAULT NULL,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('PSC','Cost Recovery','Gross Split','Service Contract','Joint Operation') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `operator` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `effective_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `field_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `government_share_oil_pct` decimal(5,2) DEFAULT NULL,
  `government_share_gas_pct` decimal(5,2) DEFAULT NULL,
  `contract_status` enum('Active','Suspended','Force Majeure','Expired','Terminated') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `dvr_reports`
--

CREATE TABLE `dvr_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `report_period_from` datetime DEFAULT NULL,
  `report_period_to` datetime DEFAULT NULL,
  `oim_name` varchar(100) DEFAULT NULL,
  `production_supervisor` varchar(100) DEFAULT NULL,
  `security_level` enum('Normal','High','Critical') DEFAULT 'Normal',
  `daily_highlights` text,
  `tomorrow_plan` text,
  `is_approved` tinyint(1) DEFAULT '0',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tag` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('Gas Turbine','Generator','Compressor','Separator','Pump','Heat Exchanger','Motor','Fan','Cooler','Crane','Lifeboat','Fire Pump','HVAC','Instrument','Valve','Tank','Vessel','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` enum('Production','Utilities','Safety','Marine','HVAC','Instrumentation','Electrical','Telecommunications') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Production',
  `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `serial_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `is_critical` tinyint(1) DEFAULT '0',
  `status` enum('Active','Standby','Maintenance','Out of Service','Decommissioned') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'GTG-A', 'V6101A', 'Gas Turbine Generator A', 'Gas Turbine', 'Utilities', 'SOLAR TURBINE', 'CENTAUR 50', '0089H', '2023-12-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(2, 1, 'GTG-B', 'V6101B', 'Gas Turbine Generator B', 'Gas Turbine', 'Utilities', 'SOLAR TURBINE', 'CENTAUR 50', '0564H', '2023-12-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(3, 1, 'EDG', 'S2Z00148', 'Emergency Diesel Generator', 'Generator', 'Utilities', 'CATERPILLAR', '3516B', 'S2Z00148', '2023-12-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(4, 1, 'GTC-A', 'TC22210', 'Gas Compressor A', 'Compressor', 'Production', 'SOLAR TURBINES', 'TAURUS 60', 'TC22210', '2024-06-30', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(5, 1, 'GTC-B', 'TC22211', 'Gas Compressor B', 'Compressor', 'Production', 'SOLAR TURBINES', 'TAURUS 60', 'TC22211', '2025-09-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(6, 1, 'FWP-A', 'GM-7001A', 'Fire Water Pump A', 'Other', 'Safety', 'Ebara', 'GM-7001A', NULL, '2022-01-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(7, 1, 'FWP-B', 'GM-7001B', 'Fire Water Pump B', 'Other', 'Safety', 'Ebara', 'GM-7001B', NULL, '2022-01-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(8, 1, 'SWLP', 'GM-6404', 'Sea Water Lift Pump', 'Other', 'Utilities', 'KSB', 'GM-6404', NULL, '2021-05-01', 1, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(9, 1, 'AIRCOMP-1', 'V-6301A', 'Instrument Air Compressor A', 'Other', 'Utilities', 'Quincy', 'QSI', 'V-6301A', '2021-05-01', 0, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(10, 1, 'AIRCOMP-2', 'V-6301B', 'Instrument Air Compressor B', 'Other', 'Utilities', 'Quincy', 'QSI', 'V-6301B', '2021-05-01', 0, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_availability`
--

CREATE TABLE `equipment_availability` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `equipment_name` varchar(100) DEFAULT NULL,
  `system_name` varchar(100) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `planned_downtime_hours` decimal(6,2) DEFAULT NULL,
  `unplanned_downtime_hours` decimal(6,2) DEFAULT NULL,
  `uptime_hours` decimal(6,2) DEFAULT NULL,
  `availability_percent` decimal(5,2) DEFAULT NULL,
  `remarks` text,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_overrides`
--

CREATE TABLE `equipment_overrides` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `ssic_number` varchar(10) DEFAULT NULL,
  `date_applied` date DEFAULT NULL,
  `area_authority` varchar(200) DEFAULT NULL,
  `tag_number` varchar(50) DEFAULT NULL,
  `override_status` enum('Active','Expired','Cancelled') DEFAULT 'Active',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `gas_allocations`
--

CREATE TABLE `gas_allocations` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `total` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_allocation_lines`
--

CREATE TABLE `gas_allocation_lines` (
  `id` bigint UNSIGNED NOT NULL,
  `gas_allocation_id` bigint UNSIGNED DEFAULT NULL,
  `gas_buyer_id` bigint DEFAULT NULL,
  `allocation` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_buyers`
--

CREATE TABLE `gas_buyers` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `gas_buyers`
--

INSERT INTO `gas_buyers` (`id`, `vessel_id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'wq', '12', 1, '2025-10-06 06:03:18', '2025-10-06 06:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `gas_nomination`
--

CREATE TABLE `gas_nomination` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `total_nomination` decimal(10,4) DEFAULT NULL,
  `total_confirmed` decimal(10,4) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `status` enum('draft','confirmed','cancel') DEFAULT NULL,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gas_nomination`
--

INSERT INTO `gas_nomination` (`id`, `vessel_id`, `name`, `date`, `total_nomination`, `total_confirmed`, `confirmed_at`, `status`, `recorded_by`, `created_at`, `updated_at`) VALUES
(1, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 06:03:48', '2025-10-06 06:03:48'),
(2, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 06:03:56', '2025-10-06 06:03:56'),
(3, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 06:05:41', '2025-10-06 06:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `gas_nomination_lines`
--

CREATE TABLE `gas_nomination_lines` (
  `id` bigint UNSIGNED NOT NULL,
  `gas_nomination_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `nomination` decimal(10,4) DEFAULT NULL,
  `confirmed` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_sales_metering`
--

CREATE TABLE `gas_sales_metering` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `pressure_psig` decimal(8,2) DEFAULT NULL,
  `temperature_f` decimal(6,2) DEFAULT NULL,
  `h2o_content_lb_mmscf` decimal(8,2) DEFAULT NULL,
  `hcdp_a_f` decimal(6,2) DEFAULT NULL,
  `hcdp_b_f` decimal(6,2) DEFAULT NULL,
  `co2_content_mol_pct` decimal(5,2) DEFAULT NULL,
  `heating_value_btu_scf` decimal(8,2) DEFAULT NULL,
  `specific_gravity` decimal(6,4) DEFAULT NULL,
  `ejgp_pressure_psig` decimal(8,2) DEFAULT NULL,
  `total_flowrates` decimal(10,2) DEFAULT NULL,
  `status` enum('draft','confirmed','cancel') DEFAULT NULL,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_sales_metering_flowrates`
--

CREATE TABLE `gas_sales_metering_flowrates` (
  `id` bigint UNSIGNED NOT NULL,
  `gas_sales_metering_id` bigint UNSIGNED DEFAULT NULL,
  `vessel_id` bigint UNSIGNED DEFAULT NULL,
  `gas_buyer_id` bigint UNSIGNED DEFAULT NULL,
  `backup_stream` decimal(10,2) DEFAULT '0.00',
  `primary_stream` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hse_operations`
--

CREATE TABLE `hse_operations` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `pob_operations` int DEFAULT '0',
  `pob_management` int DEFAULT '0',
  `pob_total` int DEFAULT '0',
  `incident_count` int DEFAULT '0',
  `near_miss_count` int DEFAULT '0',
  `drill_conducted` tinyint(1) DEFAULT '0',
  `firewater_port_last_test_date` date DEFAULT NULL COMMENT 'PORT Firewater Pump last run test date',
  `firewater_port_status` enum('OFFLINE','STAND-BY','ONLINE') DEFAULT NULL COMMENT 'PORT Firewater Pump status',
  `firewater_stbd_last_test_date` date DEFAULT NULL COMMENT 'STBD Firewater Pump last run test date',
  `firewater_stbd_status` enum('OFFLINE','STAND-BY','ONLINE') DEFAULT NULL COMMENT 'STBD Firewater Pump status',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locales`
--

CREATE TABLE `locales` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `language` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `direction` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `locales`
--

INSERT INTO `locales` (`id`, `name`, `code`, `language`, `direction`, `created_at`, `updated_at`) VALUES
(1, 'Indonesia', 'id-ID', 'id', 'ltr', '2024-12-21 16:49:24', '2024-12-21 16:49:25'),
(2, 'English (United States)', 'en-US', 'en', 'ltr', '2024-12-21 16:49:56', '2024-12-21 16:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_activities`
--

CREATE TABLE `maintenance_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `equipment_id` bigint UNSIGNED DEFAULT NULL,
  `activity_date` date NOT NULL,
  `work_order_no` varchar(50) DEFAULT NULL,
  `work_type` enum('Preventive','Corrective','Inspection','Emergency') NOT NULL,
  `description` text NOT NULL,
  `work_hours` decimal(6,2) DEFAULT NULL,
  `status` enum('Planned','In Progress','Completed','Deferred') DEFAULT 'Planned',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marine_operations`
--

CREATE TABLE `marine_operations` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `operation_date` date NOT NULL,
  `ballast_tank_bbls` decimal(12,2) DEFAULT NULL,
  `condensate_t34_cm` decimal(8,2) DEFAULT NULL,
  `condensate_t35_cm` decimal(8,2) DEFAULT NULL,
  `wind_speed_knots` decimal(5,2) DEFAULT NULL,
  `wind_direction` varchar(10) DEFAULT NULL,
  `wave_height_m` decimal(5,2) DEFAULT NULL,
  `weather_condition` text,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_03_11_000000_create_failed_jobs_table', 1),
(2, '2024_03_11_100000_create_password_reset_tokens_table', 2),
(3, '2024_03_11_130510_create_users_table', 2),
(4, '2024_03_18_160754_create_permission_tables', 2),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 3),
(6, '2024_06_09_124209_add_device_to_personal_access_tokens', 3),
(7, '2024_06_09_124507_create_sessions_table', 3),
(8, '2025_07_30_200058_create_settings_table', 3),
(9, '2025_07_30_200059_add_settings_team_field', 3);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'settings-system.general', 'sanctum', '2025-10-06 05:58:20', '2025-10-06 05:58:20'),
(2, 'settings-system.email', 'sanctum', '2025-10-07 05:58:20', '2025-10-07 05:58:20'),
(4, 'settings-user.view', 'sanctum', '2025-10-08 05:58:20', '2025-10-08 05:58:20'),
(5, 'settings-user.create', 'sanctum', '2025-10-09 05:58:20', '2025-10-09 05:58:20'),
(6, 'settings-user.update', 'sanctum', '2025-10-10 05:58:20', '2025-10-10 05:58:20'),
(7, 'settings-user.delete', 'sanctum', '2025-10-11 05:58:20', '2025-10-11 05:58:20'),
(8, 'settings-role_permission.view', 'sanctum', '2025-10-12 05:58:20', '2025-10-12 05:58:20'),
(9, 'settings-role_permission.edit', 'sanctum', '2025-10-13 05:58:20', '2025-10-13 05:58:20'),
(10, 'settings-role_permission.update', 'sanctum', '2025-10-14 05:58:20', '2025-10-14 05:58:20'),
(11, 'settings-role_permission.delete', 'sanctum', '2025-10-15 05:58:20', '2025-10-15 05:58:20'),
(12, 'master-vessels.view', 'sanctum', '2025-10-16 05:58:20', '2025-10-16 05:58:20'),
(13, 'master-vessels.edit', 'sanctum', '2025-10-17 05:58:20', '2025-10-17 05:58:20'),
(14, 'master-vessels.update', 'sanctum', '2025-10-18 05:58:20', '2025-10-18 05:58:20'),
(15, 'master-vessels.delete', 'sanctum', '2025-10-19 05:58:20', '2025-10-19 05:58:20'),
(16, 'master-wells.view', 'sanctum', '2025-10-20 05:58:20', '2025-10-20 05:58:20'),
(17, 'master-wells.edit', 'sanctum', '2025-10-21 05:58:20', '2025-10-21 05:58:20'),
(18, 'master-wells.update', 'sanctum', '2025-10-22 05:58:20', '2025-10-22 05:58:20'),
(19, 'master-wells.delete', 'sanctum', '2025-10-23 05:58:20', '2025-10-23 05:58:20'),
(20, 'master-equipment.view', 'sanctum', '2025-10-24 05:58:20', '2025-10-24 05:58:20'),
(21, 'master-equipment.edit', 'sanctum', '2025-10-25 05:58:20', '2025-10-25 05:58:20'),
(22, 'master-equipment.update', 'sanctum', '2025-10-26 05:58:20', '2025-10-26 05:58:20'),
(23, 'master-equipment.delete', 'sanctum', '2025-10-27 05:58:20', '2025-10-27 05:58:20'),
(24, 'master-chemicals.view', 'sanctum', '2025-10-28 05:58:20', '2025-10-28 05:58:20'),
(25, 'master-chemicals.edit', 'sanctum', '2025-10-29 05:58:20', '2025-10-29 05:58:20'),
(26, 'master-chemicals.update', 'sanctum', '2025-10-30 05:58:20', '2025-10-30 05:58:20'),
(27, 'master-chemicals.delete', 'sanctum', '2025-10-31 05:58:20', '2025-10-31 05:58:20'),
(28, 'master-gas_buyer.view', 'sanctum', '2025-11-01 05:58:20', '2025-11-01 05:58:20'),
(29, 'master-gas_buyer.edit', 'sanctum', '2025-11-02 05:58:20', '2025-11-02 05:58:20'),
(30, 'master-gas_buyer.update', 'sanctum', '2025-11-03 05:58:20', '2025-11-03 05:58:20'),
(31, 'master-gas_buyer.delete', 'sanctum', '2025-11-04 05:58:20', '2025-11-04 05:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(36, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '5c889fdc0c4067ad977439c45b309ccb46269caa3e70c7677e34875b80e3f941', '[\"*\"]', '2025-09-14 03:47:05', '2025-09-14 07:25:31', '2025-09-13 07:25:31', '2025-09-14 03:47:05'),
(37, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'cef24f87319bf3a6e3cfc64999a5843bd1a6032e51558f8f00e0518c94f58d44', '[\"*\"]', '2025-09-14 17:03:26', '2025-09-15 17:03:23', '2025-09-14 17:03:23', '2025-09-14 17:03:26'),
(38, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '8146424977ac828fb2abd6b16768f22adb33740a413da5d7bff2d70b9c27d4d1', '[\"*\"]', '2025-09-15 10:09:56', '2025-09-15 17:03:25', '2025-09-14 17:03:25', '2025-09-15 10:09:56'),
(39, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '66fb8fbbbc7e7eccbd87eb6eb78be67c34d9e1626f234085c053a24e9a0da18c', '[\"*\"]', '2025-09-16 18:29:39', '2025-09-16 19:04:40', '2025-09-15 19:04:41', '2025-09-16 18:29:39'),
(40, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'd1087e8487effd78241f16349d1cdab5d67c268021428302c16bb0b440ceb605', '[\"*\"]', '2025-09-18 21:17:07', '2025-09-18 22:44:18', '2025-09-17 22:44:18', '2025-09-18 21:17:07'),
(41, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'bb1e7f7f9dd76a1ba295d7d6fba3d9ecf6bc47c02d46372853fbdbedcb685e39', '[\"*\"]', NULL, '2025-09-18 22:44:18', '2025-09-17 22:44:18', '2025-09-17 22:44:18'),
(42, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'e1f7912ac17162a87a28e2ae1e273fd3b387c3854037ebd317b8362647322e3e', '[\"*\"]', '2025-09-21 06:17:16', '2025-09-21 06:25:42', '2025-09-20 06:25:42', '2025-09-21 06:17:16'),
(43, 'App\\Models\\User', 1, ' / ', '127.0.0.1', '83b471e4d98c4b83b7aba1bb877385ea550187d2813b4bcb22333443dcf0df9a', '[\"*\"]', '2025-09-20 22:35:02', '2025-09-21 22:31:45', '2025-09-20 22:31:45', '2025-09-20 22:35:02'),
(44, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'fd909e4fd8a6d4c1de1b8dd78ce6ab7c3ca7562b5e162ddb75f61218e99eafcc', '[\"*\"]', '2025-09-22 06:02:33', '2025-09-22 06:27:41', '2025-09-21 06:27:41', '2025-09-22 06:02:33'),
(45, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', '9b1250e1957d0175ebae0319b4b655dd76be760ecbb0a16a71949618cd9b8c11', '[\"*\"]', '2025-09-22 21:43:20', '2025-09-23 07:54:40', '2025-09-22 07:54:40', '2025-09-22 21:43:20'),
(46, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', '87f967bf0848397d1ac3144dfb99b55c26fb6ed8baf647bd721d65d5f5208f0c', '[\"*\"]', '2025-09-24 19:57:35', '2025-09-25 01:55:15', '2025-09-24 01:55:15', '2025-09-24 19:57:35'),
(47, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'fc0637918194dc88c502918918a30bca8362067d6a15a9ebb576117a732b3123', '[\"*\"]', '2025-09-25 05:31:45', '2025-09-26 05:31:36', '2025-09-25 05:31:36', '2025-09-25 05:31:45'),
(48, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'b8c24c9b4d2d76b67261b199f84be28d5570dadb81f91dfe50a3540fbb879522', '[\"*\"]', '2025-10-06 11:24:37', '2025-10-07 01:13:46', '2025-10-06 01:13:46', '2025-10-06 11:24:37'),
(49, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 141', '127.0.0.1', 'f54eaceffb3364fede6e2e6762399bc55493a7cef12f8337c0b24757a6926500', '[\"*\"]', '2025-10-06 11:24:58', '2025-10-07 11:24:55', '2025-10-06 11:24:55', '2025-10-06 11:24:58'),
(50, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 141', '127.0.0.1', '38aaedb38706678391eddddb0a16e10837c42a45a8b97b10db0f9870ab74aad1', '[\"*\"]', '2025-10-06 20:49:19', '2025-10-07 11:26:02', '2025-10-06 11:26:02', '2025-10-06 20:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `production_daily`
--

CREATE TABLE `production_daily` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `gas_export_uptime_hours` decimal(5,2) DEFAULT NULL,
  `gas_produced_total_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_exported_mmscf` decimal(10,4) DEFAULT NULL,
  `fuel_gas_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_production_target_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_export_pressure_psig` decimal(8,2) DEFAULT NULL COMMENT 'Reference dari gas_sales_metering avg',
  `gas_export_temp_f` decimal(6,2) DEFAULT NULL COMMENT 'Reference dari gas_sales_metering avg',
  `gas_flared_hp_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_flared_lp_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_flaring_total_mmscf` decimal(10,4) DEFAULT NULL,
  `gas_produced_mtd_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Month-to-Date gas production',
  `gas_production_target_mtd_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Month-to-Date production target',
  `gas_export_mtd_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Month-to-Date gas export',
  `gas_produced_cumulative_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Cumulative gas production from start',
  `gas_export_cumulative_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Cumulative gas export from start',
  `condensate_uptime_hours` decimal(5,2) DEFAULT NULL,
  `condensate_produced_bbls` decimal(10,2) DEFAULT NULL,
  `condensate_skimmed_bbls` decimal(10,2) DEFAULT NULL,
  `condensate_production_total_bbls` decimal(10,2) DEFAULT NULL,
  `condensate_production_cumulative_bbls` decimal(10,2) DEFAULT NULL COMMENT 'Cumulative condensate production',
  `condensate_temp_f` decimal(6,2) DEFAULT NULL,
  `condensate_used_by_gtg_bbls` decimal(10,2) DEFAULT NULL,
  `condensate_skimmed_tag` varchar(50) DEFAULT NULL,
  `condensate_temp_tag` varchar(50) DEFAULT NULL,
  `condensate_gtg_tag` varchar(50) DEFAULT NULL,
  `produced_water_total_bbls` decimal(10,2) DEFAULT NULL,
  `produced_water_offspec_bbls` decimal(10,2) DEFAULT NULL,
  `produced_water_overboard_bbls` decimal(10,2) DEFAULT NULL,
  `water_oiw_content_ppm` decimal(8,2) DEFAULT NULL,
  `water_overboard_tag` varchar(50) DEFAULT NULL,
  `multiphase_flow_total_mmscf` decimal(10,4) DEFAULT NULL,
  `multiphase_flow_mtd_mmscf` decimal(10,4) DEFAULT NULL COMMENT 'Month-to-Date multiphase flow',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'sanctum', '2025-10-06 08:20:05', '2025-10-06 08:20:05'),
(2, 'OIM', 'sanctum', '2025-10-06 08:20:10', '2025-10-06 08:20:10'),
(3, 'Production', 'sanctum', '2025-10-06 08:20:16', '2025-10-06 08:20:16'),
(4, 'Maintenance', 'sanctum', '2025-10-06 08:20:21', '2025-10-06 08:20:21'),
(5, 'Admin Office', 'sanctum', '2025-10-06 11:44:32', '2025-10-06 11:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(12, 5),
(13, 5),
(14, 5),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 5),
(20, 5),
(21, 5),
(22, 5),
(23, 5),
(24, 5),
(25, 5),
(26, 5),
(27, 5),
(28, 5),
(29, 5),
(30, 5),
(31, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE `sequences` (
  `id` int NOT NULL,
  `vessel_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_format` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_number` int DEFAULT '0',
  `padding` int DEFAULT '5',
  `separator` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_period` enum('daily','monthly','yearly','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `last_reset_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `sequences`
--

INSERT INTO `sequences` (`id`, `vessel_id`, `name`, `prefix`, `date_format`, `current_number`, `padding`, `separator`, `reset_period`, `last_reset_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'sales_gas_nomination', 'SGN', 'Ymd', 1, 5, '/', 'monthly', '2025-08-11', NULL, '2025-08-11 05:20:14'),
(2, 1, 'sales_gas_allocation', 'SGA', 'Ym', 1, 5, '/', 'monthly', '2025-02-21', NULL, '2025-02-21 00:26:16');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1F00ohfhC5lhRYxeJkOVjbPDXhQUnVRBLNljSzGk', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6InRTVFJlQlExRGpiMzNVUlVNS3RNWTRSbldxTDVsd05mZmE4Qm9GT1IiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759808959),
('GPBvLgS9bxXgRt4GQrccBcaaF1iCcaonaRTiYud5', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6IllseEpFRjVwbVpnWThCYU5aeFRvc2hYcVlrczN6ek43RE9EVGpzNTciO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759780537),
('oK2XZaVrTD6eeiqT4zTmHWko8PGDqsP5FYyTsT2G', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6IlZvU1hsZHZYOVpleWFaaFhJTGZ0a3l6bGk1RjVNNmhJVGxoYTFoa1giO3M6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjYzOiJodHRwczovL25lcHR1bmUudGVzdC9kYXNoYm9hcmQ/cmVkaXJlY3QtcmVhc29uPWFscmVhZHklMjBsb2dnZWQiO319', 1759782089),
('wv9l7MsX2Tvy7K2RbqOv6yyJ86WbtK7wyGvQUIzC', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6ImZMTEZOcktaMXVrQ2xOQUlCNlBCSE05bnlZRlVzMFNZSE1rbUJtMEoiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759811275);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(33, '361f39c14ccb42f4cfb6b8f404ab9548', 's:12:\"NEPTUNE Pakarti\";'),
(34, '84f597d6d272b8665cba1f05798bef2d', 's:18:\"Pakarti Tirtoagung\";'),
(35, '68f6b9a0e5b709e3b896e1bbaf27b58e', 's:14:\"+62218778 3422\";'),
(36, 'c2a50904fbd4ebef0e0a6bd987aa56b2', 's:16:\"info@pakarti.com\";'),
(37, '55a0ce6b4d7666651d3e7b7022163a82', 's:67:\"Jl. Raya Tengah No.4, Pasar Rebo,\r\nJakarta Timur, 13760, DKI Tunggu\";'),
(38, 'c9fa83462537d547c07205fa62080876', 's:5:\"d M Y\";'),
(39, '69857f6066512d4c357747de533878d2', 's:5:\"HH:mm\";'),
(40, 'da909d73e6d0b67f9c81607355325206', 's:12:\"Asia/Jakarta\";'),
(41, '1d4b1fce312c8502f4fad776f68f8c02', 's:56:\"/uploads/logo/logo-light-67d968d02726e_67ed657c7e534.png\";'),
(42, 'c5906d57f6bc0d42bc0e4de53ce651ec', 's:42:\"/uploads/logo/logo-light_67d968d02726e.png\";'),
(43, 'f3e05a6124cd3039288dfc18b7533cfe', 's:39:\"/uploads/logo/logo-sm_67d9c5fb96140.png\";'),
(44, '671726b0a50f06ab238a1d58d119fb8d', 's:39:\"/uploads/logo/logo-sm_67d9c5fb58685.png\";'),
(45, '6d29081325014d836043615296f41312', 'N;'),
(46, '384d706db525c9cafcb86ff26530e22d', 's:5:\"en-US\";'),
(47, '7f6100029ed2ace1dc9e737e14321131', 'i:10;'),
(48, '7fe5267d33bb1bfaf0a6c398845b4aec', 'i:7;'),
(49, 'ac581e6e5e7610bf3331589c294b1bcd', 'i:5;'),
(50, '85a53719cf55ece9d95df006f48caeae', 'i:11;'),
(51, 'f70cd62284aaa7de5e9acf0a8fce04b2', 'i:12;'),
(52, '0c7e5e73ec5ff6bb05a9a0962b73079f', 'i:12;'),
(53, '744e82810b0c67cf2936210075bcda61', 'i:11;'),
(54, '9071ec1fbb15b6c17a718e9ba30b6674', 'i:2;'),
(55, '32a1e3f7dae7a4b413d61871ff9b6d62', 'N;'),
(56, 'd3002f4c7f164e5711aa7baaeec5b36c', 'N;'),
(57, 'c86783b7736ca555b027d836f1036e0f', 'N;'),
(58, '4b847b38187ffa3c60bc71a7985bdcf2', 'N;'),
(59, '84f8df91dc6ac215931a61cf1312d0aa', 'N;'),
(60, '36f84cf3a7367ca37d4fa72b24c8e1ba', 's:39:\"/uploads/logo/logo-sm_67d9c67f27b05.png\";');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vessel_id` bigint UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `image`, `vessel_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@pakarti.com', 'admin', NULL, 1, '2025-09-12 09:59:57', '$2y$12$GboB8GlITCVJZcY5xVqWQeJ/6oLSPYQMavMgzUPoQE7kr/HmProAu', NULL, '2025-09-11 12:42:59', '2025-09-11 12:43:03'),
(7, 'Rasid', 'rasid242@pakarti.com', '123124', NULL, 1, NULL, '$2y$12$ClgR1V0Bvoj07UvFie6/Bu1kwh.LEiayDaJ6tYoWe1kDtK5fYcBf.', NULL, '2025-10-06 08:26:37', '2025-10-06 08:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_vessels`
--

CREATE TABLE `user_vessels` (
  `user_id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_vessels`
--

INSERT INTO `user_vessels` (`user_id`, `vessel_id`) VALUES
(7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vessels`
--

CREATE TABLE `vessels` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('MOPU','FPU') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `oim_id` bigint UNSIGNED DEFAULT NULL,
  `client_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `client_oim` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `vessels`
--

INSERT INTO `vessels` (`id`, `code`, `name`, `type`, `oim_id`, `client_name`, `client_oim`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PW-8', 'MOPU PRAMESWARI-8', 'MOPU', 7, 'HCML', 'MAC FIELD', 1, '2025-09-18 05:41:12', '2025-10-06 11:49:08'),
(2, '12', '21', 'FPU', NULL, 'wqe', 'wqe', 1, '2025-09-24 01:55:52', '2025-09-24 01:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `vessel_operations`
--

CREATE TABLE `vessel_operations` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('Active','Standby','Shutdown') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `mac_mmscf` decimal(6,2) DEFAULT NULL,
  `inlet_gas_mmscf` decimal(8,2) DEFAULT NULL,
  `condencate_produced_lts` decimal(10,4) DEFAULT NULL,
  `condensate_skimmed_t43` decimal(8,2) DEFAULT NULL,
  `condensate_production_total_bbls` decimal(6,2) DEFAULT NULL,
  `condensate_stock_total_bbls` decimal(10,4) DEFAULT NULL,
  `condensate_consumed_gtg_bbls` decimal(10,4) DEFAULT NULL,
  `diesel_fuel_vessel_consumption` decimal(10,4) DEFAULT NULL,
  `diesel_fuel_client_consumtion` decimal(10,4) DEFAULT NULL,
  `produce_water_total` decimal(10,2) DEFAULT NULL,
  `discharge_water_overload` decimal(10,2) DEFAULT NULL,
  `oil_in_water_content` decimal(10,2) DEFAULT NULL,
  `export_gas_from_vessel` decimal(10,4) DEFAULT NULL,
  `average_ejgp_pressure` decimal(10,2) DEFAULT NULL,
  `fuel_gas` decimal(10,4) DEFAULT NULL,
  `total_flare_gas_lp` decimal(10,4) DEFAULT NULL,
  `total_flare_gas_hp` decimal(10,4) DEFAULT NULL,
  `total_flare_gas` decimal(10,4) DEFAULT NULL,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wells`
--

CREATE TABLE `wells` (
  `id` bigint UNSIGNED NOT NULL,
  `vessel_id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('Oil','Gas','Gas Lift','Water Injection','Gas Injection') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `max_oil_rate` decimal(10,2) DEFAULT NULL,
  `max_gas_rate` decimal(10,2) DEFAULT NULL,
  `max_water_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Shut-in','Abandoned','Suspended','Testing','Workover') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `wells`
--

INSERT INTO `wells` (`id`, `vessel_id`, `code`, `name`, `type`, `max_oil_rate`, `max_gas_rate`, `max_water_rate`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'A1', 'Well A1', 'Gas', 0.00, 18.00, 0.00, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(2, 1, 'A2', 'Well A2', 'Gas', 0.00, 17.00, 0.00, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47'),
(3, 1, 'A3', 'Well A3', 'Gas', 0.00, 16.00, 0.00, 'Active', '2025-09-18 05:42:47', '2025-09-18 05:42:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `subject` (`subject_type`,`subject_id`) USING BTREE,
  ADD KEY `causer` (`causer_type`,`causer_id`) USING BTREE,
  ADD KEY `activity_log_log_name_index` (`log_name`) USING BTREE;

--
-- Indexes for table `chemicals`
--
ALTER TABLE `chemicals`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `code` (`code`) USING BTREE,
  ADD KEY `idx_code` (`code`) USING BTREE,
  ADD KEY `idx_type` (`type`) USING BTREE,
  ADD KEY `idx_is_active` (`is_active`) USING BTREE;

--
-- Indexes for table `chemical_operations`
--
ALTER TABLE `chemical_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `chemical_id` (`chemical_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `contract_number` (`number`) USING BTREE,
  ADD KEY `idx_contract_number` (`number`) USING BTREE,
  ADD KEY `idx_contract_status` (`contract_status`) USING BTREE,
  ADD KEY `idx_effective_date` (`effective_date`) USING BTREE;

--
-- Indexes for table `dvr_reports`
--
ALTER TABLE `dvr_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_vessel_id` (`vessel_id`) USING BTREE,
  ADD KEY `idx_tag` (`tag`) USING BTREE,
  ADD KEY `idx_type` (`type`) USING BTREE,
  ADD KEY `idx_category` (`category`) USING BTREE,
  ADD KEY `idx_is_critical` (`is_critical`) USING BTREE,
  ADD KEY `idx_status` (`status`) USING BTREE;

--
-- Indexes for table `equipment_availability`
--
ALTER TABLE `equipment_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `equipment_overrides`
--
ALTER TABLE `equipment_overrides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE;

--
-- Indexes for table `gas_allocations`
--
ALTER TABLE `gas_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gas_sales_allocations_ibfk_1` (`vessel_id`);

--
-- Indexes for table `gas_allocation_lines`
--
ALTER TABLE `gas_allocation_lines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gas_buyers`
--
ALTER TABLE `gas_buyers`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `gas_buyers_ibfk_1` (`vessel_id`);

--
-- Indexes for table `gas_nomination`
--
ALTER TABLE `gas_nomination`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `gas_nomination_lines`
--
ALTER TABLE `gas_nomination_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gas_nomination_id` (`gas_nomination_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `gas_sales_metering`
--
ALTER TABLE `gas_sales_metering`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vessel_date_hour` (`vessel_id`,`date`,`time`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `idx_vessel_date` (`vessel_id`,`date`);

--
-- Indexes for table `gas_sales_metering_flowrates`
--
ALTER TABLE `gas_sales_metering_flowrates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gas_sales_metering_flowrates_ibfk_2` (`vessel_id`),
  ADD KEY `gas_sales_metering_flowrates_ibfk_1` (`gas_sales_metering_id`);

--
-- Indexes for table `hse_operations`
--
ALTER TABLE `hse_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `maintenance_activities`
--
ALTER TABLE `maintenance_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `marine_operations`
--
ALTER TABLE `marine_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`) USING BTREE;

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE;

--
-- Indexes for table `production_daily`
--
ALTER TABLE `production_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vessel_date` (`vessel_id`,`date`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `idx_report_date` (`date`),
  ADD KEY `idx_vessel_id` (`vessel_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE;

--
-- Indexes for table `sequences`
--
ALTER TABLE `sequences`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `fk_sequences_outlet` (`vessel_id`) USING BTREE;

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `sessions_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `sessions_last_activity_index` (`last_activity`) USING BTREE;

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `settings_key_unique` (`key`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE,
  ADD KEY `user_ibfk1` (`vessel_id`) USING BTREE;

--
-- Indexes for table `user_vessels`
--
ALTER TABLE `user_vessels`
  ADD KEY `user_vessels_ibfk1` (`user_id`) USING BTREE,
  ADD KEY `user_vessels_ibfk2` (`vessel_id`) USING BTREE;

--
-- Indexes for table `vessels`
--
ALTER TABLE `vessels`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `code` (`code`) USING BTREE,
  ADD KEY `idx_code` (`code`) USING BTREE,
  ADD KEY `idx_status` (`is_active`) USING BTREE,
  ADD KEY `idx_field_name` (`client_oim`) USING BTREE;

--
-- Indexes for table `vessel_operations`
--
ALTER TABLE `vessel_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `wells`
--
ALTER TABLE `wells`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `code` (`code`) USING BTREE,
  ADD KEY `idx_vessel_id` (`vessel_id`) USING BTREE,
  ADD KEY `idx_code` (`code`) USING BTREE,
  ADD KEY `idx_status` (`status`) USING BTREE,
  ADD KEY `idx_type` (`type`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chemicals`
--
ALTER TABLE `chemicals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chemical_operations`
--
ALTER TABLE `chemical_operations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dvr_reports`
--
ALTER TABLE `dvr_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `equipment_availability`
--
ALTER TABLE `equipment_availability`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_overrides`
--
ALTER TABLE `equipment_overrides`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_allocations`
--
ALTER TABLE `gas_allocations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_allocation_lines`
--
ALTER TABLE `gas_allocation_lines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_buyers`
--
ALTER TABLE `gas_buyers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gas_nomination`
--
ALTER TABLE `gas_nomination`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gas_nomination_lines`
--
ALTER TABLE `gas_nomination_lines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_sales_metering`
--
ALTER TABLE `gas_sales_metering`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_sales_metering_flowrates`
--
ALTER TABLE `gas_sales_metering_flowrates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hse_operations`
--
ALTER TABLE `hse_operations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locales`
--
ALTER TABLE `locales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `maintenance_activities`
--
ALTER TABLE `maintenance_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marine_operations`
--
ALTER TABLE `marine_operations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `production_daily`
--
ALTER TABLE `production_daily`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sequences`
--
ALTER TABLE `sequences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vessels`
--
ALTER TABLE `vessels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vessel_operations`
--
ALTER TABLE `vessel_operations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wells`
--
ALTER TABLE `wells`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chemical_operations`
--
ALTER TABLE `chemical_operations`
  ADD CONSTRAINT `chemical_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `chemical_operations_ibfk_2` FOREIGN KEY (`chemical_id`) REFERENCES `chemicals` (`id`);

--
-- Constraints for table `dvr_reports`
--
ALTER TABLE `dvr_reports`
  ADD CONSTRAINT `dvr_reports_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`);

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `equipment_availability`
--
ALTER TABLE `equipment_availability`
  ADD CONSTRAINT `equipment_availability_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `equipment_availability_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `equipment_overrides`
--
ALTER TABLE `equipment_overrides`
  ADD CONSTRAINT `equipment_overrides_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `equipment_overrides_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `gas_allocations`
--
ALTER TABLE `gas_allocations`
  ADD CONSTRAINT `gas_allocations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `gas_buyers`
--
ALTER TABLE `gas_buyers`
  ADD CONSTRAINT `gas_buyers_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `gas_nomination`
--
ALTER TABLE `gas_nomination`
  ADD CONSTRAINT `gas_nomination_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `gas_nomination_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `gas_nomination_lines`
--
ALTER TABLE `gas_nomination_lines`
  ADD CONSTRAINT `gas_nomination_lines_ibfk_1` FOREIGN KEY (`gas_nomination_id`) REFERENCES `gas_nomination` (`id`),
  ADD CONSTRAINT `gas_nomination_lines_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `gas_buyers` (`id`);

--
-- Constraints for table `gas_sales_metering`
--
ALTER TABLE `gas_sales_metering`
  ADD CONSTRAINT `gas_sales_metering_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `gas_sales_metering_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `gas_sales_metering_flowrates`
--
ALTER TABLE `gas_sales_metering_flowrates`
  ADD CONSTRAINT `gas_sales_metering_flowrates_ibfk_1` FOREIGN KEY (`gas_sales_metering_id`) REFERENCES `gas_sales_metering` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `gas_sales_metering_flowrates_ibfk_2` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `hse_operations`
--
ALTER TABLE `hse_operations`
  ADD CONSTRAINT `hse_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `hse_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `maintenance_activities`
--
ALTER TABLE `maintenance_activities`
  ADD CONSTRAINT `maintenance_activities_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `maintenance_activities_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);

--
-- Constraints for table `marine_operations`
--
ALTER TABLE `marine_operations`
  ADD CONSTRAINT `marine_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `marine_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `production_daily`
--
ALTER TABLE `production_daily`
  ADD CONSTRAINT `production_daily_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `production_daily_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `role_has_permissions_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `sequences`
--
ALTER TABLE `sequences`
  ADD CONSTRAINT `sequences_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `user_vessels`
--
ALTER TABLE `user_vessels`
  ADD CONSTRAINT `user_vessels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `user_vessels_ibfk_2` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `vessel_operations`
--
ALTER TABLE `vessel_operations`
  ADD CONSTRAINT `vessel_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  ADD CONSTRAINT `vessel_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `wells`
--
ALTER TABLE `wells`
  ADD CONSTRAINT `wells_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
