<?php

$sql = "CREATE TABLE  IF NOT EXISTS  `association` (
  `keys` int(11) NOT NULL auto_increment,
  `id` int(11) NOT NULL,
  `id_lien` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type_lien` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `descriptif` varchar(255) NOT NULL,
  `obj_option` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL,
  PRIMARY KEY  (`keys`),
  KEY `id` (`id`),
  KEY `id_lien` (`id_lien`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$res = spip_query($sql);


?>