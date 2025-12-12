-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-12-2025 a las 12:37:22
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hospital_sampaka`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `analiticas`
--

CREATE TABLE `analiticas` (
  `id_analitica` int(11) NOT NULL,
  `id_consulta` int(11) DEFAULT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `resultado` text DEFAULT NULL,
  `estado` enum('Pendiente','Entregado') DEFAULT 'Pendiente',
  `id_prueba` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `pagado` tinyint(1) DEFAULT 0,
  `valores_referencia` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `analiticas`
--

INSERT INTO `analiticas` (`id_analitica`, `id_consulta`, `id_paciente`, `resultado`, `estado`, `id_prueba`, `comentario`, `fecha_registro`, `pagado`, `valores_referencia`, `archivo`, `id_usuario`) VALUES
(1, 1, 1, 'POSITIVO', 'Entregado', 1, NULL, '2025-09-11 16:40:18', 0, 'NO', NULL, 3),
(2, 1, 1, 'NEGATIVO', 'Entregado', 2, NULL, '2025-09-11 16:40:18', 0, 'NO', '1757765428_539272.pdf', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `id_medico` int(11) DEFAULT NULL,
  `fecha_consulta` datetime DEFAULT current_timestamp(),
  `tipo_consulta` enum('General','Urgencias','Gastroenterología','Ginecología','Pediatría','Cardiología','Dermatología','Neurología','Traumatología','Psiquiatría','Oncología','Oftalmología','Otorrinolaringología','Endocrinología','Neumología','Reumatología') NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `temperatura` decimal(5,2) DEFAULT NULL,
  `presion_arterial` varchar(20) DEFAULT NULL,
  `tension_arterial` varchar(20) DEFAULT NULL,
  `saturacion_oxigeno` decimal(5,2) DEFAULT NULL,
  `pulso` int(11) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `IMC` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `pagado` tinyint(1) DEFAULT 0,
  `precio` decimal(10,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `explo_fisica` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `consultas`
--

INSERT INTO `consultas` (`id_consulta`, `id_paciente`, `id_hospital`, `id_medico`, `fecha_consulta`, `tipo_consulta`, `diagnostico`, `temperatura`, `presion_arterial`, `tension_arterial`, `saturacion_oxigeno`, `pulso`, `peso`, `talla`, `motivo`, `IMC`, `fecha_registro`, `pagado`, `precio`, `id_usuario`, `explo_fisica`) VALUES
(1, 1, 1, NULL, '2025-09-09 11:01:24', 'General', '', '36.00', '89', '45', '87.00', 58, '89.00', '1.75', 'dolor de cabeza desde hace 4 dias.', 'el paciente presenta varias lesiones', '2025-09-09 11:01:24', 1, '500.00', NULL, 'el paciente esta bien'),
(3, 3, 1, 1, '2025-09-11 15:05:50', 'General', NULL, '36.00', '89', '45', '87.00', 58, '89.00', '1.75', 'fiebre desde hace 3 dias', NULL, '2025-09-11 15:05:50', 0, NULL, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `defunciones`
--

CREATE TABLE `defunciones` (
  `id_defuncion` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `fecha_defuncion` date DEFAULT NULL,
  `causa_muerte` text DEFAULT NULL,
  `lugar` enum('Hospital','Domicilio','Traslado') DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_consulta`
--

