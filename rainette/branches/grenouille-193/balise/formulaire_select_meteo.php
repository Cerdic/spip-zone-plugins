<?php

	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function balise_FORMULAIRE_SELECT_METEO ($p) { return calculer_balise_dynamique($p,'FORMULAIRE_SELECT_METEO', array());	}
	function balise_FORMULAIRE_SELECT_METEO_stat($args, $filtres) {	return $args;	}

	function balise_FORMULAIRE_SELECT_METEO_dyn() { return array('formulaires/formulaire_select_meteo', 0, array('meteosel' => $_POST['meteosel']));	}
?>
