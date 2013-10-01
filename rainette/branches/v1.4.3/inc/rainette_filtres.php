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
function rainette_icone_meteo($code_icon, $taille='petit', $chemin='', $extension="png"){
	$html_icone = '';
	include_spip('inc/rainette_utils');
	if (!$chemin) $chemin = _RAINETTE_ICONES_PATH.$taille.'/';
	$temps = code2icone($code_icon);

	// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
	if ($img = find_in_path($chemin.$temps.'.'.$extension)) {
		list ($l,$h) = @getimagesize($img);
		$html_icone = '<img src="'.$img.'" alt="'.rainette_resume_meteo($code_icon).'" title="'.rainette_resume_meteo($code_icon).'" width="'.$l.'" height="'.$h.'" />';
	} 
	// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
	elseif (($chemin = 'img_meteo/'.$taille.'/') && ($img = find_in_path($chemin.$temps.'.'.$extension))) {
		list ($l,$h) = @getimagesize($img);
		$html_icone = '<img src="'.$img.'" alt="'.rainette_resume_meteo($code_icon).'" title="'.rainette_resume_meteo($code_icon).'" width="'.$l.'" height="'.$h.'" />';
	}
	return $html_icone;
}

function rainette_resume_meteo($code_icon){
	include_spip('inc/rainette_utils');
	$resume = ucfirst(_T('rainette:meteo_'.code2icone($code_icon)));
	return $resume;
}

function rainette_afficher_direction($direction){
	static $liste_direction = 'N:NNE:NE:ENE:E:ESE:SE:SSE:S:SSW:SW:WSW:W:WNW:NW:NNW';
	
	$direction_abregee = (intval($direction)) ? angle2direction($direction) : $direction;
	if (!in_array($direction_abregee, explode(':', $liste_direction)))
		return _T('rainette:valeur_indeterminee');
	else
		return _T('rainette:direction_'.$direction_abregee);
}

function rainette_afficher_tendance($tendance_en, $methode='texte', $chemin='', $extension="png"){
	$html = '';
	include_spip('inc/rainette_utils');
	
	if ($methode == 'texte') {
		$html = _T('rainette:tendance_texte_'.$tendance_en);
	}
	else if ($methode == 'symbole') {
		$html = _T('rainette:tendance_symbole_'.$tendance_en);
	}
	else if ($methode == 'icone') {
		if (!$chemin) $chemin = _RAINETTE_ICONES_PATH;
	
		// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
		if ($img = find_in_path($chemin.$tendance_en.'.'.$extension)) {
			list ($l,$h) = @getimagesize($img);
			$html = '<img src="'.$img.'" alt="'._T('rainette:tendance_texte_'.$tendance_en).'" title="'._T('rainette:tendance_texte_'.$tendance_en).'" width="'.$l.'" height="'.$h.'" />';
		} 
		// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
		elseif (($chemin = 'img_meteo/') && ($img = find_in_path($chemin.$tendance_en.'.'.$extension))) {
			list ($l,$h) = @getimagesize($img);
			$html = '<img src="'.$img.'" alt="'._T('rainette:tendance_texte_'.$tendance_en).'" title="'._T('rainette:tendance_texte_'.$tendance_en).'" width="'.$l.'" height="'.$h.'" />';
		}
	}
	return $html;
}

function rainette_afficher_unite($valeur, $type_valeur=''){
	$suffixe = (_RAINETTE_SYSTEME_MESURE == 'm') ? 'metrique' : 'standard';
	$espace = (($type_valeur == 'temperature') || 
			   ($type_valeur == 'pourcentage') || ($type_valeur == 'angle')) ? '' : '&nbsp;';
	$valeur_affichee = strval($valeur).$espace._T('rainette:unite_'.$type_valeur.'_'.$suffixe);
	return $valeur_affichee;
}

/**
 * Charger le fichier des infos meteos jour par jour
 * et rendre l'affichage pour les $nb_jours_affiche premiers jours
 * $nb_jours_affiche peut aussi etre de la forme Y/m/D ou Y-m-D
 * auquel cas on prend le nb de jours separant cette date de la courante.
 * Si negatif ou superieur au max, on retourne "indisponible".
 *
 * @param string $code_meteo
 * @param int|string $nb_jours_affiche
 * @return string
 * @author Cedric Morin
 */
function rainette_croaaaaa_previsions($code_meteo, $type='x_jours', $jour=0, $modele='previsions_24h'){
	include_spip('inc/rainette_utils');
	if ($type == '1_jour') {
		if (($d = intval(strtotime(strval($jour)))) <= 0) 
			$jour = min($jour, _RAINETTE_JOURS_PREVISION-1);
		else {
			$d = intval(ceil(($d-time())/(24*3600)));
			if (($d < 0) OR ($d >= _RAINETTE_JOURS_PREVISION))
				return  _T('rainette:meteo') . '&nbsp;: ' .
					_T('rainette:meteo_previsions') . ' ' .
					 $jour . '&nbsp;: ' .
					_T('rainette:meteo_na');
			$jour = $d;
		}
		
		$nom_fichier = charger_meteo($code_meteo, 'previsions');
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);
		// Si jour=0 (aujourd'hui), on complete par le tableau du lendemain matin
		if ($jour == 0) {
			$tableau[$jour]['lever_soleil_demain'] = $tableau[$jour+1]['lever_soleil'];
			$tableau[$jour]['temperature_demain'] = $tableau[$jour+1]['temperature_jour'];
			$tableau[$jour]['code_icone_demain'] = $tableau[$jour+1]['code_icone_jour'];
			$tableau[$jour]['vitesse_vent_demain'] = $tableau[$jour+1]['vitesse_vent_jour'];
			$tableau[$jour]['angle_vent_demain'] = $tableau[$jour+1]['angle_vent_jour'];
			$tableau[$jour]['direction_vent_demain'] = $tableau[$jour+1]['direction_vent_jour'];
			$tableau[$jour]['risque_precipitation_demain'] = $tableau[$jour+1]['risque_precipitation_jour'];
			$tableau[$jour]['humidite_demain'] = $tableau[$jour+1]['humidite_jour'];
		}
		// On ajoute la date de derniere maj
		$tableau[$jour]['derniere_maj'] = $tableau[_RAINETTE_JOURS_PREVISION]['derniere_maj'];
		$page = recuperer_fond("modeles/$modele", $tableau[$jour]);			
		$texte = $page;
	}
	else if ($type == 'x_jours') {
		if ($jour == 0) $jour = _RAINETTE_JOURS_PREVISION;
		$jour = min($jour, _RAINETTE_JOURS_PREVISION);
		
		$nom_fichier = charger_meteo($code_meteo, 'previsions');
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);
		$texte = "";
		while (count($tableau) && $jour--){
			$page = recuperer_fond("modeles/$modele", array_shift($tableau));			
			$texte .= $page;
		}
	}
	return $texte;
}

function rainette_croaaaaa_conditions($code_meteo, $modele='conditions_tempsreel'){
	include_spip('inc/rainette_utils');

	$nom_fichier = charger_meteo($code_meteo, 'conditions');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$tableau['code'] = $code_meteo;
	$texte = recuperer_fond("modeles/$modele", $tableau);			
	return $texte;
}

function rainette_croaaaaa_infos($code_meteo, $modele='infos_ville'){
	include_spip('inc/rainette_utils');

	$nom_fichier = charger_meteo($code_meteo, 'infos');
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	$texte = recuperer_fond("modeles/$modele", $tableau);			
	return $texte;
}
?>