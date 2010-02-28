<?php
function lm2_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path(_DIR_LIB_SM.'script/soundmanager2.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_playlist_jquery.js').'"></script>'."\n";
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('lm2_player.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<script type="text/javascript" src="'.generer_url_public('lm2_config.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_inlineplayer.js').'"></script>'."\n";

	return $flux;
}

/**
 * enclosures
 * ajout d'un rel="enclosure" sur les liens mp3 absolus
 * appele en pipeline apres propre pour traiter les [mon son->http://monsite/mon_son.mp3]
 * peut etre appele dans un squelette apres |liens_absolus
 * pète cependant avec les cas (tordus) suivants :
 * [{{Une histoire d'amour}}->documents/sons/PIRATAGE/01 UNE HISTOIRE D'AMOUR.mp3]
 * [{{Une histoire d'amour à trois}}->documents/sons/PIRATAGE/02 UNE HISTOIRE D'AMOUR A TROIS[2].mp3]
 */
 
function lm2_pre_liens($texte) {
	define('_RACCOURCI_LIEN_MP3', "/\[([^][]*?([[]\w*[]][^][]*)*)->(>?)([^]\.mp3]*)\]/msS");

	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $k => $reg) {
		$l = "<a href='$reg[4]' rel='enclosure'>$reg[1]</a>";
		$p = $reg[0] ;
		$texte = str_replace($p,$l,$texte);
		}	
	}

	return $texte;
}

?>