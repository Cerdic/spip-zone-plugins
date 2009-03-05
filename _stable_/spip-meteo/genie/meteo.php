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


	function meteo_taches_generales_cron($taches_generales) {
		$taches_generales['meteo'] = 60 * 60; // toutes les heures
		return $taches_generales;
	}


	function genie_meteo($t) {
		include_spip('meteo_fonctions');
		$aujourdhui = date("Y-m-d 00:00:00");
		sql_delete('spip_previsions', 'date<"'.$aujourdhui.'"');
		$jours = 7;
		$villes = sql_select('*', 'spip_meteo');
		while ($arr = sql_fetch($villes)) {
			$code = $arr['code'];
			$id_meteo = $arr['id_meteo'];
			$url = "http://xoap.weather.com/weather/local/".$code."?cc=*&unit=s&dayf=".$jours;
			$xml = meteo_lire_xml($url,true,"day d=.*",array("hi","low","part p=\"d\"","part p=\"n\""));
			if ($xml) {
				sql_updateq('spip_meteo', array('statut' => 'publie', 'maj' => 'NOW()'), "id_meteo=$id_meteo");
				for($i=0; $i<$jours; $i++) {
				   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"d\""][$i]);
				   $xml["icon"][$i] = $tmp[1];
				}
				for($i=0; $i<$jours; $i++) {
					$date = strftime("%Y-%m-%d 12:00:00", time() + $i * 24 * 3600);
					$champs = array();
					if ($xml["hi"][$i] == "N/A") {
						$max = "NA";
					} else {
						$max = meteo_convertir_fahrenheit_celsius($xml["hi"][$i]);
						$champs['maxima'] = $max;
					}
					if ($xml["low"][$i] == "N/A") {
						$min = "NA";
					} else {
						$min = meteo_convertir_fahrenheit_celsius($xml["low"][$i]);
						$champs['minima'] = $min;
					}
					if ($xml["icon"][$i] == 48) {
						$icone = 48;
					} else {
						$icone = $xml["icon"][$i];
						$champs['id_temps'] = $xml["icon"][$i];
					}
					$champs['maj'] = 'NOW()';
					$verif = sql_select('*', 'spip_previsions', 'date="'.$date.'" AND id_meteo="'.$id_meteo.'"', '', '', '1');
					if (sql_count($verif) > 0) {
						$bob = sql_fetch($verif);
						$id_prevision = $bob['id_prevision'];
						sql_update('spip_previsions', $champs, 'id_prevision="'.$id_prevision.'" AND id_meteo="'.$id_meteo.'"');
					} else {
						sql_insertq('spip_previsions', array('id_meteo' => $id_meteo, 'date' => $date, 'maxima' => $max, 'minima' => $min, 'id_temps' => $icone, 'maj' => 'NOW()')); 
					}
				}
			} else {
				sql_updateq('spip_meteo', array('statut' => 'en_erreur', 'maj' => 'NOW()'), 'id_prevision='.$id_prevision.' AND id_meteo='.$id_meteo);
			}
		}
		return true;
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


?>