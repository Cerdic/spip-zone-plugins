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

	function balise_FORMULAIRE_SPIPIMMO($p)
	{
		return calculer_balise_dynamique($p, 'FORMULAIRE_SPIPIMMO', array());
	}

	function balise_FORMULAIRE_SPIPIMMO_stat($args, $filtres)
	{
		return $args;
	}

	function balise_FORMULAIRE_SPIPIMMO_dyn()
	{
		return array('formulaires/formulaire_spipimmo', 0,
			array(
				'lien' => (generer_url_public('resultat_spipimmo')),
				'ville' => _request('ville'),
				'cp' => _request('cp')
			)
		);
	}
?>
