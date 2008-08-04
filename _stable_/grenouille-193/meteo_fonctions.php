<?php

	#include_spip('inc/grenouille');
	include_spip('genie/previsions_meteo');
	genie_previsions_meteo($dummy);
	
	

	/**
	 * meteo_taches_generales_cron
	 *
	 * Ajout des tâches planifiées pour le plugin
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	 
	function meteo_taches_generales_cron($taches_generales) {
		$taches_generales['previsions_meteo'] = 60 * 60; // toutes les heures
		return $taches_generales;
	}
	
	
	
	

	global $tableau_meteo;
 	$tableau_meteo	= array(
							"1"	=> "pluie",
							"2"	=> "pluie",
							"3"	=> "orage",
							"4"	=> "orage",
							"5"	=> "pluie",
							"6"	=> "neige",
							"7"	=> "verglas",
							"8"	=> "pluie",
							"9"	=> "pluie",
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
							"48"	=> "inconnu",
						);


	/**
	 * meteo_ajouter_boutons
	 *
	 * Ajoute les boutons pour la meteo dans l'espace priv̩
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le m̻me tableau avec une entr̩e en plus
	 * @author Pierre Basson
	 **/
	function meteo_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			$boutons_admin['naviguer']->sousmenu['meteo_tous']= new Bouton('../'._DIR_PLUGIN_METEO.'/img_meteo/meteo.png', _T('meteo:meteo'));
		}
		return $boutons_admin;
	}



	/**
	 * balise_TEMPS
	 *
	 * @param p est un objet SPIP
	 * @return float pourcentage
	 * @author Pierre Basson
	 **/
	function balise_TEMPS($p) {
		$_id_prevision = champ_sql('id_prevision',$p);
		$p->code = "meteo_calculer_temps($_id_prevision)";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * meteo_calculer_icone_temps
	 *
	 * @param int id_prevision
	 * @return string image correspondant �  la pr̩vision
	 * @author Pierre Basson
	 **/
	function meteo_calculer_temps($id_prevision) {
		global $tableau_meteo;
		if (!$id_temps = sql_getfetsel('id_temps','spip_previsions', 'id_prevision='. sql_quote($id_prevision))){
			return '';
		} else {
			return $tableau_meteo[$id_temps];
		}
	}


	/**
	 * filtre icone_meteo
	 *
	 * @param string temps
	 * @param string chemin
	 * @param string extension
	 * @return string image correspondant �  la pr̩vision
	 * @author Pierre Basson
	 **/
	# cf pour le choix des icones http://liquidweather.net/icons.php
	function icone_meteo($temps, $chemin='', $extension="png"){
			$chemin = 'grenouille/';
		$img = _DIR_PLUGIN_METEO.$chemin.$temps.'.'.$extension;
		include_spip('inc/logos');
		if (file_exists($img)) {
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';
		} else {
			#alors le dossier /grenouille n'a pas d'image, on reprend la fonction de depart (avec images de img_meteo)
			global $tableau_meteo;
			$temps= $tableau_meteo[$temps];
			$chemin = 'img_meteo/';
			$img = _DIR_PLUGIN_METEO.$chemin.$temps.'.'.$extension;
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';

		}
		
	}
	



	/**
	 * filtre traduire_meteo
	 *
	 * @param string temps
	 * @return string traduction
	 * @author Pierre Basson
	 **/
	function traduire_meteo($temps) {
		if (empty($temps))
			return '';
		return _T('meteo:meteo_'.$temps);
	}

	
	
	//
	// <BOUCLE(METEO)>
	//
	function boucle_METEO_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_meteo";  

			if (!$boucle->statut) {
				$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
			}
			
	        return calculer_boucle($id_boucle, $boucles); 
	}

?>
