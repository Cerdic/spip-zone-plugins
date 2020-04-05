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

	list($src, $w, $h) = rainette_icone(code2icone($code_icon), $chemin, $extension, $taille);
	if (!$src) return '';
	$r = attribut_html(rainette_resume_meteo($code_icon));
	return "<img src='$src' alt='$r' title='$r' width='$w' height='$h' />";
}

function rainette_icone($nom, $chemin='', $extension='', $taille='', $size=true){
	if (!$chemin) $chemin = _RAINETTE_ICONES_PATH.$taille.'/';
	$file = $nom . '.' . ($extension ? $extension : 'png');
	// Le dossier personnalise ou le dossier passe en argument
	// a-t-il bien l'icone requise ?
	$img = find_in_path($file, $chemin);
	if (!$img) {
	// Non, on prend l'icone par defaut dans le repertoire img_meteo/
		$img = find_in_path($file, 'img_meteo/'.$taille.'/');
		if (!$img) return array('',0,0); //???
	}

	@list($w, $h) = $size ? getimagesize($img) : array();
	return array($img, intval($w), intval($h));
}

function rainette_resume_meteo($code_icon){
	include_spip('inc/rainette_utils');
	return ucfirst(_T('rainette:meteo_'.code2icone($code_icon)));
}

function rainette_afficher_direction($direction){
	static $liste_direction = array(
		0 => 'N',
		1 => 'NNE',
		2 => 'NE',
		3 => 'ENE',
		4 => 'E',
		5 => 'ESE',
		6 => 'SE',
		7 => 'SSE',
		8 => 'S',
		9 => 'SSW',
		10 => 'SW',
		11 => 'WSW',
		12 => 'W',
		13 => 'WNW',
		14 => 'NW',
		15 => 'NNW'
					);
	if (is_numeric($direction))
		$direction = $liste_direction[round($direction / 22.5) % 16];
	elseif (!in_array($direction, $liste_direction))
		return _T('rainette:valeur_indeterminee');
	return _T('rainette:direction_'.$direction);
}

function rainette_afficher_tendance($tendance_en, $methode='texte', $chemin='', $extension="png"){

	if ($methode == 'texte') 
		return _T('rainette:tendance_texte_'.$tendance_en);
	if ($methode == 'symbole')
		return _T('rainette:tendance_symbole_'.$tendance_en);
	list($src, $w, $h) = rainette_icone($tendance_en, $chemin, $extension);
	if (!$src) return '';
	$r = attribut_html( _T('rainette:tendance_texte_'.$tendance_en));
	return "<img src='$src' alt='$r' title='$r' width='$w' height='$h' />";
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
				return '<div class="rainette_previsions_2x12h"><div class="maj">' .
				  _T('rainette:meteo_na') .
				  '</div></div>';
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

/**
 * Charger le fichier des previsions meteos
 * et retourne une feuille de styles,
 * un style ayant pour selecteur #D$annee-$mois-$jour sur 8 chiffres
 * et pour propriete un background-url sur l'icone de la prevision.
 * Si le 2e argument est fourni a True, renvoie les dates Unix
 * de la derniere prevision et de la suivante, separees par --.
 *
 * @param string $code_meteo
 * @param boolean $intervalle
 * @return string
 */
function rainette_croaaaaa_previsions_css($code_meteo, $intervalle=false){
	include_spip('inc/rainette_utils');

	$texte = $vus = array();
	$maj = '';
	lire_fichier(charger_meteo($code_meteo, 'previsions'), $previsions);
	foreach(unserialize($previsions) as $j => $prevision) {
		if (empty($prevision['date'])) {
			$maj = @$prevision['derniere_maj'];
			if ($intervalle AND $maj) break; else continue;
		}
		if ($intervalle) continue;
		$icone = code2icone($prevision["code_icone_jour"]);
		list($src,,) = rainette_icone($icone, '', '', 'petit', false);
		if ($src) {
			$src = "{ background: url($src) }";
			$sel = "#D" . $prevision['date'];
			// Si deja vu, partager pour reduire la feuille
			$k = array_search($src, $vus);
			if ($k===false) {
				$vus[$j] = $src;
				$texte[$j] = "$sel\n $src";
			} else {
				$texte[$k] = "$sel, " . $texte[$k];
			}
		}
	}
	if (!$intervalle) return join("\n", $texte);
	if (!$maj) return '';
	$maj = strtotime($maj);
	$j = $maj + _RAINETTE_RELOAD_TIME_PREVISIONS;
	return "$maj -- $j";
}
?>
