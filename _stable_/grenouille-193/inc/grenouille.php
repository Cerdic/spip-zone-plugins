<?php

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


?>
