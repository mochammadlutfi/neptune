/*
 Navicat Premium Dump SQL

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80402 (8.4.2)
 Source Host           : localhost:3306
 Source Schema         : dev_neptune

 Target Server Type    : MySQL
 Target Server Version : 80402 (8.4.2)
 File Encoding         : 65001

 Date: 07/10/2025 11:35:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_log
-- ----------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `subject` (`subject_type`,`subject_id`) USING BTREE,
  KEY `causer` (`causer_type`,`causer_id`) USING BTREE,
  KEY `activity_log_log_name_index` (`log_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of activity_log
-- ----------------------------
BEGIN;
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (1, 'default', 'Created well production reading for ARJUNA-01', 'App\\Models\\Production\\WellProduction', NULL, 7, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/wells/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-07T06:52:42.433305Z\", \"attributes\": {\"id\": 7, \"remarks\": \"eddf\", \"well_id\": 1, \"vessel_id\": 1, \"created_at\": \"2025-09-07 06:52:41\", \"flow_hours\": 2, \"updated_at\": \"2025-09-07 06:52:41\", \"api_gravity\": 4, \"recorded_by\": 1, \"bs_w_percent\": 33, \"oil_rate_bph\": 54, \"gas_rate_mscfh\": 43, \"water_rate_bph\": 45, \"choke_size_64th\": 34, \"reading_datetime\": \"2025-09-07 06:51:10\", \"wellhead_pressure_psi\": 34, \"wellhead_temperature_f\": 43}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"eddf\", \"well_id\": 1, \"flow_hours\": 2, \"api_gravity\": 4, \"bs_w_percent\": 33, \"oil_rate_bph\": 54, \"gas_rate_mscfh\": 43, \"water_rate_bph\": 45, \"choke_size_64th\": 34, \"reading_datetime\": \"2025-09-07T06:51:10.295Z\", \"wellhead_pressure_psi\": 34, \"wellhead_temperature_f\": 43}}', NULL, '2025-09-07 06:52:42', '2025-09-07 06:52:42');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (2, 'default', 'activity.gas_sales_metering.created', 'App\\Models\\Production\\GasSalesMetering', NULL, 3, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/sales-gas/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-07T21:43:37.558417Z\", \"attributes\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07 21:43:37\", \"updated_at\": \"2025-09-07 21:43:37\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"recorded_by\": null, \"reading_time\": \"2025-09-07T21:28:06.285Z\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}}', NULL, '2025-09-07 21:43:37', '2025-09-07 21:43:37');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (3, 'default', 'activity.gas_sales_metering.updated', 'App\\Models\\Production\\GasSalesMetering', NULL, 3, 'App\\Models\\User', 1, '{\"old\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07T21:43:37.000000Z\", \"updated_at\": \"2025-09-07T21:43:37.000000Z\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07T21:28:06.000000Z\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"url\": \"https://neptune.local/api/production/sales-gas/3/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-08T02:27:00.968809Z\", \"attributes\": {\"id\": 3, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"created_at\": \"2025-09-07 21:43:37\", \"updated_at\": \"2025-09-07 21:43:37\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"123\", \"recorded_by\": 1, \"reading_time\": \"2025-09-07 21:28:06\", \"export_temp_f\": 21, \"flowrate_mmscfd\": 23, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 21, \"specific_gravity\": 21, \"variance_percent\": -42.86, \"total_volume_mmscf\": 21, \"co2_content_percent\": 12, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 12, \"heating_value_btu_scf\": 21}, \"changes_count\": 0}', NULL, '2025-09-08 02:27:00', '2025-09-08 02:27:00');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (4, 'default', 'activity.gas_sales_metering.created', 'App\\Models\\Production\\GasSalesMetering', NULL, 4, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/production/sales-gas/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-08T04:01:06.845490Z\", \"attributes\": {\"id\": 4, \"remarks\": \"wqeqwe\", \"vessel_id\": 1, \"buyer_name\": \"wqrwq\", \"created_at\": \"2025-09-08 04:01:06\", \"updated_at\": \"2025-09-08 04:01:06\", \"recorded_by\": 1, \"reading_time\": \"2025-09-08 03:59:55\", \"export_temp_f\": 213, \"flowrate_mmscfd\": 21, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 24, \"specific_gravity\": 21, \"variance_percent\": 41.67, \"total_volume_mmscf\": 212, \"co2_content_percent\": 32, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 34, \"heating_value_btu_scf\": 21}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"remarks\": \"wqeqwe\", \"vessel_id\": null, \"buyer_name\": \"wqrwq\", \"reading_time\": \"2025-09-08T03:59:55.646Z\", \"export_temp_f\": 213, \"flowrate_mmscfd\": 21, \"h2s_content_ppm\": 21, \"nomination_mmscf\": 24, \"specific_gravity\": 21, \"variance_percent\": 41.67, \"total_volume_mmscf\": 212, \"co2_content_percent\": 32, \"export_pressure_psi\": 21, \"actual_delivery_mmscf\": 34, \"heating_value_btu_scf\": 21}}', NULL, '2025-09-08 04:01:06', '2025-09-08 04:01:06');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (5, 'default', 'activity.well.updated', 'App\\Models\\Master\\Well', NULL, 9, 'App\\Models\\User', 1, '{\"old\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13T14:48:51.000000Z\", \"updated_at\": \"2025-09-13T14:48:51.000000Z\", \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": 21}, \"url\": \"https://neptune.local/api/master/wells/9/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-14T05:01:50.648510Z\", \"attributes\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13 14:48:51\", \"updated_at\": \"2025-09-13 14:48:51\", \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": \"21.00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"max_gas_rate\": 213, \"max_oil_rate\": 21321, \"max_water_rate\": 21}, \"changes_count\": 0}', NULL, '2025-09-14 05:01:50', '2025-09-14 05:01:50');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (6, 'default', 'activity.well.updated', 'App\\Models\\Master\\Well', NULL, 9, 'App\\Models\\User', 1, '{\"old\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13T14:48:51.000000Z\", \"updated_at\": \"2025-09-14T05:02:17.000000Z\", \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": 21}, \"url\": \"https://neptune.local/api/master/wells/9/update\", \"locale\": \"en\", \"method\": \"PUT\", \"changes\": [], \"timestamp\": \"2025-09-14T05:02:17.549176Z\", \"attributes\": {\"id\": 9, \"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"created_at\": \"2025-09-13 14:48:51\", \"updated_at\": \"2025-09-14 05:02:17\", \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": \"21.00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"code\": \"21w\", \"name\": \"wqeqw\", \"type\": \"Gas\", \"status\": \"Active\", \"vessel_id\": 13, \"max_gas_rate\": 20.2, \"max_oil_rate\": 30.2, \"max_water_rate\": 30.2}, \"changes_count\": 0}', NULL, '2025-09-14 05:02:17', '2025-09-14 05:02:17');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (7, 'default', 'Created equipment record for wq', 'App\\Models\\Master\\Equipment', NULL, 10, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.local/api/master/equipment/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-09-15T17:05:33.670473Z\", \"attributes\": {\"id\": 10, \"tag\": \"21\", \"code\": \"2131\", \"name\": \"wq\", \"type\": \"Compressor\", \"model\": \"21\", \"category\": \"Utilities\", \"vessel_id\": 13, \"created_at\": \"2025-09-15 17:05:33\", \"updated_at\": \"2025-09-15 17:05:33\", \"is_critical\": true, \"manufacturer\": \"21\", \"serial_number\": \"wq\", \"installation_date\": \"2025-09-11 00:00:00\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\", \"request_data\": {\"tag\": \"21\", \"code\": \"2131\", \"name\": \"wq\", \"type\": \"Compressor\", \"model\": \"21\", \"status\": \"stopped\", \"category\": \"Utilities\", \"vessel_id\": 13, \"is_critical\": true, \"manufacturer\": \"21\", \"sub_category\": null, \"serial_number\": \"wq\", \"installation_date\": \"2025-09-11\"}}', NULL, '2025-09-15 17:05:33', '2025-09-15 17:05:33');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (8, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 1, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:03:48.325817Z\", \"attributes\": {\"id\": 1, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:03:48\", \"updated_at\": \"2025-10-06 13:03:48\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 13:03:48', '2025-10-06 13:03:48');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (9, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 2, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:03:56.014029Z\", \"attributes\": {\"id\": 2, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:03:56\", \"updated_at\": \"2025-10-06 13:03:56\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 13:03:56', '2025-10-06 13:03:56');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (10, 'default', 'activity.sales_gas_nomination.created', 'App\\Models\\Production\\SalesGasNomination', NULL, 3, 'App\\Models\\User', 1, '{\"url\": \"https://neptune.test/api/production/sales-gas-nomination/store\", \"locale\": \"en\", \"method\": \"POST\", \"timestamp\": \"2025-10-06T13:05:41.115434Z\", \"attributes\": {\"id\": 3, \"date\": \"2025-10-06 00:00:00\", \"vessel_id\": 1, \"created_at\": \"2025-10-06 13:05:41\", \"updated_at\": \"2025-10-06 13:05:41\"}, \"ip_address\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\", \"request_data\": {\"date\": \"2025-10-06\", \"lines\": [{\"confirmed\": 21, \"nomination\": 12, \"gas_buyer_id\": 1, \"gas_buyer_name\": \"wq\"}], \"remarks\": \"wq\", \"vessel_id\": 1, \"total_confirmed\": 0, \"total_nomination\": 0}}', NULL, '2025-10-06 13:05:41', '2025-10-06 13:05:41');
COMMIT;

-- ----------------------------
-- Table structure for chemical_operations
-- ----------------------------
DROP TABLE IF EXISTS `chemical_operations`;
CREATE TABLE `chemical_operations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `operation_date` date NOT NULL,
  `chemical_id` bigint unsigned NOT NULL,
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
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `chemical_id` (`chemical_id`),
  CONSTRAINT `chemical_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `chemical_operations_ibfk_2` FOREIGN KEY (`chemical_id`) REFERENCES `chemicals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of chemical_operations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for chemicals
-- ----------------------------
DROP TABLE IF EXISTS `chemicals`;
CREATE TABLE `chemicals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `trade_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `type` enum('TEG','Methanol','Biocide','Corrosion Inhibitor','Scale Inhibitor','Demulsifier','Defoamer','H2S Scavenger','Oxygen Scavenger','Pour Point Depressant','Wax Inhibitor','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `unit` enum('Liters','Gallons','Barrels','Kg','Tons','Drums','IBC') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Liters',
  `spesific_gravity` decimal(6,4) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_type` (`type`) USING BTREE,
  KEY `idx_is_active` (`is_active`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of chemicals
-- ----------------------------
BEGIN;
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (1, 'CH-TEG', 'Triethylene Glycol (Lean)', 'TEG Lean', 'TEG', 'Liters', 1.1250, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (2, 'CH-BIO', 'Biocide (Glutaraldehyde)', 'BIO GA', 'Biocide', 'Liters', 1.0500, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (3, 'CH-CI', 'Corrosion Inhibitor (Film Form)', 'CI-FF', 'Corrosion Inhibitor', 'Liters', 0.9500, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (4, 'CH-DEM', 'Demulsifier', 'Demul-X', 'Other', 'Liters', 0.8800, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (5, 'CH-ANTIFOAM', 'Anti-Foam', 'AF-1000', 'Other', 'Liters', 0.9800, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `chemicals` (`id`, `code`, `name`, `trade_name`, `type`, `unit`, `spesific_gravity`, `is_active`, `created_at`, `updated_at`) VALUES (6, 'CH-MEOH', 'Methanol', 'MeOH', 'Methanol', 'Liters', 0.7918, 1, '2025-09-18 12:42:47', '2025-09-18 12:42:47');
COMMIT;

-- ----------------------------
-- Table structure for contracts
-- ----------------------------
DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `contract_number` (`number`) USING BTREE,
  KEY `idx_contract_number` (`number`) USING BTREE,
  KEY `idx_contract_status` (`contract_status`) USING BTREE,
  KEY `idx_effective_date` (`effective_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of contracts
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for dvr_reports
-- ----------------------------
DROP TABLE IF EXISTS `dvr_reports`;
CREATE TABLE `dvr_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `report_date` date NOT NULL,
  `report_period_from` datetime DEFAULT NULL,
  `report_period_to` datetime DEFAULT NULL,
  `oim_name` varchar(100) DEFAULT NULL,
  `production_supervisor` varchar(100) DEFAULT NULL,
  `security_level` enum('Normal','High','Critical') DEFAULT 'Normal',
  `daily_highlights` text,
  `tomorrow_plan` text,
  `is_approved` tinyint(1) DEFAULT '0',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  CONSTRAINT `dvr_reports_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of dvr_reports
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for equipment
-- ----------------------------
DROP TABLE IF EXISTS `equipment`;
CREATE TABLE `equipment` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_vessel_id` (`vessel_id`) USING BTREE,
  KEY `idx_tag` (`tag`) USING BTREE,
  KEY `idx_type` (`type`) USING BTREE,
  KEY `idx_category` (`category`) USING BTREE,
  KEY `idx_is_critical` (`is_critical`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of equipment
-- ----------------------------
BEGIN;
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (1, 1, 'GTG-A', 'V6101A', 'Gas Turbine Generator A', 'Gas Turbine', 'Utilities', 'SOLAR TURBINE', 'CENTAUR 50', '0089H', '2023-12-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (2, 1, 'GTG-B', 'V6101B', 'Gas Turbine Generator B', 'Gas Turbine', 'Utilities', 'SOLAR TURBINE', 'CENTAUR 50', '0564H', '2023-12-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (3, 1, 'EDG', 'S2Z00148', 'Emergency Diesel Generator', 'Generator', 'Utilities', 'CATERPILLAR', '3516B', 'S2Z00148', '2023-12-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (4, 1, 'GTC-A', 'TC22210', 'Gas Compressor A', 'Compressor', 'Production', 'SOLAR TURBINES', 'TAURUS 60', 'TC22210', '2024-06-30', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (5, 1, 'GTC-B', 'TC22211', 'Gas Compressor B', 'Compressor', 'Production', 'SOLAR TURBINES', 'TAURUS 60', 'TC22211', '2025-09-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (6, 1, 'FWP-A', 'GM-7001A', 'Fire Water Pump A', 'Other', 'Safety', 'Ebara', 'GM-7001A', NULL, '2022-01-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (7, 1, 'FWP-B', 'GM-7001B', 'Fire Water Pump B', 'Other', 'Safety', 'Ebara', 'GM-7001B', NULL, '2022-01-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (8, 1, 'SWLP', 'GM-6404', 'Sea Water Lift Pump', 'Other', 'Utilities', 'KSB', 'GM-6404', NULL, '2021-05-01', 1, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (9, 1, 'AIRCOMP-1', 'V-6301A', 'Instrument Air Compressor A', 'Other', 'Utilities', 'Quincy', 'QSI', 'V-6301A', '2021-05-01', 0, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `equipment` (`id`, `vessel_id`, `code`, `tag`, `name`, `type`, `category`, `manufacturer`, `model`, `serial_number`, `installation_date`, `is_critical`, `status`, `created_at`, `updated_at`) VALUES (10, 1, 'AIRCOMP-2', 'V-6301B', 'Instrument Air Compressor B', 'Other', 'Utilities', 'Quincy', 'QSI', 'V-6301B', '2021-05-01', 0, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
COMMIT;

-- ----------------------------
-- Table structure for equipment_availability
-- ----------------------------
DROP TABLE IF EXISTS `equipment_availability`;
CREATE TABLE `equipment_availability` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `report_date` date NOT NULL,
  `equipment_name` varchar(100) DEFAULT NULL,
  `system_name` varchar(100) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `planned_downtime_hours` decimal(6,2) DEFAULT NULL,
  `unplanned_downtime_hours` decimal(6,2) DEFAULT NULL,
  `uptime_hours` decimal(6,2) DEFAULT NULL,
  `availability_percent` decimal(5,2) DEFAULT NULL,
  `remarks` text,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `equipment_availability_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `equipment_availability_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of equipment_availability
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for equipment_overrides
-- ----------------------------
DROP TABLE IF EXISTS `equipment_overrides`;
CREATE TABLE `equipment_overrides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `ssic_number` varchar(10) DEFAULT NULL,
  `date_applied` date DEFAULT NULL,
  `area_authority` varchar(200) DEFAULT NULL,
  `tag_number` varchar(50) DEFAULT NULL,
  `override_status` enum('Active','Expired','Cancelled') DEFAULT 'Active',
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `equipment_overrides_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `equipment_overrides_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of equipment_overrides
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for gas_allocation_lines
-- ----------------------------
DROP TABLE IF EXISTS `gas_allocation_lines`;
CREATE TABLE `gas_allocation_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gas_allocation_id` bigint unsigned DEFAULT NULL,
  `gas_buyer_id` bigint DEFAULT NULL,
  `allocation` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_allocation_lines
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for gas_allocations
-- ----------------------------
DROP TABLE IF EXISTS `gas_allocations`;
CREATE TABLE `gas_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `total` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gas_sales_allocations_ibfk_1` (`vessel_id`),
  CONSTRAINT `gas_allocations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_allocations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for gas_buyers
-- ----------------------------
DROP TABLE IF EXISTS `gas_buyers`;
CREATE TABLE `gas_buyers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `gas_buyers_ibfk_1` (`vessel_id`),
  CONSTRAINT `gas_buyers_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of gas_buyers
-- ----------------------------
BEGIN;
INSERT INTO `gas_buyers` (`id`, `vessel_id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES (1, NULL, 'wq', '12', 1, '2025-10-06 13:03:18', '2025-10-06 13:03:18');
COMMIT;

-- ----------------------------
-- Table structure for gas_nomination
-- ----------------------------
DROP TABLE IF EXISTS `gas_nomination`;
CREATE TABLE `gas_nomination` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `total_nomination` decimal(10,4) DEFAULT NULL,
  `total_confirmed` decimal(10,4) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `status` enum('draft','confirmed','cancel') DEFAULT NULL,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `gas_nomination_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `gas_nomination_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_nomination
-- ----------------------------
BEGIN;
INSERT INTO `gas_nomination` (`id`, `vessel_id`, `name`, `date`, `total_nomination`, `total_confirmed`, `confirmed_at`, `status`, `recorded_by`, `created_at`, `updated_at`) VALUES (1, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 13:03:48', '2025-10-06 13:03:48');
INSERT INTO `gas_nomination` (`id`, `vessel_id`, `name`, `date`, `total_nomination`, `total_confirmed`, `confirmed_at`, `status`, `recorded_by`, `created_at`, `updated_at`) VALUES (2, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 13:03:56', '2025-10-06 13:03:56');
INSERT INTO `gas_nomination` (`id`, `vessel_id`, `name`, `date`, `total_nomination`, `total_confirmed`, `confirmed_at`, `status`, `recorded_by`, `created_at`, `updated_at`) VALUES (3, 1, '', '2025-10-06', NULL, NULL, NULL, NULL, NULL, '2025-10-06 13:05:41', '2025-10-06 13:05:41');
COMMIT;

-- ----------------------------
-- Table structure for gas_nomination_lines
-- ----------------------------
DROP TABLE IF EXISTS `gas_nomination_lines`;
CREATE TABLE `gas_nomination_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gas_nomination_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `nomination` decimal(10,4) DEFAULT NULL,
  `confirmed` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gas_nomination_id` (`gas_nomination_id`),
  KEY `buyer_id` (`buyer_id`),
  CONSTRAINT `gas_nomination_lines_ibfk_1` FOREIGN KEY (`gas_nomination_id`) REFERENCES `gas_nomination` (`id`),
  CONSTRAINT `gas_nomination_lines_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `gas_buyers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_nomination_lines
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for gas_sales_metering
-- ----------------------------
DROP TABLE IF EXISTS `gas_sales_metering`;
CREATE TABLE `gas_sales_metering` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vessel_date_hour` (`vessel_id`,`date`,`time`),
  KEY `recorded_by` (`recorded_by`),
  KEY `idx_vessel_date` (`vessel_id`,`date`),
  CONSTRAINT `gas_sales_metering_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `gas_sales_metering_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_sales_metering
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for gas_sales_metering_flowrates
-- ----------------------------
DROP TABLE IF EXISTS `gas_sales_metering_flowrates`;
CREATE TABLE `gas_sales_metering_flowrates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gas_sales_metering_id` bigint unsigned DEFAULT NULL,
  `vessel_id` bigint unsigned DEFAULT NULL,
  `gas_buyer_id` bigint unsigned DEFAULT NULL,
  `backup_stream` decimal(10,2) DEFAULT '0.00',
  `primary_stream` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gas_sales_metering_flowrates_ibfk_2` (`vessel_id`),
  KEY `gas_sales_metering_flowrates_ibfk_1` (`gas_sales_metering_id`),
  CONSTRAINT `gas_sales_metering_flowrates_ibfk_1` FOREIGN KEY (`gas_sales_metering_id`) REFERENCES `gas_sales_metering` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `gas_sales_metering_flowrates_ibfk_2` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of gas_sales_metering_flowrates
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for hse_operations
-- ----------------------------
DROP TABLE IF EXISTS `hse_operations`;
CREATE TABLE `hse_operations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `hse_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `hse_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of hse_operations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for locales
-- ----------------------------
DROP TABLE IF EXISTS `locales`;
CREATE TABLE `locales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `language` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `direction` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of locales
-- ----------------------------
BEGIN;
INSERT INTO `locales` (`id`, `name`, `code`, `language`, `direction`, `created_at`, `updated_at`) VALUES (1, 'Indonesia', 'id-ID', 'id', 'ltr', '2024-12-21 23:49:24', '2024-12-21 23:49:25');
INSERT INTO `locales` (`id`, `name`, `code`, `language`, `direction`, `created_at`, `updated_at`) VALUES (2, 'English (United States)', 'en-US', 'en', 'ltr', '2024-12-21 23:49:56', '2024-12-21 23:49:59');
COMMIT;

-- ----------------------------
-- Table structure for maintenance_activities
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_activities`;
CREATE TABLE `maintenance_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `equipment_id` bigint unsigned DEFAULT NULL,
  `activity_date` date NOT NULL,
  `work_order_no` varchar(50) DEFAULT NULL,
  `work_type` enum('Preventive','Corrective','Inspection','Emergency') NOT NULL,
  `description` text NOT NULL,
  `work_hours` decimal(6,2) DEFAULT NULL,
  `status` enum('Planned','In Progress','Completed','Deferred') DEFAULT 'Planned',
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `equipment_id` (`equipment_id`),
  CONSTRAINT `maintenance_activities_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `maintenance_activities_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of maintenance_activities
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for marine_operations
-- ----------------------------
DROP TABLE IF EXISTS `marine_operations`;
CREATE TABLE `marine_operations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `operation_date` date NOT NULL,
  `ballast_tank_bbls` decimal(12,2) DEFAULT NULL,
  `condensate_t34_cm` decimal(8,2) DEFAULT NULL,
  `condensate_t35_cm` decimal(8,2) DEFAULT NULL,
  `wind_speed_knots` decimal(5,2) DEFAULT NULL,
  `wind_direction` varchar(10) DEFAULT NULL,
  `wave_height_m` decimal(5,2) DEFAULT NULL,
  `weather_condition` text,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `marine_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `marine_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of marine_operations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '2024_03_11_000000_create_failed_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '2024_03_11_100000_create_password_reset_tokens_table', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '2024_03_11_130510_create_users_table', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2024_03_18_160754_create_permission_tables', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2019_12_14_000001_create_personal_access_tokens_table', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2024_06_09_124209_add_device_to_personal_access_tokens', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2024_06_09_124507_create_sessions_table', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2025_07_30_200058_create_settings_table', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2025_07_30_200059_add_settings_team_field', 3);
COMMIT;

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE,
  CONSTRAINT `model_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE,
  CONSTRAINT `model_has_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------
BEGIN;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (1, 'App\\Models\\User', 1);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (2, 'App\\Models\\User', 2);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (2, 'App\\Models\\User', 7);
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1, 'settings-system.general', 'sanctum', '2025-10-06 12:58:20', '2025-10-06 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (2, 'settings-system.email', 'sanctum', '2025-10-07 12:58:20', '2025-10-07 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (4, 'settings-user.view', 'sanctum', '2025-10-08 12:58:20', '2025-10-08 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (5, 'settings-user.create', 'sanctum', '2025-10-09 12:58:20', '2025-10-09 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (6, 'settings-user.update', 'sanctum', '2025-10-10 12:58:20', '2025-10-10 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (7, 'settings-user.delete', 'sanctum', '2025-10-11 12:58:20', '2025-10-11 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (8, 'settings-role_permission.view', 'sanctum', '2025-10-12 12:58:20', '2025-10-12 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (9, 'settings-role_permission.edit', 'sanctum', '2025-10-13 12:58:20', '2025-10-13 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (10, 'settings-role_permission.update', 'sanctum', '2025-10-14 12:58:20', '2025-10-14 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (11, 'settings-role_permission.delete', 'sanctum', '2025-10-15 12:58:20', '2025-10-15 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (12, 'master-vessels.view', 'sanctum', '2025-10-16 12:58:20', '2025-10-16 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (13, 'master-vessels.edit', 'sanctum', '2025-10-17 12:58:20', '2025-10-17 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (14, 'master-vessels.update', 'sanctum', '2025-10-18 12:58:20', '2025-10-18 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (15, 'master-vessels.delete', 'sanctum', '2025-10-19 12:58:20', '2025-10-19 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (16, 'master-wells.view', 'sanctum', '2025-10-20 12:58:20', '2025-10-20 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (17, 'master-wells.edit', 'sanctum', '2025-10-21 12:58:20', '2025-10-21 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (18, 'master-wells.update', 'sanctum', '2025-10-22 12:58:20', '2025-10-22 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (19, 'master-wells.delete', 'sanctum', '2025-10-23 12:58:20', '2025-10-23 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (20, 'master-equipment.view', 'sanctum', '2025-10-24 12:58:20', '2025-10-24 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (21, 'master-equipment.edit', 'sanctum', '2025-10-25 12:58:20', '2025-10-25 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (22, 'master-equipment.update', 'sanctum', '2025-10-26 12:58:20', '2025-10-26 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (23, 'master-equipment.delete', 'sanctum', '2025-10-27 12:58:20', '2025-10-27 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (24, 'master-chemicals.view', 'sanctum', '2025-10-28 12:58:20', '2025-10-28 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (25, 'master-chemicals.edit', 'sanctum', '2025-10-29 12:58:20', '2025-10-29 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (26, 'master-chemicals.update', 'sanctum', '2025-10-30 12:58:20', '2025-10-30 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (27, 'master-chemicals.delete', 'sanctum', '2025-10-31 12:58:20', '2025-10-31 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (28, 'master-gas_buyer.view', 'sanctum', '2025-11-01 12:58:20', '2025-11-01 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (29, 'master-gas_buyer.edit', 'sanctum', '2025-11-02 12:58:20', '2025-11-02 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (30, 'master-gas_buyer.update', 'sanctum', '2025-11-03 12:58:20', '2025-11-03 12:58:20');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (31, 'master-gas_buyer.delete', 'sanctum', '2025-11-04 12:58:20', '2025-11-04 12:58:20');
COMMIT;

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------
BEGIN;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (36, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '5c889fdc0c4067ad977439c45b309ccb46269caa3e70c7677e34875b80e3f941', '[\"*\"]', '2025-09-14 10:47:05', '2025-09-14 14:25:31', '2025-09-13 14:25:31', '2025-09-14 10:47:05');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (37, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'cef24f87319bf3a6e3cfc64999a5843bd1a6032e51558f8f00e0518c94f58d44', '[\"*\"]', '2025-09-15 00:03:26', '2025-09-16 00:03:23', '2025-09-15 00:03:23', '2025-09-15 00:03:26');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (38, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '8146424977ac828fb2abd6b16768f22adb33740a413da5d7bff2d70b9c27d4d1', '[\"*\"]', '2025-09-15 17:09:56', '2025-09-16 00:03:25', '2025-09-15 00:03:25', '2025-09-15 17:09:56');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (39, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', '66fb8fbbbc7e7eccbd87eb6eb78be67c34d9e1626f234085c053a24e9a0da18c', '[\"*\"]', '2025-09-17 01:29:39', '2025-09-17 02:04:40', '2025-09-16 02:04:41', '2025-09-17 01:29:39');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (40, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'd1087e8487effd78241f16349d1cdab5d67c268021428302c16bb0b440ceb605', '[\"*\"]', '2025-09-19 04:17:07', '2025-09-19 05:44:18', '2025-09-18 05:44:18', '2025-09-19 04:17:07');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (41, 'App\\Models\\User', 1, 'Windows 10 / Chrome 140', '127.0.0.1', 'bb1e7f7f9dd76a1ba295d7d6fba3d9ecf6bc47c02d46372853fbdbedcb685e39', '[\"*\"]', NULL, '2025-09-19 05:44:18', '2025-09-18 05:44:18', '2025-09-18 05:44:18');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (42, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'e1f7912ac17162a87a28e2ae1e273fd3b387c3854037ebd317b8362647322e3e', '[\"*\"]', '2025-09-21 13:17:16', '2025-09-21 13:25:42', '2025-09-20 13:25:42', '2025-09-21 13:17:16');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (43, 'App\\Models\\User', 1, ' / ', '127.0.0.1', '83b471e4d98c4b83b7aba1bb877385ea550187d2813b4bcb22333443dcf0df9a', '[\"*\"]', '2025-09-21 05:35:02', '2025-09-22 05:31:45', '2025-09-21 05:31:45', '2025-09-21 05:35:02');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (44, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'fd909e4fd8a6d4c1de1b8dd78ce6ab7c3ca7562b5e162ddb75f61218e99eafcc', '[\"*\"]', '2025-09-22 13:02:33', '2025-09-22 13:27:41', '2025-09-21 13:27:41', '2025-09-22 13:02:33');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (45, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', '9b1250e1957d0175ebae0319b4b655dd76be760ecbb0a16a71949618cd9b8c11', '[\"*\"]', '2025-09-23 04:43:20', '2025-09-23 14:54:40', '2025-09-22 14:54:40', '2025-09-23 04:43:20');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (46, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', '87f967bf0848397d1ac3144dfb99b55c26fb6ed8baf647bd721d65d5f5208f0c', '[\"*\"]', '2025-09-25 02:57:35', '2025-09-25 08:55:15', '2025-09-24 08:55:15', '2025-09-25 02:57:35');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (47, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'fc0637918194dc88c502918918a30bca8362067d6a15a9ebb576117a732b3123', '[\"*\"]', '2025-09-25 12:31:45', '2025-09-26 12:31:36', '2025-09-25 12:31:36', '2025-09-25 12:31:45');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (48, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 140', '127.0.0.1', 'b8c24c9b4d2d76b67261b199f84be28d5570dadb81f91dfe50a3540fbb879522', '[\"*\"]', '2025-10-06 18:24:37', '2025-10-07 08:13:46', '2025-10-06 08:13:46', '2025-10-06 18:24:37');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (49, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 141', '127.0.0.1', 'f54eaceffb3364fede6e2e6762399bc55493a7cef12f8337c0b24757a6926500', '[\"*\"]', '2025-10-06 18:24:58', '2025-10-07 18:24:55', '2025-10-06 18:24:55', '2025-10-06 18:24:58');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `ip`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (50, 'App\\Models\\User', 1, 'Mac 10.15.7 / Chrome 141', '127.0.0.1', '38aaedb38706678391eddddb0a16e10837c42a45a8b97b10db0f9870ab74aad1', '[\"*\"]', '2025-10-07 03:49:19', '2025-10-07 18:26:02', '2025-10-06 18:26:02', '2025-10-07 03:49:19');
COMMIT;

-- ----------------------------
-- Table structure for production_daily
-- ----------------------------
DROP TABLE IF EXISTS `production_daily`;
CREATE TABLE `production_daily` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vessel_date` (`vessel_id`,`date`),
  KEY `recorded_by` (`recorded_by`),
  KEY `idx_report_date` (`date`),
  KEY `idx_vessel_id` (`vessel_id`),
  CONSTRAINT `production_daily_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `production_daily_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of production_daily
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE,
  CONSTRAINT `role_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
BEGIN;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (1, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (2, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (4, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (5, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (6, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (7, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (8, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (9, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (10, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (11, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (12, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (14, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (16, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (18, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (20, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (22, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (23, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (24, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (25, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (27, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (29, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (30, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (12, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (14, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (16, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (18, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (20, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (22, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (23, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (24, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (25, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (27, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (29, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (30, 5);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 5);
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1, 'Super Admin', 'sanctum', '2025-10-06 15:20:05', '2025-10-06 15:20:05');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (2, 'OIM', 'sanctum', '2025-10-06 15:20:10', '2025-10-06 15:20:10');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (3, 'Production', 'sanctum', '2025-10-06 15:20:16', '2025-10-06 15:20:16');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (4, 'Maintenance', 'sanctum', '2025-10-06 15:20:21', '2025-10-06 15:20:21');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (5, 'Admin Office', 'sanctum', '2025-10-06 18:44:32', '2025-10-06 18:44:32');
COMMIT;

-- ----------------------------
-- Table structure for sequences
-- ----------------------------
DROP TABLE IF EXISTS `sequences`;
CREATE TABLE `sequences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_format` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_number` int DEFAULT '0',
  `padding` int DEFAULT '5',
  `separator` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_period` enum('daily','monthly','yearly','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `last_reset_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_sequences_outlet` (`vessel_id`) USING BTREE,
  CONSTRAINT `sequences_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sequences
-- ----------------------------
BEGIN;
INSERT INTO `sequences` (`id`, `vessel_id`, `name`, `prefix`, `date_format`, `current_number`, `padding`, `separator`, `reset_period`, `last_reset_date`, `created_at`, `updated_at`) VALUES (1, 1, 'sales_gas_nomination', 'SGN', 'Ymd', 1, 5, '/', 'monthly', '2025-08-11', NULL, '2025-08-11 12:20:14');
INSERT INTO `sequences` (`id`, `vessel_id`, `name`, `prefix`, `date_format`, `current_number`, `padding`, `separator`, `reset_period`, `last_reset_date`, `created_at`, `updated_at`) VALUES (2, 1, 'sales_gas_allocation', 'SGA', 'Ym', 1, 5, '/', 'monthly', '2025-02-21', NULL, '2025-02-21 07:26:16');
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `sessions_user_id_index` (`user_id`) USING BTREE,
  KEY `sessions_last_activity_index` (`last_activity`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('1F00ohfhC5lhRYxeJkOVjbPDXhQUnVRBLNljSzGk', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6InRTVFJlQlExRGpiMzNVUlVNS3RNWTRSbldxTDVsd05mZmE4Qm9GT1IiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759808959);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('GPBvLgS9bxXgRt4GQrccBcaaF1iCcaonaRTiYud5', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6IllseEpFRjVwbVpnWThCYU5aeFRvc2hYcVlrczN6ek43RE9EVGpzNTciO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759780537);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('oK2XZaVrTD6eeiqT4zTmHWko8PGDqsP5FYyTsT2G', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6IlZvU1hsZHZYOVpleWFaaFhJTGZ0a3l6bGk1RjVNNmhJVGxoYTFoa1giO3M6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjYzOiJodHRwczovL25lcHR1bmUudGVzdC9kYXNoYm9hcmQ/cmVkaXJlY3QtcmVhc29uPWFscmVhZHklMjBsb2dnZWQiO319', 1759782089);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('wv9l7MsX2Tvy7K2RbqOv6yyJ86WbtK7wyGvQUIzC', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6ImxvY2FsZSI7czoyOiJlbiI7czo2OiJfdG9rZW4iO3M6NDA6ImZMTEZOcktaMXVrQ2xOQUlCNlBCSE05bnlZRlVzMFNZSE1rbUJtMEoiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwOiJodHRwczovL25lcHR1bmUudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759811275);
COMMIT;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `settings_key_unique` (`key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of settings
-- ----------------------------
BEGIN;
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (33, '361f39c14ccb42f4cfb6b8f404ab9548', 's:12:\"NEPTUNE Pakarti\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (34, '84f597d6d272b8665cba1f05798bef2d', 's:18:\"Pakarti Tirtoagung\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (35, '68f6b9a0e5b709e3b896e1bbaf27b58e', 's:14:\"+62218778 3422\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (36, 'c2a50904fbd4ebef0e0a6bd987aa56b2', 's:16:\"info@pakarti.com\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (37, '55a0ce6b4d7666651d3e7b7022163a82', 's:67:\"Jl. Raya Tengah No.4, Pasar Rebo,\r\nJakarta Timur, 13760, DKI Tunggu\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (38, 'c9fa83462537d547c07205fa62080876', 's:5:\"d M Y\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (39, '69857f6066512d4c357747de533878d2', 's:5:\"HH:mm\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (40, 'da909d73e6d0b67f9c81607355325206', 's:12:\"Asia/Jakarta\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (41, '1d4b1fce312c8502f4fad776f68f8c02', 's:56:\"/uploads/logo/logo-light-67d968d02726e_67ed657c7e534.png\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (42, 'c5906d57f6bc0d42bc0e4de53ce651ec', 's:42:\"/uploads/logo/logo-light_67d968d02726e.png\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (43, 'f3e05a6124cd3039288dfc18b7533cfe', 's:39:\"/uploads/logo/logo-sm_67d9c5fb96140.png\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (44, '671726b0a50f06ab238a1d58d119fb8d', 's:39:\"/uploads/logo/logo-sm_67d9c5fb58685.png\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (45, '6d29081325014d836043615296f41312', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (46, '384d706db525c9cafcb86ff26530e22d', 's:5:\"en-US\";');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (47, '7f6100029ed2ace1dc9e737e14321131', 'i:10;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (48, '7fe5267d33bb1bfaf0a6c398845b4aec', 'i:7;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (49, 'ac581e6e5e7610bf3331589c294b1bcd', 'i:5;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (50, '85a53719cf55ece9d95df006f48caeae', 'i:11;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (51, 'f70cd62284aaa7de5e9acf0a8fce04b2', 'i:12;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (52, '0c7e5e73ec5ff6bb05a9a0962b73079f', 'i:12;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (53, '744e82810b0c67cf2936210075bcda61', 'i:11;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (54, '9071ec1fbb15b6c17a718e9ba30b6674', 'i:2;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (55, '32a1e3f7dae7a4b413d61871ff9b6d62', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (56, 'd3002f4c7f164e5711aa7baaeec5b36c', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (57, 'c86783b7736ca555b027d836f1036e0f', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (58, '4b847b38187ffa3c60bc71a7985bdcf2', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (59, '84f8df91dc6ac215931a61cf1312d0aa', 'N;');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (60, '36f84cf3a7367ca37d4fa72b24c8e1ba', 's:39:\"/uploads/logo/logo-sm_67d9c67f27b05.png\";');
COMMIT;

-- ----------------------------
-- Table structure for user_vessels
-- ----------------------------
DROP TABLE IF EXISTS `user_vessels`;
CREATE TABLE `user_vessels` (
  `user_id` bigint unsigned NOT NULL,
  `vessel_id` bigint unsigned NOT NULL,
  KEY `user_vessels_ibfk1` (`user_id`) USING BTREE,
  KEY `user_vessels_ibfk2` (`vessel_id`) USING BTREE,
  CONSTRAINT `user_vessels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `user_vessels_ibfk_2` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of user_vessels
-- ----------------------------
BEGIN;
INSERT INTO `user_vessels` (`user_id`, `vessel_id`) VALUES (7, 1);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vessel_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE,
  KEY `user_ibfk1` (`vessel_id`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `image`, `vessel_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'Admin', 'admin@pakarti.com', 'admin', NULL, 1, '2025-09-12 16:59:57', '$2y$12$GboB8GlITCVJZcY5xVqWQeJ/6oLSPYQMavMgzUPoQE7kr/HmProAu', NULL, '2025-09-11 19:42:59', '2025-09-11 19:43:03');
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `image`, `vessel_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (7, 'Rasid', 'rasid242@pakarti.com', '123124', NULL, 1, NULL, '$2y$12$ClgR1V0Bvoj07UvFie6/Bu1kwh.LEiayDaJ6tYoWe1kDtK5fYcBf.', NULL, '2025-10-06 15:26:37', '2025-10-06 15:26:37');
COMMIT;

-- ----------------------------
-- Table structure for vessel_operations
-- ----------------------------
DROP TABLE IF EXISTS `vessel_operations`;
CREATE TABLE `vessel_operations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
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
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `vessel_operations_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`),
  CONSTRAINT `vessel_operations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of vessel_operations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for vessels
-- ----------------------------
DROP TABLE IF EXISTS `vessels`;
CREATE TABLE `vessels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('MOPU','FPU') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `oim_id` bigint unsigned DEFAULT NULL,
  `client_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `client_oim` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_status` (`is_active`) USING BTREE,
  KEY `idx_field_name` (`client_oim`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of vessels
-- ----------------------------
BEGIN;
INSERT INTO `vessels` (`id`, `code`, `name`, `type`, `oim_id`, `client_name`, `client_oim`, `is_active`, `created_at`, `updated_at`) VALUES (1, 'PW-8', 'MOPU PRAMESWARI-8', 'MOPU', 7, 'HCML', 'MAC FIELD', 1, '2025-09-18 12:41:12', '2025-10-06 18:49:08');
INSERT INTO `vessels` (`id`, `code`, `name`, `type`, `oim_id`, `client_name`, `client_oim`, `is_active`, `created_at`, `updated_at`) VALUES (2, '12', '21', 'FPU', NULL, 'wqe', 'wqe', 1, '2025-09-24 08:55:52', '2025-09-24 08:55:52');
COMMIT;

-- ----------------------------
-- Table structure for wells
-- ----------------------------
DROP TABLE IF EXISTS `wells`;
CREATE TABLE `wells` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` bigint unsigned NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('Oil','Gas','Gas Lift','Water Injection','Gas Injection') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `max_oil_rate` decimal(10,2) DEFAULT NULL,
  `max_gas_rate` decimal(10,2) DEFAULT NULL,
  `max_water_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Shut-in','Abandoned','Suspended','Testing','Workover') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `idx_vessel_id` (`vessel_id`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_type` (`type`) USING BTREE,
  CONSTRAINT `wells_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wells
-- ----------------------------
BEGIN;
INSERT INTO `wells` (`id`, `vessel_id`, `code`, `name`, `type`, `max_oil_rate`, `max_gas_rate`, `max_water_rate`, `status`, `created_at`, `updated_at`) VALUES (1, 1, 'A1', 'Well A1', 'Gas', 0.00, 18.00, 0.00, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `wells` (`id`, `vessel_id`, `code`, `name`, `type`, `max_oil_rate`, `max_gas_rate`, `max_water_rate`, `status`, `created_at`, `updated_at`) VALUES (2, 1, 'A2', 'Well A2', 'Gas', 0.00, 17.00, 0.00, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
INSERT INTO `wells` (`id`, `vessel_id`, `code`, `name`, `type`, `max_oil_rate`, `max_gas_rate`, `max_water_rate`, `status`, `created_at`, `updated_at`) VALUES (3, 1, 'A3', 'Well A3', 'Gas', 0.00, 16.00, 0.00, 'Active', '2025-09-18 12:42:47', '2025-09-18 12:42:47');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
