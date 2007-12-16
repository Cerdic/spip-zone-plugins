<?php
/*
 liens :
	http://fr.wikipedia.org/wiki/Wikip%C3%A9dia:AutoWikiBrowser/Typos/Aide
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function insertions_installe() {
	if(!defined('_insertions_LISTE')) return;
cs_log("insertions_installe()");
	// on decode la liste entree dans la config
	$liste = preg_split("/[\r\n]+/", trim(_insertions_LISTE));
	$str = $preg = array(array(), array());
	foreach ($liste as $l) {
		list($a, $b) = explode("=", $l, 2);
		$a = trim($a); $b = trim($b);
		if (!strlen($a) || preg_match('/^(#|\/\/)/', $a)) {
			// remarques ou vide
		} elseif (preg_match('/^\((.+)\)$/', $a, $reg)) {
			// les mots seuls
			$preg[0][] = '/\b'.$reg[1].'\b/'; $preg[1][] = $b;
		} elseif (preg_match('/^(\/.+\/[imsxuADSUX]*)$/', $a)) {
			// expressions regulieres
			$preg[0][] = $a; $preg[1][] = $b;
		} elseif (strlen($a)) {
			// simples remplacements
			$str[0][] = $a; $str[1][] = $b;
		}
	}
	ecrire_meta('cs_insertions', serialize(array($str, $preg)));
	ecrire_metas();
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function insertions_rempl($texte) {
	if (!isset($GLOBALS['meta']['cs_insertions'])) insertions_installe();
	$ins = unserialize($GLOBALS['meta']['cs_insertions']);
	$texte = str_replace($ins[0][0], $ins[0][1], $texte);
	return preg_replace($ins[1][0], $ins[1][1], $texte);
}

function insertions_pre_propre($texte) {
	return cs_echappe_balises('', 'insertions_rempl', $texte);
}

?>