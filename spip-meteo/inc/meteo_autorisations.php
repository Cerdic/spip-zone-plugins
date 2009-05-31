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


	function meteo_autoriser() {}
	
	
	function autoriser_meteo_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'bouton':
			case 'voir':
			case 'editer':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


?>