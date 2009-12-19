<?php

/*
   Cet outil en couleurs permet aux redacteurs d'un site spip de d'appliquer facilement des couleurs aux textes SPIP
   Utilisation pour le redacteur : 
		[rouge]Lorem ipsum dolor sit amet[/rouge]
		[red]Lorem ipsum dolor sit amet[/red]
   Les balises anglaises sont les couleurs utilisees dans les feuilles de style.
   Attention : seules les balises en minuscules sont reconnues.
*/
/*
   +----------------------------------+
    Nom de l'outil : Couleurs dans vos textes
   +----------------------------------+
    Date : Vendredi 11 août 2003
    Idee originale :  Aurelien PIERARD : aurelien.pierard(a)dsaf.pm.gouv.fr
    Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville, mars 2007
	Doc : http://www.spip-contrib.net/?article2427
   +-------------------------------------+
  
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function couleurs_installe() {
cs_log("couleurs_installe()");

	$couleurs = array(
		array('noir', 'rouge', 'marron', 'vert', 'vert olive', 'bleu marine',
		'violet', 'gris', 'argent', 'vert clair', 'bleu', 'fuchia', 'bleu clair', 
		'blanc', 'bleu azur', 'beige', 'brun', 'bleu violet', 'brun clair', 'rose clair', 
		'vert fonce', 'orange fonce', 'mauve fonce', 'bleu ciel', 'or', 'ivoire', 'orange',
		'lavande', 'rose', 'prune', 'saumon', 'neige', 'turquoise', 'jaune paille', 'jaune'),
		array('black', 'red', 'maroon', 'green', 'olive', 'navy',
		'purple', 'gray', 'silver', 'chartreuse', 'blue', 'fuchsia', 'aqua', 
		'white', 'azure', 'bisque', 'brown', 'blueviolet', 'chocolate', 'cornsilk',
		'darkgreen', 'darkorange', 'darkorchid', 'deepskyblue', 'gold', 'ivory', 'orange',
		'lavender', 'pink', 'plum', 'salmon', 'snow', 'turquoise', 'wheat', 'yellow') );
	foreach ($couleurs[0] as $c=>$val) $couleurs[2][$val] = $couleurs[1][$c];

	$perso = trim(_COULEURS_PERSO);
	if (_COULEURS_SET===1) {
		$perso = preg_replace('^\s*(=|,)\s*^','\1', $perso);
		$perso = explode(',', $perso);
		$couleurs_perso = $aide = array();
		foreach($perso as $p) {
			list($a, $b) = explode('=', $p, 2);
			if (strlen($a) && strlen($b)) {
				if(in_array($b, $couleurs[0])) $b = $couleurs[2][$b];
				$couleurs_perso[$a] = $b;
			} elseif (strlen($a)) {
				$b=in_array($a, $couleurs[0])?$couleurs[2][$a]:$a;
				$couleurs_perso[$a] = $b;
			}
		}
		$couleurs[2] = $couleurs_perso;
		$couleurs[0] = join('|', array_keys($couleurs_perso));
		$aide = '<b>'.join('</b>, <b>', array_keys($couleurs_perso)).'</b>';
	} else {
		$aide = '<b>'.join('</b>, <b>', array_merge($couleurs[0], $couleurs[1])).'</b>';
		$couleurs[0] = join('|', $couleurs[0]);
		$couleurs[1] = join('|', $couleurs[1]);
	}
	// sauvegarde en meta : aide
	ecrire_meta('cs_couleurs_racc', $aide);
	// sauvegarde en meta : couleurs
	ecrire_meta('cs_couleurs', serialize($couleurs));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function couleurs_raccourcis() {
	return _T('couteauprive:couleurs:aide', array(
		'liste' => $GLOBALS['meta']['cs_couleurs_racc'],
		'fond' => _COULEURS_FONDS==1?_T('couteauprive:couleurs_fonds'):'',
	));
}

// callbacks
function couleurs_texte_callback($matches) {
	global $outil_couleurs;
	return "<span style=\"color:{$outil_couleurs[2][$matches[1]]};\">";
}
function couleurs_fond_callback($matches) {
	global $outil_couleurs;
	return "<span style=\"background-color:{$outil_couleurs[2][$matches[2]]};\">";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function couleurs_rempl($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	// pour les callbacks
	global $outil_couleurs;

	// voila, on remplace tous les raccourcis $outil_couleurs[0] (balises francaises ou personnalisees)...
	$texte = preg_replace_callback(",\[($outil_couleurs[0])\],", 'couleurs_texte_callback', $texte);
	if(_COULEURS_FONDS===1) {
		$texte = preg_replace_callback(",\[(bg|fond)\s+($outil_couleurs[0])\],", 'couleurs_fond_callback', $texte);
		$texte = preg_replace(",\[/(fond|bg)\],", '</span>', $texte);
		$texte = preg_replace(",\[/(bg|fond)\s+($outil_couleurs[0])\],", '</span>', $texte);
	}
	// cas des 36 couleurs css
	if(_COULEURS_SET===0) {
		// raccourcis anglais, plus facile...
		$texte = preg_replace(",\[($outil_couleurs[1])\],", '<span style="color:$1;">', $texte);
		if(_COULEURS_FONDS===1)
			$texte = preg_replace(",\[(bg|fond)\s+($outil_couleurs[1])\],", '<span style="background-color:$2;">', $texte);
		// et toutes les balises de fin...
		$texte = preg_replace(",\[/(bg|fond)?\s*(couleur|$outil_couleurs[0]|color|$outil_couleurs[1])\],", '</span>', $texte);
	} 
	// cas des couleurs personnalisees
	elseif(_COULEURS_SET===1) {
		// et toutes les balises de fin...
		$texte = preg_replace(",\[/(couleur|$outil_couleurs[0]|color|)\],", '</span>', $texte);
	}
	// patch de conformite : les <span> doivent etre inclus dans les paragraphes
	while (preg_match(",(<span style=\"(background-)?color:[^;]+;\">)([^<]*)\n[\n]+,Sms", $texte, $regs))
		$texte = str_replace($regs[0], "$regs[1]$regs[3]</span>\n\n$regs[1]", $texte);
	return $texte;  
}

function couleurs_pre_typo($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	// les raccoucis de couleur sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_couleurs']))
		couleurs_installe();
	// pour les callbacks
	global $outil_couleurs;
	// lecture des raccoucis de couleur
	$outil_couleurs = unserialize($GLOBALS['meta']['cs_couleurs']);
	// appeler couleurs_rempl() une fois que certaines balises ont ete protegees
	$texte = cs_echappe_balises('', 'couleurs_rempl', $texte);
	// menage
	unset($outil_couleurs);
	// retour
	return $texte;
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function couleurs_BarreTypo($tr) {
	// les raccoucis de couleur sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_couleurs'])) couleurs_installe();
	// le tableau des couleurs est present dans les metas
	$couleurs = unserialize($GLOBALS['meta']['cs_couleurs']);
	$r1 = $r2 = array(); 
	foreach($couleurs[2] as $i=>$v)
		$r1[] = "<a title=\"$i\" href=\"javascript:barre_raccourci('[$i]','[/$i]',@@champ@@)\"><span class=\"cs_BT cs_BTg\" style=\"color:$v;\">A</span></a>";
	$r1 = join(' ', $r1); 
	if(_COULEURS_FONDS===1) {
		foreach($couleurs[2] as $i=>$v)
			$r2[] = "<a title=\"fond $i\" href=\"javascript:barre_raccourci('[fond $i]','[/fond $i]',@@champ@@)\"><span class=\"cs_BT cs_BTg\" style=\"color:$v;\">F</span></a>";
		$r2 = ' '._T('couteauprive:fonds').' '.join(' ', $r2).''; 
	} else $r2='';
	return $tr.'<tr><td><p style="margin:0; line-height:1.9em;">'._T('couteauprive:couleurs:nom')."&nbsp;$r1$r2</div></td></tr>";
}

function couleurs_nettoyer_raccourcis($texte) {
	// les raccoucis de couleur sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_couleurs'])) couleurs_installe();
	// le tableau des smileys est present dans les metas
	$couleurs = unserialize($GLOBALS['meta']['cs_couleurs']);
	$couleurs = _COULEURS_SET===0?"$couleurs[0]|$couleurs[1]":$couleurs[0];
	return preg_replace(",\[/?(bg|fond)?\s*($couleurs|couleur|color)\],i", '', $texte);
}

// pipeline maison permettant l'interpretation de la description d'un outil
function couleurs_pre_description_outil($flux) {
	if($flux[0]!=='couleurs') return $flux;
	return array($flux[0], str_replace(array('@_CS_EXEMPLE_COULEURS@', '@_CS_EXEMPLE_COULEURS2@', '@_CS_EXEMPLE_COULEURS3@'),
		array('<br /><span style="font-weight:normal; font-size:85%;"><span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert&nbsp;olive</span>, <span style="background-color:navy; color:white;">navy/bleu&nbsp;marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert&nbsp;clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu&nbsp;clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu&nbsp;azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu&nbsp;violet</span>, <span style="background-color:chocolate;">chocolate/brun&nbsp;clair</span>, <span style="background-color:cornsilk;">cornsilk/rose&nbsp;clair</span>, <span style="background-color:darkgreen;">darkgreen/vert&nbsp;fonce</span>, <span style="background-color:darkorange;">darkorange/orange&nbsp;fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve&nbsp;fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu&nbsp;ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune&nbsp;paille</span>, <span style="background-color:yellow;">yellow/jaune</span></span><span style="font-size:50%;"><br />&nbsp;</span>',
		"\n-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>\n-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.",
		"\n-* <code>Lorem ipsum [fond rouge]dolor[/fond rouge] sit amet</code>\n-* <code>Lorem ipsum [bg red]dolor[/bg red] sit amet</code>.",
	), $flux[1]));
}

?>