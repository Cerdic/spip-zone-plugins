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


	function lettres_autoriser() {}
	
	
	function autoriser_lettres_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'bouton':
			case 'onglet':
			case 'configurer':
			case 'editer':
			case 'voir':
			case 'exporter':
			case 'importer':
			case 'purger':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


?>