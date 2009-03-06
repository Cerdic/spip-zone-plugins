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


	include_spip('public/meteo_balises');
	include_spip('public/meteo_boucles');
	include_spip('inc/meteo_filtres');
	include_spip('genie/meteo');

	global $tableau_meteo;

 	$tableau_meteo	= array(
							"1"		=> "pluie",
							"2"		=> "pluie",
							"3"		=> "orage",
							"4"		=> "orage",
							"5"		=> "pluie",
							"6"		=> "neige",
							"7"		=> "verglas",
							"8"		=> "pluie",
							"9"		=> "pluie",
							"10"	=> "pluie",
							"11"	=> "pluie",
							"12"	=> "pluie",
							"13"	=> "neige",
							"14"	=> "neige",
							"15"	=> "neige",
							"16"	=> "neige",
							"17"	=> "orage",
							"18"	=> "neige",
							"19"	=> "brouillard",
							"20"	=> "brouillard",
							"21"	=> "brouillard",
							"22"	=> "brouillard",
							"23"	=> "vent",
							"24"	=> "vent",
							"25"	=> "vent",
							"26"	=> "nuages",
							"27"	=> "lune-nuages",
							"28"	=> "soleil-nuages",
							"29"	=> "lune-nuage",
							"30"	=> "soleil-nuage",
							"31"	=> "lune",
							"32"	=> "soleil",
							"33"	=> "lune-nuage",
							"34"	=> "soleil-nuage",
							"35"	=> "orage",
							"36"	=> "soleil",
							"37"	=> "orage",
							"38"	=> "orage",
							"39"	=> "pluie",
							"40"	=> "pluie",
							"41"	=> "neige",
							"42"	=> "neige",
							"43"	=> "neige",
							"44"	=> "soleil-nuage",
							"45"	=> "pluie",
							"46"	=> "neige",
							"47"	=> "orage",
							"48"	=> "inconnu"
						);


	function calculer_url_meteo($id_meteo, $texte, $ancre) {
		$lien = generer_url_meteo($id_meteo) . $ancre;
		if (!$texte)
			$texte = sql_getfetsel('ville', 'spip_meteo', 'id_meteo='.intval($id_meteo));
		return array($lien, 'spip_in', $texte);
	}


	function generer_url_meteo($id_meteo) {
		return generer_url_public('meteo', 'id_meteo='.$id_meteo);
	}


?>