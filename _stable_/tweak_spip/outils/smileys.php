<?php

// Outil SMILEYS - 25 decembre 2006
// serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville, 2006
// Toutes les infos sur : http://www.spip-contrib.net/?article1561
// dessin des frimousses : Sylvain Michel [http://www.guaph.net/]

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function smileys_installe() {
	$path = dirname(find_in_path('img/smileys/test'));
cs_log("smileys_installe() : $path");
	$path2 = cs_htmlpath($path);
cs_log(" -- abs. path = $path2");

	$smileys = array(
	// les doubles :
	 ':-(('	=> 'en_colere.png',
	 ':-))'	=> 'mort_de_rire.png',
	 ':))'	=> 'mort_de_rire.png',
	 ":'-))"=> 'pleure_de_rire.png',
	// attention ' est different de ’ (&#8217;) (SPIP utilise/ecrit ce dernier)
	 ":&#8217;-))"=> 'pleure_de_rire.png',

	// les simples :
	 ':-&gt;' => 'diable.png',
	 ':-('	=> 'pas_content.png',
	 ':-D'	=> 'mort_de_rire.png',
	 ':-)'	=> 'sourire.png',
	 '|-)'	=> 'rouge.png',
	 ":'-)"=> 'pleure_de_rire.png',
	 ":&#8217;-)"=> 'pleure_de_rire.png',
	 ":'-D"	=> 'pleure_de_rire.png',
	 ":&#8217;-D"	=> 'pleure_de_rire.png',
	 ":'-("	=> 'triste.png',
	 ":&#8217;-("	=> 'triste.png',
	 ":-("	=> 'triste.png',
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
	// attention : ils ne sont reconnus que s'il y a un espace avant !
	 ':)'	=> 'sourire.png',
	 ':('	=> 'pas_content.png',
	 ';)'	=> 'clin_d-oeil.png',
	 ':|'	=> 'bof.png',
	 '|)'	=> 'rouge.png',
	 ':/'	=> 'mouais.png',
	 ':('	=> 'pas_content.png',
	);

	$liste = array();
	foreach ($smileys as $smy=>$val) {
		// accessibilite : protection de alt et title
		$alt = '@@64@@'.base64_encode($smy).'@@65@@';
		$espace = strlen($smy)==2?' ':'';
		$smileys2[0][] = $espace.$smy;
		list(,,,$size) = @getimagesize("$path/$val");
		$smileys2[1][] = $img = $espace."<img alt=\"$alt\" title=\"$alt\" class=\"no_image_filtrer format_png\" src=\"$path2/$val\" $size/>";
		// liste des raccourcis disponibles
		//$img = "<img alt=\"$smy\" title=\"$smy\" class=\"format_png\" src=\"$path2/$val\" $size/>";
		$liste[] = '<strong>'.$smy.'</strong>';//.'&nbsp;:&nbsp;'.$img;
	}
	ecrire_meta('tweaks_smileys_racc', join(', ', $liste));
	ecrire_meta('tweaks_smileys', serialize($smileys2));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function smileys_raccourcis() {
	return _T('cout:smileys:aide', array('liste' => $GLOBALS['meta']['tweaks_smileys_racc']));
}

// fonction de remplacement
// les balises suivantes sont protegees : html|code|cadre|frame|script|acronym|cite
function tweak_rempl_smileys($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	$smileys_rempl = unserialize($GLOBALS['meta']['tweaks_smileys']);
	// smileys a probleme :
	$texte = str_replace(':->', ':-&gt;', $texte); // remplacer > par &gt;
	$texte = str_replace(':'.chr(146).'-', ':&#8217;-', $texte); // remplacer ’ (apostrophe curly) par &#8217;
	// voila, on remplace tous les smileys d'un coup...
	$texte = str_replace($smileys_rempl[0], $smileys_rempl[1], $texte);
	// accessibilite : alt et title avec le smiley en texte
	while(preg_match('`@@64@@([^@]*)@@65@@`', $texte, $regs)) $texte = str_replace('@@64@@'.$regs[1].'@@65@@', base64_decode($regs[1]), $texte);
//cs_log('smileys traités : '.$texte);
	return $texte;
}

// fonction principale (pipeline pre_typo)
function tweak_smileys_pre_typo($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_smileys']) || isset($GLOBALS['var_mode']))
		smileys_installe();
//cs_log('smileys trouvés !');
	// appeler tweak_rempl_smileys() une fois que certaines balises ont ete protegees
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'tweak_rempl_smileys', $texte);
}
?>