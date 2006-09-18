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


	include_spip('base/create');
	include_spip('base/meteo');
	include_spip('inc/plugin');

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
			$boutons_admin['naviguer']->sousmenu['meteo']= new Bouton('../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', _T('meteo:meteo'));
		}
		return $boutons_admin;
	}


	/**
	 * meteo_header_prive
	 *
	 * Vérifie que la base est à jour
	 *
	 * @param string texte
	 * @return string texte avec le chemin modifié
	 * @author Pierre Basson
	 **/
	function meteo_header_prive($texte) { 
		meteo_verifier_base();
		return $texte;
	}


	/**
	 * meteo_verifier_base
	 *
	 * @return true
	 * @author Pierre Basson
	 **/
	function meteo_verifier_base() {
		$info_plugin_boutique = plugin_get_infos(_NOM_PLUGIN_METEO);
		$version_plugin = $info_plugin_boutique['version'];
		if (!isset($GLOBALS['meta']['spip_meteo_version'])) {
			creer_base();
			ecrire_meta('spip_meteo_version', $version_plugin);
			ecrire_metas();
		} else {
			$version_base = $GLOBALS['meta']['spip_meteo_version'];
		}
		return true;
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
		$jours = 7;
		$villes = spip_query('SELECT * FROM spip_meteo');
		while ($arr = @spip_fetch_array($villes)) {
			$code = $arr['code'];
			$id_meteo = $arr['id_meteo'];
			$url = "http://xoap.weather.com/weather/local/".$code."?cc=*&unit=s&dayf=".$jours;
			$xml = lit_xml($url,true,"day d=.*",array("hi","low","part p=\"d\"","part p=\"n\""));
			if ($xml) {
				spip_query('UPDATE spip_meteo SET statut="publie", maj=NOW() WHERE id_meteo="'.$id_meteo.'"');
				for($i=0; $i<$jours; $i++) {
				   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"d\""][$i]);
				   $xml["icon"][$i] = $tmp[1];
				}
				for($i=0; $i<$jours; $i++) {
					$date = strftime("%Y-%m-%d 12:00:00", time() + $i * 24 * 3600);
					if ($xml["hi"][$i] == "N/A") {
						$max = "NA";
						$set_max = '';
					} else {
						$max = meteo_convertir_fahrenheit_celsius($xml["hi"][$i]);
						$set_max = 'maxima="'.$max.'", ';
					}
					if ($xml["low"][$i] == "N/A") {
						$min = "NA";
						$set_min = '';
					} else {
						$min = meteo_convertir_fahrenheit_celsius($xml["low"][$i]);
						$set_min = 'minima="'.$min.'", ';
					}
					if ($xml["icon"][$i] == 48) {
						$icone = 48;
						$set_temps = '';
					} else {
						$icone = $xml["icon"][$i];
						$set_temps = 'id_temps="'.$xml["icon"][$i].'", ';
					}

					$verif = spip_query('SELECT * FROM spip_previsions WHERE date="'.$date.'" AND id_meteo="'.$id_meteo.'" LIMIT 1');
					if (spip_num_rows($verif) > 0) {
						$bob = spip_fetch_array($verif);
						$id_prevision = $bob['id_prevision'];
						spip_query('UPDATE spip_previsions SET '.$set_max.$set_min.$set_temps.'maj=NOW() WHERE id_prevision="'.$id_prevision.'" AND id_meteo="'.$id_meteo.'"');
					} else {
						spip_query('INSERT INTO spip_previsions (id_meteo, date, maxima, minima, id_temps, maj) VALUES ("'.$id_meteo.'", "'.$date.'", "'.$max.'", "'.$min.'", "'.$icone.'", NOW())');
					}
				}
			} else {
				spip_query('UPDATE spip_meteo SET statut="en_erreur", maj=NOW() WHERE id_meteo="'.$id_meteo.'"');
			}
		}
		return true;
	}


	/**
	 * meteo_afficher_meteos
	 *
	 * @param string titre_table
	 * @param string requete
	 * @return string tableau de meteo
	 * @author Pierre Basson
	 **/
	function meteo_afficher_meteos($titre_table, $requete) {
		global $couleur_foncee, $options;

	    $tmp_var = substr(md5(join('', $requete)), 0, 4);
	    $deb_aff = intval(_request('t_' .$tmp_var));
		if ($options == "avancees") {
			$largeurs = array('12', '', '200', '80', '50');
			$styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
			$col = 4;
		} else {
			$largeurs = array('12', '', '200', '80');
			$styles = array('', 'arial2', 'arial1', 'arial1');
			$col = 3;
		}
		return affiche_tranche_bandeau($requete, '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', $col, 'white', 'black', $tmp_var, $deb_aff, $titre_table, false, $largeurs, $styles, 'meteo_afficher_meteos_boucle');
	}


	/**
	 * meteo_afficher_meteos_boucle
	 *
	 * @param array row
	 * @param array tous_id
	 * @return string ligne
	 * @author Pierre Basson
	 **/
	function meteo_afficher_meteos_boucle($row, &$tous_id) {
		global $options;

		$vals = '';

		$id_meteo	= $row['id_meteo'];
		$titre		= $row['ville'];
		$code		= $row['code'];
		$statut		= $row['statut'];
	
		$vals[] = meteo_puce_statut_meteo($id_meteo, $statut);

		$s = "<a href='" . generer_url_ecrire("meteo_visualisation","id_meteo=$id_meteo") . "'>";
		$s .= typo($titre);
		$s .= "</A>";
		$vals[] = $s;
	
		if ($statut == 'en_erreur')
			$vals[] = "<font color='red'>"._T('meteo:probleme_de_recuperation_du_flux')." </font>";
		else
			$vals[] = "&nbsp;";

		// La date
		$vals[] = $code;

		// Le numero (moche)
		if ($options == "avancees") {
			$vals[] = "<b>"._T('info_numero_abbreviation')."$id_meteo</b>";
		}

		return $vals;
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
		global $spip_lang_left, $dir_lang, $connect_statut, $options;
	
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
	 * meteo_afficher_previsions
	 *
	 * @param string titre_table
	 * @param string requete
	 * @return string tableau de prévisions
	 * @author Pierre Basson
	 **/
	function meteo_afficher_previsions($titre_table, $requete) {
		global $couleur_foncee, $options;

	    $tmp_var = substr(md5(join('', $requete)), 0, 4);
	    $deb_aff = intval(_request('t_' .$tmp_var));
		$largeurs = array('18', '100', '100', '50', '50');
		$styles = array('', 'arial2', 'arial2', 'arial1', 'arial1');
		$col = 4;
		return affiche_tranche_bandeau($requete, '../'._DIR_PLUGIN_METEO.'/img_pack/previsions.png', $col, 'white', 'black', $tmp_var, $deb_aff, $titre_table, false, $largeurs, $styles, 'meteo_afficher_previsions_boucle');
	}


	/**
	 * meteo_afficher_previsions_boucle
	 *
	 * @param array row
	 * @param array tous_id
	 * @return string ligne
	 * @author Pierre Basson
	 **/
	function meteo_afficher_previsions_boucle($row, &$tous_id) {
		global $options, $tableau_meteo;

		$vals = '';

		$date		= $row['date'];
		$id_temps	= $row['id_temps'];
		$minima		= $row['minima'];
		$maxima		= $row['maxima'];
	
		// L'icone du temps
		$vals[] = http_img_pack('../'._DIR_PLUGIN_METEO.'/img_pack/'.$tableau_meteo[$id_temps].'.png', _T('meteo:meteo_'.$tableau_meteo[$id_temps]), "");
		
		// La date
		$vals[] = nom_jour($date).' '.affdate_jourcourt($date);
	
		// Le temps
		$vals[] = _T('meteo:meteo_'.$tableau_meteo[$id_temps]);

		// minima
		if ($minima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $minima.'&nbsp;&deg;C';

		// maxima
		if ($maxima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $maxima.'&nbsp;&deg;C';

		return $vals;
	}


	// Lecture d'un fichier XML
	function lit_xml($chaine,$isFile,$item,$champs) {
	   // on lit le fichier ou la chaîne
	   if($isFile) $chaine = @file_get_contents($chaine);
		else return false;
	   if($chaine) {
	      // on explode sur <item>
	      $tmp = preg_split("/<\/?".$item.">/",$chaine);
	      // pour chaque <item>
	      for($i=1;$i<sizeof($tmp);$i++)
	         // on lit les champs demandés <champ>
	         foreach($champs as $champ) {
	            $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
	            // on ajoute au tableau
	            $tmp3[$champ][] = trim(@$tmp2[1]);
	         }
	      // et on retourne le tableau
	      return @$tmp3;
	   }
		else return false;
	}

	function meteo_recuperer_donnees_depuis_xml($xml) {
		for($i=0;$i<$jours;$i++) {
		   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"d\""][$i]);
		   $xml["icond"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?t>/",$xml["part p=\"d\""][$i]);
		   $xml["altd"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?hmid>/",$xml["part p=\"d\""][$i]);
		   $xml["hmid"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"n\""][$i]);
		   $xml["iconn"][$i] = $tmp[1];
		   $tmp = preg_split("/<\/?t>/",$xml["part p=\"n\""][$i]);
		   $xml["altn"][$i] = $tmp[1];
		}
		return $xml;
	}


	// Conversion Fahrenheit->Celsius
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
		$temps = spip_query('SELECT id_temps FROM spip_previsions WHERE id_prevision="'.$id_prevision.'"');
		if (spip_num_rows($temps) == 0) {
			return '';
		} else {
			list($id_temps) = spip_fetch_array($temps,SPIP_NUM);
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
			$chemin = _DIR_IMG.'meteo/';
		$img = $chemin.$temps.'.'.$extension;
		if (file_exists($img)) {
			include_spip('inc/logos');
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';
		} else {
			return '';
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


?>