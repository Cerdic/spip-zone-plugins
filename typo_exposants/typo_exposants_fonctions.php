<?php


// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// si $fonction = false, alors le texte est retourne simplement protege
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
// si $balises = false alors le texte est utilise tel quel
function cs_typo_echappe_balises($balises, $fonction, $texte, $arg=NULL){
	if(!strlen($texte)) return '';
	if (($fonction!==false) && !function_exists($fonction)) {
		spip_log("Erreur - cs_typo_echappe_balises() : $fonction() non definie !");
		return $texte;
	}
	// protection du texte
	if($balise!==false) {
		if(!strlen($balises)) $balises = 'html|code|cadre|frame|script';
		$balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
		include_spip('inc/texte');
		$texte = echappe_html($texte, 'CS', true, $balises);
	}
	// retour du texte simplement protege
	if ($fonction===false) return $texte;
	// retour du texte transforme par $fonction puis deprotege
	return echappe_retour($arg==NULL?$fonction($texte):$fonction($texte, $arg), 'CS');
}
// Echapper les elements perilleux en les passant en base64
// Creer un bloc base64 correspondant a $rempl ; au besoin en marquant
// une $source differente ; optimisation du code spip !
// echappe_retour() permet de revenir en arriere
function cs_typo_code_echappement($rempl, $source='') {
	// Convertir en base64
	$base64 = base64_encode($rempl);
	// guillemets simple dans la balise pour simplifier l'outil 'guillemets'
	return "<span class='base64$source' title='$base64'></span>";
}


// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : https://contrib.spip.net/?article1564

// TODO : raccourci pour les exposants et indices (Pouce^2 ou Pouce^2^, H_2O ou H_2_O ou H,,2,,O
// exemple : https://zone.spip.org/trac/spip-zone/wiki/WikiFormatting

include_spip('inc/charsets');
@define('_TYPO_sup', '<sup class="typo_exposants">\\1</sup>');
@define('_TYPO_sup2', '\\1<sup class="typo_exposants">\\2</sup>');

