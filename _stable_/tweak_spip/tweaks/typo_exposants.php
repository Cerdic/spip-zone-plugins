<?php
// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	$trouve = array(
		'/(\\bM)e?(lles?)\\b/',		// Mlle(s), Mme(s) et erreurs Melle(s)
		'/(\\bM)(mes?)\\b/',

		'/\b(D|P)(rs?)([\s\.-])/',	// Dr(s), Pr(s), St(e)(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(S)(te?s?)([\s\.-])/',

		'/(\\bm)(2|3)\\b/',			// m2, m3

		'/(\\b[1I])i?(ers?)\\b/',	// Erreurs ier, iers
		'/(\\b[1I])(iè|è|i&egrave;|&egrave;)(res?)\\b/',	// Erreurs ère, ière, ères, ières
		'/(\\b1)(r?es?)\\b/', // 1e(s), 1re(s)

		'/(\\b[02-9IVX]+)(ième|ème|i&egrave;me|&egrave;me|me)(s?)\\b/', // Erreurs me, ème, ième, mes, èmes, ièmes
		'/(\\b[02-9IVX]+)(es?)\\b/', // 2e(s), IIIe(s)...
	);
	$remplace = array(
		'M<small><sup>\\2</sup></small>',		// Mlle(s), Mme(s)
		'M<small><sup>\\2</sup></small>',

		'\\1<small><sup>\\2</sup></small>\\3',	// Dr(s), Pr(s), St(e)(s)
		'\\1<small><sup>\\2</sup></small>\\3',

		'm<small><sup>\\2</sup></small>',	// m2, m3

		'\\1<small><sup>\\2</sup></small>', // Corrige 1er(s), 1re(s)
		'\\1<small><sup>\\3</sup></small>',
		'\\1<small><sup>\\2</sup></small>', // 1e(s), 1re(s)

		'\\1<small><sup>e\\3</sup></small>', // Corrige 2e(s), IIIe(s)...
		'\\1<small><sup>\\2</sup></small>', // 2e(s), IIIe(s)...
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