<?php
/**
 * Formulaire de création et d'édition d'un point géolocalisé
 */

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Chargement des valeurs par défaut du formulaire
 * 
 * @param int|string $id_gis Identifiant numérique du point ou 'new' pour un nouveau
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $ajaxload initialiser la carte à chaque onAjaxLoad()
 */
function formulaires_editer_gis_charger_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui'){
	$valeurs = formulaires_editer_objet_charger('gis', $id_gis, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$valeurs['ajaxload'] = $ajaxload;
	return $valeurs;
}

/**
 * Vérification des valeurs du formulaire
 * 
 * 4 champs sont obligatoires :
 * -* Son titre
 * -* Sa latitude
 * -* Sa longitude
 * -* Son niveau de zoom
 * 
 * @param int|string $id_gis Identifiant numérique du point ou 'new' pour un nouveau
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $ajaxload initialiser la carte à chaque onAjaxLoad()
 */
function formulaires_editer_gis_verifier_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui'){
	$erreurs = formulaires_editer_objet_verifier('gis', $id_gis,array('titre','lat','lon','zoom'));
	return $erreurs;
}

/**
 * Traitement des valeurs du formulaire
 * 
 * @param int|string $id_gis Identifiant numérique du point ou 'new' pour un nouveau
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $ajaxload initialiser la carte à chaque onAjaxLoad()
 */
function formulaires_editer_gis_traiter_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui'){
	if (_request('supprimer')){
		include_spip('action/editer_gis');
		supprimer_gis($id_gis);
		$id_table_objet = id_table_objet($objet);
		if ($retour)
			$res['redirect'] = parametre_url($retour,$id_table_objet,$id_objet);
		return $res;
	} else {
		return formulaires_editer_objet_traiter('gis', $id_gis, '', '', $retour, '');
	}
}

?>