// cette fonction ne fonctionne que pour l'anglais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_en($texte){
	static $typo;
	if(!$typo) $typo = array( array(
		',(?<=1)(st)\b,',
		',(?<=2)(nd)\b,',
		',(?<=3)(rd)\b,',
		',(?<=\d)(th)\b,',
	), array(
		_TYPO_sup, _TYPO_sup, _TYPO_sup, _TYPO_sup,
	));
	return preg_replace($typo[0], $typo[1], $texte);
}

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function typo_exposants_fr($texte){
	static $typo = null;
	static $egrave; static $eaigu1; static $eaigu2; static $accents;
	if (is_null($typo)) {
		// en principe, pas besoin de : caractere_utf_8(232)
		$egrave = unicode2charset('&#232;').'|&#232;|&egrave;';
		$eaigu1 = unicode2charset('&#233;').'|&#233;|&eacute;';
		$eaigu2 = unicode2charset('&#201;').'|&#201;|&Eacute;';
		$accents = unicode2charset('&#224;&#225;&#226;&#228;&#229;&#230;&#232;&#233;&#234;&#235;&#236;&#237;&#238;&#239;&#242;&#243;&#244;&#246;&#249;&#250;&#251;&#252;');
		$typo = array( array(
			'/(?<=\bM)e?(lles?)\b/u',		// Mlle(s), Mme(s) et erreurs Melle(s)
			'/(?<=\bM)(gr|mes?)\b/u',	// Mme(s) et Mgr
			'/(?<=\b[DP])(r)(?=[\s\.-])/u',	// Dr, Pr suivis d'un espace d'un point ou d'un tiret

			'/\bm≤\b/', '/(?<=\bm)([23])\b/u',	 // m2, m3, m≤
			'/(?<=\b[Mm])([nd]s?)\b/u',	// millions, milliards
			'/(?<=\bV)(ve)\b/', '/(?<=\bC)(ies?)\b/u',	// Vve et Cie(s)
			"/(?<=\bS)(t(?:$eaigu1)s?)(?=\W)/u", "/(?<=\W)(?:E|$eaigu2)ts\b/u",	 // Societes(s), Etablissements
	
			'/(?<=\b[1I])i?(ers?)\b/u',	// 1er(s), Erreurs 1ier(s), 1ier(s)
			"/(?<=\b[1I])i?(?:e|$egrave)(res?)\b/u",	// Erreurs 1(i)ere(s) + accents
			'/(?<=\b1)(r?es?)\b/u', // 1e(s), 1re(s)
			'/(?<=\b2)(nde?s?)\b/u',	// 2nd(e)(s)
	
			"/(\b[0-9IVX]+)i?(?:e|$egrave)?me(s?)\b/u", // Erreurs (i)(e)me(s) + accents
			'/\b([0-9IVX]+)(es?)\b/u', // 2e(s), IIIe(s)... (les 1(e?r?s?) ont deja ete remplaces)
			"/(?<![;$accents])\b(\d+|r|v)o\b/u", // recto, verso, primo, secondo, etc.
			'/(?<=\bM)(e)(?= [A-Z])/u', // Maitre (suivi d'un espace et d'une majuscule)
		), array(
			_TYPO_sup, _TYPO_sup,		// Mlle(s), Mme(s), Mgr
			_TYPO_sup,		// Dr, Pr, 
	
			'm<sup class="typo_exposants">2</sup>',	_TYPO_sup,	// m2, m3, m≤
			_TYPO_sup, _TYPO_sup, _TYPO_sup,	// Vve, Mn(s), Md(s), Bd(s), Cie(s)
			_TYPO_sup, '&#201;<sup class="typo_exposants">ts</sup>',	// StÈ(s), Ets
	
			_TYPO_sup, _TYPO_sup, _TYPO_sup, // 1er et Cie
			_TYPO_sup,	// 2nd(e)(s)
	
			'$1<sup class="typo_exposants">e$2</sup>', // Erreurs me, eme, Ëme, iËme + pluriels
			_TYPO_sup2, // 2e(s), IIIe(s)...
			'$1<sup class="typo_exposants">o</sup>', // ro, vo, 1o, 2o, etc.
			_TYPO_sup,	// Me
		));

		if(defined('_CS_EXPO_BOFBOF')) {
			$typo[0] = array_merge($typo[0], array(
				'/(?<=\bS)(te?s?)(?=[\s\.-])/u',  // St(e)(s) suivis d'un espace d'un point ou d'un tiret
				'/(?<=\bB)(x|se|ses)(?=[\s\.-])/u',  // Bx, Bse(s) suivis d'un espace d'un point ou d'un tiret
				'/(?<=\b[Bb])(ds?)\b/u',	 '/(?<=\b[Ff])(gs?)\b/u', // boulevard(s) et faubourgs(s)
			));
			$typo[1] = array_merge($typo[1], array(
				_TYPO_sup, _TYPO_sup,	// St(e)(s), Bx, Bse(s)
				_TYPO_sup, _TYPO_sup,	// Bd(s) et Fg(s)
			));
		}
	}
	return preg_replace($typo[0], $typo[1], $texte);
}

function typo_exposants_echappe_balises_callback($matches) {
 return cs_typo_code_echappement($matches[1], 'EXPO');
}

// ici on est en pipeline post_typo
function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	if(!function_exists($fonction = 'typo_exposants_'.lang_typo($lang))) return $texte;
	// prudence : on protege les balises <a> et <img>
	if (strpos($texte, '<')!==false)
		$texte = preg_replace_callback('/(<(a|img) [^>]+>)/Ums', 'typo_exposants_echappe_balises_callback', $texte);
	$texte = cs_typo_echappe_balises('html|code|cadre|frame|script|acronym|cite', $fonction, $texte);
	return echappe_retour($texte, 'EXPO');
}
?>