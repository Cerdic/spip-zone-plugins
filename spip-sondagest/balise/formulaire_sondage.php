<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function balise_FORMULAIRE_SONDAGE($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_SONDAGE', array('id_sondage'));
	}


	function balise_FORMULAIRE_SONDAGE_stat($args, $filtres) {
		$id_sondage = $args[0];
		$test_statut = sql_countsel('spip_sondages', 'id_sondage='.intval($id_sondage).' AND statut IN ("publie", "termine")');
		$test_choix = sql_countsel('spip_choix', 'id_sondage='.intval($id_sondage));
		if ($test_statut and $test_choix)
			return $args;
		else
			return '';
	}


?>