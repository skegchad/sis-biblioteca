-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: bd_sis_biblioteca
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Ciencias Sociales','public/uploads/img/libros/categorias/CIENCIAS SOCIALES.png','2026-07-05 18:26:55'),(2,'Literatura Retorica','public/uploads/img/libros/categorias/LITERATURA RETÓRICA.png','2026-07-06 00:52:10'),(3,'Tecnología','public/uploads/img/libros/categorias/TECNOLOGÍA.png','2026-07-06 00:53:35'),(4,'Religión','public/uploads/img/libros/categorias/RELIGIÓN.png','2026-07-06 00:53:35'),(5,'Filosofía y Psicología','public/uploads/img/libros/categorias/FILOSOFÍA Y PSICOLOGÍA.png','2026-07-06 00:53:35'),(6,'Ciencias Naturales y Matemáticas','	public/uploads/img/libros/categorias/CIENCIAS NATURALES Y MATEMÁTICAS.png','2026-07-06 00:53:35'),(7,'Geografía e Historia','	public/uploads/img/libros/categorias/GEOGRAFÍA E HISTORIA.png','2026-07-06 00:53:35'),(8,'Lenguas','	public/uploads/img/libros/categorias/LENGUAS.png','2026-07-06 00:53:35'),(9,'Artes','	public/uploads/img/libros/categorias/ARTES.png','2026-07-06 00:53:35'),(10,'Generalidades','	public/uploads/img/libros/categorias/GENERALIDADES.png','2026-07-06 00:53:35'),(11,'Deportes','public/uploads/img/libros/categorias/cat_6a5421ee51a5f.jpg','2026-07-12 23:23:26');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libro_tema`
--

