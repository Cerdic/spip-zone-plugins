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
	$aide = $trouve = $remplace = $alias = $auto_balises = $auto_remplace = array();
	foreach ($deco_balises as $balise) {
		if (preg_match('/(span|div|auto)\.([^.]+)\.(class|lang)\s*=(.+)$/', $balise, $regs)) {
			// les class/lang
			list($div, $racc, $class) = array($regs[1], trim($regs[2]), trim($regs[4]));
			if ($div=='auto') {
				$auto_balises[] = $racc; 
				$auto_remplace[$racc] = "$regs[3]=\"$class\">";
			} else {
				$aide[] = $racc; 
				$trouve[] = "<$racc>"; $trouve[] = "</$racc>"; $trouve[] = "<$racc/>";
				$remplace[] = $a = "<$regs[1] $regs[3]=\"$class\">"; 
				$remplace[] = $b = "</$regs[1]>"; $remplace[] = $a.$b;
			}
		} elseif (preg_match('/(span|div|auto)\.([^=]+)=(.+)$/', $balise, $regs)) {
			// les styles inline
			list($div, $racc, $style) = array($regs[1], trim($regs[2]), trim($regs[3]));
			if ($div=='auto') {
				$auto_balises[] = $racc; 
				$auto_remplace[$racc] = "style=\"$style\">";
			} else {
				$aide[] = $racc; 
				$trouve[] = "<$racc>"; $trouve[] = "</$racc>"; $trouve[] = "<$racc/>";
				$remplace[] = $a = "<$regs[1] style=\"$style\">";
				$remplace[] = $b = "</$regs[1]>"; $remplace[] = $a.$b;
			}
		} elseif (preg_match('/([^=]+)=(.+)$/', $balise, $regs)) {
			// les alias
			$alias[trim($regs[1])] = trim($regs[2]);
		}
	}
	// ajout des alias qu'on a trouves
	foreach ($alias as $a=>$v) 
		if(($i=array_search("<$v>", $trouve, true))!==false) {
			$aide[] = $a; $trouve[] = "<$a>"; $trouve[] = "</$a>"; $trouve[] = "<$a/>";
			$remplace[] = $remplace[$i]; $remplace[] = $remplace[$i+1]; $remplace[] = $remplace[$i+2];
		} elseif(array_search($v, $auto_balises, true)!==false) {
			$auto_balises[] = $a;
			$auto_remplace[$a] = $auto_remplace[$v];
		}
	// liste des balises disponibles
	$aide = '<b>'.join('</b>, <b>', array_merge($aide, $auto_balises)).'</b>';
	// sauvegarde en meta : aide
	ecrire_meta('cs_decoration_racc', $aide);
	// protection $auto_balises pour la future regExpr
	foreach($auto_balises as $i=>$v) $auto_balises[$i] = preg_quote($v, ',');
	// sauvegarde en meta : decoration
	ecrire_meta('cs_decoration', serialize(array(
		// balises fixes a trouver
		$trouve, 
		// replacement des balises fixes
		$remplace,
		// RegExpr pour les balises automatiques
		count($auto_balises)?',<('.join('|', $auto_balises).')>(.*?)</\1>,ms':'',
		// association pour les balises automatiques
		$auto_remplace
	)));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
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
	// reecrire les raccourcis du type <balise   />
	$texte = preg_replace(', +/>,', '/>', $texte);
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
	if (!isset($GLOBALS['meta']['cs_decoration']))
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

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function decoration_BarreTypoEnrichie($tr) {
	return $tr.'<tr><td>'._T('cout:decoration:nom').' (en projet)</td></tr>';
}

?>