-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2026 a las 21:30:20
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
-- Base de datos: `bd_sis_biblioteca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `foto`, `fyh_creacion`) VALUES
(1, 'Ciencias Sociales', 'public/uploads/img/libros/categorias/CIENCIAS SOCIALES.png', '2026-07-05 18:26:55'),
(2, 'Literatura Retorica', 'public/uploads/img/libros/categorias/LITERATURA RETÓRICA.png', '2026-07-06 00:52:10'),
(3, 'Tecnología', 'public/uploads/img/libros/categorias/TECNOLOGÍA.png', '2026-07-06 00:53:35'),
(4, 'Religión', 'public/uploads/img/libros/categorias/RELIGIÓN.png', '2026-07-06 00:53:35'),
(5, 'Filosofía y Psicología', 'public/uploads/img/libros/categorias/FILOSOFÍA Y PSICOLOGÍA.png', '2026-07-06 00:53:35'),
(6, 'Ciencias Naturales y Matemáticas', '	public/uploads/img/libros/categorias/CIENCIAS NATURALES Y MATEMÁTICAS.png', '2026-07-06 00:53:35'),
(7, 'Geografía e Historia', '	public/uploads/img/libros/categorias/GEOGRAFÍA E HISTORIA.png', '2026-07-06 00:53:35'),
(8, 'Lenguas', '	public/uploads/img/libros/categorias/LENGUAS.png', '2026-07-06 00:53:35'),
(9, 'Artes', '	public/uploads/img/libros/categorias/ARTES.png', '2026-07-06 00:53:35'),
(10, 'Generalidades', '	public/uploads/img/libros/categorias/GENERALIDADES.png', '2026-07-06 00:53:35'),
(11, 'Deportes', 'public/uploads/img/libros/categorias/cat_6a5421ee51a5f.jpg', '2026-07-12 23:23:26'),
(12, 'fisherinho', 'public/uploads/img/libros/categorias/cat_6a63a8261e3ac.jpg', '2026-07-24 18:00:06'),
(13, 'aws', 'public/uploads/img/libros/categorias/cat_6a63bc7583027.png', '2026-07-24 19:26:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro_tema`
--

CREATE TABLE `libro_tema` (
  `id_libro` int(11) NOT NULL,
  `tema_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `libro_tema`
--

INSERT INTO `libro_tema` (`id_libro`, `tema_id`) VALUES
(1, 8),
(1, 9),
(1, 14),
(1, 18),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(3, 8),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(4, 4),
(5, 19),
(5, 20),
(5, 21),
(5, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL,
  `ruta_foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `ruta_foto`) VALUES
