<?php
/**
 * filtre rainette_icone_meteo
 *
 * @param string temps
 * @param string chemin
 * @param string extension
 * @return string image correspondant au code de temps
 * @author Pierre Basson
 **/
# cf pour le choix des icones http://liquidweather.net/icons.php
function rainette_icone_meteo($code, $chemin='', $extension="png"){
	$html_icone = '';
	include_spip('inc/rainette_utils');

	if (!$chemin) $chemin = _RAINETTE_ICONES_PATH;
	$temps = code2icone($code);

	// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
	if ($img = find_in_path($chemin.$temps.'.'.$extension)) {
		list ($h,$l) = taille_image($img);
		$html_icone = '<img src="'.$img.'" alt="'.rainette_resume_meteo($code).'" title="'.rainette_resume_meteo($code).'" width="'.$l.'" height="'.$h.'" />';
	} 
	// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
	elseif (($chemin = 'img_meteo/') && ($img = find_in_path($chemin.$temps.'.'.$extension))) {
		list ($h,$l) = taille_image($img);
		$html_icone = '<img src="'.$img.'" alt="'.rainette_resume_meteo($code).'" title="'.rainette_resume_meteo($code).'" width="'.$l.'" height="'.$h.'" />';
	}
	return $html_icone;
}

function rainette_resume_meteo($code){
	include_spip('inc/rainette_utils');
	$resume = _T('rainette:meteo_'.code2icone($code));
	return $resume;
}

function rainette_texte_direction($direction){
	return _T('rainette:direction_'.$direction);
}

function rainette_afficher_unite($valeur, $type_valeur=''){
	$suffixe = (_RAINETTE_SYSTEME_MESURE == 'm') ? 'metrique' : 'standard';
	$espace = (($type_valeur == 'temperature') || 
			   ($type_valeur == 'humidite') || ($type_valeur == 'angle')) ? '' : '&nbsp;';
	$valeur_affichee = strval($valeur).$espace._T('rainette:unite_'.$type_valeur.'_'.$suffixe);
	return $valeur_affichee;
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
function rainette_croaaaaa_previsions($code_frxx, $nb_jours_affiche=0, $modele='previsions_jour'){
	include_spip('inc/rainette_utils');
	if ($nb_jours_affiche == 0) $nb_jours_affiche = _RAINETTE_JOURS_PREVISION;
	$nb_jours_affiche = min($nb_jours_affiche, _RAINETTE_JOURS_PREVISION);
	
	$nom_fichier = charger_meteo($code_frxx, 'previsions');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$texte = "";
	while (count($tableau) && $nb_jours_affiche--){
		$page = evaluer_fond("modeles/$modele", array_shift($tableau));			
		$texte .= $page['texte'];
	}
	return $texte;
}

function rainette_croaaaaa_conditions($code_frxx, $modele='conditions_tempsreel'){
	include_spip('inc/rainette_utils');
	$nom_fichier = charger_meteo($code_frxx, 'conditions');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$page = evaluer_fond("modeles/$modele", $tableau);			
	return $page['texte'];
}
?>