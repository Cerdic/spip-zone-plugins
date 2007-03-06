<?php

// Tweak SMILEYS - 25 décembre 2006
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1561

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
function smileys_installe() {
//tweak_log('smileys_installe()');
	$path = dirname(find_in_path('img/smileys/test'));

	$smileys = array(
	// les doubles :
	 ':-(('	=> 'en_colere.png',
	 ':-))'	=> 'mort_de_rire.png',
	 ':))'	=> 'mort_de_rire.png',
	 ":'-))"=> 'pleure_de_rire.png',
	 ":’-))"=> 'pleure_de_rire.png',
	
	// les simples :
	// ':->'	=> 'diable.png',	// remplace par le suivant...
	 ':-&gt;' => 'diable.png',
	 ':-('	=> 'pas_content.png',
	 ':-D'	=> 'mort_de_rire.png',
	 ':-)'	=> 'sourire.png',
	 '|-)'	=> 'rouge.png',
	 ":'-)"=> 'pleure_de_rire.png',
	 ":'-D"	=> 'pleure_de_rire.png',
	 ":'-("	=> 'triste.png',
	 ":’-)"=> 'pleure_de_rire.png',
	 ":’-D"	=> 'pleure_de_rire.png',
	 ":’-("	=> 'triste.png',
	 ':o)'	=> 'rigolo.png',
	 'B-)'	=> 'lunettes.png',
	 ';-)'	=> 'clin_d-oeil.png',
	 ':-p'	=> 'tire_la_langue.png',
	 ':-P'	=> 'tire_la_langue.png',
	 ':-|'	=> 'bof.png',
	 ':-/'	=> 'mouais.png',
	 ':-o'	=> 'surpris.png',
	 ':-O'	=> 'surpris.png',
	
	// les courts : tester a l'usage...
	// reconnus uniquement s'i y a un espace avant
	 ':)'	=> 'sourire.png',
	 ':('	=> 'pas_content.png',
	 ';)'	=> 'clin_d-oeil.png',
	 ':|'	=> 'bof.png',
	 '|)'	=> 'rouge.png',
	 ':/'	=> 'mouais.png',	// conflit avec 'http://' par exemple
	 ':('	=> 'pas_content.png',
	);

	// accessibilite : protection de alt et title
	foreach ($smileys as $smy=>$val) {
		$alt = '@@64@@'.base64_encode($smy).'@@65@@';
		$espace = strlen($smy)==2?' ':'';
		$smileys2[0][] = $espace.$smy;
		list(,,,$size) = @getimagesize("$path/$val");
		$smileys2[1][] = $espace."<img alt=\"$alt\" title=\"$alt\" class=\"no_image_filtrer\" src=\"".tweak_htmlpath($path)."/$val\" $size/>";
	}
	ecrire_meta('tweaks_smileys', serialize($smileys2));
	ecrire_metas();
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function tweak_rempl_smileys($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	$smileys_rempl = unserialize($GLOBALS['meta']['tweaks_smileys']);
	// smileys a probleme :
	$texte = str_replace(':->', ':-&gt;', $texte);
	$texte = str_replace($smileys_rempl[0], $smileys_rempl[1], $texte);
	// accessibilite : alt et title avec le smiley en texte
	while(preg_match('`@@64@@([^@]*)@@65@@`', $texte, $regs)) $texte = str_replace('@@64@@'.$regs[1].'@@65@@', base64_decode($regs[1]), $texte);
//tweak_log('smileys traités : '.$texte);
	return $texte;
}

function tweak_smileys_pre_typo($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_smileys']) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul')
		smileys_installe();
//tweak_log('smileys trouvés !');
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'tweak_rempl_smileys', $texte);
}
?>