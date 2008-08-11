<?php

	define ('_GRENOUILLE_ICONES_PATH','grenouille/');
	define ('_GRENOUILLE_RELOAD_TIME',3*3600); // pas la peine de recharger un flux de moins de 3h
	define ('_GRENOUILLE_JOURS_PREVISION',7);

	/**
	 * filtre icone_meteo
	 *
	 * @param string temps
	 * @param string chemin
	 * @param string extension
	 * @return string image correspondant Ì  la prÌ©vision
	 * @author Pierre Basson
	 **/
	# cf pour le choix des icones http://liquidweather.net/icons.php
	function grenouille_icone_meteo($icone, $chemin='', $extension="png"){
		if (!$chemin) $chemin = _GRENOUILLE_ICONES_PATH;
		include_spip('inc/grenouille');
		$temps = grenouille_decode_icone($icone);
		if ($img = find_in_path($chemin.$icone.'.'.$extension)) {
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.grenouille_traduire_temps($temps).'" title="'.grenouille_traduire_temps($temps).'" width="'.$l.'" height="'.$h.'" />';
		} elseif (
		  ($chemin = 'img_meteo/')
		  AND	($img = find_in_path($chemin.$temps.'.'.$extension))) {
			#alors le dossier /grenouille n'a pas d'image, on reprend la fonction de depart (avec images de img_meteo)
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.grenouille_traduire_temps($temps).'" title="'.grenouille_traduire_temps($temps).'" width="'.$l.'" height="'.$h.'" />';
		}
		return "";
	}
	function grenouille_icone_details($icone){
		include_spip('inc/grenouille');
		$temps = grenouille_decode_icone($icone);
		return grenouille_traduire_temps($temps);
	}

	/**
	 * Charger le fichier des infos meteos jour par jour
	 * et rendre l'affichage pour les N premiers jours
	 *
	 * @param string $code_frxx
	 * @param int $nb_jours_affiche
	 * @return string
	 * @author Cedric Morin
	 */
	function croaaaaa($code_frxx,$nb_jours_affiche=99){
		include_spip('inc/grenouille');
		$nom_fichier = grenouille_charge_meteo($code_frxx);
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);
		$texte = "";
		while (count($tableau) AND $nb_jours_affiche--){
			$page = evaluer_fond('modeles/meteo_jour',array_shift($tableau));			
			$texte .= $page['texte'];
		}
		return $texte;
	}

?>