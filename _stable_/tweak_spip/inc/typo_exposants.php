<?php
// Filtre typographique exposants pour langue francaise
// Cette fonction emprunte les expressions régulières publiees par Raphaël Meyssen
// sur <http://www.spip-contrib.net/Filtre-typographique-exposants>.

// cette fonction ne fonctionne que pour le francais
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

// evite les transformations dans les balises <cadre>, <code>, <acronym> et <cite>
function typo_exposants_filtre($texte, $lang){ 
//	echo $texte;
	$t=preg_split(',<(\/?)(cadre|code|acronym|cite)>,', $texte, 3, PREG_SPLIT_DELIM_CAPTURE);
//	print_r($t);
	if ($t[2]=='' || $t[5]=='') return $texte;
	$fonc = 'typo_exposants_' . $lang;
	if ($t[1]=='' && $t[2]==$t[5] && $t[4]=='/') 
		return $fonc($t[0]).'<'.$t[2].'>'.$t[3].'</'.$t[5].'>'.typo_exposants_filtre($t[6], $lang);
	else 
		return $fonc($t[0]).'<'.$t[1].$t[2].'>'.$t[3].typo_exposants_filtre('<'.$t[4].$t[5].'>'.$t[6], $lang);
}
function typo_exposants($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	switch (lang_typo($lang)) {
		case 'fr':
			return typo_exposants_filtre($texte, 'fr');
		default:
			return $texte;
	}
}
?>