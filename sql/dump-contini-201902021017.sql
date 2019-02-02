-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: contini
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `afip_documento_tipo`
--

DROP TABLE IF EXISTS `afip_documento_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_documento_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_99D85FF20332D99` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_documento_tipo`
--

LOCK TABLES `afip_documento_tipo` WRITE;
/*!40000 ALTER TABLE `afip_documento_tipo` DISABLE KEYS */;
INSERT INTO `afip_documento_tipo` VALUES (1,80,'CUIT',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30'),(2,87,'CDI',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30'),(3,91,'CI Extranjera',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30'),(4,94,'Pasaporte',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30'),(5,96,'DNI',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30'),(6,99,'Otro',1,'2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30');
/*!40000 ALTER TABLE `afip_documento_tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afip_alicuota`
--

DROP TABLE IF EXISTS `afip_alicuota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_alicuota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_A4F7CB0720332D99` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_alicuota`
--

LOCK TABLES `afip_alicuota` WRITE;
/*!40000 ALTER TABLE `afip_alicuota` DISABLE KEYS */;
INSERT INTO `afip_alicuota` VALUES (1,1,'No Gravado','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(2,2,'Exento','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(3,3,'0','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(4,9,'2.5','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(5,8,'5','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(6,4,'10.5','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(7,5,'21','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1),(8,6,'27','2009-02-20 00:00:00',NULL,1,'2018-12-27 13:31:30',1,'2018-12-27 13:31:30',1);
/*!40000 ALTER TABLE `afip_alicuota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afip_provincias`
--

DROP TABLE IF EXISTS `afip_provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_provincias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_628A5D8220332D99` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_provincias`
--

LOCK TABLES `afip_provincias` WRITE;
/*!40000 ALTER TABLE `afip_provincias` DISABLE KEYS */;
/*!40000 ALTER TABLE `afip_provincias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afip_iva_condicion`
--

DROP TABLE IF EXISTS `afip_iva_condicion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_iva_condicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_iva_condicion`
--

LOCK TABLES `afip_iva_condicion` WRITE;
/*!40000 ALTER TABLE `afip_iva_condicion` DISABLE KEYS */;
INSERT INTO `afip_iva_condicion` VALUES (1,1,'IVA Responsable Inscripto',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(2,2,'IVA Responsable no Inscripto',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(3,3,'IVA no Responsable',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(4,4,'IVA Sujeto Exento',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(5,5,'Consumidor Final',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(6,6,'Responsable Monotributo',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(7,7,'Sujeto no Categorizado',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(8,8,'Proveedor del Exterior',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00'),(9,9,'Cliente del Exterior',1,1,'2018-01-01 00:00:00',1,'2018-01-01 00:00:00');
/*!40000 ALTER TABLE `afip_iva_condicion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afip_comprobante_tipo`
--

DROP TABLE IF EXISTS `afip_comprobante_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_comprobante_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_comprobante_tipo`
--

LOCK TABLES `afip_comprobante_tipo` WRITE;
/*!40000 ALTER TABLE `afip_comprobante_tipo` DISABLE KEYS */;
INSERT INTO `afip_comprobante_tipo` VALUES (1,1,'FACTURA A','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(2,2,'NOTAS DE DEBITO A','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(3,3,'NOTAS DE CREDITO A','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(4,4,'RECIBOS A','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(5,5,'NOTAS DE VENTA AL CONTADO A','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(6,6,'FACTURA B','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(7,7,'NOTAS DE DEBITO B','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(8,8,'NOTAS DE CREDITO B','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(9,9,'RECIBOS B','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(10,10,'NOTAS DE VENTA AL CONTADO B','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(11,11,'FACTURA C','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(12,12,'NOTAS DE DEBITO C','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(13,13,'NOTAS DE CREDITO C','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(15,15,'RECIBOS C','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(16,16,'NOTAS DE VENTA AL CONTADO C','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00');
/*!40000 ALTER TABLE `afip_comprobante_tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afip_condicion_venta`
--

DROP TABLE IF EXISTS `afip_condicion_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afip_condicion_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FB4E34120332D99` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afip_condicion_venta`
--

LOCK TABLES `afip_condicion_venta` WRITE;
/*!40000 ALTER TABLE `afip_condicion_venta` DISABLE KEYS */;
INSERT INTO `afip_condicion_venta` VALUES (1,1,'Contado','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(2,2,'Tarjeta Debito','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(3,3,'Tarjeta Credito','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(4,4,'Cuenta Corriente','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(5,5,'Cheque','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(6,6,'Ticket','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00'),(7,7,'Otra','2009-02-20 00:00:00','2030-02-20 00:00:00',1,1,'2019-01-06 00:00:00',1,'2019-01-06 00:00:00');
/*!40000 ALTER TABLE `afip_condicion_venta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-02-02 10:17:44
