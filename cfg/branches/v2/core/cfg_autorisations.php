<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_autoriser(){}

// autorisation de voir le bouton de config
function autoriser_cfg_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer', $type, $id, $qui, $opt);
}
?>
