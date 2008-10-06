<?php
// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

include_spip('inc/charsets');
// en principe, pas besoin de : caractere_utf_8(232)
define('_TYPO_EGRAVE', unicode2charset('&#232;').'|&#232;|&egrave;');
define('_TYPO_EAIGU1', unicode2charset('&#233;').'|&#233;|&eacute;');
define('_TYPO_EAIGU2', unicode2charset('&#201;').'|&#201;|&Eacute;');
define('_TYPO_sup', '<sup class="typo_exposants">\\1</sup>');
define('_TYPO_sup2', '\\1<sup class="typo_exposants">\\2</sup>');

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	static $typo;
	if(!$typo) $typo = array( array(
		'/(?<=\bM)e?(lles?)\b/',		// Mlle(s), Mme(s) et erreurs Melle(s)
		'/(?<=\bM)(gr|mes?)\b/',	// Mme(s) et Mgr

		'/(?<=\b[DP])(rs?)(?=[\s\.-])/',	// Dr(s), Pr(s) suivis d'un espace d'un point ou d'un tiret
		'/(?<=\bS)(te?s?)(?=[\s\.-])/',  // St(e)(s) suivis d'un espace d'un point ou d'un tiret
		'/(?<=\bB)(x|se|ses)(?=[\s\.-])/',  // Bx, Bse(s) suivis d'un espace d'un point ou d'un tiret

		'/\bm²\b/', '/(?<=\bm)([23])\b/',	 // m2, m3, m²
		'/(?<=\b[Mm])([nd]s?)\b/',	// millions, milliards
		'/(?<=\bV)(ves?)\b/', '/(?<=\b[Bb])(ds?)\b/', '/(?<=\bC)(ies?)\b/',	// veuves, boulevard(s) et Cie(s)
		'/(?<=\bS)(t(?:'._TYPO_EAIGU1.')s?)(?=\W)/', '/(?<=\W)(?:E|'._TYPO_EAIGU2.')ts\b/',	 // Societes(s), Etablissements

		'/(?<=\b[1I])i?(ers?)\b/',	// 1er(s), Erreurs 1ier(s), 1ier(s)
		'/(?<=\b[1I])i?(?:e|'._TYPO_EGRAVE.')(res?)\b/',	// Erreurs 1(i)ere(s) + accents
		'/(?<=\b1)(r?es?)\b/', // 1e(s), 1re(s)
		'/(?<=\b2)(nde?s?)\b/',	// 2nd(e)(s)

		'/(\b[0-9IVX]+)i?(?:e|'._TYPO_EGRAVE.')?me(s?)\b/', // Erreurs (i)(e)me(s) + accents
		'/\b([0-9IVX]+)(es?)\b/', // 2e(s), IIIe(s)... (les 1(e?r?s?) ont deja ete remplaces)
		'/\b(\d+)o\b/', // primo, secondo, etc.
	), array(
		_TYPO_sup, _TYPO_sup,		// Mlle(s), Mme(s), Mgr

		_TYPO_sup, _TYPO_sup, _TYPO_sup,	// Dr(s), Pr(s), St(e)(s), Bx, Bse(s)

		'm<sup class="typo_exposants">2</sup>',	_TYPO_sup,	// m2, m3, m²
		_TYPO_sup, _TYPO_sup, _TYPO_sup, _TYPO_sup,	// Vve(s), Mn(s), Md(s), Bd(s), Cie(s)
		_TYPO_sup, '&#201;<sup class="typo_exposants">ts</sup>',	// Ste(s), Ets

		_TYPO_sup, _TYPO_sup, _TYPO_sup, // 1er et Cie
		_TYPO_sup,	// 2nd(e)(s)

		'$1<sup class="typo_exposants">e$2</sup>', // Erreurs me, eme, ème, ième + pluriels
		_TYPO_sup2, // 2e(s), IIIe(s)...
		'$1<sup class="typo_exposants">o</sup>', // 1o, 2o, etc.
	));

	return preg_replace($typo[0], $typo[1], $texte);
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