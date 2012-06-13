<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * �dition des types de marqueurs
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');

//
// Param�trage des objets g�olocalisables
//

function configuration_faire_markers_dist()
{
	// Initialisation retour
	$result = "";
	$msg = "";
	
	// R�cup�rer les tableaux
	// Il y a une petite bidouille pour �viter des probl�mes d'encodage :
	// Je n'ai pas compris pourquoi, les zone d'�dition qui sont nom�es par un tableau
	// pr�sentent un probl�me d'encodage des accent (site en iso-8859-1), alors que des
	// zones d'�dition nomm�es simplement ne le pr�sentent pas, donc j'ai transformer les
	// tableaux en nom suffix� de l'identifiant pour les champs qui peuvent contenir
	// des accents...
	// Si quelqu'un a une meilleure id�e...
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

	// D�composer en tableau de ce qui est � cr�er, d�truire ou modifier
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
	
	// Mettre � jour
	foreach ($updTypes as $type)
		gmap_update_type($type['id'], $type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);
	
	// Cr�er
	foreach ($newTypes as $type)
		gmap_cree_type($type['nom'], $type['descriptif'], $type['objet'], $type['visible'], $type['priorite']);

	// Message de retour
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
