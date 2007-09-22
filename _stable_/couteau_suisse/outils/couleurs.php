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
 *   +----------------------------------+
 *    Nom de l'outil : Couleurs dans vos textes
 *   +----------------------------------+
 *    Date : Vendredi 11 août 2003
 *    Idee originale :  Aurelien PIERARD : aurelien.pierard(a)dsaf.pm.gouv.fr
 *    Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville, mars 2007
 *   +-------------------------------------+
 *  
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
		$aide = '<strong>'.join('</strong>, <strong>', array_keys($couleurs_perso)).'</strong>';
	} else {
		$aide = '<strong>'.join('</strong>, <strong>', array_merge($couleurs[0], $couleurs[1])).'</strong>';
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
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function couleurs_raccourcis() {
	return _T('cout:couleurs:aide', array(
		'liste' => $GLOBALS['meta']['cs_couleurs_racc'],
		'fond' => _COULEURS_FONDS==1?_T('cout:couleurs_fonds'):'',
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
	// lecture des metas
	$outil_couleurs = unserialize($GLOBALS['meta']['cs_couleurs']);

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
		$texte = preg_replace(",\[/(couleur|$outil_couleurs[0]|color|$outil_couleurs[1])\],", '</span>', $texte);
	} 
	// cas des couleurs personnalisees
	elseif(_COULEURS_SET===1) {
		// et toutes les balises de fin...
		$texte = preg_replace(",\[/(couleur|$outil_couleurs[0]|color|)\],", '</span>', $texte);
	}
	// patch de conformite : les <span> doivent etre inclus dans les paragraphes
	while (preg_match(",(<span style=\"(background-)?color:[^;]+;\">)([^<]*)\n[\n]+,Sms", $texte, $regs))
		$texte = str_replace($regs[0], "$regs[1]$regs[3]</span>\n\n$regs[1]", $texte);
	// menage
	unset($outil_couleurs);
	return $texte;  
}

function couleurs_pre_typo($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	if (!isset($GLOBALS['meta']['cs_couleurs']) || isset($GLOBALS['var_mode']))
		couleurs_installe();
	// appeler couleurs_rempl() une fois que certaines balises ont ete protegees
	return cs_echappe_balises('', 'couleurs_rempl', $texte);
}

?>