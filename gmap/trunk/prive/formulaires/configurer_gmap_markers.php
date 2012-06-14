<?php
/*
 * Géolocalisation et cartographie
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2012 - licence GNU/GPL
 *
 * Page de paramétrage principale du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_db_utils');

function _gmap_get_types_from_request() {

	// Récupérer les tableaux
	// Il y a une petite bidouille pour éviter des problèmes d'encodage :
	// Je n'ai pas compris pourquoi, les zones d'édition qui sont nommées par un tableau
	// présentent un problème d'encodage des accents (site en iso-8859-1), alors que des
	// zones d'édition nommées simplement ne le présentent pas, donc j'ai transformé les
	// tableaux en noms suffixés de l'identifiant pour les champs qui peuvent contenir
	// des accents...
	// Si quelqu'un a une meilleure idée...
	
	// Récupérer les tableaux
	$ids = _request("id");
	$objets = _request("objet");
	$visibles = _request("visible");
	$priorites = _request("priorite");
	$operations = _request("oper");
	$nb = count($ids);
	if ((count($objets) != $nb) || (count($visibles) != $nb) || (count($priorites) != $nb) || (count($operations) != $nb))
		return null;

	// Traiter tout ça pour récupérer une structure de données plus sympa
	$types = array();
	$noms = array();
	$descriptifs = array();
	for ($index = 0; $index < $nb; $index++)
	{
		if ($ids[$index] === 'template')
			continue;
		$type = array(
			'nom' => _request("nom_".$ids[$index]),
			'descriptif' => _request("descriptif_".$ids[$index]),
			'objet' => $objets[$index],
			'visible' => $visibles[$index],
			'priorite' => $priorites[$index],
			'operation' => $operations[$index],
		);
		if (!strlen($type['nom']) || !$type['descriptif'])
			return null;
		$types[$ids[$index]] = $type;
	}
	
	return $types;
}

function _gmap_get_types_from_database() {

	$dbTypes = gmap_get_all_types();
	$types = array();
	foreach ($dbTypes as $type) {
		$types[$type['id']] = array(
			'nom' => $type['nom'],
			'descriptif' => $type['descriptif'],
			'objet' => $type['objet'],
			'visible' => $type['visible'],
			'priorite' => $type['priorite'],
			'nb_points' => $type['nb_points'],
			'editable' => ((($type['nom'] == 'defaut') || ($type['nom'] == 'centre')) ? false : true),
		);
	}
	
	return $types;
}

function formulaire_configurer_gmap_markers_initialiser_dist() {

}

function formulaires_configurer_gmap_markers_charger_dist(){

	$valeurs = array();

	$valeurs['types'] = _gmap_get_types_from_database();
	
	return $valeurs;
}

function formulaires_configurer_gmap_markers_verifier_dist(){

	$erreurs = array();
	
	$types = _gmap_get_types_from_request();
	if (!$types || !is_array($types))
		$erreurs['message_erreur'] = _T('gmap:erreur_logique_formulaire');

	$names = array();
	foreach ($types as $id => $type) {
		if (isset($names[$type['nom']])) {
			$erreurs['message_erreur'] = _T('gmap:erreur_type_nom_duplique');
			return $erreurs;
		}
		$names[$type['nom']] = true;
	}
	
	return $erreurs;
}

function formulaires_configurer_gmap_markers_traiter_dist(){

	$types = _gmap_get_types_from_request();
	if (!$types || !is_array($types))
		return array('message_erreur'=>_T('gmap:erreur_logique_formulaire'),'editable'=>true);

	// Décomposer en tableau de ce qui est à créer, détruire ou modifier
	foreach ($types as $id => $type) {
		if ($type['operation'] === "delete")
			gmap_delete_type($id);
		else if ($type['operation'] === "create")
			gmap_cree_type($type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);
		else if ($type['operation'] === "update")
			gmap_update_type($id, $type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);
	}
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>
