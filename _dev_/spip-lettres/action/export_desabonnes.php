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


	include_spip('lettres_fonctions');
	include_spip('surcharges_fonctions');


	function action_export_desabonnes() {

		if (autoriser('exporter', 'lettres')) {

			$desabonnes = array();
			$i = 0;
			$res = spip_query('SELECT email FROM spip_desabonnes');
			while ($arr = spip_fetch_array($res)) {
				$desabonnes[$i][] = $arr['email'];
				$i++;
			}

			surcharges_exporter_csv('desabonnes', $desabonnes);

		}

	}


?>