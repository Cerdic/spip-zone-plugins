<?php
// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

include_spip('inc/charsets');
// en principe, pas besoin de : caractere_utf_8(232)
define('_TYPO_EGRAVE', unicode2charset('&#232;').'|&#232;|&egrave;');
define('_TYPO_sup', '<sup class="typo_exposants">\\1</sup>');
define('_TYPO_Msup', 'M'._TYPO_sup);
define('_TYPO_Psup', '\\1<sup class="typo_exposants">\\2</sup>');
define('_TYPO_Dsup', _TYPO_Psup.'\\3');

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	$trouve = array(
		'/\bMe?(lles?)\b/',		// Mlle(s), Mme(s) et erreurs Melle(s)
		'/\bM(mes?)\b/',
		'/\bM(gr)\b/',		// Mgr

		'/\b(D|P)(rs?)([\s\.-])/',	// Dr(s), Pr(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(S)(te?s?)([\s\.-])/',  // St(e)(s) suivis d'un espace d'un point ou d'un tiret
		'/\b(B)(x|se|ses)([\s\.-])/',  // Bx, Bse(s) suivis d'un espace d'un point ou d'un tiret

		'/\bm(2|3)\b/',	 // m2, m3, m²
		'/\bm²\b/',

		'/(\\b[1I])i?(ers?)\\b/',	// Erreurs ier, iers
		'/(\\b[1I])i?(?:'._TYPO_EGRAVE.')(res?)\\b/',	// Erreurs ère(s), ière(s)
		'/(\\b1)(r?es?)\\b/', // 1e(s), 1re(s)
		'/\\b2(nde?s?)\\b/',	// 2nd(e)(s)

		'/(\\b[0-9IVX]+)i?(?:e|'._TYPO_EGRAVE.')?me(s?)\\b/', // Erreurs me, eme, ème, ième + pluriels
		'/\b([0-9IVX]+)(es?)\b/', // 2e(s), IIIe(s)... (les 1(e?r?s?) ont deja ete remplaces)
	);
	$remplace = array(
		_TYPO_Msup, _TYPO_Msup, _TYPO_Msup,		// Mlle(s), Mme(s)

		_TYPO_Dsup, _TYPO_Dsup, _TYPO_Dsup,	// Dr(s), Pr(s), St(e)(s), Bx, Bse(s)

		'm'._TYPO_sup,	// m2, m3, m²
		'm<sup class="typo_exposants">2</sup>',

		_TYPO_Psup, _TYPO_Psup, _TYPO_Psup, // 1er et Cie
		'2'._TYPO_sup,	// 2nd(e)(s)

		'\\1<sup class="typo_exposants">e\\2</sup>', // Erreurs me, eme, ème, ième + pluriels
		_TYPO_Psup, // 2e(s), IIIe(s)...
	);

	return preg_replace($trouve, $remplace, $texte);
}

function typo_exposants_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'EXPO');
}

function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	// prudence : on protege les balises <a> et <img>
	if (strpos($texte, '<')!==false) 
		$texte = preg_replace_callback('/(<(a|img) [^>]+>)/Ums', 'typo_exposants_echappe_balises_callback', $texte);
	switch (lang_typo($lang)) {
		case 'fr':
			$texte = cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'typo_exposants_fr', $texte);
			break;
		default:
	}
	return echappe_retour($texte, 'EXPO');
}
?>