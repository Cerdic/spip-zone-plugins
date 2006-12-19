<?php
// Filtre typographique exposants
// Cette fonction emprunte les expressions régulières publiées par Raphaël Meyssen
// sur <http://www.spip-contrib.net/Filtre-typographique-exposants>.

function typo_exposants($texte){

	$trouve = array(
		'/(\\bM)(elle|lle)\\b/', // Mlle(s), Mme(s) et erreurs Melle(s)
		'/(\\bM)(elles|lles)\\b/',
		'/(\\bM)(mes?)\\b/',
		'/(\\bD)(rs?)\\b/', // Dr(s), Pr(s), St(e)(s)
		//'/(\\bP)(rs?)\\b/',
		//'/(\\bS)(te?s?)\\b/',
		'/(\\bm)(2|3)\\b/', // m2, m3
		'/(\\b[1I])(ier)\\b/', // Erreurs ier, iers, ère, ière, ères, ières
		'/(\\b[1I])(iers)\\b/',
		'/(\\b[1I])(ière|ère|i&egrave;re|&egrave;re)\\b/', 
		'/(\\b[1I])(ières|ères|i&egrave;res|&egrave;res)\\b/',
		'/(\\b[02-9IVX]+)(ième|ème|i&egrave;me|&egrave;me|me)\\b/', // Erreurs me, ème, ième, mes, èmes, ièmes
		'/(\\b[02-9IVX]+)(ièmes|èmes|i&egrave;mes|&egrave;mes|mes)\\b/',
		'/(\\b[1I])(res?)\\b/', // 1re(s)
		'/(\\b[0-9IVX]+)(er?s?)\\b/' // 1er(s), 2e(s), IIIe(s)...
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
		'\\1<small><sup>\\2</sup></small>' // 1er(s), 2e(s), IIIe(s)...

	);

	$texte=preg_replace($trouve, $remplace, $texte);

	return $texte;
}
?>