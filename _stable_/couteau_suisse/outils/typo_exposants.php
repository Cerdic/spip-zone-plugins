<?php
// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	$sup='<sup class="typo_exposants">';
	$fin='</sup>';
	$trouve = array(
		'/(\\bM)e?(lles?)\\b/',		// Mlle(s), Mme(s) et erreurs Melle(s)
		'/(\\bM)(mes?)\\b/',

		'/(\\bM)(gr)\\b/',		// Mgr

		'/\b(D|P)(rs?)([\s\.-])/',	// Dr(s), Pr(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(S)(te?s?)([\s\.-])/',  // St(e)(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(B)(x|se|ses)([\s\.-])/',  // Bx, Bse(s) suivis d'un espace d'un point ou d'un tiret

		'/(\\bm)(2|3)\\b/',	 // m2, m3, m²
		'/\bm²\b/',

		'/(\\b[1I])i?(ers?)\\b/',	// Erreurs ier, iers
		'/(\\b[1I])(iè|è|i&egrave;|&egrave;)(res?)\\b/',	// Erreurs ère, ière, ères, ières
		'/(\\b1)(r?es?)\\b/', // 1e(s), 1re(s)

		'/(\\b[02-9IVX]+)(ième|ème|i&egrave;me|&egrave;me|me)(s?)\\b/', // Erreurs me, ème, ième, mes, èmes, ièmes
		'/\b([0-9IVX]+?)(es?)\b/', // 2e(s), IIIe(s)... (les 1(e?r?s?) ont deja ete remplaces)
	);
	$remplace = array(
		"M$sup\\2$fin",		// Mlle(s), Mme(s)
		"M$sup\\2$fin",
		"M$sup\\2$fin",

		"\\1$sup\\2$fin\\3",	// Dr(s), Pr(s), St(e)(s), Bx, Bse(s)
		"\\1$sup\\2$fin\\3",
		"\\1$sup\\2$fin\\3",

		"m$sup\\2$fin",	// m2, m3, m²
		"m{$sup}2$fin",

		"\\1$sup\\2$fin", // Corrige 1er(s), 1re(s)
		"\\1$sup\\3$fin",
		"\\1$sup\\2$fin", // 1e(s), 1re(s)

		"\\1{$sup}e\\3$fin", // Corrige 2e(s), IIIe(s)...
		"\\1$sup\\2$fin", // 2e(s), IIIe(s)...
	);

	return preg_replace($trouve, $remplace, $texte);
}

function typo_exposants_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'EXPO');
}

function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	// prudence : on protege les balises <a>
	if (strpos($texte, '<a ')!==false) 
		$texte = preg_replace_callback('/(<a [^>]+>)/Ums', 'typo_exposants_echappe_balises_callback', $texte);
	switch (lang_typo($lang)) {
		case 'fr':
			$texte = cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'typo_exposants_fr', $texte);
			break;
		default:
	}
	return echappe_retour($texte, 'EXPO');
}
?>