<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function fusionner_intervalles($intervalle_a, $intervalle_b) {

	// On recupere les bornes de chaque intervalle
	$borne_a = extraire_bornes($intervalle_a);
	$borne_b = extraire_bornes($intervalle_b);

	// On initialise la borne min de chaque intervalle a 1.9.0 inclus si vide
	if (!$borne_a['min']['valeur']) {
		$borne_a['min']['valeur'] = _SVP_VERSION_SPIP_MIN;
		$borne_a['min']['incluse'] = true;
	}
	if (!$borne_b['min']['valeur']) {
		$borne_b['min']['valeur'] = _SVP_VERSION_SPIP_MIN;
		$borne_b['min']['incluse'] = true;
	}
	
	// On calcul maintenant :
	// -- la borne min de l'intervalle fusionne = min(min_a, min_b)
	// -- suivant l'intervalle retenu la borne max est forcement dans l'autre intervalle = max(autre intervalle)
	//    On presuppose evidemment que les intervalles ne sont pas disjoints et coherents entre eux
	if (spip_version_compare($borne_a['min']['valeur'], $borne_b['min']['valeur'], '<=')) {
		$bornes_fusionnees['min'] = $borne_a['min'];
		$bornes_fusionnees['max'] = $borne_b['max'];
	}
	else {
		$bornes_fusionnees['min'] = $borne_b['min'];
		$bornes_fusionnees['max'] = $borne_a['max'];
	}

	return contruire_intervalle($bornes_fusionnees);
}

function extraire_bornes($intervalle, $initialiser=false) {
	static $borne_vide = array('valeur' => '', 'incluse' => false);
	static $borne_inf_init = array('valeur' => _SVP_VERSION_SPIP_MIN, 'incluse' => true);
	static $borne_sup_init = array('valeur' => _SVP_VERSION_SPIP_MAX, 'incluse' => true);

	if ($initialiser)
		$bornes = array('min' => $borne_inf_init, 'max' => $borne_sup_init);
	else
		$bornes = array('min' => $borne_vide, 'max' => $borne_vide);

	if ($intervalle
	AND preg_match(',^[\[\(\]]([0-9.a-zRC\s\-]*)[;]([0-9.a-zRC\s\-\*]*)[\]\)\[]$,Uis', $intervalle, $matches)) {
		if ($matches[1]) {
			$bornes['min']['valeur'] = trim($matches[1]);
			$bornes['min']['incluse'] = ($intervalle{0} == "[");
		}
		if ($matches[2]) {
			$bornes['max']['valeur'] = trim($matches[2]);
			$bornes['max']['incluse'] = (substr($intervalle,-1) == "]");
		}
	}

	
	return $bornes;
}

function contruire_intervalle($bornes, $dtd='paquet') {
	return ($bornes['min']['incluse'] ? '[' : ($dtd=='paquet' ? ']' : '('))
			. $bornes['min']['valeur'] . ';' . $bornes['max']['valeur']
			. ($bornes['max']['incluse'] ? ']' : ($dtd=='paquet' ? '[' : ')'));
}


function compiler_branches_spip($intervalle) {
	include_spip('plugins/installer');

	global $infos_branches_spip;
	$liste_branches_spip = array_keys($GLOBALS['infos_branches_spip']);
		
	$bornes = extraire_bornes($intervalle, false);
	// On traite d'abord les cas ou l'intervalle est :
	// - vide 
	// - non vide mais avec les deux bornes vides
	// Dans ces cas la compatibilite est totale, on renvoie toutes les branches
	if (!$intervalle OR (!$bornes['min']['valeur'] AND !$bornes['max']['valeur']))
		return implode(',', $liste_branches_spip);

	// On force l'initialisation des bornes et on les nettoie des suffixes d'etat
	$bornes = extraire_bornes($intervalle, true);
	$borne_inf = strtolower(preg_replace(',([0-9])[\s-.]?(dev|alpha|a|beta|b|rc|pl|p),i','\\1',$bornes['min']['valeur']));
	$borne_sup = strtolower(preg_replace(',([0-9])[\s-.]?(dev|alpha|a|beta|b|rc|pl|p),i','\\1',$bornes['max']['valeur']));

	// On determine les branches inf et sup issues du phrasage de l'intervalle
	// -- on initialise la branche inf de l'intervalle que l'on va preciser ensuite
	$t = explode('.', $borne_inf);
	$branche_inf = $t[0] . '.' . $t[1];
	// -- pour eviter toutes erreur fatale on verifie que la branche est bien dans la liste des possibles
	// -- -> si non, on renvoie vide
	if (!in_array($branche_inf, $liste_branches_spip))
		return '';
	// -- on complete la borne inf de l'intervalle de x.y en x.y.z et on determine la vraie branche
	if (!$t[2]) {
		if ($bornes['min']['incluse'])
			$borne_inf = $infos_branches_spip[$branche_inf][0];
		else {
			$branche_inf = $liste_branches_spip[array_search($branche_inf, $liste_branches_spip)+1];
			$borne_inf = $infos_branches_spip[$branche_inf][0];
		}
	}
	
	// -- on initialise la branche sup de l'intervalle que l'on va preciser ensuite
	// HACK !!!!! on traite le cas particulier 3.1.0 tant que 3.0.* n'est pas utilisable
	$borne_sup = $borne_sup=='3.1.0' ? _SVP_VERSION_SPIP_MAX : $borne_sup;
	$t = explode('.', $borne_sup);
	$branche_sup = $t[0] . '.' . $t[1];
	// -- pour eviter toutes erreur fatale on verifie que la branche est bien dans la liste des possibles
	// -- -> si non, on renvoie vide
	if (!in_array($branche_sup, $liste_branches_spip))
		return '';
	// -- on complete la borne sup de l'intervalle de x.y en x.y.z et on determine la vraie branche
	if (!$t[2]) {
		if ($bornes['max']['incluse'])
			$borne_sup = $infos_branches_spip[$branche_sup][1];
		else {
			$branche_sup = $liste_branches_spip[array_search($branche_sup, $liste_branches_spip)-1];
			$borne_sup = $infos_branches_spip[$branche_sup][1];
		}
	}

	// -- on verifie que les bornes sont bien dans l'ordre : 
	//    -> sinon on retourne la branche sup uniquement
	if (spip_version_compare($borne_inf, $borne_sup, '>='))
		return $branche_sup;

	// A ce stade, on a un intervalle ferme en bornes ou en branches
	// Il suffit de trouver les branches qui y sont incluses, sachant que les branches inf et sup 
	// le sont a coup sur maintenant
	$index_inf = array_search($branche_inf, $liste_branches_spip);
	$index_sup = array_search($branche_sup, $liste_branches_spip);
	$liste = array();
	for ($i = $index_inf; $i <= $index_sup; $i++) {
		$liste[] = $liste_branches_spip[$i];
	}

	return implode(',', $liste);
}

?>
