<?php

// Tweak CHATONS - 30 janvier 2007
// refonte : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1554

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function tweak_rempl_chatons($texte) {
	if (strpos($texte, ':')===false) return $texte;
	$chatons_rempl = unserialize($GLOBALS['meta']['tweaks_chatons']);
	return str_replace($chatons_rempl[0], $chatons_rempl[1], $texte);
}

function chatons_pre_typo($texte) {
	if (strpos($texte, ':')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_chatons']) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul')
		chatons_installe();
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'tweak_rempl_chatons', $texte);
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
function chatons_installe() {
//tweak_log('chatons_installe()');
	$path = dirname(find_in_path('img/chatons/test'));
	$liste = $chatons = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			$chatons[0][] = ':'.$reg[1];
			$liste[] = '<strong>:'.$reg[1].'</strong>';	
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$chatons[1][] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"".tweak_htmlpath($path)."/$reg[1].$reg[2]\" $size/>";
		}
	}
	ecrire_meta('tweaks_chatons_racc', join(', ', $liste));
	ecrire_meta('tweaks_chatons', serialize($chatons));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('tweak:mon_tweak:aide');
function chatons_raccourcis() {
	return _T('tweak:chatons:aide', array('liste' => $GLOBALS['meta']['tweaks_chatons_racc']));
}

?>