(1, 'public/uploads/img/noticias/noticia1.jpg'),
(2, 'public/uploads/img/noticias/noticia2.jpg'),
(3, 'public/uploads/img/noticias/noticia3.jpg'),
(8, 'public/uploads/img/noticias/noticia8.jpg'),
(9, 'public/uploads/img/noticias/noticia9.jpg'),
(10, 'public/uploads/img/noticias/noticia10.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fyh_devolucion` datetime DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_libro`, `id_usuario`, `fyh_creacion`, `fyh_devolucion`, `estado`) VALUES
(6, 2, 3, '2026-07-23 02:21:18', '2026-07-31 00:00:00', 'EN CURSO'),
(7, 1, 4, '2026-07-23 02:22:12', '2026-07-31 00:00:00', 'EN CURSO'),
(8, 1, 19, '2026-07-23 04:58:52', '2026-07-29 00:00:00', 'EN CURSO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `subcategorias`
--

INSERT INTO `subcategorias` (`id`, `categoria_id`, `nombre`, `fyh_creacion`) VALUES
(1, 1, 'Sociología', '2026-07-13 01:47:25'),
(2, 5, 'Filosofía Antigua', '2026-07-06 00:55:52'),
(3, 5, 'Filosofía Moderna', '2026-07-06 00:55:52'),
(4, 5, 'Ética', '2026-07-06 00:55:52'),
(5, 5, 'Lógica', '2026-07-06 00:55:52'),
(6, 5, 'Psicología General', '2026-07-06 00:55:52'),
(7, 5, 'Psicología Clínica', '2026-07-06 00:55:52'),
(8, 5, 'Psicoanálisis', '2026-07-06 00:55:52'),
(9, 2, 'Novela', '2026-07-11 05:04:55'),
(10, 3, 'Programación', '2026-07-12 04:47:22'),
(11, 11, 'Fútbol', '2026-07-12 23:23:26'),
(12, 11, 'Baloncesto', '2026-07-12 23:23:26'),
(13, 11, 'Atletismo', '2026-07-12 23:23:26'),
(14, 1, 'Psicología social', '2026-07-13 01:46:27'),
(17, 1, 'Cultura', '2026-07-13 02:02:08'),
(18, 12, 'fisher', '2026-07-24 18:00:06'),
(19, 12, 'prime', '2026-07-24 18:00:06'),
(20, 13, 'aaa', '2026-07-24 19:26:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_libros`
--

CREATE TABLE `tb_libros` (
  `id_libro` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(150) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `idioma` varchar(255) NOT NULL,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT 1,
  `temas` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `edicion` varchar(255) NOT NULL,
  `ano` varchar(255) NOT NULL,
  `cdd` varchar(255) NOT NULL,
  `bloque` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `subcategoria` varchar(100) DEFAULT NULL,
  `seccion` varchar(255) NOT NULL,
  `editorial` varchar(255) NOT NULL,
  `ejemplares` int(11) NOT NULL DEFAULT 0,
  `prestados` int(11) NOT NULL DEFAULT 0,
  `ruta_pdf` varchar(500) DEFAULT NULL,
  `ruta_foto` varchar(500) DEFAULT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `fyh_eliminacion` datetime DEFAULT NULL,
  `estado` varchar(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tb_libros`
--

INSERT INTO `tb_libros` (`id_libro`, `titulo`, `autor`, `descripcion`, `idioma`, `disponibilidad`, `temas`, `tipo`, `edicion`, `ano`, `cdd`, `bloque`, `categoria`, `subcategoria`, `seccion`, `editorial`, `ejemplares`, `prestados`, `ruta_pdf`, `ruta_foto`, `fyh_creacion`, `fyh_actualizacion`, `fyh_eliminacion`, `estado`) VALUES
(1, 'Así habló Zaratustra', 'Friedrich Nietzsche', 'Obra de género híbrido —poema, relato, ensayo filosófico— que expone las principales ideas filosóficas de Nietzsche', 'Español', 1, 'Ideas, Crítica Religiosa, Cristianismo, Filosofia', 'Obra Filosofica', 'Educ.ar', '1886', '193', '100', 'Filosofía y Psicología', 'Filosofía Moderna', 'A1', 'Educ.ar', 3, 2, 'public/uploads/pdf/libros/libro_6a4afdbff2a95.pdf', 'public/uploads/img/libros/portadaZaratustra.png', '2026-07-05 19:58:39', '2026-07-22 23:58:52', NULL, '1'),
(2, 'La metamorfosis', 'Franz Kafka', 'La metamorfosis (1915), escrita por Franz Kafka, narra la historia de Gregorio Samsa, un viajante de comercio que despierta una mañana convertido en un monstruoso insecto. La obra explora el impacto de este suceso en su vida y en la de su familia, reflejando temas como la alienación, el aislamiento y el peso de la responsabilidad.', 'Español', 0, 'Identidad, Soledad, Existencial, Culpa', 'Novela', 'Libricultura', '1915', '833', '800', 'Literatura Retorica', 'Novela', 'A1', 'Editorial alma', 1, 1, 'public/uploads/pdf/libros/libro_6a51cf4abce30.pdf', 'public/uploads/img/libros/libro_6a51cf4abcc31.png', '2026-07-11 00:06:18', '2026-07-22 21:21:18', NULL, '1'),
(3, 'Anticristo', 'Friedrich Nietzsche', 'Obra filosófica en la que Friedrich Nietzsche realiza una crítica profunda al cristianismo, la moral tradicional y los valores occidentales. El autor propone una reevaluación de los valores humanos y defiende una visión de la vida basada en la afirmación, la fuerza creadora y la superación del ser humano.', 'Español', 1, 'Filosofia, Crítica Religiosa, Moral, Valores, Nihilismo, Cristianismo', 'Obra Filosofica', 'Biblioteca Digital del ILCE', '1895', '193', '100', 'Filosofía y Psicología', 'Filosofía Moderna', 'A1', 'Biblioteca Digital del ILCE', 1, 0, 'public/uploads/pdf/libros/libro_6a51e241ef253.pdf', 'public/uploads/img/libros/libro_6a51e241eeeb8.png', '2026-07-11 01:27:13', '2026-07-20 18:03:12', NULL, '1'),
(4, 'aa', 'aa', 'aaa', 'Español', 1, 'Aventura', 'Novela', 'Libricultura', '1', '1223', '122', 'Ciencias Sociales', 'Sociología', '1212', 'Educ.ar', 12, 1, NULL, NULL, '2026-07-11 23:36:38', NULL, '2026-07-11 23:40:58', '0'),
(5, 'The C Programming Language', 'Brian W. Kernighan y Dennis M. Ritchie', 'El libro \"The C Programming Language\" (conocido como K&R), escrito por Brian Kernighan y Dennis Ritchie (creador del lenguaje), es la guía de referencia definitiva y el texto fundamental que popularizó el lenguaje C.', 'Inglés', 1, 'Programación,C,Computadoras,Informática', 'Enciclopedia', 'Prentice Hall', '1978', '005.133', '000', 'Tecnología', 'Programación', 'B1', 'Prentice Hall', 1, 0, 'public/uploads/pdf/libros/libro_6a531ca17acfe.pdf', 'public/uploads/img/libros/libro_6a531ca17aa85.png', '2026-07-11 23:48:33', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuarios`
--

CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `cedula` varchar(255) NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `fyh_eliminacion` datetime DEFAULT NULL,
  `curso` varchar(255) DEFAULT NULL,
  `paralelo` varchar(255) DEFAULT NULL,
  `estado` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id_usuario`, `nombre_completo`, `apellidos`, `cedula`, `nombre_usuario`, `password`, `foto`, `cargo`, `fyh_creacion`, `fyh_actualizacion`, `fyh_eliminacion`, `curso`, `paralelo`, `estado`) VALUES
(1, 'Sergio', 'Martínez', '0951348952', 'admin', '$2y$10$YjDjXATMLc8rmq4TlL9ZWeHih0GA350gWb9Fb9tjcGlshRxIT9DRy', 'public/uploads/img/admin/user_6a4157e827f16.jpg', 'Administrador', '2026-06-10 23:16:13', '2026-06-28 12:20:40', NULL, NULL, NULL, '1'),
(3, 'DIEGO', 'CEREZO', '6767676767', 'ELBURRO67', '$2y$10$zccD.DVJldWy/97DjnsW7uCX88yA8JBNLQvsTKLyLioYAAq6DXyg6', 'public/uploads/img/admin/user_6a4157c013d5f.jpg', 'Estudiante', '2026-06-21 01:03:00', '2026-06-28 12:20:00', NULL, '3ro Bachillerato', 'INFORMÁTICA', '1'),
(4, 'Jose Maria', 'Nevarez', '9999999999', 'jose', '$2y$10$QQ4Wvv08uJ/530L/NcahqeRS7xlUrYt4NqoOkQpcZBur1bVoAv0lO', NULL, 'Administrador', '2026-06-21 01:32:00', NULL, NULL, NULL, NULL, '1'),
(5, 'Juan', 'Cabello', '555555555', 'aaaaaa', '$2y$10$uAsdd9fow.T.yGxZ05Nmn.1jpR5jIRmrAWL7HnJr2jSsyW/Ky41MK', NULL, 'Estudiante', '2026-06-21 01:45:21', NULL, NULL, '10mo', 'C', '1'),
(6, 'KRISTYN', 'LUNA', '12121212', 'BURRA', '$2y$10$spOCic1biV5LwH3/TD.y9u6cdOuJLwpgNjLphv699UT/sopbyIJfm', NULL, 'Estudiante', '2026-06-21 03:00:28', NULL, NULL, '3ro Bachillerato', 'INFORMÁTICA', '1'),
(7, 'aaaaaaa', 'aaaaaaaaa', '1111111111', 'accc', '$2y$10$m/4umCOScgGeQvcFlmQk8.l8C7rmUsVRiERoJQQ7WmzqKoK//Pp7.', NULL, 'Docente', '2026-06-28 00:09:24', NULL, '2026-06-28 00:11:52', NULL, NULL, '0'),
(8, '', '', '', '', '$2y$10$DXn3UUr6UCESeJhwtyMar.6AB9H7gBecXScx11p1RxpQsxm/WrHYi', NULL, '', '2026-06-28 01:39:01', NULL, '2026-06-28 01:43:34', NULL, NULL, '0'),
(9, '1', '2', '3', '4', '$2y$10$f7Wq3GMeJI7fcd9bcQvZNOsQt6a5K/nmmLT3HFw1fSDrb1D9Wh60u', NULL, 'Administrador', '2026-06-28 01:41:02', NULL, NULL, NULL, NULL, '1'),
(10, 'aaa', '', '', '', '$2y$10$DH9k2B5afEQOvJD0wMxMqeYeMJfk3FbdpyoP3hdM/nPxBMQ5iY2HC', NULL, '', '2026-06-28 01:41:29', NULL, '2026-06-28 01:43:29', NULL, NULL, '0'),
(11, '555', '555', '555', '555', '$2y$10$CpzrXw14yrY484nGezgO3Of0qchfuk9T04GBAANvw0ahhdRRPyi0S', NULL, 'Administrador', '2026-06-28 01:43:14', NULL, NULL, NULL, NULL, '1'),
(12, '1', '2', '3', '5', '$2y$10$ec0B.2VlCGAa5R0e80.p6eqgr86zdVhaQLqhon75tDp07vl4IbP8C', NULL, 'Administrador', '2026-06-28 02:14:02', NULL, NULL, NULL, NULL, '1'),
(13, 'sssssssssssssssssssss', 'ssssssssss', '22222', 'aaaaaaaax', '$2y$10$XSZb5r2YMhQFkTokR13cpewD8CC.FDNFZPgYX6xZ9IfOLPgyCHXg6', NULL, 'Administrador', '2026-06-28 02:21:35', NULL, NULL, NULL, NULL, '1'),
(14, 'aaaaaaaaaaaaaaaxczxcz', 'zxczxczxc', '111', 'xxx', '$2y$10$f7bqz6O4NxybX0TANG7uD.v6jEd5bydyz2R6NlJZL6fIjC6DnGmL.', 'public/uploads/img/admin/default.jpg', 'Administrador', '2026-06-28 02:24:03', NULL, NULL, NULL, NULL, '1'),
(15, '777', '777', '777', '777', '$2y$10$SrqHGLpmpiJTJ71xEmGT9.1I9kEnlKsvjj3JUfwY2p9o0R8hh1RXm', 'public/uploads/img/admin/default.jpg', 'Administrador', '2026-06-28 02:25:02', NULL, NULL, NULL, NULL, '1'),
(16, 'cxcxc', 'xcxcxc', '111', '111111', '$2y$10$DF7469Bib6BNyJyy.G8iCuYTDoqjnYJjfcSuEjJYJmO//cKA/FkWi', 'public/uploads/img/admin/default.jpg', 'Administrador', '2026-06-28 02:29:13', NULL, '2026-06-28 12:25:23', NULL, NULL, '0'),
(17, '999', '999', '999', '999', '$2y$10$MsslqFEUquXvjiNbUjE.C.V3N26dFDlNsW2/bpVQaU4IqhY0K5DcO', 'public/uploads/img/admin/user_6a40ce4d1d333.jpeg', 'Administrador', '2026-06-28 02:33:33', NULL, NULL, NULL, NULL, '1'),
(18, 'Sergio', 'ssss', '1333133313', '0000', '$2y$10$9U/APL2Offmii2FrSD0qIOTQ.UOBZLoYK5VRNN26qDJ70QIv90Ni2', 'public/uploads/img/admin/default.jpg', 'Estudiante', '2026-07-05 10:02:18', NULL, NULL, '3ro Bachillerato', 'INFORMÁTICA', '1'),
(19, 'Homelander', 'Peruano', '0996677667', 'elpatriota67', '$2y$10$MXQQvLJr2pz/uPs1HWAqX.CPbQr18zYjjGcY4c7vniDNBbWTUrqgO', 'public/uploads/img/admin/usuario19.png', 'Estudiante', '2026-07-11 02:40:39', '2026-07-23 23:59:57', NULL, '3ro Bachillerato', 'INSTALACIONES', '1'),
(20, 'Damián', 'Gonzalez', '0999999999', 'andrx', '$2y$10$rvZLxD9qcQ570B8xAQbJWOEKQ57bG4Z4Rpb/qoNRESVEMckQVdqou', 'public/uploads/img/admin/user_6a585f45ba62d.webp', 'Estudiante', '2026-07-15 23:34:13', NULL, NULL, '3ro Bachillerato', 'INFORMÁTICA', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temas`
--

CREATE TABLE `temas` (
  `id` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `temas`
--

INSERT INTO `temas` (`id`, `tipo_id`, `nombre`, `fyh_creacion`) VALUES
(1, 1, 'Romance', '2026-07-05 18:44:36'),
(2, 1, 'Ciencia Ficción', '2026-07-05 18:44:36'),
(3, 1, 'Terror', '2026-07-05 18:44:36'),
(4, 1, 'Aventura', '2026-07-05 18:44:36'),
(5, 3, 'Computadoras', '2026-07-05 18:44:36'),
(6, 3, 'Electricidad', '2026-07-05 18:44:36'),
(7, 3, 'Cocina', '2026-07-05 18:44:36'),
(8, 5, 'Filosofia', '2026-07-06 00:50:48'),
(9, 5, 'Ideas', '2026-07-06 00:50:56'),
(10, 1, 'Identidad', '2026-07-11 05:00:40'),
(11, 1, 'Soledad', '2026-07-11 05:00:42'),
(12, 1, 'Existencial', '2026-07-11 05:01:04'),
(13, 1, 'Culpa', '2026-07-11 05:01:41'),
(14, 5, 'Crítica Religiosa', '2026-07-11 06:25:01'),
(15, 5, 'Moral', '2026-07-11 06:25:07'),
(16, 5, 'Valores', '2026-07-11 06:25:08'),
(17, 5, 'Nihilismo', '2026-07-11 06:25:17'),
(18, 5, 'Cristianismo', '2026-07-11 06:25:18'),
(19, 2, 'Programación', '2026-07-12 04:44:42'),
(20, 2, 'C', '2026-07-12 04:44:48'),
(21, 2, 'Computadoras', '2026-07-12 04:44:55'),
(22, 2, 'Informática', '2026-07-12 04:45:00'),
(23, 6, 'Shonen', '2026-07-13 02:30:55'),
(24, 6, 'Seinen', '2026-07-13 02:38:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`id`, `nombre`, `fyh_creacion`) VALUES
(1, 'Novela', '2026-07-05 18:44:36'),
(2, 'Enciclopedia', '2026-07-05 18:44:36'),
(3, 'Manual', '2026-07-05 18:44:36'),
(4, 'Diccionario', '2026-07-05 18:44:36'),
(5, 'Obra Filosofica', '2026-07-06 00:49:28'),
(6, 'Manga', '2026-07-13 02:30:55');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `libro_tema`
--
ALTER TABLE `libro_tema`
  ADD PRIMARY KEY (`id_libro`,`tema_id`),
  ADD KEY `tema_id` (`tema_id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD KEY `id_libro` (`id_libro`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sub` (`categoria_id`,`nombre`);

--
-- Indices de la tabla `tb_libros`
--
ALTER TABLE `tb_libros`
  ADD PRIMARY KEY (`id_libro`);

--
-- Indices de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `temas`
--
ALTER TABLE `temas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tema` (`tipo_id`,`nombre`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tb_libros`
--
ALTER TABLE `tb_libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `temas`
--
ALTER TABLE `temas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `libro_tema`
--
ALTER TABLE `libro_tema`
  ADD CONSTRAINT `libro_tema_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `tb_libros` (`id_libro`) ON DELETE CASCADE,
  ADD CONSTRAINT `libro_tema_ibfk_2` FOREIGN KEY (`tema_id`) REFERENCES `temas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `tb_libros` (`id_libro`) ON DELETE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `temas`
--
ALTER TABLE `temas`
  ADD CONSTRAINT `temas_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `actualizar_prestamos_vencidos` ON SCHEDULE EVERY 1 HOUR STARTS '2026-07-22 22:04:47' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE prestamos
   SET estado = 'VENCIDO'
   WHERE NOW() > fyh_devolucion
     AND (estado IS NULL OR estado != 'VENCIDO')$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
