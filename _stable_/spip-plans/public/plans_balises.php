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


	function balise_URL_POINT($p) {
		$_lien = champ_sql('lien', $p);
		$p->code = "plans_calculer_url_point($_lien)";
		$p->statut = 'php';
		return $p;
	}


	function plans_calculer_url_point($lien) {
		$chaine = '[bidon->'.$lien.']';
		$url = extraire_attribut(propre($chaine),'href');
		return $url;
	}
	

?>