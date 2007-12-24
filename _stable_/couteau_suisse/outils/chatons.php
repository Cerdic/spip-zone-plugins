<?php

// Outils CHATONS - 30 janvier 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article2166

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function cs_rempl_chatons($texte) {
	if (strpos($texte, ':')===false) return $texte;
	$chatons_rempl = unserialize($GLOBALS['meta']['cs_chatons']);
	return str_replace($chatons_rempl[0], $chatons_rempl[1], $texte);
}

function chatons_pre_typo($texte) {
	if (strpos($texte, ':')===false) return $texte;
	if (!isset($GLOBALS['meta']['cs_chatons']))
		chatons_installe();
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'cs_rempl_chatons', $texte);
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function chatons_installe() {
//cs_log('chatons_installe()');
	$liste = $chatons = array();
	$path = find_in_path('img/chatons');
	$dossier = opendir($path);
	if($path) while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			$chatons[0][] = ':'.$reg[1];
			$liste[] = '<b>:'.$reg[1].'</b>';	
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$chatons[1][] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"".url_absolue($path)."/$reg[1].$reg[2]\" $size/>";
		}
	}
	ecrire_meta('cs_chatons_racc', join(', ', $liste));
	ecrire_meta('cs_chatons', serialize($chatons));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('desc:un_outil:aide');
function chatons_raccourcis() {
	return _T('desc:chatons:aide', array('liste' => $GLOBALS['meta']['cs_chatons_racc']));
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function chatons_BarreTypo($tr) {
	if (!isset($GLOBALS['meta']['cs_chatons']))	chatons_installe();
	// le tableau des chatons est present dans les metas
	$chatons = unserialize($GLOBALS['meta']['cs_chatons']);
	$max = count($chatons[0]);
	$res = '';
	for ($i=0; $i<$max; $i++)
		$res .= "<a href=\"javascript:barre_inserer('{$chatons[0][$i]}',@@champ@@)\">{$chatons[1][$i]}</a>";
	return $tr.'<tr><td><@@span@@>'._T('desc:chatons:nom')."</span>&nbsp;$res</td></tr>";
}

?>