CREATE TABLE `detalle_consulta` (
  `id_detalle` int(11) NOT NULL,
  `id_consulta` int(11) DEFAULT NULL,
  `orina` varchar(100) DEFAULT NULL,
  `defeca` varchar(100) DEFAULT NULL,
  `horas_sueno` int(11) DEFAULT NULL,
  `antecedentes_familiares` text DEFAULT NULL,
  `antecedentes_conyuge` text DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `operaciones` text DEFAULT NULL,
  `transfuciones` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detalle_consulta`
--

INSERT INTO `detalle_consulta` (`id_detalle`, `id_consulta`, `orina`, `defeca`, `horas_sueno`, `antecedentes_familiares`, `antecedentes_conyuge`, `alergias`, `operaciones`, `transfuciones`, `id_usuario`) VALUES
(1, 1, 'si', 'si', 7, 'Valor de familiares', 'Valor de conyuge', 'Valor de alergias', 'Valor de operaciones', 'Valor de transfusiones', NULL),
(2, 3, 'No', 'no', 7, 'no', 'no', 'si', 'si', 'si', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_gasto` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `turno` enum('Manana','Tarde','Noche') DEFAULT NULL,
  `dias_asignados` varchar(100) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospitales`
--

CREATE TABLE `hospitales` (
  `id_hospital` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `hospitales`
--

INSERT INTO `hospitales` (`id_hospital`, `nombre`, `distrito`, `categoria`, `direccion`, `telefono`, `logo`) VALUES
(1, 'Hospital de Sampaka', 'Malabo', '1', 'Malabo, Bioko Norte', '555897654', 'jsjsjs');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospitalizaciones`
--

CREATE TABLE `hospitalizaciones` (
  `id_hospitalizacion` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `id_sala` int(11) DEFAULT NULL,
  `numero_cama` varchar(10) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_alta` date DEFAULT NULL,
  `causa` text DEFAULT NULL,
  `estado_alta` enum('Curado','Mejorado','Fallecido') DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id_ingreso` int(11) NOT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp(),
  `ip_origen` varchar(45) DEFAULT NULL,
  `dispositivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id_log`, `id_usuario`, `accion`, `descripcion`, `fecha_hora`, `ip_origen`, `dispositivo`) VALUES
(1, NULL, 'LOGIN_FALLIDO', 'Usuario \'clint\' no existe', '2025-09-05 14:00:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(2, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 14:01:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(3, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 14:13:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(4, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 14:27:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(5, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 14:32:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(6, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-05 14:37:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(7, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 14:38:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(8, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-05 14:38:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(9, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 15:07:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(10, 1, 'REGISTRO_PACIENTE', 'Se registró al paciente Jesus Crispin Topola con código JC25T908', '2025-09-05 15:53:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(11, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-05 16:11:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(12, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-05 16:11:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(13, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-06 11:08:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(14, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-08 10:49:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(15, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-08 10:50:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(16, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-08 10:50:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(17, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-08 11:32:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(18, 1, 'Creación de consulta', 'Consulta ID 1 creada para el paciente ID 1', '2025-09-09 11:01:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(19, NULL, 'Actualización de consulta', 'Consulta ID 1 actualizada por usuario ID ', '2025-09-09 11:14:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(20, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-09 11:46:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(21, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-09 11:49:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(22, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 08:55:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(23, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 13:08:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(24, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 13:12:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(25, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 14:10:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(26, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 14:10:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(27, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 14:16:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(28, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 14:17:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(29, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 14:25:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(30, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 14:25:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(31, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 14:47:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(32, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 14:47:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(33, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 14:59:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(34, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 14:59:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(35, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 15:23:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(36, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-10 15:23:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(37, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 15:23:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(38, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 15:37:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(39, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 15:37:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(40, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-10 15:44:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(41, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-10 15:44:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(42, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 09:12:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(43, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 09:12:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(44, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 11:22:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(45, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 11:22:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(46, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 12:07:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(47, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 12:07:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(48, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 13:50:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(49, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 13:51:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(50, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 14:00:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(51, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:00:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(52, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 14:20:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(53, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:20:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(54, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-11 14:21:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(55, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:21:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(56, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 14:29:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(57, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:29:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(58, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 14:40:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(59, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:40:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(60, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-11 14:54:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(61, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-11 14:54:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(62, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:54:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(63, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-11 14:54:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(64, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 14:54:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(65, 3, 'REGISTRO_PACIENTE', 'Se registró al paciente Maximiliano Compe puye con código MC25RM4T', '2025-09-11 15:00:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(66, 3, 'LOGOUT', 'El usuario \'Mh123\' (Minerva 2 ) cerró sesión.', '2025-09-11 15:05:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(67, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:05:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(68, 1, 'Creación de consulta', 'Consulta ID 3 creada para el paciente ID 3', '2025-09-11 15:05:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(69, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-11 15:06:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(70, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:06:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(71, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 15:11:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(72, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:11:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(73, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 15:19:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(74, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:19:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(75, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-11 15:23:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(76, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:23:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(77, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 15:38:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(78, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:38:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(79, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 15:45:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(80, 3, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-11 15:45:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(81, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:45:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(82, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 15:53:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(83, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 15:53:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(84, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 16:13:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(85, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 16:13:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(86, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 16:20:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(87, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 16:20:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(88, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-11 16:32:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(89, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-11 16:33:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(90, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 11:32:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(91, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 11:35:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(92, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 12:06:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(93, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 12:06:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(94, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 12:18:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(95, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 12:18:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(96, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 12:23:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(97, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 12:23:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(98, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 12:46:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(99, 3, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-12 12:47:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(100, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 12:47:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(101, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 12:53:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(102, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 12:53:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(103, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 13:05:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(104, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 13:05:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0'),
(105, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 13:21:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(106, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 13:31:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(107, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 13:31:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(108, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 13:48:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(109, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 14:01:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(110, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 14:01:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(111, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 14:14:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(112, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-12 14:14:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(113, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-12 14:14:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(114, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 10:16:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(115, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-13 10:16:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(116, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 10:16:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(117, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-13 10:23:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(118, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 10:23:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(119, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-13 10:23:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(120, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 10:23:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(121, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 10:42:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(122, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 10:42:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(123, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 11:00:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(124, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 11:01:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(125, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 11:16:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(126, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 11:16:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(127, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 11:50:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(128, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 11:50:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(129, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 11:56:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(130, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 11:57:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(131, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 12:22:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(132, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 12:22:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(133, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 12:36:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(134, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 12:37:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(135, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 12:59:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(136, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 12:59:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(137, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-13 13:08:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(138, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-13 13:08:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(139, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-15 09:41:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(140, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-09-15 09:41:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(141, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-15 09:41:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(142, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-09-15 09:41:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(143, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-15 09:41:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(144, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-15 10:15:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(145, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-15 10:15:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(146, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-15 10:48:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(147, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-09-15 10:48:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(148, 3, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-09-19 12:11:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'),
(149, 1, 'LOGIN_FALLIDO', 'Contraseña incorrecta', '2025-12-01 14:30:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(150, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-12-11 12:05:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(151, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-12-11 12:09:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(152, 1, 'Cierre de sesión', 'Sesión cerrada automáticamente por inactividad de más de 5 minutos', '2025-12-11 12:15:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(153, 1, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-12-11 12:16:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(154, 1, 'LOGOUT', 'El usuario \'admin\' (Salvador ) cerró sesión.', '2025-12-11 12:23:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(155, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-12-11 12:23:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(156, 3, 'LOGOUT', 'El usuario \'Mh123\' (Minerva 2 ) cerró sesión.', '2025-12-11 12:24:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(157, 3, 'LOGIN_EXITO', 'Inicio de sesión exitoso', '2025-12-11 12:24:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `sexo` enum('M','F') DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `codigo`, `nombre`, `apellido`, `sexo`, `fecha_nacimiento`, `correo`, `direccion`, `telefono`, `nacionalidad`, `ocupacion`, `fecha_registro`, `id_usuario`) VALUES
(1, 'SAMEcdea', 'salvador', 'mete', 'M', '1995-01-03', 'salvadormete2@gmail.com', '', '+240222478702', 'ecuatoguineano', 'estudiante', '2025-08-30 16:30:42', 1),
(2, 'JC25T908', 'Jesus', 'Crispin Topola', 'M', '2011-02-09', 'mpcontacto19@gmail.com', 'Malabo', '+240222155113', 'Guinea Ecuatorial', 'Informatico', '2025-09-05 15:53:36', 1),
(3, 'MC25RM4T', 'Maximiliano', 'Compe puye', 'M', '2006-01-04', 'maximiliano@gmail.com', 'Malabo', '+240222478702', 'Guinea Ecuatorial', 'estudiante', '2025-09-11 15:00:02', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `id_analitica` int(11) DEFAULT NULL,
  `id_prueba` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `codigo` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `nivel_estudios` varchar(150) NOT NULL,
  `nacionalidad` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id_personal`, `id_hospital`, `nombre`, `apellido`, `especialidad`, `cargo`, `telefono`, `correo`, `codigo`, `direccion`, `nivel_estudios`, `nacionalidad`) VALUES
(1, 1, 'Salvador', 'Mete Bijeri', 'Medico', 'medico', '222780932', 'salvadormete2@gmail.com', '', '', '', ''),
(4, 1, 'Minerva 2', 'Muatiche', 'Dermatologo', 'Dermatologo', '+240222155113', 'minerva@prueba.com', 'MMQPALNGC9', 'Sampaka', 'Licenciado', 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pruebas_medicas`
--

CREATE TABLE `pruebas_medicas` (
  `id_prueba` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pruebas_medicas`
--

INSERT INTO `pruebas_medicas` (`id_prueba`, `nombre`, `precio`, `id_usuario`) VALUES
(1, 'Paludismo', '5000.00', 1),
(2, 'Tifoidea', '5000.00', 1),
(3, 'Hepatitis B', '10000.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

CREATE TABLE `salas` (
  `id_sala` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `num_cama` int(2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('Administrador','General','Urgencias','Farmaceutico','Laboratorio','Finanzas') DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_personal`, `username`, `password`, `rol`, `estado`) VALUES
(1, 1, 'admin', '$2y$10$L3JaPO64XA5DhDGyuWwUc.T91ZSmPRuCa4PWXgEGU9gltoVfFMbrC', 'Administrador', 'Activo'),
(3, 4, 'Mh123', '$2y$10$XEN/v1FyA2vHtE7tJvMfauKPWWAr2dXx5DHy4lo.bPhAUDlWKnYNy', 'General', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunaciones`
--

CREATE TABLE `vacunaciones` (
  `id_vacunacion` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_hospital` int(11) DEFAULT NULL,
  `tipo_vacuna` varchar(150) DEFAULT NULL,
  `fecha_aplicacion` date DEFAULT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `analiticas`
--
ALTER TABLE `analiticas`
  ADD PRIMARY KEY (`id_analitica`),
  ADD KEY `id_consulta` (`id_consulta`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_prueba` (`id_prueba`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_medico` (`id_medico`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `defunciones`
--
ALTER TABLE `defunciones`
  ADD PRIMARY KEY (`id_defuncion`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalle_consulta`
--
ALTER TABLE `detalle_consulta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_consulta` (`id_consulta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gasto`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_personal` (`id_personal`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `hospitales`
--
ALTER TABLE `hospitales`
  ADD PRIMARY KEY (`id_hospital`);

--
-- Indices de la tabla `hospitalizaciones`
--
ALTER TABLE `hospitalizaciones`
  ADD PRIMARY KEY (`id_hospitalizacion`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id_ingreso`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_analitica` (`id_analitica`),
  ADD KEY `id_prueba` (`id_prueba`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id_personal`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_hospital` (`id_hospital`);

--
-- Indices de la tabla `pruebas_medicas`
--
ALTER TABLE `pruebas_medicas`
  ADD PRIMARY KEY (`id_prueba`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id_sala`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `vacunaciones`
--
ALTER TABLE `vacunaciones`
  ADD PRIMARY KEY (`id_vacunacion`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_hospital` (`id_hospital`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `analiticas`
--
ALTER TABLE `analiticas`
  MODIFY `id_analitica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `defunciones`
--
ALTER TABLE `defunciones`
  MODIFY `id_defuncion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_consulta`
--
ALTER TABLE `detalle_consulta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hospitales`
--
ALTER TABLE `hospitales`
  MODIFY `id_hospital` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hospitalizaciones`
--
ALTER TABLE `hospitalizaciones`
  MODIFY `id_hospitalizacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id_personal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pruebas_medicas`
--
ALTER TABLE `pruebas_medicas`
  MODIFY `id_prueba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salas`
--
ALTER TABLE `salas`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `vacunaciones`
--
ALTER TABLE `vacunaciones`
  MODIFY `id_vacunacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `analiticas`
--
ALTER TABLE `analiticas`
  ADD CONSTRAINT `analiticas_ibfk_1` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`),
  ADD CONSTRAINT `analiticas_ibfk_2` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`),
  ADD CONSTRAINT `analiticas_ibfk_3` FOREIGN KEY (`id_prueba`) REFERENCES `pruebas_medicas` (`id_prueba`),
  ADD CONSTRAINT `analiticas_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `consultas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`),
  ADD CONSTRAINT `consultas_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `consultas_ibfk_3` FOREIGN KEY (`id_medico`) REFERENCES `personal` (`id_personal`),
  ADD CONSTRAINT `consultas_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `defunciones`
--
ALTER TABLE `defunciones`
  ADD CONSTRAINT `defunciones_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`),
  ADD CONSTRAINT `defunciones_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `defunciones_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_consulta`
--
ALTER TABLE `detalle_consulta`
  ADD CONSTRAINT `detalle_consulta_ibfk_1` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`),
  ADD CONSTRAINT `detalle_consulta_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `gastos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `hospitalizaciones`
--
ALTER TABLE `hospitalizaciones`
  ADD CONSTRAINT `hospitalizaciones_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`),
  ADD CONSTRAINT `hospitalizaciones_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `hospitalizaciones_ibfk_3` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`),
  ADD CONSTRAINT `hospitalizaciones_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `ingresos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `pacientes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_analitica`) REFERENCES `analiticas` (`id_analitica`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_prueba`) REFERENCES `pruebas_medicas` (`id_prueba`),
  ADD CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`);

--
-- Filtros para la tabla `pruebas_medicas`
--
ALTER TABLE `pruebas_medicas`
  ADD CONSTRAINT `pruebas_medicas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `salas`
--
ALTER TABLE `salas`
  ADD CONSTRAINT `salas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`);

--
-- Filtros para la tabla `vacunaciones`
--
ALTER TABLE `vacunaciones`
  ADD CONSTRAINT `vacunaciones_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`),
  ADD CONSTRAINT `vacunaciones_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`),
  ADD CONSTRAINT `vacunaciones_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
