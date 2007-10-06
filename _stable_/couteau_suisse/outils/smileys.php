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

	// l'ordre des smileys ici est important :
	//  - les doubles, puis les simples, puis les courts
	//  - le raccourci insere par la balise #SMILEYS est la premiere occurence de chaque fichier
	$smileys = array(
	// attention ' est different de ’ (&#8217;) (SPIP utilise/ecrit ce dernier)
	 ":&#8217;-))"=> 'pleure_de_rire.png',
	 ":&#8217;-)"=> 'pleure_de_rire.png',
	 ":&#8217;-D"	=> 'pleure_de_rire.png',
	 ":&#8217;-("	=> 'triste.png',
	
	// les doubles :
	 ':-))'	=> 'mort_de_rire.png',
	 ':))'	=> 'mort_de_rire.png',
	 ":'-))"=> 'pleure_de_rire.png',
	 ':-(('	=> 'en_colere.png',

	// les simples :
	 ';-)'	=> 'clin_d-oeil.png',
	 ':-)'	=> 'sourire.png',
	 ':-D'	=> 'mort_de_rire.png',
	 ":'-)"=> 'pleure_de_rire.png',
	 ":'-D"	=> 'pleure_de_rire.png',
	 ':-('	=> 'pas_content.png',
	 ":'-("	=> 'triste.png',
	 ':-&gt;' => 'diable.png',
	 '|-)'	=> 'rouge.png',
	 ':o)'	=> 'rigolo.png',
	 'B-)'	=> 'lunettes.png',
	 ':-P'	=> 'tire_la_langue.png',
	 ':-p'	=> 'tire_la_langue.png',
	 ':-|'	=> 'bof.png',
	 ':-/'	=> 'mouais.png',
	 ':-O'	=> 'surpris.png',
	 ':-o'	=> 'surpris.png',

	// les courts : tester a l'usage...
	// attention : ils ne sont reconnus que s'il y a un espace avant !
	 ':)'	=> 'sourire.png',
	 ':('	=> 'pas_content.png',
	 ';)'	=> 'clin_d-oeil.png',
	 ':|'	=> 'bof.png',
	 '|)'	=> 'rouge.png',
	 ':/'	=> 'mouais.png',
	);

	$liste = array();
	foreach ($smileys as $smy=>$val) {
		// accessibilite : protection de alt et title
		$alt = '@@64@@'.base64_encode($smy).'@@65@@';
		$espace = strlen($smy)==2?' ':'';
		$smileys2[0][] = $espace.$smy;
		list(,,,$size) = @getimagesize("$path/$val");
		$smileys2[1][] = $img = $espace."<img alt=\"$alt\" title=\"$alt\" class=\"no_image_filtrer format_png\" src=\"$path2/$val\" $size/>";
		$smileys2[2][] = $val;
		// liste des raccourcis et smileys disponibles
		$liste[] = '<strong>'.$smy.'</strong>';
	}
	ecrire_meta('cs_smileys_racc', join(', ', $liste));
	ecrire_meta('cs_smileys', serialize($smileys2));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function smileys_raccourcis() {
	return _T('cout:smileys:aide', array('liste' => $GLOBALS['meta']['cs_smileys_racc']));
}

// fonction de remplacement
// les balises suivantes sont protegees : html|code|cadre|frame|script|acronym|cite
function cs_rempl_smileys($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	$smileys_rempl = unserialize($GLOBALS['meta']['cs_smileys']);
	// smileys a probleme :
	$texte = str_replace(':->', ':-&gt;', $texte); // remplacer > par &gt;
	// remplacer ’ (apostrophe curly) par &#8217;
	$texte = str_replace(':’-', ':&#8217;-', $texte);
	$texte = str_replace(':'.chr(146).'-', ':&#8217;-', $texte);
	// voila, on remplace tous les smileys d'un coup...
	$texte = str_replace($smileys_rempl[0], $smileys_rempl[1], $texte);
	// accessibilite : alt et title avec le smiley en texte
	$texte = preg_replace('/@@64@@([^@]*)@@65@@/e', "base64_decode('\\1')", $texte);
	//while(preg_match('`@@64@@([^@]*)@@65@@`', $texte, $regs)) $texte = str_replace('@@64@@'.$regs[1].'@@65@@', base64_decode($regs[1]), $texte);
//cs_log('smileys traités : '.$texte);
	return $texte;
}

// fonction principale (pipeline pre_typo)
function cs_smileys_pre_typo($texte) {
	if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
	if (!isset($GLOBALS['meta']['cs_smileys']) || isset($GLOBALS['var_mode']))
		smileys_installe();
//cs_log('smileys trouvés !');
	// appeler cs_rempl_smileys() une fois que certaines balises ont ete protegees
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'cs_rempl_smileys', $texte);
}
?>