<?php


	/**
	 * SPIP-Météo
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function boucle_METEO_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_meteo";
		$mstatut = $id_table .'.statut';

		if (!isset($boucle->modificateur['criteres']['statut'])) {
			if (!$GLOBALS['var_preview'])
				if (!isset($boucle->modificateur['tout']))
					array_unshift($boucle->where, array("'='", "'$mstatut'", "'\\'publie\\''"));
		}

		return calculer_boucle($id_boucle, $boucles); 
	}


?>