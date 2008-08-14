<?php
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
function rainette_icone_meteo($icone, $chemin='', $extension="png"){
	if (!$chemin) $chemin = _RAINETTE_ICONES_PATH;
	include_spip('inc/rainette_utils');
	$temps = rainette_decode_icone($icone);
	if ($img = find_in_path($chemin.$icone.'.'.$extension)) {
		list ($h,$l) = taille_image($img);
		return '<img src="'.$img.'" alt="'.rainette_traduire_temps($temps).'" title="'.rainette_traduire_temps($temps).'" width="'.$l.'" height="'.$h.'" />';
	} elseif (
	  ($chemin = 'img_meteo/')
	  AND	($img = find_in_path($chemin.$temps.'.'.$extension))) {
		#alors le dossier /grenouille n'a pas d'image, on reprend la fonction de depart (avec images de img_meteo)
		list ($h,$l) = taille_image($img);
		return '<img src="'.$img.'" alt="'.rainette_traduire_temps($temps).'" title="'.rainette_traduire_temps($temps).'" width="'.$l.'" height="'.$h.'" />';
	}
	return "";
}
function rainette_icone_details($icone){
	include_spip('inc/rainette_utils');
	$temps = rainette_decode_icone($icone);
	return rainette_traduire_temps($temps);
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
function rainette_croaaaaa_previsions($code_frxx, $nb_jours_affiche=99, $modele='previsions_jour'){
	include_spip('inc/rainette_utils');
	$nom_fichier = rainette_charge_meteo($code_frxx, 'previsions');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$texte = "";
	while (count($tableau) AND $nb_jours_affiche--){
		$page = evaluer_fond("modeles/$modele", array_shift($tableau));			
		$texte .= $page['texte'];
	}
	return $texte;
}

function rainette_croaaaaa_conditions($code_frxx, $modele='conditions_tempsreel'){
	include_spip('inc/rainette_utils');
	$nom_fichier = rainette_charge_meteo($code_frxx, 'conditions');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$page = evaluer_fond("modeles/$modele", $tableau);			
	return $page['texte'];
}
?>