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
 * Chargement/rechargement du formulaire 
 * Les arguments sont determines par la fonction balise_FORMULAIRE_EDITER_FAVORI_CATEGORI_stat()
 * 
 * @param string $objet
 * @param int $id_objet
 * @param string $categorie
 * @return array
 */
function formulaires_editer_favori_categorie_charger_dist($objet='', $id_objet=0, $categorie='') {
	$id_auteur = intval($GLOBALS['visiteur_session']['id_auteur']);
	$row = sql_fetsel(
		'categorie',
		'spip_favoris',
		array(
			'id_auteur = ' . $id_auteur,
			'id_objet = ' . intval($id_objet),
			'objet = ' . sql_quote($objet),
		)
	);
	$contexte = array(
		'editable'     => $id_auteur?true:false,
		'_objet'       => $objet,
		'_id_objet'    => $id_objet,
		'id_auteur'    => $id_auteur,
		'categorie'    => $categorie?$categorie:$row['categorie'],
	);

	if ( defined('_MESFAVORIS_PERSO_ID_OBJET') ) {
		$contexte['_id_objet'] = $id_objet;
	}

	return $contexte;
}

/**
 * Traitement/enregistrement du formulaire
 * 
 * @param  string $objet
 * @param  int $id_objet
 * @param  string $categorie
 * @return array
 */
function formulaires_editer_favori_categorie_traiter_dist($objet='', $id_objet=0, $categorie='') {
	$res = array();
	if ( $id_auteur = intval($GLOBALS['visiteur_session']['id_auteur']) ) {
		include_spip('inc/mesfavoris');
		if ( !is_null(_request('categoriser')) ) {
			$res['id_favori'] = mesfavoris_categoriser($id_objet, $objet, 
				$id_auteur, _request('categorie') );
			if ( $res['id_favori'] ) {
				$res['message_ok'] = ' Huray, updated: '.$res['id_favori'] ;
				// merci la liste pour cette astuce geniale 
				// (mais helas JS dependante) Bon, remplacer
				// la balise P par une balise DIV et utiliser
				// la balise ENV** pour preserver le script !
				$res['message_ok'] .= '<script type="text/javascript"> ' ;
				$res['message_ok'] .= ' ;jQuery(function($){ ' ;
				$res['message_ok'] .= '   $("#mesfavoris_selection_' .
					$id_auteur . '").ajaxReload(); ' ;
				$res['message_ok'] .= ' }) ' ;
				$res['message_ok'] .= '</script> ' ;
			}
			else {
				$res['message_erreur'] = ' Oops, something went wrong ' ;
			}
		}
	}
	return $res;
}
