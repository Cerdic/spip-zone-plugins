<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * 
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');
	
// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/cfg_config');

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
$GLOBALS['spip_pipeline']['editer_contenu_formulaire_cfg'] = "";

?>
