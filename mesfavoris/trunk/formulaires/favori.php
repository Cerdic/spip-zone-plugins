<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Charger du formulaire CVT 
 * Les arguments sont déterminés par la fonction balise_FORMULAIRE_FAVORI_stat()
 * 
 * @param string $objet
 * @param int $id_objet
 * @param string $categorie
 * @return array
 */
function formulaires_favori_charger_dist($objet, $id_objet, $categorie='') {
	$contexte = array(
		'editable'     => true,
		'_deja_favori' => false,
		'_objet'       => $objet,
		'categorie'    => $categorie,
	);
	
	if (defined('_MESFAVORIS_PERSO_ID_OBJET')) {
		$contexte['_id_objet'] = $id_objet;
	}
	
	if (!isset($GLOBALS['visiteur_session']['statut'])) {
		$contexte['editable'] = false;
	}
	else {
		include_spip('inc/mesfavoris');
		$favori = mesfavoris_trouver($id_objet, $objet, $GLOBALS['visiteur_session']['id_auteur'], $categorie);
		
		if ($favori) {
			$contexte['_deja_favori'] = true;
		}
	}
	
	return $contexte;
}

/**
 * Traiter le formulaire CVT
 * 
 * @param  string $objet     
 * @param  int $id_objet  
 * @param  string $categorie 
 * @return array            
 */
function formulaires_favori_traiter_dist($objet, $id_objet, $categorie='') {
	$res = array('message_ok'=>' ');
	
	if ($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])) {
		include_spip('inc/mesfavoris');
		
		if (!is_null(_request('ajouter'))) {
			$res['id_favori'] = mesfavoris_ajouter($id_objet, $objet, $id_auteur, $categorie);
		}
		
		if (!is_null(_request('retirer'))) {
			mesfavoris_supprimer(array(
				'id_objet'  => $id_objet,
				'objet'     => $objet,
				'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
				'categorie' => $categorie,
			));
		}
	}
	
	return $res;
}