DROP TABLE IF EXISTS `libro_tema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libro_tema` (
  `id_libro` int(11) NOT NULL,
  `tema_id` int(11) NOT NULL,
  PRIMARY KEY (`id_libro`,`tema_id`),
  KEY `tema_id` (`tema_id`),
  CONSTRAINT `libro_tema_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `tb_libros` (`id_libro`) ON DELETE CASCADE,
  CONSTRAINT `libro_tema_ibfk_2` FOREIGN KEY (`tema_id`) REFERENCES `temas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_tema`
--

LOCK TABLES `libro_tema` WRITE;
/*!40000 ALTER TABLE `libro_tema` DISABLE KEYS */;
INSERT INTO `libro_tema` VALUES (1,8),(1,9),(1,14),(1,18),(2,10),(2,11),(2,12),(2,13),(3,8),(3,14),(3,15),(3,16),(3,17),(3,18),(4,4),(5,19),(5,20),(5,21),(5,22);
/*!40000 ALTER TABLE `libro_tema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL AUTO_INCREMENT,
  `ruta_foto` varchar(255) NOT NULL,
  PRIMARY KEY (`id_noticia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias`
--

LOCK TABLES `noticias` WRITE;
/*!40000 ALTER TABLE `noticias` DISABLE KEYS */;
INSERT INTO `noticias` VALUES (1,'public/uploads/img/noticias/noticia1.jpg'),(2,'public/uploads/img/noticias/noticia2.jpg'),(3,'public/uploads/img/noticias/noticia3.jpg');
/*!40000 ALTER TABLE `noticias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL AUTO_INCREMENT,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fyh_devolucion` datetime DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_prestamo`),
  KEY `id_libro` (`id_libro`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `tb_libros` (`id_libro`) ON DELETE CASCADE,
  CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
INSERT INTO `prestamos` VALUES (6,2,3,'2026-07-23 02:21:18','2026-07-31 00:00:00','EN CURSO'),(7,1,4,'2026-07-23 02:22:12','2026-07-31 00:00:00','EN CURSO');
/*!40000 ALTER TABLE `prestamos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategorias`
--

DROP TABLE IF EXISTS `subcategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sub` (`categoria_id`,`nombre`),
  CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategorias`
--

LOCK TABLES `subcategorias` WRITE;
/*!40000 ALTER TABLE `subcategorias` DISABLE KEYS */;
INSERT INTO `subcategorias` VALUES (1,1,'Sociología','2026-07-13 01:47:25'),(2,5,'Filosofía Antigua','2026-07-06 00:55:52'),(3,5,'Filosofía Moderna','2026-07-06 00:55:52'),(4,5,'Ética','2026-07-06 00:55:52'),(5,5,'Lógica','2026-07-06 00:55:52'),(6,5,'Psicología General','2026-07-06 00:55:52'),(7,5,'Psicología Clínica','2026-07-06 00:55:52'),(8,5,'Psicoanálisis','2026-07-06 00:55:52'),(9,2,'Novela','2026-07-11 05:04:55'),(10,3,'Programación','2026-07-12 04:47:22'),(11,11,'Fútbol','2026-07-12 23:23:26'),(12,11,'Baloncesto','2026-07-12 23:23:26'),(13,11,'Atletismo','2026-07-12 23:23:26'),(14,1,'Psicología social','2026-07-13 01:46:27'),(17,1,'Cultura','2026-07-13 02:02:08');
/*!40000 ALTER TABLE `subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_libros`
--

DROP TABLE IF EXISTS `tb_libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_libros` (
  `id_libro` int(11) NOT NULL AUTO_INCREMENT,
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
  `estado` varchar(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_libro`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_libros`
--

LOCK TABLES `tb_libros` WRITE;
/*!40000 ALTER TABLE `tb_libros` DISABLE KEYS */;
INSERT INTO `tb_libros` VALUES (1,'Así habló Zaratustra','Friedrich Nietzsche','Obra de género híbrido —poema, relato, ensayo filosófico— que expone las principales ideas filosóficas de Nietzsche','Español',1,'Ideas, Crítica Religiosa, Cristianismo, Filosofia','Obra Filosofica','Educ.ar','1886','193','100','Filosofía y Psicología','Filosofía Moderna','A1','Educ.ar',3,1,'public/uploads/pdf/libros/libro_6a4afdbff2a95.pdf','public/uploads/img/libros/portadaZaratustra.png','2026-07-05 19:58:39','2026-07-22 21:22:12',NULL,'1'),(2,'La metamorfosis','Franz Kafka','La metamorfosis (1915), escrita por Franz Kafka, narra la historia de Gregorio Samsa, un viajante de comercio que despierta una mañana convertido en un monstruoso insecto. La obra explora el impacto de este suceso en su vida y en la de su familia, reflejando temas como la alienación, el aislamiento y el peso de la responsabilidad.','Español',0,'Identidad, Soledad, Existencial, Culpa','Novela','Libricultura','1915','833','800','Literatura Retorica','Novela','A1','Editorial alma',1,1,'public/uploads/pdf/libros/libro_6a51cf4abce30.pdf','public/uploads/img/libros/libro_6a51cf4abcc31.png','2026-07-11 00:06:18','2026-07-22 21:21:18',NULL,'1'),(3,'Anticristo','Friedrich Nietzsche','Obra filosófica en la que Friedrich Nietzsche realiza una crítica profunda al cristianismo, la moral tradicional y los valores occidentales. El autor propone una reevaluación de los valores humanos y defiende una visión de la vida basada en la afirmación, la fuerza creadora y la superación del ser humano.','Español',1,'Filosofia, Crítica Religiosa, Moral, Valores, Nihilismo, Cristianismo','Obra Filosofica','Biblioteca Digital del ILCE','1895','193','100','Filosofía y Psicología','Filosofía Moderna','A1','Biblioteca Digital del ILCE',1,0,'public/uploads/pdf/libros/libro_6a51e241ef253.pdf','public/uploads/img/libros/libro_6a51e241eeeb8.png','2026-07-11 01:27:13','2026-07-20 18:03:12',NULL,'1'),(4,'aa','aa','aaa','Español',1,'Aventura','Novela','Libricultura','1','1223','122','Ciencias Sociales','Sociología','1212','Educ.ar',12,1,NULL,NULL,'2026-07-11 23:36:38',NULL,'2026-07-11 23:40:58','0'),(5,'The C Programming Language','Brian W. Kernighan y Dennis M. Ritchie','El libro \"The C Programming Language\" (conocido como K&R), escrito por Brian Kernighan y Dennis Ritchie (creador del lenguaje), es la guía de referencia definitiva y el texto fundamental que popularizó el lenguaje C.','Inglés',1,'Programación,C,Computadoras,Informática','Enciclopedia','Prentice Hall','1978','005.133','000','Tecnología','Programación','B1','Prentice Hall',1,0,'public/uploads/pdf/libros/libro_6a531ca17acfe.pdf','public/uploads/img/libros/libro_6a531ca17aa85.png','2026-07-11 23:48:33',NULL,NULL,'1');
/*!40000 ALTER TABLE `tb_libros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_usuarios`
--

DROP TABLE IF EXISTS `tb_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
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
  `estado` varchar(11) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_usuarios`
--

LOCK TABLES `tb_usuarios` WRITE;
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` VALUES (1,'Sergio','Martínez','0951348952','admin','$2y$10$YjDjXATMLc8rmq4TlL9ZWeHih0GA350gWb9Fb9tjcGlshRxIT9DRy','public/uploads/img/admin/user_6a4157e827f16.jpg','Administrador','2026-06-10 23:16:13','2026-06-28 12:20:40',NULL,NULL,NULL,'1'),(3,'DIEGO','CEREZO','6767676767','ELBURRO67','$2y$10$zccD.DVJldWy/97DjnsW7uCX88yA8JBNLQvsTKLyLioYAAq6DXyg6','public/uploads/img/admin/user_6a4157c013d5f.jpg','Estudiante','2026-06-21 01:03:00','2026-06-28 12:20:00',NULL,'3ro Bachillerato','INFORMÁTICA','1'),(4,'Jose Maria','Nevarez','9999999999','jose','$2y$10$QQ4Wvv08uJ/530L/NcahqeRS7xlUrYt4NqoOkQpcZBur1bVoAv0lO',NULL,'Administrador','2026-06-21 01:32:00',NULL,NULL,NULL,NULL,'1'),(5,'Juan','Cabello','555555555','aaaaaa','$2y$10$uAsdd9fow.T.yGxZ05Nmn.1jpR5jIRmrAWL7HnJr2jSsyW/Ky41MK',NULL,'Estudiante','2026-06-21 01:45:21',NULL,NULL,'10mo','C','1'),(6,'KRISTYN','LUNA','12121212','BURRA','$2y$10$spOCic1biV5LwH3/TD.y9u6cdOuJLwpgNjLphv699UT/sopbyIJfm',NULL,'Estudiante','2026-06-21 03:00:28',NULL,NULL,'3ro Bachillerato','INFORMÁTICA','1'),(7,'aaaaaaa','aaaaaaaaa','1111111111','accc','$2y$10$m/4umCOScgGeQvcFlmQk8.l8C7rmUsVRiERoJQQ7WmzqKoK//Pp7.',NULL,'Docente','2026-06-28 00:09:24',NULL,'2026-06-28 00:11:52',NULL,NULL,'0'),(8,'','','','','$2y$10$DXn3UUr6UCESeJhwtyMar.6AB9H7gBecXScx11p1RxpQsxm/WrHYi',NULL,'','2026-06-28 01:39:01',NULL,'2026-06-28 01:43:34',NULL,NULL,'0'),(9,'1','2','3','4','$2y$10$f7Wq3GMeJI7fcd9bcQvZNOsQt6a5K/nmmLT3HFw1fSDrb1D9Wh60u',NULL,'Administrador','2026-06-28 01:41:02',NULL,NULL,NULL,NULL,'1'),(10,'aaa','','','','$2y$10$DH9k2B5afEQOvJD0wMxMqeYeMJfk3FbdpyoP3hdM/nPxBMQ5iY2HC',NULL,'','2026-06-28 01:41:29',NULL,'2026-06-28 01:43:29',NULL,NULL,'0'),(11,'555','555','555','555','$2y$10$CpzrXw14yrY484nGezgO3Of0qchfuk9T04GBAANvw0ahhdRRPyi0S',NULL,'Administrador','2026-06-28 01:43:14',NULL,NULL,NULL,NULL,'1'),(12,'1','2','3','5','$2y$10$ec0B.2VlCGAa5R0e80.p6eqgr86zdVhaQLqhon75tDp07vl4IbP8C',NULL,'Administrador','2026-06-28 02:14:02',NULL,NULL,NULL,NULL,'1'),(13,'sssssssssssssssssssss','ssssssssss','22222','aaaaaaaax','$2y$10$XSZb5r2YMhQFkTokR13cpewD8CC.FDNFZPgYX6xZ9IfOLPgyCHXg6',NULL,'Administrador','2026-06-28 02:21:35',NULL,NULL,NULL,NULL,'1'),(14,'aaaaaaaaaaaaaaaxczxcz','zxczxczxc','111','xxx','$2y$10$f7bqz6O4NxybX0TANG7uD.v6jEd5bydyz2R6NlJZL6fIjC6DnGmL.','public/uploads/img/admin/default.jpg','Administrador','2026-06-28 02:24:03',NULL,NULL,NULL,NULL,'1'),(15,'777','777','777','777','$2y$10$SrqHGLpmpiJTJ71xEmGT9.1I9kEnlKsvjj3JUfwY2p9o0R8hh1RXm','public/uploads/img/admin/default.jpg','Administrador','2026-06-28 02:25:02',NULL,NULL,NULL,NULL,'1'),(16,'cxcxc','xcxcxc','111','111111','$2y$10$DF7469Bib6BNyJyy.G8iCuYTDoqjnYJjfcSuEjJYJmO//cKA/FkWi','public/uploads/img/admin/default.jpg','Administrador','2026-06-28 02:29:13',NULL,'2026-06-28 12:25:23',NULL,NULL,'0'),(17,'999','999','999','999','$2y$10$MsslqFEUquXvjiNbUjE.C.V3N26dFDlNsW2/bpVQaU4IqhY0K5DcO','public/uploads/img/admin/user_6a40ce4d1d333.jpeg','Administrador','2026-06-28 02:33:33',NULL,NULL,NULL,NULL,'1'),(18,'Sergio','ssss','1333133313','0000','$2y$10$9U/APL2Offmii2FrSD0qIOTQ.UOBZLoYK5VRNN26qDJ70QIv90Ni2','public/uploads/img/admin/default.jpg','Estudiante','2026-07-05 10:02:18',NULL,NULL,'3ro Bachillerato','INFORMÁTICA','1'),(19,'Homelander','Peruano','0996677667','elpatriota67','$2y$10$nTjekJvJ28S6OC2TMdarpOhTkzj1cb8cZ6SK.OMx6YGhHmu273C0i','public/uploads/img/admin/user_6a51f377938e3.png','Estudiante','2026-07-11 02:40:39','2026-07-15 23:31:55',NULL,'3ro Bachillerato','INSTALACIONES','1'),(20,'Damián','Gonzalez','0999999999','andrx','$2y$10$rvZLxD9qcQ570B8xAQbJWOEKQ57bG4Z4Rpb/qoNRESVEMckQVdqou','public/uploads/img/admin/user_6a585f45ba62d.webp','Estudiante','2026-07-15 23:34:13',NULL,NULL,'3ro Bachillerato','INFORMÁTICA','1');
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temas`
--

DROP TABLE IF EXISTS `temas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tema` (`tipo_id`,`nombre`),
  CONSTRAINT `temas_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temas`
--

LOCK TABLES `temas` WRITE;
/*!40000 ALTER TABLE `temas` DISABLE KEYS */;
INSERT INTO `temas` VALUES (1,1,'Romance','2026-07-05 18:44:36'),(2,1,'Ciencia Ficción','2026-07-05 18:44:36'),(3,1,'Terror','2026-07-05 18:44:36'),(4,1,'Aventura','2026-07-05 18:44:36'),(5,3,'Computadoras','2026-07-05 18:44:36'),(6,3,'Electricidad','2026-07-05 18:44:36'),(7,3,'Cocina','2026-07-05 18:44:36'),(8,5,'Filosofia','2026-07-06 00:50:48'),(9,5,'Ideas','2026-07-06 00:50:56'),(10,1,'Identidad','2026-07-11 05:00:40'),(11,1,'Soledad','2026-07-11 05:00:42'),(12,1,'Existencial','2026-07-11 05:01:04'),(13,1,'Culpa','2026-07-11 05:01:41'),(14,5,'Crítica Religiosa','2026-07-11 06:25:01'),(15,5,'Moral','2026-07-11 06:25:07'),(16,5,'Valores','2026-07-11 06:25:08'),(17,5,'Nihilismo','2026-07-11 06:25:17'),(18,5,'Cristianismo','2026-07-11 06:25:18'),(19,2,'Programación','2026-07-12 04:44:42'),(20,2,'C','2026-07-12 04:44:48'),(21,2,'Computadoras','2026-07-12 04:44:55'),(22,2,'Informática','2026-07-12 04:45:00'),(23,6,'Shonen','2026-07-13 02:30:55'),(24,6,'Seinen','2026-07-13 02:38:45');
/*!40000 ALTER TABLE `temas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos`
--

DROP TABLE IF EXISTS `tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos`
--

LOCK TABLES `tipos` WRITE;
/*!40000 ALTER TABLE `tipos` DISABLE KEYS */;
INSERT INTO `tipos` VALUES (1,'Novela','2026-07-05 18:44:36'),(2,'Enciclopedia','2026-07-05 18:44:36'),(3,'Manual','2026-07-05 18:44:36'),(4,'Diccionario','2026-07-05 18:44:36'),(5,'Obra Filosofica','2026-07-06 00:49:28'),(6,'Manga','2026-07-13 02:30:55');
/*!40000 ALTER TABLE `tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'bd_sis_biblioteca'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `actualizar_prestamos_vencidos` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `actualizar_prestamos_vencidos` ON SCHEDULE EVERY 1 HOUR STARTS '2026-07-22 22:04:47' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE prestamos

   SET estado = 'VENCIDO'

   WHERE NOW() > fyh_devolucion

     AND (estado IS NULL OR estado != 'VENCIDO') */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'bd_sis_biblioteca'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-22 22:22:24
