<?php

/*
   Cet outil 'decoration' permet aux redacteurs d'un site spip de d'appliquer les styles souligné, barré, au dessus aux textes SPIP
   Attention : seules les balises en minuscules sont reconnues.
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function decoration_installe() {
cs_log("decoration_installe()");
	// on decode les balises entrees par le webmaster
	$balises = preg_split("/[\r\n]+/", trim(_decoration_BALISES));
	$aide = $chevrons1 = $chevrons2 = $styles = $alias = array();
	foreach ($balises as $balise) {
		if (preg_match('/(span|div)\.([^=]+)=(.+)$/', $balise, $regs)) {
			list($div, $racc, $style) = array($regs[1], trim($regs[2]), trim($regs[3]));
			$aide[] = $racc; $chevrons1[] = "<$racc>"; $chevrons2[] = "</$racc>";
			$styles[] = "<$regs[1] style=\"$style\">"; $fins[] = "</$regs[1]>";
		} elseif (preg_match('/([^=]+)=(.+)$/', $balise, $regs)) {
			$alias[trim($regs[1])] = trim($regs[2]);
		}
	}
	// ajout des alias qu'on a trouves
	foreach ($alias as $a=>$v) if(($i=array_search($v, $aide, true))!==false) {
		$aide[] = $a; $chevrons1[] = "<$a>"; $chevrons2[] = "</$a>";
		$styles[] = $styles[$i]; $fins[] = $fins[$i];
	}
	// liste des balises disponibles
	$aide = '<strong>'.join('</strong>, <strong>', $aide).'</strong>';
	// sauvegarde en meta : aide
	ecrire_meta('cs_decoration_racc', $aide);
	// sauvegarde en meta : decoration
	ecrire_meta('cs_decoration', serialize(array(array_merge($chevrons1, $chevrons2), array_merge($styles, $fins))));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function decoration_raccourcis() {
//	$liste = '<strong>sc</strong>, <strong>souligne</strong>, <strong>barre</strong>, <strong>dessus</strong>, <strong>clignote</strong>, <strong>surfluo</strong>, <strong>surgris</strong>';
	return _T('cout:decoration:aide', array('liste' => $GLOBALS['meta']['cs_decoration_racc']));
}


// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function decoration_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	$balises = unserialize($GLOBALS['meta']['cs_decoration']);
	// facile : on remplace tout d'un coup !
	return str_replace($balises[0], $balises[1], $texte);
}

// fonction pipeline
function decoration_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	if (!isset($GLOBALS['meta']['cs_decoration']) || isset($GLOBALS['var_mode']))
		decoration_installe();
	return cs_echappe_balises('', 'decoration_rempl', $texte);
}

?>