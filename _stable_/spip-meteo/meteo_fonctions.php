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


	function inc_afficher_meteos($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('arial1', 12), array('arial2'), array('arial1', 200), array('arial1', 80), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_meteo_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_METEO.'prive/images/meteo-24.png');
	}


	function afficher_meteo_boucle($row, $own) {
		$vals = '';

		$id_meteo	= $row['id_meteo'];
		$titre		= $row['ville'];
		$code		= $row['code'];
		$statut		= $row['statut'];
	
		switch ($statut) {
			case 'publie':
				$puce = 'verte';
				break;
			case 'en_erreur':
				$puce = 'orange-anim';
				break;
		}
		$puce = "puce-$puce.gif";
		$vals[] = http_img_pack($puce, '', ' width="8" height="8" style="margin: 1px;"');

		$s = "<a href='" . generer_url_ecrire("meteo","id_meteo=$id_meteo") . "'>";
		$s .= typo($titre);
		$s .= "</a>";
		$vals[] = $s;
	
		if ($statut == 'en_erreur')
			$vals[] = "<font color='red'>"._T('meteo:probleme_de_recuperation_du_flux')." </font>";
		else
			$vals[] = "&nbsp;";

		$vals[] = $code;

		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_meteo</b>";

		return $vals;
	}


	function inc_afficher_previsions($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('arial1', 30), array('arial2'), array('arial1'), array('arial1'), array('arial1'));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_prevision_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_METEO.'prive/images/meteo-24.png');
	}


	function afficher_prevision_boucle($row, $own) {
		global $tableau_meteo;
		
		$vals = '';

		$date		= $row['date'];
		$id_temps	= $row['id_temps'];
		$minima		= $row['minima'];
		$maxima		= $row['maxima'];
	
		$vals[] = icone_meteo($tableau_meteo[$id_temps]);
		
		$vals[] = nom_jour($date).' '.affdate_jourcourt($date);
	
		$vals[] = _T('meteo:meteo_'.$tableau_meteo[$id_temps]);

		if ($minima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $minima.'&nbsp;&deg;C';

		if ($maxima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $maxima.'&nbsp;&deg;C';

		return $vals;
	}


?>