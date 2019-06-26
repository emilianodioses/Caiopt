/*
SQLyog Ultimate v8.61 
MySQL - 5.7.26-0ubuntu0.18.04.1 : Database - contini
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `aa_articulos` */

CREATE TABLE `aa_articulos` (
  `Codigo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Descripcion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IVA` double DEFAULT NULL,
  `iva_tipo_id` int(11) DEFAULT NULL,
  `Precio_de_costo` decimal(19,4) DEFAULT NULL,
  `ganancia` double DEFAULT NULL,
  `Precio_de_venta_sin_iva` decimal(19,4) DEFAULT NULL,
  `Precio_de_venta` decimal(19,4) DEFAULT NULL,
  `ganancia_carlos` double DEFAULT NULL,
  `Precio_de_venta_sin_iva_carlos` decimal(19,4) DEFAULT NULL,
  `Precio_de_venta_carlos` decimal(19,4) DEFAULT NULL,
  `Cantidad_minima` double DEFAULT NULL,
  `Categoria` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `Marca` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `Genero` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Estilo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Material` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Forma` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Color_Marco` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Color_Cristal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `aa_clientes` */

CREATE TABLE `aa_clientes` (
  `Nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Condicion_de_IVA` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iva_tipo_id` int(11) DEFAULT NULL,
  `Direccion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Tipo_de_documento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `documento_tipo_id` int(11) DEFAULT NULL,
  `Nro_documento` double DEFAULT NULL,
  `Contacto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Telefono` double DEFAULT NULL,
  `Localidad` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `localidad_id` int(11) DEFAULT NULL,
  `Mail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `aa_medicos` */

CREATE TABLE `aa_medicos` (
  `Nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Matricula` double DEFAULT NULL,
  `Nro_documento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Localidad` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `localidad_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `afip_alicuota` */

CREATE TABLE `afip_alicuota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_A4F7CB0720332D99` (`codigo`),
  KEY `IDX_A4F7CB07DE12AB56` (`created_by`),
  KEY `IDX_A4F7CB0716FE72E1` (`updated_by`),
  CONSTRAINT `FK_A4F7CB0716FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_A4F7CB07DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `afip_comprobante_tipo` */

CREATE TABLE `afip_comprobante_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `compra` tinyint(1) NOT NULL,
  `venta` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9A0C5DDADE12AB56` (`created_by`),
  KEY `IDX_9A0C5DDA16FE72E1` (`updated_by`),
  CONSTRAINT `FK_9A0C5DDA16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_9A0C5DDADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `afip_condicion_venta` */

CREATE TABLE `afip_condicion_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FB4E34120332D99` (`codigo`),
  KEY `IDX_5FB4E341DE12AB56` (`created_by`),
  KEY `IDX_5FB4E34116FE72E1` (`updated_by`),
  CONSTRAINT `FK_5FB4E34116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_5FB4E341DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `afip_documento_tipo` */

CREATE TABLE `afip_documento_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_99D85FF20332D99` (`codigo`),
  KEY `IDX_99D85FFDE12AB56` (`created_by`),
  KEY `IDX_99D85FF16FE72E1` (`updated_by`),
  CONSTRAINT `FK_99D85FF16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_99D85FFDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `afip_iva_condicion` */

CREATE TABLE `afip_iva_condicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_25EFE430DE12AB56` (`created_by`),
  KEY `IDX_25EFE43016FE72E1` (`updated_by`),
  CONSTRAINT `FK_25EFE43016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_25EFE430DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `articulo` */

CREATE TABLE `articulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `codigo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `precio_costo` decimal(16,2) NOT NULL,
  `ganancia_porcentaje` decimal(16,2) NOT NULL,
  `precio_venta` decimal(16,2) NOT NULL,
  `cantidad` decimal(16,2) NOT NULL,
  `cantidad_minima` decimal(16,2) NOT NULL,
  `precio_modifica` tinyint(1) NOT NULL,
  `genero` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `material` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forma` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estilo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_marco` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_cristal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `orden_trabajo` tinyint(1) NOT NULL,
  `afip_alicuota_id` int(11) DEFAULT NULL,
  `ultimo_comprobante_id` int(11) DEFAULT NULL,
  `precio_venta_sin_iva` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_69E94E913397707A` (`categoria_id`),
  KEY `IDX_69E94E9181EF0041` (`marca_id`),
  KEY `IDX_69E94E9168D9BA2B` (`afip_alicuota_id`),
  KEY `IDX_69E94E916E8AE207` (`ultimo_comprobante_id`),
  KEY `IDX_69E94E91DE12AB56` (`created_by`),
  KEY `IDX_69E94E9116FE72E1` (`updated_by`),
  CONSTRAINT `FK_69E94E9116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_69E94E913397707A` FOREIGN KEY (`categoria_id`) REFERENCES `articulo_categoria` (`id`),
  CONSTRAINT `FK_69E94E9168D9BA2B` FOREIGN KEY (`afip_alicuota_id`) REFERENCES `afip_alicuota` (`id`),
  CONSTRAINT `FK_69E94E916E8AE207` FOREIGN KEY (`ultimo_comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_69E94E9181EF0041` FOREIGN KEY (`marca_id`) REFERENCES `articulo_marca` (`id`),
  CONSTRAINT `FK_69E94E91DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=282 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `articulo_categoria` */

CREATE TABLE `articulo_categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B904BF0EDE12AB56` (`created_by`),
  KEY `IDX_B904BF0E16FE72E1` (`updated_by`),
  CONSTRAINT `FK_B904BF0E16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_B904BF0EDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `articulo_marca` */

CREATE TABLE `articulo_marca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_50BA305CDE12AB56` (`created_by`),
  KEY `IDX_50BA305C16FE72E1` (`updated_by`),
  CONSTRAINT `FK_50BA305C16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_50BA305CDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `cliente` */

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `documento_numero` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `localidad_id` int(11) DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contacto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `iva_condicion_id` int(11) DEFAULT NULL,
  `documento_tipo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F41C9B252DCBF3BC` (`documento_numero`),
  KEY `IDX_F41C9B2542D4BDD0` (`iva_condicion_id`),
  KEY `IDX_F41C9B2567707C89` (`localidad_id`),
  KEY `IDX_F41C9B257C9FBE9A` (`documento_tipo_id`),
  KEY `IDX_F41C9B25DE12AB56` (`created_by`),
  KEY `IDX_F41C9B2516FE72E1` (`updated_by`),
  CONSTRAINT `FK_F41C9B2516FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_F41C9B2542D4BDD0` FOREIGN KEY (`iva_condicion_id`) REFERENCES `afip_iva_condicion` (`id`),
  CONSTRAINT `FK_F41C9B2567707C89` FOREIGN KEY (`localidad_id`) REFERENCES `localidad` (`id`),
  CONSTRAINT `FK_F41C9B257C9FBE9A` FOREIGN KEY (`documento_tipo_id`) REFERENCES `afip_documento_tipo` (`id`),
  CONSTRAINT `FK_F41C9B25DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=486 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `cliente_pago` */

CREATE TABLE `cliente_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recibo_id` int(11) DEFAULT NULL,
  `pago_tipo_id` int(11) DEFAULT NULL,
  `importe` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AF7697592C458692` (`recibo_id`),
  KEY `IDX_AF769759C6690F67` (`pago_tipo_id`),
  KEY `IDX_AF769759DE12AB56` (`created_by`),
  KEY `IDX_AF76975916FE72E1` (`updated_by`),
  CONSTRAINT `FK_AF76975916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_AF7697592C458692` FOREIGN KEY (`recibo_id`) REFERENCES `recibo` (`id`),
  CONSTRAINT `FK_AF769759C6690F67` FOREIGN KEY (`pago_tipo_id`) REFERENCES `pago_tipo` (`id`),
  CONSTRAINT `FK_AF769759DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `comprobante` */

CREATE TABLE `comprobante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `punto_venta` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `total_bonificacion` decimal(16,2) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `total_no_gravado` decimal(16,2) NOT NULL,
  `total_neto` decimal(16,2) NOT NULL,
  `importe_iva_exento` decimal(16,2) NOT NULL,
  `importe_iva` decimal(16,2) NOT NULL,
  `importe_tributos` decimal(16,2) NOT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `obra_social_id` int(11) DEFAULT NULL,
  `obra_social_plan_id` int(11) DEFAULT NULL,
  `total_costo` decimal(16,2) NOT NULL,
  `total_ganancia` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `movimiento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cae_fecha_vencimiento` datetime DEFAULT NULL,
  `cae_numero` bigint(20) DEFAULT NULL,
  `afip_numero` int(11) DEFAULT NULL,
  `tipo_id` int(11) DEFAULT NULL,
  `condicion_venta_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `ordentrabajo_id` int(11) DEFAULT NULL,
  `saldo` decimal(16,2) NOT NULL,
  `pendiente` decimal(16,2) NOT NULL,
  `ordentrabajocontactologia_id` int(11) DEFAULT NULL,
  `cliente_razon_social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cliente_documento_tipo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cliente_documento_numero` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cliente_domicilio` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cliente_localidad` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cliente_iva_condicion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_55DEEE82CB305D73` (`proveedor_id`),
  KEY `IDX_55DEEE82DE734E51` (`cliente_id`),
  KEY `IDX_55DEEE82A9276E6C` (`tipo_id`),
  KEY `IDX_55DEEE821C97F2C6` (`condicion_venta_id`),
  KEY `IDX_55DEEE82279A5D5E` (`sucursal_id`),
  KEY `IDX_55DEEE826D8BE9D2` (`obra_social_id`),
  KEY `IDX_55DEEE825BEDAD82` (`obra_social_plan_id`),
  KEY `IDX_55DEEE827D8D9FA3` (`ordentrabajo_id`),
  KEY `IDX_55DEEE82BBD33588` (`ordentrabajocontactologia_id`),
  KEY `IDX_55DEEE82DE12AB56` (`created_by`),
  KEY `IDX_55DEEE8216FE72E1` (`updated_by`),
  CONSTRAINT `FK_55DEEE8216FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_55DEEE821C97F2C6` FOREIGN KEY (`condicion_venta_id`) REFERENCES `afip_condicion_venta` (`id`),
  CONSTRAINT `FK_55DEEE82279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_55DEEE825BEDAD82` FOREIGN KEY (`obra_social_plan_id`) REFERENCES `obra_social_plan` (`id`),
  CONSTRAINT `FK_55DEEE826D8BE9D2` FOREIGN KEY (`obra_social_id`) REFERENCES `obra_social` (`id`),
  CONSTRAINT `FK_55DEEE827D8D9FA3` FOREIGN KEY (`ordentrabajo_id`) REFERENCES `orden_trabajo` (`id`),
  CONSTRAINT `FK_55DEEE82A9276E6C` FOREIGN KEY (`tipo_id`) REFERENCES `afip_comprobante_tipo` (`id`),
  CONSTRAINT `FK_55DEEE82BBD33588` FOREIGN KEY (`ordentrabajocontactologia_id`) REFERENCES `orden_trabajo_contactologia` (`id`),
  CONSTRAINT `FK_55DEEE82CB305D73` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`),
  CONSTRAINT `FK_55DEEE82DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_55DEEE82DE734E51` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `comprobante_detalle` */

CREATE TABLE `comprobante_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comprobante_id` int(11) DEFAULT NULL,
  `articulo_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `total_no_gravado` decimal(16,2) NOT NULL,
  `total_neto` decimal(16,2) NOT NULL,
  `importe_iva_exento` decimal(16,2) NOT NULL,
  `importe_iva` decimal(16,2) NOT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `precio_costo` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `precio_venta` decimal(16,2) NOT NULL,
  `importe_ganancia` decimal(16,2) NOT NULL,
  `precio_unitario` decimal(16,2) NOT NULL,
  `movimiento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `porcentaje_bonificacion` decimal(16,2) NOT NULL,
  `importe_bonificacion` decimal(16,2) NOT NULL,
  `porcentaje_iva` decimal(16,2) NOT NULL,
  `porcentaje_ganancia` decimal(16,2) NOT NULL,
  `precio_neto` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6E37FF4425662B3A` (`comprobante_id`),
  KEY `IDX_6E37FF442DBC2FC9` (`articulo_id`),
  KEY `IDX_6E37FF44DE12AB56` (`created_by`),
  KEY `IDX_6E37FF4416FE72E1` (`updated_by`),
  CONSTRAINT `FK_6E37FF4416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_6E37FF4425662B3A` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_6E37FF442DBC2FC9` FOREIGN KEY (`articulo_id`) REFERENCES `articulo` (`id`),
  CONSTRAINT `FK_6E37FF44DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `funcion` */

CREATE TABLE `funcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `accion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `localidad` */

CREATE TABLE `localidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provincia_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `codigo_postal` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4F68E0104E7121AF` (`provincia_id`),
  KEY `IDX_4F68E010DE12AB56` (`created_by`),
  KEY `IDX_4F68E01016FE72E1` (`updated_by`),
  CONSTRAINT `FK_4F68E01016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_4F68E0104E7121AF` FOREIGN KEY (`provincia_id`) REFERENCES `provincia` (`id`),
  CONSTRAINT `FK_4F68E010DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15327 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `medico` */

CREATE TABLE `medico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documento_tipo_id` int(11) DEFAULT NULL,
  `localidad_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `documento_numero` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `matricula` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contacto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_34E5914C7C9FBE9A` (`documento_tipo_id`),
  KEY `IDX_34E5914C67707C89` (`localidad_id`),
  KEY `IDX_34E5914CDE12AB56` (`created_by`),
  KEY `IDX_34E5914C16FE72E1` (`updated_by`),
  CONSTRAINT `FK_34E5914C16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_34E5914C67707C89` FOREIGN KEY (`localidad_id`) REFERENCES `localidad` (`id`),
  CONSTRAINT `FK_34E5914C7C9FBE9A` FOREIGN KEY (`documento_tipo_id`) REFERENCES `afip_documento_tipo` (`id`),
  CONSTRAINT `FK_34E5914CDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `obra_social` */

CREATE TABLE `obra_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA939553DE12AB56` (`created_by`),
  KEY `IDX_AA93955316FE72E1` (`updated_by`),
  CONSTRAINT `FK_AA93955316FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_AA939553DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `obra_social_plan` */

CREATE TABLE `obra_social_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obra_social_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DEEFDD216D8BE9D2` (`obra_social_id`),
  KEY `IDX_DEEFDD21DE12AB56` (`created_by`),
  KEY `IDX_DEEFDD2116FE72E1` (`updated_by`),
  CONSTRAINT `FK_DEEFDD2116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_DEEFDD216D8BE9D2` FOREIGN KEY (`obra_social_id`) REFERENCES `obra_social` (`id`),
  CONSTRAINT `FK_DEEFDD21DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_pago` */

CREATE TABLE `orden_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal_id` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `numero` int(11) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `disponible` decimal(16,2) NOT NULL,
  `saldo` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CD3AA7C9279A5D5E` (`sucursal_id`),
  KEY `IDX_CD3AA7C9CB305D73` (`proveedor_id`),
  KEY `IDX_CD3AA7C9DE12AB56` (`created_by`),
  KEY `IDX_CD3AA7C916FE72E1` (`updated_by`),
  CONSTRAINT `FK_CD3AA7C916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_CD3AA7C9279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_CD3AA7C9CB305D73` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`),
  CONSTRAINT `FK_CD3AA7C9DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_pago_comprobante` */

CREATE TABLE `orden_pago_comprobante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_pago_id` int(11) DEFAULT NULL,
  `comprobante_id` int(11) DEFAULT NULL,
  `importe` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2871E7D8DF759BAE` (`orden_pago_id`),
  KEY `IDX_2871E7D825662B3A` (`comprobante_id`),
  KEY `IDX_2871E7D8DE12AB56` (`created_by`),
  KEY `IDX_2871E7D816FE72E1` (`updated_by`),
  CONSTRAINT `FK_2871E7D816FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_2871E7D825662B3A` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_2871E7D8DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_2871E7D8DF759BAE` FOREIGN KEY (`orden_pago_id`) REFERENCES `orden_pago` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_trabajo` */

CREATE TABLE `orden_trabajo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `comprobante_id` int(11) DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `otros_trabajos` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `taller_id` int(11) DEFAULT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `fecha_receta` datetime DEFAULT NULL,
  `fecha_taller_pedido` datetime DEFAULT NULL,
  `fecha_talle_entrega` datetime DEFAULT NULL,
  `armado` tinyint(1) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `total_bonificacion` decimal(16,2) NOT NULL,
  `entrega` decimal(16,2) NOT NULL,
  `saldo` decimal(16,2) NOT NULL,
  `lejos_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `lejos_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `lejos_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `ojo_derecho_dnp` decimal(16,2) NOT NULL,
  `ojo_izquierdo_dnp` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `antes_ojo_derecho_dnp` decimal(16,2) NOT NULL,
  `antes_ojo_izquierdo_dnp` decimal(16,2) NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `medico_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4158A024DE734E51` (`cliente_id`),
  KEY `IDX_4158A02425662B3A` (`comprobante_id`),
  KEY `IDX_4158A0246DC343EA` (`taller_id`),
  KEY `IDX_4158A024279A5D5E` (`sucursal_id`),
  KEY `IDX_4158A024A7FB1C0C` (`medico_id`),
  KEY `IDX_4158A024DE12AB56` (`created_by`),
  KEY `IDX_4158A02416FE72E1` (`updated_by`),
  CONSTRAINT `FK_4158A02416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_4158A02425662B3A` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_4158A024279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_4158A0246DC343EA` FOREIGN KEY (`taller_id`) REFERENCES `taller` (`id`),
  CONSTRAINT `FK_4158A024A7FB1C0C` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`id`),
  CONSTRAINT `FK_4158A024DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_4158A024DE734E51` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_trabajo_contactologia` */

CREATE TABLE `orden_trabajo_contactologia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `comprobante_id` int(11) DEFAULT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `fecha_receta` datetime DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diagnostico` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motivacion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uso_lc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total` decimal(16,2) NOT NULL,
  `total_bonificacion` decimal(16,2) NOT NULL,
  `entrega` decimal(16,2) NOT NULL,
  `saldo` decimal(16,2) NOT NULL,
  `rc_ojo_derecho_horizontal` decimal(16,2) NOT NULL,
  `rc_ojo_izquierdo_horizontal` decimal(16,2) NOT NULL,
  `rc_ojo_derecho_vertical` decimal(16,2) NOT NULL,
  `rc_ojo_izquierdo_vertical` decimal(16,2) NOT NULL,
  `ojo_derecho_curvas` decimal(16,2) NOT NULL,
  `ojo_izquierdo_curvas` decimal(16,2) NOT NULL,
  `ojo_derecho_diametro` decimal(16,2) NOT NULL,
  `ojo_izquierdo_diametro` decimal(16,2) NOT NULL,
  `ojo_derecho_av` decimal(16,2) NOT NULL,
  `ojo_izquierdo_av` decimal(16,2) NOT NULL,
  `ojo_derecho_caracteristicas` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ojo_izquierdo_caracteristicas` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lejos_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `lejos_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `lejos_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `lejos_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `cerca_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `cerca_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `ojo_derecho_dnp` decimal(16,2) NOT NULL,
  `ojo_izquierdo_dnp` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `antes_lejos_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_eje` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_eje` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_cilindro` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_cilindro` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_derecho_esfera` decimal(16,2) NOT NULL,
  `antes_cerca_ojo_izquierdo_esfera` decimal(16,2) NOT NULL,
  `antes_ojo_derecho_dnp` decimal(16,2) NOT NULL,
  `antes_ojo_izquierdo_dnp` decimal(16,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `medico_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_56DAF2F1DE734E51` (`cliente_id`),
  KEY `IDX_56DAF2F125662B3A` (`comprobante_id`),
  KEY `IDX_56DAF2F1279A5D5E` (`sucursal_id`),
  KEY `IDX_56DAF2F1A7FB1C0C` (`medico_id`),
  KEY `IDX_56DAF2F1DE12AB56` (`created_by`),
  KEY `IDX_56DAF2F116FE72E1` (`updated_by`),
  CONSTRAINT `FK_56DAF2F116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_56DAF2F125662B3A` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_56DAF2F1279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_56DAF2F1A7FB1C0C` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`id`),
  CONSTRAINT `FK_56DAF2F1DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_56DAF2F1DE734E51` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_trabajo_contactologia_detalle` */

CREATE TABLE `orden_trabajo_contactologia_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_trabajo_contactologia_id` int(11) DEFAULT NULL,
  `articulo_id` int(11) DEFAULT NULL,
  `importe_bonificacion` decimal(16,2) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `precio_venta` decimal(16,2) NOT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `tipo_cristal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `porcentaje_bonificacion` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_30A7A74B92CB6474` (`orden_trabajo_contactologia_id`),
  KEY `IDX_30A7A74B2DBC2FC9` (`articulo_id`),
  KEY `IDX_30A7A74BDE12AB56` (`created_by`),
  KEY `IDX_30A7A74B16FE72E1` (`updated_by`),
  CONSTRAINT `FK_30A7A74B16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_30A7A74B2DBC2FC9` FOREIGN KEY (`articulo_id`) REFERENCES `articulo` (`id`),
  CONSTRAINT `FK_30A7A74B92CB6474` FOREIGN KEY (`orden_trabajo_contactologia_id`) REFERENCES `orden_trabajo_contactologia` (`id`),
  CONSTRAINT `FK_30A7A74BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `orden_trabajo_detalle` */

CREATE TABLE `orden_trabajo_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_trabajo_id` int(11) DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `estado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `articulo_id` int(11) DEFAULT NULL,
  `importe_bonificacion` decimal(16,2) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `precio_venta` decimal(16,2) NOT NULL,
  `tipo_cristal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `porcentaje_bonificacion` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4BA8D9EC92B24E62` (`orden_trabajo_id`),
  KEY `IDX_4BA8D9EC2DBC2FC9` (`articulo_id`),
  KEY `IDX_4BA8D9ECDE12AB56` (`created_by`),
  KEY `IDX_4BA8D9EC16FE72E1` (`updated_by`),
  CONSTRAINT `FK_4BA8D9EC16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_4BA8D9EC2DBC2FC9` FOREIGN KEY (`articulo_id`) REFERENCES `articulo` (`id`),
  CONSTRAINT `FK_4BA8D9EC92B24E62` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `orden_trabajo` (`id`),
  CONSTRAINT `FK_4BA8D9ECDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `pago_tipo` */

CREATE TABLE `pago_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_567222FBDE12AB56` (`created_by`),
  KEY `IDX_567222FB16FE72E1` (`updated_by`),
  CONSTRAINT `FK_567222FB16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_567222FBDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `proveedor` */

CREATE TABLE `proveedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `documento_numero` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `localidad_id` int(11) DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contacto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `iva_condicion_id` int(11) DEFAULT NULL,
  `documento_tipo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_16C068CE2DCBF3BC` (`documento_numero`),
  KEY `IDX_16C068CE67707C89` (`localidad_id`),
  KEY `IDX_16C068CE42D4BDD0` (`iva_condicion_id`),
  KEY `IDX_16C068CE7C9FBE9A` (`documento_tipo_id`),
  KEY `IDX_16C068CEDE12AB56` (`created_by`),
  KEY `IDX_16C068CE16FE72E1` (`updated_by`),
  CONSTRAINT `FK_16C068CE16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_16C068CE42D4BDD0` FOREIGN KEY (`iva_condicion_id`) REFERENCES `afip_iva_condicion` (`id`),
  CONSTRAINT `FK_16C068CE67707C89` FOREIGN KEY (`localidad_id`) REFERENCES `localidad` (`id`),
  CONSTRAINT `FK_16C068CE7C9FBE9A` FOREIGN KEY (`documento_tipo_id`) REFERENCES `afip_documento_tipo` (`id`),
  CONSTRAINT `FK_16C068CEDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `proveedor_pago` */

CREATE TABLE `proveedor_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_pago_id` int(11) DEFAULT NULL,
  `pago_tipo_id` int(11) DEFAULT NULL,
  `importe` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8C7A85E4DF759BAE` (`orden_pago_id`),
  KEY `IDX_8C7A85E4C6690F67` (`pago_tipo_id`),
  KEY `IDX_8C7A85E4DE12AB56` (`created_by`),
  KEY `IDX_8C7A85E416FE72E1` (`updated_by`),
  CONSTRAINT `FK_8C7A85E416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_8C7A85E4C6690F67` FOREIGN KEY (`pago_tipo_id`) REFERENCES `pago_tipo` (`id`),
  CONSTRAINT `FK_8C7A85E4DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_8C7A85E4DF759BAE` FOREIGN KEY (`orden_pago_id`) REFERENCES `orden_pago` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `provincia` */

CREATE TABLE `provincia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D39AF213DE12AB56` (`created_by`),
  KEY `IDX_D39AF21316FE72E1` (`updated_by`),
  CONSTRAINT `FK_D39AF21316FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_D39AF213DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `recibo` */

CREATE TABLE `recibo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `numero` int(11) NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `disponible` decimal(16,2) NOT NULL,
  `saldo` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `observaciones` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42A928FA279A5D5E` (`sucursal_id`),
  KEY `IDX_42A928FADE734E51` (`cliente_id`),
  KEY `IDX_42A928FADE12AB56` (`created_by`),
  KEY `IDX_42A928FA16FE72E1` (`updated_by`),
  CONSTRAINT `FK_42A928FA16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_42A928FA279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_42A928FADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_42A928FADE734E51` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `recibo_comprobante` */

CREATE TABLE `recibo_comprobante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recibo_id` int(11) DEFAULT NULL,
  `comprobante_id` int(11) DEFAULT NULL,
  `importe` decimal(16,2) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3CAF3E012C458692` (`recibo_id`),
  KEY `IDX_3CAF3E0125662B3A` (`comprobante_id`),
  KEY `IDX_3CAF3E01DE12AB56` (`created_by`),
  KEY `IDX_3CAF3E0116FE72E1` (`updated_by`),
  CONSTRAINT `FK_3CAF3E0116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_3CAF3E0125662B3A` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`),
  CONSTRAINT `FK_3CAF3E012C458692` FOREIGN KEY (`recibo_id`) REFERENCES `recibo` (`id`),
  CONSTRAINT `FK_3CAF3E01DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `rol` */

CREATE TABLE `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E553F37DE12AB56` (`created_by`),
  KEY `IDX_E553F3716FE72E1` (`updated_by`),
  CONSTRAINT `FK_E553F3716FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_E553F37DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `rol_funcion` */

CREATE TABLE `rol_funcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) DEFAULT NULL,
  `funcion_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6805290D4BAB96C` (`rol_id`),
  KEY `IDX_6805290D8C185C36` (`funcion_id`),
  CONSTRAINT `FK_6805290D4BAB96C` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`),
  CONSTRAINT `FK_6805290D8C185C36` FOREIGN KEY (`funcion_id`) REFERENCES `funcion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `sucursal` */

CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E99C6D563A909126` (`nombre`),
  KEY `IDX_E99C6D56DE12AB56` (`created_by`),
  KEY `IDX_E99C6D5616FE72E1` (`updated_by`),
  CONSTRAINT `FK_E99C6D5616FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_E99C6D56DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `taller` */

CREATE TABLE `taller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_139F4584DE12AB56` (`created_by`),
  KEY `IDX_139F458416FE72E1` (`updated_by`),
  CONSTRAINT `FK_139F458416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_139F4584DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `usuario` */

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `usuario` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `login_ultimo` datetime NOT NULL,
  `login_cantidad` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2265B05D2265B05D` (`usuario`),
  KEY `IDX_2265B05D279A5D5E` (`sucursal_id`),
  KEY `IDX_2265B05D4BAB96C` (`rol_id`),
  KEY `IDX_2265B05DDE12AB56` (`created_by`),
  KEY `IDX_2265B05D16FE72E1` (`updated_by`),
  CONSTRAINT `FK_2265B05D16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_2265B05D279A5D5E` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  CONSTRAINT `FK_2265B05D4BAB96C` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`),
  CONSTRAINT `FK_2265B05DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
