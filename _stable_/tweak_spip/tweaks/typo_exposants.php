<?php
// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	$sup='<small style="display:inline;"><sup>';
	$fin='</sup></small>';
	$trouve = array(
		'/(\\bM)e?(lles?)\\b/',		// Mlle(s), Mme(s) et erreurs Melle(s)
		'/(\\bM)(mes?)\\b/',

		'/\b(D|P)(rs?)([\s\.-])/',	// Dr(s), Pr(s), St(e)(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(S)(te?s?)([\s\.-])/',

		'/(\\bm)(2|3)\\b/',			// m2, m3

		'/(\\b[1I])i?(ers?)\\b/',	// Erreurs ier, iers
		'/(\\b[1I])(i�|�|i&egrave;|&egrave;)(res?)\\b/',	// Erreurs �re, i�re, �res, i�res
		'/(\\b1)(r?es?)\\b/', // 1e(s), 1re(s)

		'/(\\b[02-9IVX]+)(i�me|�me|i&egrave;me|&egrave;me|me)(s?)\\b/', // Erreurs me, �me, i�me, mes, �mes, i�mes
		'/(\\b[02-9IVX]+)(es?)\\b/', // 2e(s), IIIe(s)...
	);
	$remplace = array(
		"M$sup\\2$fin",		// Mlle(s), Mme(s)
		"M$sup\\2$fin",

		"\\1$sup\\2$fin\\3",	// Dr(s), Pr(s), St(e)(s)
		"\\1$sup\\2$fin\\3",

		"m$sup\\2$fin",	// m2, m3

		"\\1$sup\\2$fin", // Corrige 1er(s), 1re(s)
		"\\1$sup\\3$fin",
		"\\1$sup\\2$fin", // 1e(s), 1re(s)

		"\\1{$sup}e\\3$fin", // Corrige 2e(s), IIIe(s)...
		"\\1$sup\\2$fin", // 2e(s), IIIe(s)...
	);

	return preg_replace($trouve, $remplace, $texte);
}

function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	switch (lang_typo($lang)) {
		case 'fr':
			return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'typo_exposants_fr', $texte);
		default:
			return $texte;
	}
}
?>