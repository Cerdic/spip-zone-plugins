<?php

/*
	Cet outil 'decoration' permet aux redacteurs d'un site spip de d'appliquer des styles aux textes SPIP
	Attention : seules les balises en minuscules sont reconnues.
	Doc : http://www.spip-contrib.net/?article2427
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function decoration_installe() {
	if(!defined('_decoration_BALISES')) return;
cs_log("decoration_installe()");
	// on decode les balises entrees dans la config
	$deco_balises = preg_split("/[\r\n]+/", trim(_decoration_BALISES));
	$aide = $trouve = $remplace = $alias = $auto_balises = $auto_remplace = $BT = array();
	foreach ($deco_balises as $balise) {
		if (preg_match('/(span|div|auto)\.([^.]+)\.(class|lang)\s*=(.+)$/', $balise, $regs)) {
			// les class/lang
			list($div, $racc, $attr, $valeur) = array($regs[1], trim($regs[2]), trim($regs[3]), trim($regs[4]));
			$attr="$attr=\"$valeur\"";
			$BT[] = array(
				$racc,//$div=='div'?strtoupper($racc):$racc,
				$div=='auto'?"('<$racc>','</$racc>'":"_etendu('<$racc>','</$racc>','<$racc/>'",
			);
			if ($div=='auto') {
				$auto_balises[] = $racc; 
				$auto_remplace[$racc] = "$attr>";
			} else {
				$aide[] = $racc; 
				$trouve[] = "<$racc>"; $trouve[] = "</$racc>"; $trouve[] = "<$racc/>";
				$remplace[] = $a = "<$div $attr>"; 
				$remplace[] = $b = "</$div>"; $remplace[] = $a.$b;
			}
		} elseif (preg_match('/(span|div|auto)\.([^=]+)=(.+)$/', $balise, $regs)) {
			// les styles inline
			list($div, $racc, $style) = array($regs[1], trim($regs[2]), trim($regs[3]));
			$attr="style=\"$style\"";
			$BT[] = array(
				$racc,//$div=='span'?"<$div $attr>$racc</$div>":($div=='div'?strtoupper($racc):$racc),
				$div=='auto'?"('<$racc>','</$racc>'":"_etendu('<$racc>','</$racc>','<$racc/>'",
			);
			if ($div=='auto') {
				$auto_balises[] = $racc; 
				$auto_remplace[$racc] = "$attr>";
			} else {
				$aide[] = $racc; 
				$trouve[] = "<$racc>"; $trouve[] = "</$racc>"; $trouve[] = "<$racc/>";
				$remplace[] = $a = "<$div $attr>";
				$remplace[] = $b = "</$div>"; $remplace[] = $a.$b;
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
	$aide = array_merge($aide, $auto_balises);
	// sauvegarde en meta : aide
	ecrire_meta('cs_decoration_racc', '<b>'.join('</b>, <b>', $aide).'</b>');
	// protection $auto_balises pour la future regExpr
	array_walk($auto_balises, 'cs_preg_quote');
	// sauvegarde en meta : decoration
	ecrire_meta('cs_decoration', serialize(array(
		// balises fixes a trouver
		$trouve, 
		// replacement des balises fixes
		$remplace,
		// RegExpr pour les balises automatiques
		count($auto_balises)?',<('.join('|', $auto_balises).')>(.*?)</\1>,ms':'',
		// association pour les balises automatiques
		$auto_remplace,
		// balises disponibles
		$BT,
	)));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('desc:un_outil:aide');
function decoration_raccourcis() {
	return _T('desc:decoration:aide', array('liste' => $GLOBALS['meta']['cs_decoration_racc']));
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
	if (!isset($GLOBALS['meta']['cs_decoration'])) decoration_installe();
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
function decoration_BarreTypo($tr) {
	// les raccoucis de couleur sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_decoration'])) decoration_installe();
	// le tableau des smileys est present dans les metas
decoration_installe();
	$balises = unserialize($GLOBALS['meta']['cs_decoration']);
//print_r($balises);die();
	$res = array(); 
	foreach($balises[4] as $v)
		$res[] = "<a href=\"javascript:barre_raccourci$v[1],@@champ@@)\"><span class=\"cs_BT\">$v[0]</span></a>";
	$res = join(' ', $res); 
	return $tr.'<tr><td><p style="margin:0; line-height:1.8em;">'._T('desc:decoration:nom')."&nbsp;$res</p></td></tr>";
}

?>