<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_verifier_type_idnum($champ, &$cfg){
	if (!is_numeric($cfg->val[$champ])){
		$cfg->ajouter_erreur(_T('cfg:erreur_type_idnum', array('champ'=>$champ)));
	}
	return true;
}

function cfg_pre_traiter_type_idnum($champ, &$cfg){
	$cfg->val[$champ] = intval($cfg->val[$champ]);
	return true;
}

?>
