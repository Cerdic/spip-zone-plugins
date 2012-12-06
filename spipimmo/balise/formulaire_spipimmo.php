<?php

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
