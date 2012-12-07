<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('base/spipimmo');

	//Boucle pour les annonces
	function boucle_ANNONCES_dist($id_boucle, &$boucles)
	{
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] = "spip_annonces";

		$boucle->where[]= array("'='", "publier", "1");

		return calculer_boucle($id_boucle, $boucles);
	}

	// Boucle pour les documents des annonces
	function boucle_DOCUMENTS_ANNONCES_dist($id_boucle, &$boucles)
	{
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] = "spip_documents_annonces";

		return calculer_boucle($id_boucle, $boucles);
	}

	// Boucle pour les types d'offres
	function boucle_TYPES_OFFRES_dist($id_boucle, &$boucles)
	{
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] = "spip_types_offres";

		return calculer_boucle($id_boucle, $boucles);
	}
?>
