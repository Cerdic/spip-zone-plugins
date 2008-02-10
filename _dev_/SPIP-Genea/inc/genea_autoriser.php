<?php

/*******************************************************************
 *
 * Copyright (c) 2008
 * Xavier BUROT
 * fichier : inc/genea_autoriser
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

function autoriser_genea_voir_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut'] == '0minirezo' AND !$qui['restreint'] AND genea_verifier_droit_rubrique($id));
}

function autoriser_genea_voirfiche_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut'] == '0minirezo' OR $qui['statut'] == '1comite' AND genea_verifier_droit_rubrique($id));
}

function genea_verifier_droit_rubrique($id_genea){
	global $table_prefix;
	$ctrl = true; // Accepte par defaut l'acces
	if ($id_genea) {
		$res = spip_query("SELECT id_rubrique FROM " . $table_prefix . "_genea WHERE id_genea=$id_genea LIMIT 1");
		if ($res) $ctrl = autoriser('voir', 'rubrique', $res['id_rubrique']);
	}
	return $crtl;
}
?>