<?php

/***************************************************************************\
 * Plugin Vider Rubrique pour Spip 3.0
 * Licence GPL (c) 2012 - Apsulis
 * Suppression de tout le contenu d'une rubrique
 *
\***************************************************************************/


function vider_rubrique_objet_poubelle($objet,$id_objet,$statut){
	$c = array('statut' => $statut);

	include_spip('action/editer_objet');
	if ($err=objet_instituer($objet, $id_objet, $c))
		$res = array('message_erreur'=>$err,'objet'=>$objet);
	else {
		$res = array('message_ok'=>_T('info_modification_enregistree'));
	}
	return $res;
}