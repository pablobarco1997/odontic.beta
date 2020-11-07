CREATE TABLE `tab_pagos_independ_pacientes_cab` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `fk_tipopago` int(11) DEFAULT 0,
  `observacion` varchar(700) DEFAULT NULL,
  `monto` double DEFAULT 0,
  `n_fact_boleta` varchar(25) DEFAULT NULL,
  `fk_plantram` int(11) DEFAULT 0,
  `fk_paciente` int(11) DEFAULT 0,
  `id_login` int(11) DEFAULT 0,
  PRIMARY KEY (`rowid`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8
;
