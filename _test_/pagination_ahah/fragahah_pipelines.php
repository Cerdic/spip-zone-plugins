<?php

function fragahah_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('pagination-ahah.css').'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".find_in_path('pagination-ahah.js')."'></script>\n";
	return $flux;
}

// ajouter les fragments sur la pagination !
function critere_pagination($idb, &$boucles, $crit) {
	critere_pagination($idb,$boucles,$crit);
	if (!isset($boucle->modificateur['fragment']))
		$boucle->modificateur['fragment'] = 'fragment_'.$boucle->descr['nom'].$idb;
}

function fragahah_affichage_final($texte){
	// si un fragment est demande, l'isoler
	if (($var_fragment=_request('var_fragment'))!==NULL) {
		preg_match(',<div id="'.preg_quote($var_fragment)
		.'" class="fragment">(.*)<!-- /'.preg_quote($var_fragment)
		.' --></div>,Uims', $texte, $r);
			$texte = $r[1];
	}
	return $texte;
}

?>