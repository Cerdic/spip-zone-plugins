<?php
/* Creation de la base de donnee */

$sql = "CREATE TABLE IF NOT EXISTS `association` (
  `cle` int(11) NOT NULL auto_increment,
  `id` int(11) NOT NULL,
  `id_lien` int(11) NOT NULL,
  `type_id` varchar(20) NOT NULL,
  `type_lien` varchar(20) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `descriptif` varchar(255) NOT NULL,
  `obj_option` varchar(255) NOT NULL,
  PRIMARY KEY  (`cle`),
  KEY `id` (`id`),
  KEY `id_lien` (`id_lien`),
  KEY `type_id` (`type_id`),
  KEY `type_lien` (`type_lien`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$res = spip_query($sql);


/* mise en place des elements/droits pour fast_plugin et spip_ajax  */

include_spip('inc/cfg_config');
ecrire_config("php::fast_plugin/assoc_admin/plugin","assoc");
ecrire_config("php::fast_plugin/assoc_admin/statut","admin");
ecrire_config("php::fast_plugin/assoc_admin/bouton","naviguer,uneimage,association");
ecrire_config("php::spip_ajax/assoc_admin/statut","admin");


?>