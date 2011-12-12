<?php

/*
 * Plugin CFG2 pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
	
// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/config'); // spip 2.3+
include_spip('inc/cfg_config');

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
$GLOBALS['spip_pipeline']['editer_contenu_formulaire_cfg'] = "";

?>
