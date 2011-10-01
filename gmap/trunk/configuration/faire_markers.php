<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Import des données gis / geomap
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');

//
// Paramétrage des objets géolocalisables
//

function configuration_faire_markers_dist()
{
	// Initialisation retour
	$result = "";
	$msg = "";
	
	// Récupérer les tableaux
	// Il y a une petite bidouille pour éviter des problèmes d'encodage :
	// Je n'ai pas compris pourquoi, les zone d'édition qui sont nomées par un tableau
	// présentent un problème d'encodage des accent (site en iso-8859-1), alors que des
	// zones d'édition nommées simplement ne le présentent pas, donc j'ai transformer les
	// tableaux en nom suffixé de l'identifiant pour les champs qui peuvent contenir
	// des accents...
	// Si quelqu'un a une meilleure idée...
	$ids = _request("id");
	$nb = count($ids);
	$noms = array();
	$descriptifs = array();
	for ($index = 0; $index < $nb; $index++)
	{
		$noms[$index] = _request("nom_".$ids[$index]);
		$descriptifs[$index] = _request("descriptif_".$ids[$index]);
	}
	$objets = _request("objet");
	$visibles = _request("visible");
	$priorites = _request("priorite");
	$operations = _request("oper");
	if ((count($objets) != $nb) || (count($visibles) != $nb) || (count($priorites) != $nb) || (count($operations) != $nb))
		return gmap_ajoute_msg($result, _T('gmap:invalid_form_data'));

	// Décomposer en tableau de ce qui est à créer, détruire ou modifier
	$updTypes = array();
	$newTypes = array();
	for ($index = 0; $index < $nb; $index++)
	{
		if ($ids[$index] == "template")
			continue;
		if ($operations[$index] == "delete")
			gmap_delete_type($ids[$index]);
		else
		{
			$type = array(
				"id" => $ids[$index],
				"objet" => $objets[$index],
				"nom" => $noms[$index],
				"descriptif" => $descriptifs[$index],
				"visible" => $visibles[$index],
				"priorite" => $priorites[$index]);
			if ($operations[$index] == "create")
				$newTypes[] = $type;
			else if ($operations[$index] == "delete")
				$delTypes[] = $type;
			else if ($operations[$index] == "update")
				$updTypes[] = $type;
		}
	}
	
	// Mettre à jour
	foreach ($updTypes as $type)
		gmap_update_type($type['id'], $type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);
	
	// Créer
	foreach ($newTypes as $type)
		gmap_cree_type($type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);

	// Message de retour
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
