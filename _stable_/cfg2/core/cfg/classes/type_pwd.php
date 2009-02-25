<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_verifier_type_pwd($champ, &$cfg) {
	if (strlen($cfg->val[$champ]) < 5){
		$cfg->ajouter_erreur($champ, _T('cfg:erreur_type_pwd', array('champ'=>$champ)));
	}
	return true;
}


?>
