<?php

function sm_insert_head($flux){

	return $flux;
}

// Ajouter soundmanager s'il n'y est pas déjà et qu'on a des enclosures dans la page
function sm_affichage_final($page) {
	if (!strpos($page, 'script/soundmanager2.js')){
		if(strpos($page, 'rel="enclosure"')  OR strpos($page, "rel='enclosure'") AND $GLOBALS['html']){					
			$script .= "\n"."<script type=\"text/javascript\" src=\"" . find_in_path('script/soundmanager2.js') . "\"></script>"."\n";
			$script .= "<script type=\"text/javascript\" src=\"" . find_in_path('soundmanager.js') . "\"></script>"."\n";
			$script .= "<link rel='stylesheet' href='" . generer_url_public('soundmanager.css') . "' type='text/css' media='projection, screen, tv' />"."\n";
			
			$page = substr_replace($page, $script, strpos($page, '</head>'), 0);
		}
	}		
	return $page;
}
 
 /**
 * Ajout d'un rel="enclosure" sur les liens mp3.
 * Permet de traiter les [mon son->http://monsite/mon_son.mp3] dans un texte.
 * Le filtre peut etre appele dans un squelette apres |liens_absolus
 *
 * Pete cependant dans les cas (tordus) suivants :
 * [{{Une histoire d'amour}}->documents/sons/PIRATAGE/01 UNE HISTOIRE D'AMOUR.mp3]
 * [{{Une histoire d'amour à trois}}->documents/sons/PIRATAGE/02 UNE HISTOIRE D'AMOUR A TROIS[2].mp3]
 *
 */

function sm_pre_liens($texte) {
	
	define('_RACCOURCI_LIEN_MP3', "/\[([^][]*?([[]\w*[]][^][]*)*)->(>?)([^]]*\.mp3)\]/msS");
	
	if (preg_match_all(_RACCOURCI_LIEN_MP3, $texte, $regs, PREG_SET_ORDER)) {

		foreach ($regs as $k => $reg) {
			if($reg[1]){
				$l = "<a href='$reg[4]' rel='enclosure'>$reg[1]</a>";
			}else{
				$l = "<a href='$reg[4]' rel='enclosure'>".couper($reg[4],50)."</a>";
			}
			$p = $reg[0];
			$texte = str_replace($p,$l,$texte);
		} 
	}

	return $texte;
}

?>