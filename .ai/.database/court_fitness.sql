-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 09:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `court_fitness`
--

-- --------------------------------------------------------

--
-- Table structure for table `coach_player_assignments`
--

CREATE TABLE `coach_player_assignments` (
  `id` int(11) UNSIGNED NOT NULL,
  `coach_user_id` int(11) UNSIGNED NOT NULL,
  `player_user_id` int(11) UNSIGNED NOT NULL,
  `assigned_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coach_player_assignments`
--

INSERT INTO `coach_player_assignments` (`id`, `coach_user_id`, `player_user_id`, `assigned_date`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, '2026-04-01', 1, NULL, '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL),
(2, 1, 3, '2026-04-10', 1, NULL, '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exercise_types`
--

CREATE TABLE `exercise_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort_order` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercise_types`
--

INSERT INTO `exercise_types` (`id`, `name`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cardio', 1, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(2, 'Weights', 2, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(3, 'Agility', 3, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_categories`
--

CREATE TABLE `fitness_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `exercise_type_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `sort_order` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitness_categories`
--

INSERT INTO `fitness_categories` (`id`, `exercise_type_id`, `name`, `slug`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Aerobic Cardio', 'aerobic-cardio', 1, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(2, 1, 'Anaerobic Alactic', 'anaerobic-alactic', 2, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(3, 2, 'Push', 'push', 1, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(4, 2, 'Pull', 'pull', 2, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(5, 2, 'Hinge', 'hinge', 3, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(6, 2, 'Squat', 'squat', 4, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(7, 2, 'Lunge', 'lunge', 5, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(8, 2, 'Carry', 'carry', 6, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(9, 2, 'Accessory', 'accessory', 7, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(10, 2, 'Core', 'core', 8, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(11, 3, 'Speed', 'speed', 1, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(12, 3, 'Agility', 'agility', 2, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_subcategories`
--

CREATE TABLE `fitness_subcategories` (
  `id` int(11) UNSIGNED NOT NULL,
  `fitness_category_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Future: fitness-directory description',
  `sort_order` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitness_subcategories`
--

INSERT INTO `fitness_subcategories` (`id`, `fitness_category_id`, `name`, `slug`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Recovery run', 'Recovery run', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(2, 1, 'Off feet easy bike or row', 'off-feet-easy-bike-or-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(3, 1, 'Incline treadmill walk', 'incline-treadmill-walk', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(4, 1, 'Mobility flow plus nasal breathing jog', 'mobility-flow-plus-nasal-breathing-jog', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(5, 1, 'Aerobic tempo intervals', 'aerobic-tempo-intervals', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(6, 1, 'Threshold cruise intervals', 'threshold-cruise-intervals', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(7, 1, 'Fartlek free run', 'fartlek-free-run', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(8, 1, 'Hill repeats aerobic power', 'hill-repeats-aerobic-power', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(9, 1, 'Pyramid intervals', 'pyramid-intervals', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(10, 1, 'On court rally blocks aerobic pace', 'on-court-rally-blocks-aerobic-pace', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(11, 1, 'VO2max 1 to 1 intervals', 'vo2max-1-to-1-intervals', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(12, 1, 'VO2max 30 30', 'vo2max-30-30', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(13, 1, 'Long court diagonals at controlled pace', 'long-court-diagonals-at-controlled-pace', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(14, 2, 'Strides technique', 'strides-technique', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(15, 2, 'Primer short session', 'primer-short-session', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(16, 2, 'Hill sprints alactic', 'hill-sprints-alactic', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(17, 2, 'Flying 20 accelerations', 'flying-20-accelerations', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(18, 2, 'Five to ten meter start repeats', 'five-to-ten-meter-start-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(19, 2, 'Pro agility 5 10 5', 'pro-agility-5-10-5', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(20, 2, '505 technical reps', '505-technical-reps', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(21, 2, 'Court diagonals moderate pace repeats', 'court-diagonals-moderate-pace-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(22, 2, 'Figure eight cone runs', 'figure-eight-cone-runs', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(23, 2, 'W pattern footwork runs', 'w-pattern-footwork-runs', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(24, 2, 'Sprint intervals 10 on 20 off', 'sprint-intervals-10-on-20-off', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(25, 2, 'Repeat sprint ability 20 m shuttles', 'repeat-sprint-ability-20-m-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(26, 2, 'Pro agility 5 10 5 repeats', 'pro-agility-5-10-5-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(27, 2, '505 shuttle repeats', '505-shuttle-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(28, 2, 'T pattern shuttles', 't-pattern-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(29, 2, 'Zig zag shuttles', 'zig-zag-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(30, 2, 'Suicides court lines', 'suicides-court-lines', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(31, 2, 'Short box service area shuttles', 'short-box-service-area-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(32, 3, 'Bench Press', 'bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(33, 3, 'Chest Cable Fly', 'chest-cable-fly', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(34, 3, 'Decline Bench Press', 'decline-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(35, 3, 'Decline Dumbbell Bench Press', 'decline-dumbbell-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(36, 3, 'Decline Plat Loaded Chest Press', 'decline-plat-loaded-chest-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(37, 3, 'Dumbbell Bench Press', 'dumbbell-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(38, 3, 'Dumbbell Press', 'dumbbell-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(39, 3, 'Dumbbell Push Press', 'dumbbell-push-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(40, 3, 'Incline Bench Press', 'incline-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(41, 3, 'Incline Dumbbell Bench Press', 'incline-dumbbell-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(42, 3, 'Incline Plate Loaded Press', 'incline-plate-loaded-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(43, 3, 'Pin Loaded Chest Fly', 'pin-loaded-chest-fly', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(44, 3, 'Pin Loaded Chest Press', 'pin-loaded-chest-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(45, 3, 'Plate Loaded Chest Press', 'plate-loaded-chest-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(46, 3, 'Push Press', 'push-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(47, 3, 'Seated Dumbbell Press', 'seated-dumbbell-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(48, 3, 'Seated Press', 'seated-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(49, 3, 'Smith Machine Bench Press', 'smith-machine-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(50, 3, 'Smith Machine Press', 'smith-machine-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(51, 3, 'Single Arm Dumbbell Press', 'single-arm-dumbbell-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(52, 3, 'Single Arm Dumbbell Push Press', 'single-arm-dumbbell-push-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(53, 3, 'Single Arm Dumbbell Bench Press', 'single-arm-dumbbell-bench-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(54, 4, 'Assisted Pull Up', 'assisted-pull-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(55, 4, 'Barbell Row', 'barbell-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(56, 4, 'Bent Over Dumbbell Row', 'bent-over-dumbbell-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(57, 4, 'Chest Supported Barbell Row', 'chest-supported-barbell-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(58, 4, 'Chest Supported Dumbbell Row', 'chest-supported-dumbbell-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(59, 4, 'Chest Supported T-Bar Row', 'chest-supported-t-bar-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(60, 4, 'Chin Up', 'chin-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(61, 4, 'Dumbbell Row', 'dumbbell-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(62, 4, 'Half Kneeling Cable Row', 'half-kneeling-cable-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(63, 4, 'Half Kneeling Lat Pulldown', 'half-kneeling-lat-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(64, 4, 'Landmine Row', 'landmine-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(65, 4, 'Lat Pulldown', 'lat-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(66, 4, 'Neutral Grip Pulldown', 'neutral-grip-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(67, 4, 'Pendlay Row', 'pendlay-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(68, 4, 'Pin Loaded Lat Pulldown', 'pin-loaded-lat-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(69, 4, 'Pin Loaded Row', 'pin-loaded-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(70, 4, 'Plate Loaded Lat Pulldown', 'plate-loaded-lat-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(71, 4, 'Plate Loaded Row', 'plate-loaded-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(72, 4, 'Pull Up', 'pull-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(73, 4, 'Seated Neutral Grip Cable Row', 'seated-neutral-grip-cable-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(74, 4, 'Single Arm Cable Row', 'single-arm-cable-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(75, 4, 'Single Arm Plate Loaded Row', 'single-arm-plate-loaded-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(76, 4, 'Straight Arm Pulldown', 'straight-arm-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(77, 4, 'Supinated Pulldown', 'supinated-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(78, 4, 'T-Bar Row', 't-bar-row', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(79, 4, 'Wide Neutral Grip Pulldown', 'wide-neutral-grip-pulldown', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(80, 5, 'Barbell Hip Thrust', 'barbell-hip-thrust', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(81, 5, 'Barbell Romanian Deadlift', 'barbell-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(82, 5, 'Barbell Stiff Leg Deadlift', 'barbell-stiff-leg-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(83, 5, 'Deadlift', 'deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(84, 5, 'Dual Dumbbell Deadlift', 'dual-dumbbell-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(85, 5, 'Dual Kettelbell Deadlift', 'dual-kettelbell-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(86, 5, 'Dumbbell Romanian Deadlift', 'dumbbell-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(87, 5, 'Glute Bridge Leg Curl', 'glute-bridge-leg-curl', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(88, 5, 'Hip Hinge', 'hip-hinge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(89, 5, 'Hip Hinge Single Leg', 'hip-hinge-single-leg', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(90, 5, 'Kettlebell Romanian Deadlift', 'kettlebell-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(91, 5, 'Kettlebell Swing', 'kettlebell-swing', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(92, 5, 'Single Dumbbell Deadlift', 'single-dumbbell-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(93, 5, 'Single Kettlebell Deadlift', 'single-kettlebell-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(94, 5, 'Single Leg Barbell Hip Thrust', 'single-leg-barbell-hip-thrust', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(95, 5, 'Single Leg Barbell Romanian Deadlift', 'single-leg-barbell-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(96, 5, 'Single Leg Dumbbell Romanian Deadlift', 'single-leg-dumbbell-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(97, 5, 'Trap Bar Deadlift', 'trap-bar-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(98, 5, 'Trap Bar Romanian Deadlift', 'trap-bar-romanian-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(99, 5, 'Snatch Grip Deadlift', 'snatch-grip-deadlift', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(100, 6, 'Back Squat', 'back-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(101, 6, 'Front Squat', 'front-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(102, 6, 'Goblet Squat', 'goblet-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(103, 6, 'Hack Squat', 'hack-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(104, 6, 'Leg Extension', 'leg-extension', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(105, 6, 'Pin Loaded Leg Press', 'pin-loaded-leg-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(106, 6, 'Plate Loated Leg Press', 'plate-loated-leg-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(107, 6, 'Power Squat Machine', 'power-squat-machine', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(108, 6, 'Safety Bar Squat', 'safety-bar-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(109, 6, 'Single Leg Extension', 'single-leg-extension', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(110, 6, 'Sissy Squat', 'sissy-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(111, 6, 'Smith Machine Hack Squat', 'smith-machine-hack-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(112, 6, 'Smith Machine Squat', 'smith-machine-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(113, 6, 'Zercher Squat', 'zercher-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(114, 7, 'Barbell Reverse Lunge', 'barbell-reverse-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(115, 7, 'Barbell Split Squat', 'barbell-split-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(116, 7, 'Barbell Step Up', 'barbell-step-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(117, 7, 'Barbell Walking Lunge', 'barbell-walking-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(118, 7, 'Dumbbell Reverse Lunge', 'dumbbell-reverse-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(119, 7, 'Dumbbell Split Squat', 'dumbbell-split-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(120, 7, 'Dumbbell Step Up', 'dumbbell-step-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(121, 7, 'Dumbbell Walking Lunge', 'dumbbell-walking-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(122, 7, 'Lunge', 'lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(123, 7, 'Reverse Lunge', 'reverse-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(124, 7, 'Single Leg Pin Loaded Press', 'single-leg-pin-loaded-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(125, 7, 'Single Leg Plate Loaded Press', 'single-leg-plate-loaded-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(126, 7, 'Split Squat', 'split-squat', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(127, 7, 'Walking Lunge', 'walking-lunge', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(128, 8, 'Carry (Open)', 'carry-open', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(129, 8, 'Farmers Walk', 'farmers-walk', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(130, 8, 'Front Load Carry', 'front-load-carry', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(131, 8, 'Front Rack Carry', 'front-rack-carry', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(132, 8, 'Overhead Carry', 'overhead-carry', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(133, 8, 'Single Dumbbell March', 'single-dumbbell-march', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(134, 8, 'Single Leg-Single Side Farmers Walk', 'single-leg-single-side-farmers-walk', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(135, 8, 'Sled Pull', 'sled-pull', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(136, 8, 'Sled Push', 'sled-push', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(137, 8, 'Sled Rear Tow', 'sled-rear-tow', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(138, 8, 'Sled Side Shuffle', 'sled-side-shuffle', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(139, 9, 'Accessory (Open)', 'accessory-open', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(140, 9, 'Conditioning - Cardiac Output', 'conditioning-cardiac-output', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(141, 9, 'Conditioning - Cyclical', 'conditioning-cyclical', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(142, 9, 'Conditioning - HIIT Protocol', 'conditioning-hiit-protocol', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(143, 9, 'Conditioning - Strongman Endurance', 'conditioning-strongman-endurance', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(144, 9, 'Conditioning - Tempo Protocol', 'conditioning-tempo-protocol', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(145, 9, 'Lower - Calf Isolation', 'lower-calf-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(146, 9, 'Lower - Corrective', 'lower-corrective', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(147, 9, 'Lower - Glute Isolation', 'lower-glute-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(148, 9, 'Lower - Hamstring Isolation', 'lower-hamstring-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(149, 9, 'Lower - Mobility', 'lower-mobility', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(150, 9, 'Lower - Quadriceps Isolation', 'lower-quadriceps-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(151, 9, 'Lower - Stability', 'lower-stability', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(152, 9, 'Upper - Biceps', 'upper-biceps', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(153, 9, 'Upper - Chest Isolation', 'upper-chest-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(154, 9, 'Upper - Corrective', 'upper-corrective', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(155, 9, 'Upper - Forearms', 'upper-forearms', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(156, 9, 'Upper - Lat Isolation', 'upper-lat-isolation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(157, 9, 'Upper - Mobility', 'upper-mobility', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(158, 9, 'Upper - Rear Deltoids', 'upper-rear-deltoids', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(159, 9, 'Upper - Rhomboids', 'upper-rhomboids', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(160, 9, 'Upper - Side Deltoids', 'upper-side-deltoids', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(161, 9, 'Upper - Stability', 'upper-stability', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(162, 9, 'Upper - Trapezius', 'upper-trapezius', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(163, 9, 'Upper - Triceps', 'upper-triceps', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(164, 10, 'Core (Open)', 'core-open', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(165, 10, 'Anti-Rotation', 'anti-rotation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(166, 10, 'Cable Pallot Press & Rotation', 'cable-pallot-press-and-rotation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(167, 10, 'Cable Woodchop', 'cable-woodchop', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(168, 10, 'GHD Sit Up', 'ghd-sit-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(169, 10, 'Half Kneeling Medicine Ball Throw', 'half-kneeling-medicine-ball-throw', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(170, 10, 'Hanging Knee Raise', 'hanging-knee-raise', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(171, 10, 'Hanging Leg Raise', 'hanging-leg-raise', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(172, 10, 'Hollow Body', 'hollow-body', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(173, 10, 'Hollow Body Rock', 'hollow-body-rock', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(174, 10, 'Isometric', 'isometric', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(175, 10, 'Kneeling Medicine Ball Press', 'kneeling-medicine-ball-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(176, 10, 'Pallot Press', 'pallot-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(177, 10, 'Plank', 'plank', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(178, 10, 'Rotation', 'rotation', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(179, 10, 'Sprinter Sit-Up', 'sprinter-sit-up', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(180, 10, 'Stability', 'stability', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(181, 10, 'Standing Medicial Ball Press', 'standing-medicial-ball-press', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(182, 11, 'Five meter acceleration starts', 'five-meter-acceleration-starts', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(183, 11, 'Ten meter acceleration starts', 'ten-meter-acceleration-starts', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(184, 11, 'Flying ten accelerations', 'flying-ten-accelerations', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(185, 11, 'Flying twenty accelerations', 'flying-twenty-accelerations', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(186, 11, 'Max velocity strides thirty meters', 'max-velocity-strides-thirty-meters', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(187, 11, 'Resisted sled sprints light', 'resisted-sled-sprints-light', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(188, 11, 'Assisted band accelerations', 'assisted-band-accelerations', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(189, 11, 'First step reaction sprints', 'first-step-reaction-sprints', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(190, 11, 'Hill sprints short', 'hill-sprints-short', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(218, 12, 'Five zero five technical reps', 'five-zero-five-technical-reps', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(219, 12, 'Five zero five shuttle repeats', 'five-zero-five-shuttle-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(220, 12, 'Pro agility 5 10 5', 'pro-agility-5-10-5', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(221, 12, 'T pattern shuttles', 't-pattern-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(222, 12, 'Zig zag shuttles', 'zig-zag-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(223, 12, 'Figure eight cone runs', 'figure-eight-cone-runs', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(224, 12, 'W pattern footwork runs', 'w-pattern-footwork-runs', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(225, 12, 'Court diagonals', 'court-diagonals', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(226, 12, 'Lateral shuffle repeats', 'lateral-shuffle-repeats', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(227, 12, 'Split step and first step reaction', 'split-step-and-first-step-reaction', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(228, 12, 'Random cone reactive cuts', 'random-cone-reactive-cuts', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(229, 12, 'Mirror drill partner', 'mirror-drill-partner', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(230, 12, 'Short box service area shuttles', 'short-box-service-area-shuttles', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(231, 12, 'CODAT zig zag course', 'codat-zig-zag-course', NULL, 0, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(4, '2026-04-23-130000', 'App\\Database\\Migrations\\CreateUsersAndAssignmentsTables', 'default', 'App', 1776927088, 1),
(5, '2026-04-23-130100', 'App\\Database\\Migrations\\CreateExerciseCatalogTables', 'default', 'App', 1776927088, 1),
(6, '2026-04-23-130200', 'App\\Database\\Migrations\\CreatePlanTables', 'default', 'App', 1776927088, 1);

-- --------------------------------------------------------

--
-- Table structure for table `plan_entries`
--

CREATE TABLE `plan_entries` (
  `id` int(11) UNSIGNED NOT NULL,
  `training_plan_id` int(11) UNSIGNED NOT NULL,
  `training_date` date NOT NULL,
  `session_period` varchar(10) NOT NULL COMMENT 'morning | afternoon | evening',
  `exercise_type_id` int(11) UNSIGNED NOT NULL,
  `fitness_category_id` int(11) UNSIGNED NOT NULL,
  `fitness_subcategory_id` int(11) UNSIGNED NOT NULL,
  `sort_order` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `target_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Coach-prescribed targets (reps, weight, duration, …)' CHECK (json_valid(`target_json`)),
  `actual_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Recorded actuals (reps, weight, …)' CHECK (json_valid(`actual_json`)),
  `actual_by_user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Who filled in actuals — coach or player',
  `actual_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan_entries`
--

INSERT INTO `plan_entries` (`id`, `training_plan_id`, `training_date`, `session_period`, `exercise_type_id`, `fitness_category_id`, `fitness_subcategory_id`, `sort_order`, `target_json`, `actual_json`, `actual_by_user_id`, `actual_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-04-27', 'morning', 1, 1, 1, 1, '{\"duration_min\":30,\"max_hr_pct\":70}', NULL, NULL, NULL, '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL),
(2, 1, '2026-04-27', 'morning', 1, 2, 19, 2, '{\"reps\":6,\"rest_sec\":90}', NULL, NULL, NULL, '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL),
(3, 1, '2026-04-29', 'evening', 2, 3, 32, 1, '{\"sets\":4,\"reps\":8,\"weight_kg\":60,\"rest_sec\":120}', NULL, NULL, NULL, '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_plans`
--

CREATE TABLE `training_plans` (
  `id` int(11) UNSIGNED NOT NULL,
  `coach_user_id` int(11) UNSIGNED NOT NULL,
  `player_user_id` int(11) UNSIGNED NOT NULL,
  `week_of` date NOT NULL COMMENT 'Must be a Monday — enforced at model layer',
  `training_target` varchar(100) NOT NULL COMMENT 'Picked suggestion or coach custom text (max 100 chars)',
  `weight_unit` varchar(3) NOT NULL DEFAULT 'kg' COMMENT 'kg | lb',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_plans`
--

INSERT INTO `training_plans` (`id`, `coach_user_id`, `player_user_id`, `week_of`, `training_target`, `weight_unit`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, '2026-04-27', 'Endurance', 'kg', 'Light week — recovery focus after ITF Futures.', '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_targets`
--

CREATE TABLE `training_targets` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort_order` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_targets`
--

INSERT INTO `training_targets` (`id`, `name`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Endurance', 1, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(2, 'Strength', 2, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(3, 'Power', 3, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(4, 'Speed', 4, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(5, 'Agility', 5, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(6, 'Recovery', 6, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29'),
(7, 'Mixed', 7, 1, '2026-04-23 06:51:29', '2026-04-23 06:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `hitcourt_user_id` int(11) UNSIGNED NOT NULL COMMENT 'Stable identity from HitCourt',
  `email` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `family_name` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL COMMENT 'coach | player | admin | ... (from HitCourt JWT)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `hitcourt_user_id`, `email`, `first_name`, `family_name`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1001, 'rajat.coach@hitcourt.example', 'Rajat', 'Kapoor', 'coach', '2026-04-23 06:51:29', '2026-04-23 07:04:41', NULL),
(2, 2001, 'rohan.player@hitcourt.example', 'Rohan', 'Mehta', 'player', '2026-04-23 06:51:29', '2026-04-23 07:04:32', NULL),
(3, 2002, 'priya.player@hitcourt.example', 'Priya', 'Sharma', 'player', '2026-04-23 06:51:29', '2026-04-23 06:51:29', NULL),
(4, 9001, 'admin.user@hitcourt.example', 'Admin', 'User', 'admin', '2026-04-23 06:55:57', '2026-04-23 07:05:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coach_player_assignments`
--
ALTER TABLE `coach_player_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coach_user_id_player_user_id` (`coach_user_id`,`player_user_id`),
  ADD KEY `player_user_id` (`player_user_id`);

--
-- Indexes for table `exercise_types`
--
ALTER TABLE `exercise_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `fitness_categories`
--
ALTER TABLE `fitness_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `exercise_type_id` (`exercise_type_id`);

--
-- Indexes for table `fitness_subcategories`
--
ALTER TABLE `fitness_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fitness_category_id_slug` (`fitness_category_id`,`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan_entries`
--
ALTER TABLE `plan_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_entries_exercise_type_id_foreign` (`exercise_type_id`),
  ADD KEY `plan_entries_fitness_category_id_foreign` (`fitness_category_id`),
  ADD KEY `plan_entries_fitness_subcategory_id_foreign` (`fitness_subcategory_id`),
  ADD KEY `plan_entries_actual_by_user_id_foreign` (`actual_by_user_id`),
  ADD KEY `training_plan_id_training_date_session_period_sort_order` (`training_plan_id`,`training_date`,`session_period`,`sort_order`);

--
-- Indexes for table `training_plans`
--
ALTER TABLE `training_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_user_id_week_of` (`player_user_id`,`week_of`),
  ADD KEY `coach_user_id_week_of` (`coach_user_id`,`week_of`);

--
-- Indexes for table `training_targets`
--
ALTER TABLE `training_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hitcourt_user_id` (`hitcourt_user_id`),
  ADD KEY `email` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coach_player_assignments`
--
ALTER TABLE `coach_player_assignments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exercise_types`
--
ALTER TABLE `exercise_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fitness_categories`
--
ALTER TABLE `fitness_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fitness_subcategories`
--
ALTER TABLE `fitness_subcategories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `plan_entries`
--
ALTER TABLE `plan_entries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_plans`
--
ALTER TABLE `training_plans`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training_targets`
--
ALTER TABLE `training_targets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `coach_player_assignments`
--
ALTER TABLE `coach_player_assignments`
  ADD CONSTRAINT `coach_player_assignments_coach_user_id_foreign` FOREIGN KEY (`coach_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coach_player_assignments_player_user_id_foreign` FOREIGN KEY (`player_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fitness_categories`
--
ALTER TABLE `fitness_categories`
  ADD CONSTRAINT `fitness_categories_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fitness_subcategories`
--
ALTER TABLE `fitness_subcategories`
  ADD CONSTRAINT `fitness_subcategories_fitness_category_id_foreign` FOREIGN KEY (`fitness_category_id`) REFERENCES `fitness_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_entries`
--
ALTER TABLE `plan_entries`
  ADD CONSTRAINT `plan_entries_actual_by_user_id_foreign` FOREIGN KEY (`actual_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `plan_entries_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plan_entries_fitness_category_id_foreign` FOREIGN KEY (`fitness_category_id`) REFERENCES `fitness_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plan_entries_fitness_subcategory_id_foreign` FOREIGN KEY (`fitness_subcategory_id`) REFERENCES `fitness_subcategories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plan_entries_training_plan_id_foreign` FOREIGN KEY (`training_plan_id`) REFERENCES `training_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_plans`
--
ALTER TABLE `training_plans`
  ADD CONSTRAINT `training_plans_coach_user_id_foreign` FOREIGN KEY (`coach_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_plans_player_user_id_foreign` FOREIGN KEY (`player_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
