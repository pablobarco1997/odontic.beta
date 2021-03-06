CREATE TABLE `tab_admin_pacientes` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(70) DEFAULT NULL,
  `apellido` varchar(70) DEFAULT NULL,
  `ruc_ced` varchar(45) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `fk_convenio` int(11) DEFAULT NULL,
  `numero_interno` varchar(55) DEFAULT NULL,
  `sexo` varchar(45) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fk_ciudad` varchar(45) DEFAULT NULL,
  `comuna` varchar(45) DEFAULT NULL,
  `direccion` varchar(45) DEFAULT NULL,
  `telefono_fijo` varchar(45) DEFAULT NULL,
  `telefono_movil` varchar(45) DEFAULT NULL,
  `actividad_profecion` varchar(45) DEFAULT NULL,
  `empleador` varchar(45) DEFAULT NULL,
  `observacion` varchar(700) DEFAULT NULL,
  `apoderado` varchar(45) DEFAULT NULL,
  `referencia` varchar(45) DEFAULT NULL,
  `fk_tipo` int(11) DEFAULT NULL,
  `tms` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` varchar(45) DEFAULT 'A',
  `icon` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8
;
