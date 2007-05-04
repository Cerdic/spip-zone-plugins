<?php

// Outils CHATONS - 30 janvier 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1554

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function cs_rempl_chatons($texte) {
	if (strpos($texte, ':')===false) return $texte;
	$chatons_rempl = unserialize($GLOBALS['meta']['tweaks_chatons']);
	return str_replace($chatons_rempl[0], $chatons_rempl[1], $texte);
}

function chatons_pre_typo($texte) {
	if (strpos($texte, ':')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_chatons']) || isset($GLOBALS['var_mode']))
		chatons_installe();
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'cs_rempl_chatons', $texte);
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function chatons_installe() {
//cs_log('chatons_installe()');
	$path = dirname(find_in_path('img/chatons/test'));
	$liste = $chatons = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			$chatons[0][] = ':'.$reg[1];
			$liste[] = '<strong>:'.$reg[1].'</strong>';	
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$chatons[1][] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"".cs_htmlpath($path)."/$reg[1].$reg[2]\" $size/>";
		}
	}
	ecrire_meta('tweaks_chatons_racc', join(', ', $liste));
	ecrire_meta('tweaks_chatons', serialize($chatons));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function chatons_raccourcis() {
	return _T('cout:chatons:aide', array('liste' => $GLOBALS['meta']['tweaks_chatons_racc']));
}

?>