<?php

/*
   Cet outil 'decoration' permet aux redacteurs d'un site spip de d'appliquer les styles souligné, barré, au dessus aux textes SPIP
   Attention : seules les balises en minuscules sont reconnues.
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function decoration_installe() {
	if(!defined('_decoration_BALISES')) return;
cs_log("decoration_installe()");
	// on decode les balises entrees dans la config
	$deco_balises = preg_split("/[\r\n]+/", trim(_decoration_BALISES));
	$aide = $chevrons1 = $chevrons2 = $styles = $fins = $alias = $auto_bal = $auto_rempl = array();
	foreach ($deco_balises as $balise) {
		if (preg_match('/(span|div|auto)\.([^.]+)\.(class|lang)\s*=(.+)$/', $balise, $regs)) {
			// les class/lang
			list($div, $racc, $class) = array($regs[1], trim($regs[2]), trim($regs[4]));
			if ($div=='auto') {
				$auto_bal[] = preg_quote($racc, ','); 
				$auto_rempl[$racc] = "$regs[3]=\"$class\">";
			} else {
				$aide[] = $racc; $chevrons1[] = "<$racc>"; $chevrons2[] = "</$racc>";
				$styles[] = "<$regs[1] $regs[3]=\"$class\">"; $fins[] = "</$regs[1]>";
			}
		} elseif (preg_match('/(span|div|auto)\.([^=]+)=(.+)$/', $balise, $regs)) {
			// les styles inline
			list($div, $racc, $style) = array($regs[1], trim($regs[2]), trim($regs[3]));
			if ($div=='auto') {
				$auto_bal[] = preg_quote($racc, ','); 
				$auto_rempl[$racc] = "style=\"$style\">";
			} else {
				$aide[] = $racc; $chevrons1[] = "<$racc>"; $chevrons2[] = "</$racc>";
				$styles[] = "<$regs[1] style=\"$style\">"; $fins[] = "</$regs[1]>";
			}
		} elseif (preg_match('/([^=]+)=(.+)$/', $balise, $regs)) {
			// les alias
			$alias[trim($regs[1])] = trim($regs[2]);
		}
	}
	// ajout des alias qu'on a trouves
	foreach ($alias as $a=>$v) 
		if(($i=array_search($v, $aide, true))!==false) {
			$aide[] = $a; $chevrons1[] = "<$a>"; $chevrons2[] = "</$a>";
			$styles[] = $styles[$i]; $fins[] = $fins[$i];
		} elseif(($i=array_search(preg_quote($v), $auto_bal, true))!==false) {
			$auto_bal[] = $a;
			$auto_rempl[$a] = $auto_rempl[$v];
		}
	// liste des balises disponibles
	$aide = '<strong>'.join('</strong>, <strong>', array_merge($aide, $auto_bal)).'</strong>';
	// sauvegarde en meta : aide
	ecrire_meta('cs_decoration_racc', $aide);
	// sauvegarde en meta : decoration
	ecrire_meta('cs_decoration', serialize(array(
		// balises fixes a trouver
		array_merge($chevrons1, $chevrons2), 
		// replacement des balises fixes
		array_merge($styles, $fins),
		// RegExpr pour les balises automatiques
		count($auto_bal)?',<('.join('|', $auto_bal).')>(.*?)</\1>,ms':'',
		// association pour les balises automatiques
		$auto_rempl
	)));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function decoration_raccourcis() {
	return _T('cout:decoration:aide', array('liste' => $GLOBALS['meta']['cs_decoration_racc']));
}

function decoration_callback($matches) {
	global $deco_balises;
	return cs_block($matches[2])
			?'<div ' . $deco_balises[3][$matches[1]] . $matches[2] . '</div>'
			:'<span ' . $deco_balises[3][$matches[1]] . $matches[2] . '</span>';
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function decoration_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	global $deco_balises;
	// balises fixes, facile : on remplace tout d'un coup !
	$texte = str_replace($deco_balises[0], $deco_balises[1], $texte);
	// balises automatiques, plus long : il faut un callback pour analyser l'interieur du texte
	return strlen($deco_balises[2])
		?preg_replace_callback($deco_balises[2], 'decoration_callback', $texte)
		:$texte;
}

// fonction pipeline
function decoration_pre_typo($texte) {
	if (strpos($texte, '<')===false || !defined('_decoration_BALISES')) return $texte;
	if (!isset($GLOBALS['meta']['cs_decoration']) || isset($GLOBALS['var_mode']))
		decoration_installe();
	// pour les callbacks
	global $deco_balises;
	// lecture des balises et des remplacements
	$deco_balises = unserialize($GLOBALS['meta']['cs_decoration']);
	// on remplace apres echappement
	$texte = cs_echappe_balises('', 'decoration_rempl', $texte);
	// menage
	unset($deco_balises);
	return $texte;
}

?>