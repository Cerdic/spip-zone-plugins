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


	/**
	 * filtre_themes
	 *
	 * @author Pierre Basson
	 **/
	function filtre_themes($rubriques_virgule, $avant, $apres) {
		$rubriques = explode(',', $rubriques_virgule);
		foreach ($rubriques as $id_rubrique) {
			if ($id_rubrique == -1) {
				$affichage.= $avant._T('lettres:tout_le_site').$apres."\n";
				continue;
			}
			$res = spip_query('SELECT titre FROM spip_themes WHERE id_rubrique="'.$id_rubrique.'" LIMIT 1');
			if (spip_num_rows($res) == 1) {
				list($titre) = spip_fetch_array($res, SPIP_NUM);
				$affichage.= $avant.typo($titre).$apres."\n";
			}
		}
		return $affichage;
	}


?>