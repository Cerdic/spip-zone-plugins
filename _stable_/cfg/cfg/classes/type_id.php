<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function cfg_verifier_type_id($champ, &$cfg){
	if (!preg_match('#^[a-z_]\w*$#', $cfg->val[$champ])){
		$cfg->ajouter_erreur(_T('cfg:erreur_type_id', array('champ'=>$champ)));
	}
	return true;
}

?>
