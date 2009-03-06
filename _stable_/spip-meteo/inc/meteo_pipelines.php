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


	function meteo_declarer_tables_objets_surnoms($surnoms) {
		$surnoms['meteo'] = 'meteo';
		return $surnoms;
	}
	
	
	function meteo_rechercher_liste_des_champs($tables) {
		$tables['meteo']['ville'] = 8;
		return $tables;
	}


?>