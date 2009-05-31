<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function balise_FORMULAIRE_LETTRES($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_LETTRES', array());
	}


	function balise_FORMULAIRE_LETTRES_stat($args, $filtres) {
		$test = sql_countsel('spip_themes');
		if ($test)
			return $args;
		else
			return '';
	}


?>