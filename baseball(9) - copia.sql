-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2025 a las 20:36:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `baseball`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abonos`
--

CREATE TABLE `abonos` (
  `id_abn` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `ncantidad` int(100) NOT NULL,
  `prize_four` varchar(1) DEFAULT NULL,
  `cant_four` int(100) DEFAULT NULL,
  `prize_once` int(1) DEFAULT NULL,
  `cant_once` int(100) DEFAULT NULL,
  `prize_second` int(1) DEFAULT NULL,
  `cant_second` int(100) DEFAULT NULL,
  `prize_third` int(1) DEFAULT NULL,
  `cant_third` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `abonos`
--

INSERT INTO `abonos` (`id_abn`, `id_temp`, `categoria`, `ncantidad`, `prize_four`, `cant_four`, `prize_once`, `cant_once`, `prize_second`, `cant_second`, `prize_third`, `cant_third`) VALUES
(1, 3, 'B', 6, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 14, 'C', 6, '0', 0, 1, 350, 1, 250, 1, 100),
(3, 15, 'D', 7, '0', 0, 1, 300, 1, 200, 0, 0),
(4, 17, 'B', 6, '0', 0, 1, 350, 1, 250, 1, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendario`
--

CREATE TABLE `calendario` (
  `id_cal` int(100) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `id_temporada` int(100) NOT NULL,
  `id_team_one` int(100) NOT NULL,
  `name_team_one` varchar(60) NOT NULL,
  `id_team_two` int(100) NOT NULL,
  `name_team_two` varchar(60) NOT NULL,
  `dia` varchar(11) NOT NULL,
  `id_hora` int(100) NOT NULL,
  `hora` varchar(10) NOT NULL,
  `campo` int(1) NOT NULL,
  `partida` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calendario`
--

INSERT INTO `calendario` (`id_cal`, `fecha`, `categoria`, `id_temporada`, `id_team_one`, `name_team_one`, `id_team_two`, `name_team_two`, `dia`, `id_hora`, `hora`, `campo`, `partida`) VALUES
(45, '2025-06-21', 'd', 15, 21, 'LA MAQUINA', 20, 'CENTAUROS', 'Sábado', 2, '08:30 AM', 1, 7),
(46, '2025-06-21', 'c', 14, 17, 'CENTAUROS', 14, 'CPV', 'Sábado', 6, '10:30 AM', 1, 5),
(47, '2025-06-21', 'c', 14, 12, 'RENEGADOS', 16, 'EL COMBO', 'Sábado', 10, '12:30 PM', 1, 5),
(48, '2025-06-21', 'B', 3, 5, 'ASTROS', 4, 'LOS PANAS', 'Sábado', 15, '03:00 PM', 1, 9),
(49, '2025-06-21', 'd', 15, 19, 'WILLRIT', 22, 'PIÑONAL', 'Sábado', 2, '08:30 AM', 2, 6),
(55, '2025-06-28', 'b', 3, 6, 'HIDRO-RIEGO', 8, 'GREMIO', 'Sábado', 16, '03:30 PM', 1, 7),
(218, '2025-11-22', 'b', 18, 5, 'ASTROS', 7, 'LA FAMILIA', 'Sábado', 14, '02:30 PM', 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_cat` int(10) NOT NULL,
  `categoria` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_cat`, `categoria`) VALUES
(1, 'B'),
(2, 'C'),
(3, 'D');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_team` int(10) NOT NULL,
  `nom_team` varchar(50) NOT NULL,
  `n_jugadores` int(100) DEFAULT NULL,
  `categoria` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id_team`, `nom_team`, `n_jugadores`, `categoria`) VALUES
(4, 'LOS PANAS', 24, 'B'),
(5, 'ASTROS', 26, 'B'),
(7, 'LA FAMILIA', 25, 'B'),
(8, 'GREMIO', 26, 'B'),
(12, 'RENEGADOS', 26, 'C'),
(13, 'VETERINARIOS', 21, 'C'),
(14, 'CPV', 22, 'C'),
(15, 'PIÑONAL', 24, 'C'),
(16, 'EL COMBO', 21, 'C'),
(17, 'CENTAUROS', 24, 'C'),
(18, 'LA MAQUINA', 20, 'C'),
(19, 'WILLRIT', 21, 'D'),
(20, 'CENTAUROS', 23, 'D'),
(21, 'LA MAQUINA', 20, 'D'),
(22, 'PIÑONAL', 23, 'D'),
(23, 'LOS COMPADRES', 20, 'D'),
(25, 'DREAM TEAM', 24, 'B'),
(26, 'HIDRORIEGO', 19, 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_estados`
--

CREATE TABLE `equipo_estados` (
  `id` int(11) NOT NULL,
  `id_tab` int(11) NOT NULL,
  `id_temp` int(11) NOT NULL,
  `estado` enum('C','E','G','R') NOT NULL COMMENT 'C=Clasificado, E=Eliminado, G=Ganador, R=Retirado',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipo_estados`
--

INSERT INTO `equipo_estados` (`id`, `id_tab`, `id_temp`, `estado`, `fecha_registro`) VALUES
(1, 90, 17, 'C', '2025-08-29 21:17:00'),
(2, 94, 17, 'E', '2025-08-29 21:17:44'),
(3, 95, 17, 'E', '2025-08-29 21:17:44'),
(4, 77, 14, 'C', '2025-08-29 21:19:31'),
(5, 72, 14, 'C', '2025-08-29 21:19:31'),
(18, 76, 14, 'C', '2025-09-07 14:01:52'),
(19, 75, 14, 'E', '2025-09-07 14:02:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_retiros`
--

CREATE TABLE `equipo_retiros` (
  `id` int(11) NOT NULL,
  `id_equipo_retirado` int(11) NOT NULL,
  `id_temp` int(11) NOT NULL,
  `puntos_jj` int(11) NOT NULL DEFAULT 1 COMMENT 'Puntos a sumar en JJ por equipo activo',
  `puntos_jg` int(11) NOT NULL DEFAULT 1 COMMENT 'Puntos a sumar en JG por equipo activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `homenaje`
--

CREATE TABLE `homenaje` (
  `id_hnr` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `honor` text DEFAULT NULL,
  `prize_four` int(1) DEFAULT NULL,
  `cant_four` int(100) DEFAULT NULL,
  `prize_once` int(1) DEFAULT NULL,
  `cant_once` int(100) DEFAULT NULL,
  `prize_second` int(1) DEFAULT NULL,
  `cant_second` int(100) DEFAULT NULL,
  `prize_third` int(1) DEFAULT NULL,
  `cant_third` int(100) DEFAULT NULL,
  `prize_pg` int(1) DEFAULT NULL,
  `cant_pg` int(100) DEFAULT NULL,
  `prize_pe` int(1) DEFAULT NULL,
  `cant_pe` int(100) DEFAULT NULL,
  `prize_lbt` int(1) DEFAULT NULL,
  `cant_lbt` int(100) DEFAULT NULL,
  `prize_lj` int(1) DEFAULT NULL,
  `cant_lj` int(100) DEFAULT NULL,
  `prize_ld` int(1) DEFAULT NULL,
  `cant_ld` int(100) DEFAULT NULL,
  `prize_lt` int(1) DEFAULT NULL,
  `cant_lt` int(100) DEFAULT NULL,
  `prize_lca` int(1) DEFAULT NULL,
  `cant_lca` int(100) DEFAULT NULL,
  `prize_lce` int(1) DEFAULT NULL,
  `cant_lce` int(100) DEFAULT NULL,
  `prize_lp` int(1) DEFAULT NULL,
  `cant_lp` int(100) DEFAULT NULL,
  `prize_lb` int(1) DEFAULT NULL,
  `cant_lb` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `homenaje`
--

INSERT INTO `homenaje` (`id_hnr`, `id_temp`, `categoria`, `honor`, `prize_four`, `cant_four`, `prize_once`, `cant_once`, `prize_second`, `cant_second`, `prize_third`, `cant_third`, `prize_pg`, `cant_pg`, `prize_pe`, `cant_pe`, `prize_lbt`, `cant_lbt`, `prize_lj`, `cant_lj`, `prize_ld`, `cant_ld`, `prize_lt`, `cant_lt`, `prize_lca`, `cant_lca`, `prize_lce`, `cant_lce`, `prize_lp`, `cant_lp`, `prize_lb`, `cant_lb`) VALUES
(1, 3, 'B', 'ARGENIS TOVAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 11, 'C', 'JOSE ECHEZURIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 12, 'D', 'JOSE RENE RIOS-JAIRO RAMIREZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 20, 'D', 'JOSE FELIPE ARAUJO', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(8, 19, 'C', 'RODY CADAGAN', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(9, 18, 'B', 'ORLANDO CASTILLO', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juegos`
--

CREATE TABLE `juegos` (
  `id_juego` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `nj` int(100) DEFAULT NULL,
  `jj` int(100) DEFAULT NULL,
  `team_one` int(100) DEFAULT NULL,
  `ca` int(100) DEFAULT NULL,
  `team_two` int(100) DEFAULT NULL,
  `ce` int(100) DEFAULT NULL,
  `estado` varchar(40) NOT NULL,
  `valido` int(1) DEFAULT NULL,
  `fech_part` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `juegos`
--

INSERT INTO `juegos` (`id_juego`, `id_tab`, `id_temp`, `nj`, `jj`, `team_one`, `ca`, `team_two`, `ce`, `estado`, `valido`, `fech_part`) VALUES
(1, 6, 3, 1, 1, 4, 2, 5, 0, 'Ganando', 1, '2025-05-31'),
(3, 12, 3, 1, 1, 10, 6, 8, 3, 'Ganando', 1, '2025-05-31'),
(5, 10, 3, 1, 1, 8, 3, 10, 6, 'Perdido', 1, '2025-05-31'),
(7, 9, 3, 1, 1, 7, 9, 6, 1, 'Ganando', 1, '2025-05-31'),
(416, 108, 19, 5, 5, 12, 2, 14, 6, 'Perdido', 1, '2025-11-08'),
(417, 103, 19, 5, 5, 14, 6, 12, 2, 'Ganando', 1, '2025-11-08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id_player` int(100) NOT NULL,
  `id_team` int(10) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `edad` int(100) DEFAULT NULL,
  `lanzador` int(1) DEFAULT NULL,
  `categoria` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id_player`, `id_team`, `cedula`, `nombre`, `apellido`, `fecha`, `edad`, `lanzador`, `categoria`) VALUES
(1, 2, '4305940', 'alexis', 'Gonzales', NULL, NULL, 0, 'B'),
(2, 2, '26369213', 'Arturo', 'Vielma', NULL, NULL, 1, 'B'),
(3, 1, '23452524', 'Jose', 'Perez', NULL, NULL, 0, 'B'),
(631, 13, '7.225.117', 'RICHARD', 'PADILLA', '1964-06-15', 61, 0, 'C'),
(632, 12, '7.249.423', 'EDGAR', 'DIAZ', '1965-05-08', 60, 1, 'C'),
(633, 12, '9.652.000', 'PEDRO', 'GARCIA', '1968-03-06', 57, 1, 'C'),
(634, 12, '7.226.575', 'JORGE', 'MORALES', '1963-05-15', 62, 1, 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores_lanz`
--

CREATE TABLE `jugadores_lanz` (
  `id_lanz` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `id_player` int(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `name_lanz` varchar(100) NOT NULL,
  `nj` int(100) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `jl` int(100) DEFAULT NULL,
  `jg` int(100) DEFAULT NULL,
  `il` decimal(10,2) DEFAULT NULL,
  `cp` int(100) DEFAULT NULL,
  `cpl` int(100) DEFAULT NULL,
  `h` int(100) DEFAULT NULL,
  `2b` int(100) DEFAULT NULL,
  `3b` int(100) DEFAULT NULL,
  `hr` int(100) DEFAULT NULL,
  `b` int(100) DEFAULT NULL,
  `k` int(100) DEFAULT NULL,
  `va` int(100) DEFAULT NULL,
  `gp` int(100) DEFAULT NULL,
  `ile` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores_lanz`
--

INSERT INTO `jugadores_lanz` (`id_lanz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_lanz`, `nj`, `categoria`, `jl`, `jg`, `il`, `cp`, `cpl`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(7, 71, 14, 12, 190, '3.291.020', 'PEDRO ECHENIQUE', 1, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(8, 71, 14, 12, 193, '8.728.615', 'VICENTE COLMENARES', 1, 'C', 0, 0, 2.00, 0, 0, 0, 0, 0, 0, 0, 0, 6, NULL, NULL),
(9, 71, 14, 12, 197, '4.399.505', 'GENARO RIOS', 1, 'C', 1, 1, 3.00, 1, 0, 4, 0, 0, 0, 0, 0, 14, NULL, NULL),
(10, 71, 14, 12, 207, '7.227.962', 'CARLOS MENDOZA', 1, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(1090, 73, 14, 14, 245, '8.735.553', 'NELSON CONTRERAS', 9, 'C', 0, 0, 2.00, 0, 0, 0, 0, 0, 0, 0, 2, 6, NULL, NULL);
INSERT INTO `jugadores_lanz` (`id_lanz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_lanz`, `nj`, `categoria`, `jl`, `jg`, `il`, `cp`, `cpl`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(1091, 73, 14, 14, 253, '9.657.589', 'MIGUEL CHAVEZ', 9, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(1102, 74, 14, 15, 255, '9.594.803', 'WILLIANS CRUZ', 9, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(1103, 74, 14, 15, 263, '9.656.542', 'JULIO PAEZ', 9, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(1104, 74, 14, 15, 264, '3.712.186', 'JOSE ARAUJO', 9, 'C', 0, 0, 2.00, 1, 1, 1, 1, 0, 0, 0, 3, 9, NULL, NULL),
(1105, 74, 14, 15, 268, '7.257.990', 'JOSE BALDERRAMA', 9, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
(1702, 90, 17, 4, 118, '13.780.614', 'FREDERIN GUEVARA', 10, 'B', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL);
INSERT INTO `jugadores_lanz` (`id_lanz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_lanz`, `nj`, `categoria`, `jl`, `jg`, `il`, `cp`, `cpl`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(1703, 72, 14, 13, 215, '8.744.444', 'EVELIO HERNANDEZ', 10, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(1704, 72, 14, 13, 218, '9.652.000', 'PEDRO GARCIA', 10, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(1705, 72, 14, 13, 222, '9.647.436', 'JOSE NORIEGA', 10, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2183, 72, 14, 13, 228, '7.214.079', 'PASTOR GOYO', 19, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_lanz` (`id_lanz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_lanz`, `nj`, `categoria`, `jl`, `jg`, `il`, `cp`, `cpl`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(2184, 72, 14, 13, 481, '5.264.603', 'SIMON PINTO', 19, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2185, 90, 17, 4, 112, '9.439.237', 'JOSE ROJAS', 19, 'B', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2673, 115, 18, 25, 547, '14.492.390', 'CARLOS ROJAS', 5, 'B', 0, 0, 3.00, 1, 1, 4, 0, 0, 0, 1, 0, 14, 0, 0);
INSERT INTO `jugadores_lanz` (`id_lanz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_lanz`, `nj`, `categoria`, `jl`, `jg`, `il`, `cp`, `cpl`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(2674, 115, 18, 25, 553, '15.472.530', 'JOSE PANTOJAS', 5, 'B', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2675, 98, 18, 8, 61, '8.677.611', 'HECTOR PEREZ', 5, 'B', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2676, 98, 18, 8, 63, '6.303.697', 'JOSE ROJAS', 5, 'B', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2743, 108, 19, 12, 517, '3.758.714', 'DANIEL GALLARDO', 4, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2744, 108, 19, 12, 626, '9.652.000', 'PEDRO GARCIA', 4, 'C', 1, 1, 4.00, 0, 0, 4, 0, 0, 0, 0, 3, 17, 0, 0),
(2745, 108, 19, 12, 632, '7.249.423', 'EDGAR DIAZ', 4, 'C', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores_stats`
--

CREATE TABLE `jugadores_stats` (
  `id_js` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `id_player` int(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `name_jugador` varchar(60) NOT NULL,
  `nj` int(100) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `vb` int(100) DEFAULT NULL,
  `h` int(100) DEFAULT NULL,
  `hr` int(100) DEFAULT NULL,
  `2b` int(100) DEFAULT NULL,
  `3b` int(100) DEFAULT NULL,
  `ca` int(100) DEFAULT NULL,
  `ci` int(100) DEFAULT NULL,
  `k` int(100) DEFAULT NULL,
  `b` int(100) DEFAULT NULL,
  `a` int(100) DEFAULT NULL,
  `sf` int(100) DEFAULT NULL,
  `br` int(100) DEFAULT NULL,
  `gp` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores_stats`
--

INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(27, 71, 14, 12, 186, '4.305.940', 'ALEXIS GONZALEZ', 1, 'C', 1, 1, 0, 0, 0, 2, 0, 0, 1, 1, NULL, NULL, NULL),
(28, 71, 14, 12, 187, '9.645.519', 'EMILIO CONDE', 1, 'C', 2, 0, 0, 0, 0, 0, 0, 0, 1, 1, NULL, NULL, NULL),
(29, 71, 14, 12, 188, '6.315.372', 'RUSMEL CAMARIPANO', 1, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(585, 72, 14, 13, 230, '9.826.248', 'JOSE PEREZ', 6, 'C', 2, 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(586, 72, 14, 13, 231, '6.068.831', 'ROMULO RUIZ', 6, 'C', 4, 2, 1, 0, 0, 1, 5, 1, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(587, 72, 14, 13, 232, '9.651.236', 'LUIS CARRILLO', 6, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(588, 72, 14, 13, 210, '11.085.088', 'ROBERT LUGO', 8, 'C', 3, 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(589, 72, 14, 13, 211, '9.680.298', 'RAUL HERNANDEZ', 8, 'C', 2, 1, 0, 0, 0, 1, 0, 0, 1, 1, NULL, NULL, NULL),
(590, 72, 14, 13, 212, '8.743.519', 'FRANKLIN CAMACHO', 8, 'C', 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, NULL, NULL, NULL),
(591, 72, 14, 13, 213, '8.816.381', 'LUIS ORTEGA', 8, 'C', 3, 0, 0, 0, 0, 1, 0, 0, 0, 1, NULL, NULL, NULL),
(1045, 76, 14, 17, 302, '7.273.739', 'FREDDY CHAVEZ', 8, 'C', 3, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(1046, 76, 14, 17, 303, '7.212.704', 'SALOMON SUAREZ', 8, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(1047, 76, 14, 17, 304, '9.685.846', 'JESUS CROQUER', 8, 'C', 3, 1, 0, 1, 0, 1, 0, 0, 0, 1, NULL, NULL, NULL),
(1048, 76, 14, 17, 305, '9.433.982', 'CESAR RINCONES', 8, 'C', 2, 0, 0, 0, 0, 0, 1, 0, 0, 1, NULL, NULL, NULL),
(1049, 76, 14, 17, 306, '8588485', 'FRANKLIN GONZALEZ', 8, 'C', 3, 1, 0, 0, 0, 1, 0, 1, 0, 1, NULL, NULL, NULL),
(1506, 81, 15, 22, 404, '3.745.559', 'JOSE ARAUJO', 5, 'D', 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(1507, 81, 15, 22, 405, '5.935.427', 'RAFAEL GRATEROL', 5, 'D', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(1508, 81, 15, 22, 406, '6.465.882', 'JUAN MANMANY', 5, 'D', 3, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(1509, 81, 15, 22, 407, '6.887.926', 'FELIX SISO', 5, 'D', 2, 0, 0, 0, 0, 0, 1, 0, 0, 1, NULL, NULL, NULL),
(2398, 10, 3, 8, 72, '13.518.163', 'LEONARDO MATINEZ', 1, 'B', 1, 1, 0, 0, 0, 1, 0, 0, 1, 1, NULL, NULL, NULL),
(2399, 10, 3, 8, 73, '15.818.218', 'LUIS AULAR', 1, 'B', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(2400, 10, 3, 8, 74, '14.958.431', 'ALEXANDER GALLARDO', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(2401, 10, 3, 8, 75, '15.473.439', 'ALVARO FARFAN', 1, 'B', 2, 0, 0, 0, 0, 0, 0, 1, 1, 1, NULL, NULL, NULL),
(2402, 10, 3, 8, 76, '15.364.253', 'LUIS LOPEZ', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(2981, 7, 3, 6, 450, '15.532.706', 'VICTOR LOPEZ', 5, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(2982, 7, 3, 6, 462, '13.200.850', 'FREDDY LINARES', 5, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(2983, 7, 3, 6, 463, '9.654.897', 'EUGENIO CHIPRE', 5, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(2984, 7, 3, 6, 464, '7.236.986', 'REINERO LATUPF', 5, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3354, 6, 3, 4, 128, '15.610.809', 'DANIEL ESCALONA', 9, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3445, 10, 3, 8, 66, '11.055.132', 'EDSON ROJAS', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(3446, 10, 3, 8, 67, '12.728.503', 'LENIN FERNANDEZ', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3447, 10, 3, 8, 68, '12.643.909', 'ELIEZER TORRES', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3448, 10, 3, 8, 70, '13.699.347', 'ELVIS HERNANDEZ', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3950, 7, 3, 6, 31, '9.675.541', 'ROBERTO LATUPF', 8, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3951, 7, 3, 6, 32, '14.492.217', 'RAFAEL BORGUES', 8, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(3952, 7, 3, 6, 34, '9.673.758', 'DANNY CARVETTI', 8, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3953, 7, 3, 6, 35, '13.201.885', 'ALIRIO NUÑEZ', 8, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3954, 7, 3, 6, 37, '14.430.679', 'ROGER RODRIGUEZ', 8, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(4462, 91, 17, 5, 17, '11.357.055', 'JUAN MENDEZ', 2, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(4463, 91, 17, 5, 18, '9.643.300', 'ROBERTO OJEDA', 2, 'B', 3, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(4464, 91, 17, 5, 19, '10.752.036', 'LUIS GONZALEZ', 2, 'B', 2, 0, 0, 0, 0, 0, 0, 1, 1, 1, NULL, NULL, NULL),
(5087, 96, 17, 10, 94, '13.133.463', 'HERNAN MATINEZ', 6, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(5088, 96, 17, 10, 95, '12.927.967', 'DUILIO NAVAS', 6, 'B', 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(5089, 96, 17, 10, 96, '11.091.148', 'EMERSON NIEVES', 6, 'B', 2, 0, 0, 0, 0, 0, 0, 1, 0, 1, NULL, NULL, NULL),
(5090, 96, 17, 10, 97, '11.591.554', 'JUAN NIEVES', 6, 'B', 3, 2, 0, 0, 0, 1, 0, 0, 0, 1, NULL, NULL, NULL),
(5616, 79, 15, 20, 359, '4.305.940', 'ALEXIS GONZALEZ', 10, 'D', 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(5617, 79, 15, 20, 360, '8.588.485', 'FRANKLIN GONZALEZ', 10, 'D', 4, 2, 0, 0, 0, 2, 0, 0, 0, 1, NULL, NULL, NULL),
(5618, 79, 15, 20, 361, '4.553.224', 'JOSE CERVEN', 10, 'D', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(6095, 96, 17, 10, 111, '9.439.613', 'OSWALDO VERENZUELA', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(6096, 95, 17, 9, 159, '6.175.223', 'JOSE VELIZ', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(6097, 95, 17, 9, 160, '14.478.636', 'DARIO COLINA', 10, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(6694, 95, 17, 9, 171, '14.355.887', 'MARIO SUMOZA', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(6695, 95, 17, 9, 172, '16.291.878', 'ARMANDO ROMERO', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(6696, 95, 17, 9, 173, '14.872.733', 'RAFAEL BASTOS', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(7387, 90, 17, 4, 459, '11.685.739', 'JESUS RIVERA', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(7480, 90, 17, 4, 112, '9.439.237', 'JOSE ROJAS', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7481, 90, 17, 4, 113, '9.679.218', 'FRANKLIN APARICIO', 1, 'B', 3, 2, 0, 1, 0, 0, 2, 0, 0, 1, 0, 0, 0),
(8120, 96, 17, 10, 94, '13.133.463', 'HERNAN MATINEZ', 18, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(8121, 96, 17, 10, 95, '12.927.967', 'DUILIO NAVAS', 18, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(8122, 96, 17, 10, 96, '11.091.148', 'EMERSON NIEVES', 18, 'B', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL),
(8652, 78, 15, 19, 356, '6.487.497', 'ALEXIS SOLORZANO', 14, 'D', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(8653, 78, 15, 19, 357, '5.279.607', 'SERGIO OCHOA', 14, 'D', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(8654, 78, 15, 19, 358, '7.270.961', 'GUSTAVO GONZALEZ', 14, 'D', 2, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0),
(9144, 91, 17, 5, 468, '14.034.626', 'EDGAR REBOLLEDO', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(9145, 91, 17, 5, 469, '16.291.878', 'JESUS ROMERO', 15, 'B', 2, 0, 0, 0, 0, 1, 0, 2, 2, 1, 0, 0, 0),
(9146, 91, 17, 5, 484, '14.627.781', 'AMILCAR GUERRA', 15, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9681, 74, 14, 15, 267, '7.190.882', 'FROILAN REYES', 16, 'C', 2, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(9682, 74, 14, 15, 268, '7.257.990', 'JOSE BALDERRAMA', 16, 'C', 2, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(9683, 74, 14, 15, 269, '7.190.882', 'RAFAEL GRATEROL', 16, 'C', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(10196, 81, 15, 22, 405, '5.935.427', 'RAFAEL GRATEROL', 17, 'D', 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(10197, 81, 15, 22, 406, '6.465.882', 'JUAN MANMANY', 17, 'D', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(10198, 81, 15, 22, 407, '6.887.926', 'FELIX SISO', 17, 'D', 3, 1, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0),
(10683, 92, 17, 6, 39, '13.199.373', 'HERMES FLORES', 20, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(10684, 92, 17, 6, 40, '14.491.156', 'IRWIN CENTENO', 20, 'B', 2, 1, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0),
(10685, 92, 17, 6, 43, '7.274.224', 'JORGE LOPEZ', 20, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(11222, 99, 18, 7, 578, '12.118.541', 'JOSE AREVALO', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(11223, 100, 18, 4, 113, '9.679.218', 'FRANKLIN APARICIO', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(11224, 100, 18, 4, 114, '10.753.762', 'GILBERTO LUNA', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(11711, 104, 19, 16, 282, '7.235.602', 'ANIBAL TORO', 1, 'C', 3, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(11712, 104, 19, 16, 283, '8.331.228', 'JOSE MELEAN', 1, 'C', 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(12248, 103, 19, 14, 518, '9.646.818', 'LEONARDO VARGAS', 4, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(12249, 103, 19, 14, 519, '8.852.436', 'DIOGENES RODRIGUEZ', 4, 'C', 3, 2, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(12250, 103, 19, 14, 520, '11.085.088', 'ROBERT LUGO', 4, 'C', 3, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(12839, 108, 19, 12, 188, '6.315.372', 'RUSMEL CAMARIPANO', 5, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `jugadores_stats` (`id_js`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jugador`, `nj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`) VALUES
(12840, 108, 19, 12, 189, '7.181.992', 'ISRAEL MENDEZ', 5, 'C', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(12841, 108, 19, 12, 192, '9.994.397', 'GIOVANI MENDEZ', 5, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12888, 108, 19, 12, 632, '7.249.423', 'EDGAR DIAZ', 4, 'C', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monto`
--

CREATE TABLE `monto` (
  `id_monto` int(100) NOT NULL,
  `id_abn` int(100) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `numero` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monto`
--

INSERT INTO `monto` (`id_monto`, `id_abn`, `categoria`, `id_temp`, `id_team`, `monto`, `numero`) VALUES
(1, 1, 'B', 3, 4, 50.00, 1),
(2, 1, 'B', 3, 6, 0.00, 1),
(3, 1, 'B', 3, 5, 0.00, 1),
(4, 1, 'B', 3, 7, 45.00, 1),
(5, 1, 'B', 3, 8, 60.00, 1),
(6, 1, 'B', 3, 9, 40.00, 1),
(7, 1, 'B', 3, 10, 0.00, 1),
(8, 1, 'B', 3, 4, 0.00, 2),
(9, 1, 'B', 3, 6, 0.00, 2),
(10, 1, 'B', 3, 5, 0.00, 2),
(11, 1, 'B', 3, 7, 0.00, 2),
(12, 1, 'B', 3, 8, 0.00, 2),
(13, 1, 'B', 3, 9, 0.00, 2),
(14, 1, 'B', 3, 10, 0.00, 2),
(15, 2, 'C', 14, 12, 95.00, 1),
(16, 2, 'C', 14, 13, 130.00, 1),
(17, 2, 'C', 14, 14, 120.00, 1),
(18, 2, 'C', 14, 15, 39.00, 1),
(19, 2, 'C', 14, 16, 105.00, 1),
(20, 2, 'C', 14, 17, 123.00, 1),
(21, 2, 'C', 14, 18, 116.00, 1),
(22, 3, 'D', 15, 19, 120.00, 1),
(23, 3, 'D', 15, 20, 90.00, 1),
(24, 3, 'D', 15, 21, 112.00, 1),
(25, 3, 'D', 15, 22, 56.00, 1),
(26, 3, 'D', 15, 23, 120.00, 1),
(27, 4, 'B', 17, 4, 50.00, 1),
(28, 4, 'B', 17, 5, 100.00, 1),
(29, 4, 'B', 17, 6, 10.00, 1),
(30, 4, 'B', 17, 7, 65.00, 1),
(31, 4, 'B', 17, 8, 60.00, 1),
(32, 4, 'B', 17, 9, 40.00, 1),
(33, 4, 'B', 17, 10, 80.00, 1),
(34, 3, 'D', 15, 19, 0.00, 2),
(35, 3, 'D', 15, 20, 0.00, 2),
(36, 3, 'D', 15, 21, 0.00, 2),
(37, 3, 'D', 15, 22, 0.00, 2),
(38, 3, 'D', 15, 23, 0.00, 2),
(39, 3, 'D', 15, 19, 0.00, 3),
(40, 3, 'D', 15, 20, 0.00, 3),
(41, 3, 'D', 15, 21, 0.00, 3),
(42, 3, 'D', 15, 22, 0.00, 3),
(43, 3, 'D', 15, 23, 0.00, 3),
(44, 2, 'C', 14, 12, 0.00, 3),
(45, 2, 'C', 14, 13, 0.00, 3),
(46, 2, 'C', 14, 14, 0.00, 3),
(47, 2, 'C', 14, 15, 0.00, 3),
(48, 2, 'C', 14, 16, 0.00, 3),
(49, 2, 'C', 14, 17, 0.00, 3),
(50, 2, 'C', 14, 18, 0.00, 3),
(51, 2, 'C', 14, 12, 5.00, 2),
(52, 2, 'C', 14, 13, 0.00, 2),
(53, 2, 'C', 14, 14, 0.00, 2),
(54, 2, 'C', 14, 15, 0.00, 2),
(55, 2, 'C', 14, 16, 0.00, 2),
(56, 2, 'C', 14, 17, 0.00, 2),
(57, 2, 'C', 14, 18, 0.00, 2),
(58, 4, 'B', 17, 4, 0.00, 2),
(59, 4, 'B', 17, 5, 0.00, 2),
(60, 4, 'B', 17, 6, 0.00, 2),
(61, 4, 'B', 17, 7, 0.00, 2),
(62, 4, 'B', 17, 8, 0.00, 2),
(63, 4, 'B', 17, 9, 0.00, 2),
(64, 4, 'B', 17, 10, 0.00, 2),
(65, 3, 'D', 15, 19, 0.00, 4),
(66, 3, 'D', 15, 20, 0.00, 4),
(67, 3, 'D', 15, 21, 0.00, 4),
(68, 3, 'D', 15, 22, 0.00, 4),
(69, 3, 'D', 15, 23, 0.00, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntaje`
--

CREATE TABLE `puntaje` (
  `id_ttb` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `nj` int(100) DEFAULT NULL,
  `jj` int(100) DEFAULT NULL,
  `jg` int(100) DEFAULT NULL,
  `jp` int(100) DEFAULT NULL,
  `je` int(100) DEFAULT NULL,
  `ca` int(100) DEFAULT NULL,
  `ce` int(100) DEFAULT NULL,
  `val` int(1) DEFAULT NULL,
  `fech_part` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puntaje`
--

INSERT INTO `puntaje` (`id_ttb`, `id_tab`, `id_team`, `nj`, `jj`, `jg`, `jp`, `je`, `ca`, `ce`, `val`, `fech_part`) VALUES
(1, 6, 4, 1, 1, 1, 0, 0, 2, 0, 1, '2025-05-31'),
(3, 12, 10, 1, 1, 1, 0, 0, 6, 3, 1, '2025-05-31'),
(416, 108, 12, 5, 5, 0, 1, 0, 2, 6, 1, '2025-11-08'),
(417, 103, 14, 5, 5, 1, 0, 0, 6, 2, 1, '2025-11-08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report`
--

CREATE TABLE `report` (
  `timeday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `report`
--

INSERT INTO `report` (`timeday`) VALUES
('2025-11-17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_lanz`
--

CREATE TABLE `resumen_lanz` (
  `id_rslz` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `id_player` int(100) NOT NULL,
  `cedula` varchar(100) NOT NULL,
  `name_jglz` varchar(60) NOT NULL,
  `tnj` int(100) DEFAULT NULL,
  `categoria` varchar(1) NOT NULL,
  `tjl` int(100) DEFAULT NULL,
  `tjg` int(100) DEFAULT NULL,
  `avg` int(100) DEFAULT NULL,
  `til` int(100) DEFAULT NULL,
  `tcp` int(100) DEFAULT NULL,
  `tcpl` int(100) DEFAULT NULL,
  `efec` decimal(10,2) DEFAULT NULL,
  `h` int(100) DEFAULT NULL,
  `2b` int(100) DEFAULT NULL,
  `3b` int(100) DEFAULT NULL,
  `hr` int(100) DEFAULT NULL,
  `b` int(100) DEFAULT NULL,
  `k` int(100) DEFAULT NULL,
  `va` int(100) DEFAULT NULL,
  `gp` int(100) DEFAULT NULL,
  `ile` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resumen_lanz`
--

INSERT INTO `resumen_lanz` (`id_rslz`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jglz`, `tnj`, `categoria`, `tjl`, `tjg`, `avg`, `til`, `tcp`, `tcpl`, `efec`, `h`, `2b`, `3b`, `hr`, `b`, `k`, `va`, `gp`, `ile`) VALUES
(1, 71, 14, 12, 190, '3.291.020', 'PEDRO ECHENIQUE', 20, 'C', 1, 0, 0, 3, NULL, 5, 11.67, 5, 0, 1, 1, 0, 0, 21, 0, 0),
(2, 71, 14, 12, 193, '8.728.615', 'VICENTE COLMENARES', 16, 'C', 0, 0, 0, 8, 5, 5, 4.57, 5, 3, 2, 0, 1, 1, 34, 0, 0),
(3, 71, 14, 12, 197, '4.399.505', 'GENARO RIOS', 22, 'C', 9, 3, 333, 32, 20, 19, 4.12, 49, 8, 0, 1, 3, 16, 164, 0, 0),
(4, 71, 14, 12, 207, '7.227.962', 'CARLOS MENDOZA', 22, 'C', 4, 3, 750, 28, 15, 15, 3.76, 23, 2, 0, 0, 13, 10, 120, 0, 0),
(347, 108, 19, 12, 626, '9.652.000', 'PEDRO GARCIA', 6, 'C', 3, 2, 667, 11, 0, 1, 0.64, 14, 1, 0, 0, 8, 6, 68, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_stats`
--

CREATE TABLE `resumen_stats` (
  `id_rsts` int(100) NOT NULL,
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `id_player` int(100) NOT NULL,
  `cedula` varchar(100) NOT NULL,
  `name_jgstats` varchar(60) NOT NULL,
  `tnj` int(100) DEFAULT NULL,
  `categoria` varchar(1) NOT NULL,
  `vb` int(100) DEFAULT NULL,
  `h` int(100) DEFAULT NULL,
  `hr` int(100) DEFAULT NULL,
  `2b` int(100) DEFAULT NULL,
  `3b` int(100) DEFAULT NULL,
  `ca` int(100) DEFAULT NULL,
  `ci` int(100) DEFAULT NULL,
  `k` int(100) DEFAULT NULL,
  `b` int(100) DEFAULT NULL,
  `a` int(100) DEFAULT NULL,
  `sf` int(100) DEFAULT NULL,
  `br` int(100) DEFAULT NULL,
  `gp` int(100) DEFAULT NULL,
  `tvb` int(100) DEFAULT NULL,
  `th` int(100) DEFAULT NULL,
  `avg` int(100) DEFAULT NULL,
  `cb` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resumen_stats`
--

INSERT INTO `resumen_stats` (`id_rsts`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jgstats`, `tnj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`, `tvb`, `th`, `avg`, `cb`) VALUES
(1, 71, 14, 12, 186, '4.305.940', 'ALEXIS GONZALEZ', 22, 'C', 23, 6, 0, 0, 0, 7, 2, 1, 4, 13, 0, 0, 0, 23, 6, 261, 57.50),
(2, 71, 14, 12, 187, '9.645.519', 'EMILIO CONDE', 21, 'C', 27, 4, 1, 0, 1, 7, 2, 2, 2, 11, 0, 0, 0, 27, 6, 222, 67.50),
(606, 7, 3, 6, 444, '10.484.261', 'ROBERTO GARCIA', 4, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0, 0.00);
INSERT INTO `resumen_stats` (`id_rsts`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jgstats`, `tnj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`, `tvb`, `th`, `avg`, `cb`) VALUES
(607, 7, 3, 6, 450, '15.532.706', 'VICTOR LOPEZ', 4, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0, 0.00),
(608, 7, 3, 6, 462, '13.200.850', 'FREDDY LINARES', 4, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0, 0.00),
(1487, 100, 18, 4, 122, '15.129.468', 'LUIS RODRIGUEZ', 4, 'B', 10, 4, 0, 1, 0, 2, 2, 0, 2, 3, 0, 0, 0, 10, 5, 500, 25.00);
INSERT INTO `resumen_stats` (`id_rsts`, `id_tab`, `id_temp`, `id_team`, `id_player`, `cedula`, `name_jgstats`, `tnj`, `categoria`, `vb`, `h`, `hr`, `2b`, `3b`, `ca`, `ci`, `k`, `b`, `a`, `sf`, `br`, `gp`, `tvb`, `th`, `avg`, `cb`) VALUES
(1488, 100, 18, 4, 123, '14.854.652', 'CARLOS GOMEZ', 1, 'B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00),
(1727, 108, 19, 12, 626, '9.652.000', 'PEDRO GARCIA', 6, 'C', 3, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 3, 0, 0, 7.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_clasf`
--

CREATE TABLE `tab_clasf` (
  `id_tab` int(100) NOT NULL,
  `id_temp` int(100) NOT NULL,
  `id_team` int(100) NOT NULL,
  `name_team` varchar(50) NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `jj` int(100) DEFAULT NULL,
  `jg` int(100) DEFAULT NULL,
  `jp` int(100) DEFAULT NULL,
  `je` int(100) DEFAULT NULL,
  `avg` int(10) DEFAULT NULL,
  `ca` int(100) DEFAULT NULL,
  `ce` int(100) DEFAULT NULL,
  `dif` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_clasf`
--

INSERT INTO `tab_clasf` (`id_tab`, `id_temp`, `id_team`, `name_team`, `categoria`, `jj`, `jg`, `jp`, `je`, `avg`, `ca`, `ce`, `dif`) VALUES
(1, 1, 1, 'los panas', 'B', 0, 0, 0, 0, 0, 0, 0, 0),
(2, 1, 2, 'aviadores', 'B', 0, 0, 0, 0, 0, 0, 0, 0),
(3, 2, 3, 'lll', 'B', 0, 0, 0, 0, 0, 0, 0, 0),
(115, 18, 25, 'DREAM TEAM', 'B', 4, 2, 2, 0, 500, 19, 19, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporada`
--

CREATE TABLE `temporada` (
  `id_temp` int(100) NOT NULL,
  `name_temp` text NOT NULL,
  `categoria` varchar(1) NOT NULL,
  `partidas` int(100) NOT NULL,
  `innings` int(10) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `nequipos` int(10) NOT NULL,
  `activo` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporada`
--

INSERT INTO `temporada` (`id_temp`, `name_temp`, `categoria`, `partidas`, `innings`, `valor`, `nequipos`, `activo`) VALUES
(14, 'MAYO 25', 'C', 20, 7, 2.50, 7, 0),
(15, 'MAYO 25', 'D', 20, 7, 2.50, 5, 0),
(17, 'MAYO 2025', 'B', 20, 7, 2.50, 7, 0),
(18, 'TEMP OCTUBRE', 'B', 22, 7, 2.50, 4, 1),
(19, 'TEMP OCTUBRE', 'C', 20, 7, 2.50, 0, 1),
(20, 'TEMP OCTUBRE', 'D', 20, 7, 2.50, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiempos`
--

CREATE TABLE `tiempos` (
  `id_tiempo` int(100) NOT NULL,
  `hora` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiempos`
--

INSERT INTO `tiempos` (`id_tiempo`, `hora`) VALUES
(1, '08:00 AM'),
(2, '08:30 AM'),
(3, '09:00 AM'),
(4, '09:30 AM'),
(5, '10:00 AM'),
(6, '10:30 AM'),
(7, '11:00 AM'),
(8, '11:30 AM'),
(9, '12:00 PM'),
(10, '12:30 PM'),
(11, '01:00 PM'),
(12, '01:30 PM'),
(13, '02:00 PM'),
(14, '02:30 PM'),
(15, '03:00 PM'),
(16, '03:30 PM'),
(17, '04:00 PM'),
(18, '04:30 PM'),
(19, '05:00 PM'),
(20, '05:30 PM'),
(21, '06:00 PM'),
(22, '06:30 PM'),
(23, '07:00 PM'),
(24, '07:30 PM'),
(25, '08:00 PM'),
(26, '08:30 PM'),
(27, '09:00 PM'),
(28, '09:30 PM'),
(29, '10:00 PM');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD PRIMARY KEY (`id_abn`);

--
-- Indices de la tabla `calendario`
--
ALTER TABLE `calendario`
  ADD PRIMARY KEY (`id_cal`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_cat`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_team`);

--
-- Indices de la tabla `equipo_estados`
--
ALTER TABLE `equipo_estados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tab` (`id_tab`),
  ADD KEY `id_temp` (`id_temp`);

--
-- Indices de la tabla `equipo_retiros`
--
ALTER TABLE `equipo_retiros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_equipo_retirado` (`id_equipo_retirado`),
  ADD KEY `id_temp` (`id_temp`);

--
-- Indices de la tabla `homenaje`
--
ALTER TABLE `homenaje`
  ADD PRIMARY KEY (`id_hnr`);

--
-- Indices de la tabla `juegos`
--
ALTER TABLE `juegos`
  ADD PRIMARY KEY (`id_juego`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id_player`);

--
-- Indices de la tabla `jugadores_lanz`
--
ALTER TABLE `jugadores_lanz`
  ADD PRIMARY KEY (`id_lanz`);

--
-- Indices de la tabla `jugadores_stats`
--
ALTER TABLE `jugadores_stats`
  ADD PRIMARY KEY (`id_js`);

--
-- Indices de la tabla `monto`
--
ALTER TABLE `monto`
  ADD PRIMARY KEY (`id_monto`);

--
-- Indices de la tabla `puntaje`
--
ALTER TABLE `puntaje`
  ADD PRIMARY KEY (`id_ttb`);

--
-- Indices de la tabla `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`timeday`);

--
-- Indices de la tabla `resumen_lanz`
--
ALTER TABLE `resumen_lanz`
  ADD PRIMARY KEY (`id_rslz`);

--
-- Indices de la tabla `resumen_stats`
--
ALTER TABLE `resumen_stats`
  ADD PRIMARY KEY (`id_rsts`);

--
-- Indices de la tabla `tab_clasf`
--
ALTER TABLE `tab_clasf`
  ADD PRIMARY KEY (`id_tab`);

--
-- Indices de la tabla `temporada`
--
ALTER TABLE `temporada`
  ADD PRIMARY KEY (`id_temp`);

--
-- Indices de la tabla `tiempos`
--
ALTER TABLE `tiempos`
  ADD PRIMARY KEY (`id_tiempo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `abonos`
--
ALTER TABLE `abonos`
  MODIFY `id_abn` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `calendario`
--
ALTER TABLE `calendario`
  MODIFY `id_cal` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_cat` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_team` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `equipo_estados`
--
ALTER TABLE `equipo_estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `equipo_retiros`
--
ALTER TABLE `equipo_retiros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `homenaje`
--
ALTER TABLE `homenaje`
  MODIFY `id_hnr` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `juegos`
--
ALTER TABLE `juegos`
  MODIFY `id_juego` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id_player` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=635;

--
-- AUTO_INCREMENT de la tabla `jugadores_lanz`
--
ALTER TABLE `jugadores_lanz`
  MODIFY `id_lanz` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2746;

--
-- AUTO_INCREMENT de la tabla `jugadores_stats`
--
ALTER TABLE `jugadores_stats`
  MODIFY `id_js` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12889;

--
-- AUTO_INCREMENT de la tabla `monto`
--
ALTER TABLE `monto`
  MODIFY `id_monto` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `puntaje`
--
ALTER TABLE `puntaje`
  MODIFY `id_ttb` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT de la tabla `resumen_lanz`
--
ALTER TABLE `resumen_lanz`
  MODIFY `id_rslz` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT de la tabla `resumen_stats`
--
ALTER TABLE `resumen_stats`
  MODIFY `id_rsts` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1728;

--
-- AUTO_INCREMENT de la tabla `tab_clasf`
--
ALTER TABLE `tab_clasf`
  MODIFY `id_tab` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `temporada`
--
ALTER TABLE `temporada`
  MODIFY `id_temp` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tiempos`
--
ALTER TABLE `tiempos`
  MODIFY `id_tiempo` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipo_estados`
--
ALTER TABLE `equipo_estados`
  ADD CONSTRAINT `equipo_estados_ibfk_1` FOREIGN KEY (`id_tab`) REFERENCES `tab_clasf` (`id_tab`),
  ADD CONSTRAINT `equipo_estados_ibfk_2` FOREIGN KEY (`id_temp`) REFERENCES `temporada` (`id_temp`);

--
-- Filtros para la tabla `equipo_retiros`
--
ALTER TABLE `equipo_retiros`
  ADD CONSTRAINT `equipo_retiros_ibfk_1` FOREIGN KEY (`id_equipo_retirado`) REFERENCES `tab_clasf` (`id_tab`),
  ADD CONSTRAINT `equipo_retiros_ibfk_2` FOREIGN KEY (`id_temp`) REFERENCES `temporada` (`id_temp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
