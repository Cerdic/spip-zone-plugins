<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2010 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

/**
 *
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function formulaires_favori_charger_dist($objet, $id_objet){
	$valeur = array(
		'editable'=>true,
		'_deja_favori'=>false
	);
	if (!isset($GLOBALS['visiteur_session']['id_auteur'])){
		$valeur['editable'] = false;
	}
	else {
		include_spip('inc/mesfavoris');
		$favori = mesfavoris_trouver($id_objet,$objet,$GLOBALS['visiteur_session']['id_auteur']);
		if ($favori['id_favori']){
			$valeur['_deja_favori'] = true;
		}
	}
	return $valeur;
}

function formulaires_favori_traiter_dist($objet, $id_objet){
	$res = array();
	if ($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])){
		include_spip('inc/mesfavoris');
		if (_request('ajouter')){
			mesfavoris_ajouter($id_objet, $objet, $id_auteur);
		}
		if (_request('retirer')){
			mesfavoris_supprimer(array('id_objet'=>$id_objet,'objet'=>$objet,'id_auteur'=>$GLOBALS['visiteur_session']['id_auteur']));
		}
	}
	return $res;
}
?>