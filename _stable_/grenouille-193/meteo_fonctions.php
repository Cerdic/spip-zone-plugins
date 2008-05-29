<?php


	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

	#include_spip('inc/plugin');

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
	 * Ajoute les boutons pour la meteo dans l'espace privé
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le même tableau avec une entrée en plus
	 * @author Pierre Basson
	 **/
	function meteo_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			$boutons_admin['naviguer']->sousmenu['meteo_tous']= new Bouton('../'._DIR_PLUGIN_METEO.'/img_meteo/meteo.png', _T('meteo:meteo'));
		}
		return $boutons_admin;
	}


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


	/**
	 * cron_previsions_meteo
	 *
	 * Tâche de fond pour mettre à jour les prévisions météo
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function cron_previsions_meteo($t) {
		$aujourdhui = date("Y-m-d 00:00:00");
		spip_query('DELETE FROM spip_previsions WHERE date<"'.$aujourdhui.'"');
		$jours = 7;
		$villes = spip_query('SELECT * FROM spip_meteo');
		while ($arr = @spip_fetch_array($villes)) {
			$code = $arr['code'];
			$id_meteo = $arr['id_meteo'];
			$url = "http://xoap.weather.com/weather/local/".$code."?cc=*&unit=s&dayf=".$jours;
			$xml = meteo_lire_xml($url,true,"day d=.*",array("hi","low","part p=\"d\"","part p=\"n\""));
			if ($xml) {
				sql_update('spip_meteo', array('statut'=>sql_quote("publie"),'maj'=>'NOW()'),
					'id_meteo=' . sql_quote($id_meteo));
				for($i=0; $i<$jours; $i++) {
				   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"d\""][$i]);
				   $xml["icon"][$i] = $tmp[1];
				}
				for($i=0; $i<$jours; $i++) {
					$set = array();
					$date = strftime("%Y-%m-%d 12:00:00", time() + $i * 24 * 3600);
					if ($xml["hi"][$i] != "N/A") {
						$set['maxima'] = meteo_convertir_fahrenheit_celsius($xml["hi"][$i]);
					}
					if ($xml["low"][$i] != "N/A") {
						$set['minima'] = meteo_convertir_fahrenheit_celsius($xml["low"][$i]);
					}
					if ($xml["icon"][$i] != 48) {
						$set['id_temps'] = $xml["icon"][$i];
					}

					if ($id_prevision = sql_getfetsel('id_prevision', 'spip_previsions', 
						array("date=". sql_quote($date), "id_meteo=". sql_quote($id_meteo)))) {
							sql_updateq('spip_previsions', $set,  
								array(
								'id_prevision='. sql_quote($id_prevision), 
								'id_meteo='. sql_quote($id_meteo)));
					} else {
						isset($set['maxima']) || $set['maxima']='NA';
						isset($set['minima']) || $set['minima']='NA';
						isset($set['id_temps']) || $set['minima']=48;
						$set['id_meteo'] = $id_meteo;
						$set['date'] = $date;
						$set['date'] = $date;
						$set['maj'] = 'MAJ()';
						sql_insertq('spip_previsions', $set);
					}
				}
			} else {
				sql_updateq('spip_meteo', 
					array("statut"=>"en_erreur", "maj"=>"NOW()"),
					"id_meteo=". sql_quote($id_meteo));
			}
		}
		return true;
	}


	/**
	 * meteo_recherche
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function meteo_recherche($flux) {
		$args = $flux['args'];
		$data = $flux['data'];

		$testnum		= $args['testnum'];
		$recherche		= $args['recherche'];
		$where			= $args['where'];
		$hash_recherche = $args['hash_recherche'];

		$query_meteo['FROM'] = 'spip_meteo';
		$query_meteo['WHERE'] = ($testnum ? "(id_meteo = $recherche)" :'') . $where;
		$query_meteo['ORDER BY'] = "maj DESC";

		$query_meteo_int = requete_txt_integral('spip_meteo', $hash_recherche);

		if ($data) $resultat = true;
		else
			if ($nbm) $resultat = true;
			else $resultat = false;

		return array('args' => $args, 'data' => $resultat);
	}




	/**
	 * meteo_puce_statut_meteo
	 *
	 * @param int id
	 * @param string statut
	 * @return string statut
	 * @author Pierre Basson
	 **/
	function meteo_puce_statut_meteo($id, $statut) { 
	
		switch ($statut) {
		case 'publie':
			$puce = 'verte';
			$title = _T('meteo:en_ligne');
			break;
		case 'en_erreur':
			$puce = 'orange-anim';
			$title = _T('meteo:en_erreur');
			break;
		}
		$puce = "puce-$puce.gif";
	
		$inser_puce = http_img_pack("$puce", "", " width='8' height='8' id='imgstatutmeteo$id' style='margin: 1px;'");

		return $inser_puce;
	}


	/**
	 * meteo_lire_xml
	 *
	 * @param string chaine
	 * @param boolean est_fichier
	 * @param string item
	 * @param array champs
	 * @return array tmp3
	 * @author Pierre Basson
	 **/
	function meteo_lire_xml($chaine, $est_fichier, $item, $champs) {
		if ($est_fichier) $chaine = @file_get_contents($chaine);
		else return false;
		if ($chaine) {
			// on explode sur <item>
			$tmp = preg_split("/<\/?".$item.">/", $chaine);
			// pour chaque <item>
			for ($i=1; $i<sizeof($tmp); $i++)
			// on lit les champs demandés <champ>
			foreach ($champs as $champ) {
				$tmp2 = preg_split("/<\/?".$champ.">/", $tmp[$i]);
				// on ajoute au tableau
				$tmp3[$champ][] = trim(@$tmp2[1]);
			}
			// et on retourne le tableau
			return @$tmp3;
		} else {
			return false;	
		}
	}


	/**
	 * meteo_recuperer_donnees_depuis_xml
	 *
	 * @param string xml
	 * @return array xml
	 * @author Pierre Basson
	 **/
	function meteo_recuperer_donnees_depuis_xml($xml) {
		for ($i=0; $i<$jours; $i++) {
		   $tmp = preg_split("/<\/?icon>/", $xml["part p=\"d\""][$i]);
		   $xml["icond"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?t>/", $xml["part p=\"d\""][$i]);
		   $xml["altd"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?hmid>/", $xml["part p=\"d\""][$i]);
		   $xml["hmid"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?icon>/", $xml["part p=\"n\""][$i]);
		   $xml["iconn"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?t>/", $xml["part p=\"n\""][$i]);
		   $xml["altn"][$i] = $tmp[1];
		}
		return $xml;
	}


	/**
	 * meteo_convertir_fahrenheit_celsius
	 *
	 * @param int temperature en fahrenheit
	 * @return int temperature en celcius
	 * @author Pierre Basson
	 **/
	function meteo_convertir_fahrenheit_celsius($t) {
		return round( ($t - 32) * 5 / 9 );
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
	 * @return string image correspondant à la prévision
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
	 * @return string image correspondant à la prévision
	 * @author Pierre Basson
	 **/
	function icone_meteo($temps, $chemin='', $extension="png") {
		if (empty($chemin))
			$chemin = '/img_meteo/';
		$img = find_in_path($chemin.$temps.'.'.$extension);
		if (file_exists($img)) {
			include_spip('inc/logos');
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';
		} else {
			return $img;
		}
		
	}
	
	#icone a partir du numero (plus rapide et facilement modifiable), les images portent un numero 1.png etc
	# cf pour le choix http://liquidweather.net/icons.php
	#si le dossier /grenouille/ n'a pas d'image, on reprend la fonction de depart (avec images de img_meteo)
	
	function icone_num_meteo($id_prevision, $temps='', $chemin='', $extension="png") {
		if (empty($chemin))
			$chemin = '/grenouille/';
		$img = find_in_path($chemin.$id_prevision.'.'.$extension);
		if (file_exists($img)) {
			include_spip('inc/logos');
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';
		} else {
			return icone_meteo($temps, $chemin='', $extension="png");
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
