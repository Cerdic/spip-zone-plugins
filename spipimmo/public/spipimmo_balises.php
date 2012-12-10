<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('base/spipimmo');
	include_spip('urls/spipimmo_propres');

	//Balise pour l'url des annonces
	function balise_URL_ANNONCE($params)
	{
		$_id_annonce = interprete_argument_balise(1,$params);
		if (!$_id_annonce)
			$_id_annonce = champ_sql('id_annonce', $params);
			$params->code = "generer_url_annonce($_id_annonce)";

		$params->interdire_scripts = false;
		return $params;
	}
?>
