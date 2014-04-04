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

function supprimer_objet($type,$id){
	return true;
}

function supprimer_rubrique($liste_id) {
	spip_log("Suppression de la rubrique : $value.",'vider_rubrique');
	$supprimer_rubrique = charger_fonction('supprimer_rubrique','action');

	$les_id = array_reverse(explode(",",$liste_id));
	foreach ($les_id as $key => $value) {
		$supprimer_rubrique($value);
	}

	return true;
}