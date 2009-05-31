<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function plan_present($texte) {
		if (ereg("<dl class=\"plan\"", $texte))
			return $texte;
		else
			return '';
	}


	function filtre_png($url) {
		if (ereg('png$', $url))
			return ' ';
		else
			return '';
	}


?>