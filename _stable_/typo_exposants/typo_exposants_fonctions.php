<?php
// Filtre typographique exposants pour langue francaise, par Vincent Ramos
// <spip AD kailaasa PVNCTVM net>, sous licence GNU/GPL.
// Ce filtre emprunte les expressions régulières publiees par Raphaël Meyssen
// sur <http://www.spip-contrib.net/Filtre-typographique-exposants> et 
// ne fonctionne que pour le francais.
// Ce filtre est aussi utilisé dans le plugin tweaks.

function typo_exposants_fr($texte){
	$trouve = array(
		'/(\\bM)(elle|lle)\\b/', // Mlle(s), Mme(s) et erreurs Melle(s)
		'/(\\bM)(elles|lles)\\b/',
		'/(\\bM)(mes?)\\b/',
		'/(\\bD)(rs?)\\b/', // Dr(s), Pr(s), St(e)(s)
		//'/(\\bP)(rs?)\\b/',
		//'/(\\bS)(te?s?)\\b/',
		'/(\\bm)(2|3)\\b/', // m2, m3
		'/(\\b[1I])(er|ier)\\b/', // Erreurs ier, iers, ère, ière, ères, ières
		'/(\\b[1I])(ers|iers)\\b/',
		'/(\\b[1I])(ière|ère|i&egrave;re|&egrave;re)\\b/', 
		'/(\\b[1I])(ières|ères|i&egrave;res|&egrave;res)\\b/',
		'/(\\b[02-9IVX]+)(ième|ème|i&egrave;me|&egrave;me|me)\\b/', // Erreurs me, ème, ième, mes, èmes, ièmes
		'/(\\b[02-9IVX]+)(ièmes|èmes|i&egrave;mes|&egrave;mes|mes)\\b/',
		'/(\\b[1I])(res?)\\b/', // 1re(s)
//		'/(\\b[0-9IVX]+)(er?s?)\\b/' // 1er(s), 2e(s), IIIe(s)...
		'/(\\b[0-9IVX]+)e\\b/' // 1e, 2e, IIIe...
	);
	$remplace = array(
		'M<small><sup>lle</sup></small>', // Mlle(s), Mme(s)
		'M<small><sup>lles</sup></small>',
		'M<small><sup>\\2</sup></small>',
		'D<small><sup>\\2</sup></small>', // Dr(s), Pr(s), St(e)(s)
		//'P<small><sup>\\2</sup></small>',
		//'S<small><sup>\\2</sup></small>',
		'm<small><sup>\\2</sup></small>', // m2, m3
		'\\1<small><sup>er</sup></small>', // Corrige 1er(s), 1re(s)
		'\\1<small><sup>ers</sup></small>',
		'\\1<small><sup>re</sup></small>',
		'\\1<small><sup>res</sup></small>',
		'\\1<small><sup>e</sup></small>', // Corrige 2e(s), IIIe(s)...
		'\\1<small><sup>es</sup></small>',
		'\\1<small><sup>\\2</sup></small>', // 1re(s)
//		'\\1<small><sup>\\2</sup></small>' // 1er(s), 2e(s), IIIe(s)...
		'\\1<small><sup>e</sup></small>' // 1e, 2e, IIIe...

	);

	return preg_replace($trouve, $remplace, $texte);
}

function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	switch (lang_typo($lang)) {
		case 'fr':
			return typo_exposants_fr($texte);
		default:
			return $texte;
	}
}
?>