<?php
// Ce fichier contient des fonctions toujours compilees dans tmp/couteau-suisse/mes_fonctions.php

// compatibilite SPIP < 1.92
if(defined('_SPIP19100')) {
	if (!function_exists('stripos')) {
		function stripos($botte, $aiguille) {
			if (preg_match('@^(.*)' . preg_quote($aiguille, '@') . '@isU', $botte, $regs)) return strlen($regs[1]);
			return false;
		}
	}
	if (!function_exists('interprete_argument_balise')){
		function interprete_argument_balise($n,$p) {
			if (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n))
				return calculer_liste($p->param[0][$n],	$p->descr, $p->boucles,	$p->id_boucle);	
			else return NULL;
		}
	}
	function f_insert_head($texte) {
		if (!$GLOBALS['html']) return $texte;
		//include_spip('public/admin'); // pour strripos
		($pos = stripos($texte, '</head>'))	|| ($pos = stripos($texte, '<body>'))|| ($pos = 0);
		if (false === strpos(substr($texte, 0,$pos), '<!-- insert_head -->')) {
			$insert = "\n".pipeline('insert_head','<!-- f_insert_head -->')."\n";
			$texte = substr_replace($texte, $insert, $pos, 0);
		}
		return $texte;
	}
}

// fonction appelant une liste de fonctions qui permettent de nettoyer un texte original de ses raccourcis indesirables
function cs_introduire($texte) {
	// liste de filtres qui sert a la balise #INTRODUCTION
	if(!is_array($GLOBALS['cs_introduire'])) return $texte;
	$liste = array_unique($GLOBALS['cs_introduire']);
	foreach($liste as $f)
		if (function_exists($f)) $texte = $f($texte);
	return $texte;
}

// Fonction propre() sans paragraphage
function cs_propre($texte) {
	include_spip('inc/texte');
	$mem = $GLOBALS['toujours_paragrapher'];
	$GLOBALS['toujours_paragrapher'] = false;
	$texte = propre($texte);
	$GLOBALS['toujours_paragrapher'] = $mem;
	return $texte;
}

// Filtre creant un lien <a> sur un texte
// Exemple d'utilisation : [(#EMAIL*|cs_lien{#NOM})]
function cs_lien($lien, $texte='') {
	if(!$lien) return $texte;
	return cs_propre("[{$texte}->{$lien}]");
}

// Controle (basique!) des 3 balises usuelles p|div|span eventuellement coupees
// Attention : simple traitement pour des balises non imbriquees
function cs_safebalises($texte) {
	$texte = trim($texte);
	// ouvre/supprime la premiere balise trouvee fermee (attention aux modeles SPIP)
	if(preg_match(',^(.*)</([a-z]+)>,Ums', $texte, $m) && !preg_match(",<$m[2][ >],", $m[1])) 
		$texte = strlen($m[1])?"<$m[2]>$texte":trim(substr($texte, strlen($m[2])+3));
	// referme/supprime la derniere balise laissee ouverte (attention aux modeles SPIP)
	if(preg_match(',^(.*)[ >]([a-z]+)<,Ums', $rev = strrev($texte), $m) && !preg_match(",>$m[2]/<,", $m[1])) 
		$texte = strrev(strlen($m[1])?">$m[2]/<$rev":trim(substr($rev, strlen($m[2])+2)));
	// balises <p|span|div> a traiter
	foreach(array('span', 'div', 'p') as $b) {
		// ouvrante manquante
		if(($fin = strpos($texte, "</$b>")) !== false)
			if(!preg_match(",<{$b}[ >],", substr($texte, 0, $fin)))
				$texte = "<$b>$texte";
		// fermante manquante
		$texte = strrev($texte);
		if(preg_match(',[ >]'.strrev("<{$b}").',', $texte, $reg)) {
			$fin = strpos(substr($texte, 0, $deb = strpos($texte, $reg[0])), strrev("</$b>"));
			if($fin===false || $fin>$deb) $texte = strrev("</$b>").$texte;
		}
		$texte = strrev($texte);
	}
	return $texte;
}
	
?>