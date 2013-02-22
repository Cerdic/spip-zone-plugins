<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

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
 * @param array $options TODO à documenter, voir avec l'auteur de http://zone.spip.org/trac/spip-zone/changeset/53906
 */
function formulaires_editer_gis_charger_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){
	$valeurs = formulaires_editer_objet_charger('gis', $id_gis, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$valeurs['ajaxload'] = $ajaxload;
    /* Traitement des options */
	/* peut etre a envoyer dans une fonction generique de verification des options */
	if (is_array($options)) {
    	if (!$valeurs['lat'] and is_numeric($options['lat']))
	        $valeurs['lat']=$options['lat'];
	    if (!$valeurs['lon'] and is_numeric($options['lon']))
        	$valeurs['lon']=$options['lon'];
    	if (!$valeurs['zoom'] and is_numeric($options['zoom']) && intval($options['zoom'])==$options['zoom'])
	        $valeurs['zoom']=$options['zoom'];
		/* Bounding Box */
	    if (is_numeric($options['sw_lat']))
        	$valeurs['sw_lat']=$options['sw_lat'];
	    if (is_numeric($options['sw_lon']))
        	$valeurs['sw_lon']=$options['sw_lon'];
	    if (is_numeric($options['ne_lat']))
        	$valeurs['ne_lat']=$options['ne_lat'];
	    if (is_numeric($options['ne_lon']))
        	$valeurs['ne_lon']=$options['ne_lon'];
	}
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
 * @param array $options ???
 */
function formulaires_editer_gis_verifier_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){
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
 * @param array $options ???
 */
function formulaires_editer_gis_traiter_dist($id_gis='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){
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
