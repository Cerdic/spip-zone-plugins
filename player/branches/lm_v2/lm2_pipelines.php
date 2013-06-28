<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function lm2_jquery_plugins($plugins){
	if(!test_espace_prive() && (_request('page') !== 'login')){
		$plugins[] = _DIR_LIB_SM.'script/soundmanager2.js';
	}
	return $plugins;
}

function lm2_insert_head($flux){
	$config_inline = lire_config('lecteur_multimedia/inlineplayer');
	$inline_player = $config_inline ? recuperer_fond('head/lm2_'.$config_inline) : '';
	$config_lecteur_audio = lire_config('lecteur_multimedia/lecteur_audio');
	if(($config_lecteur_audio != $config_inline) && find_in_path('head/lm2_'.$config_lecteur_audio.'.html')){
		$lecteur_audio = recuperer_fond('head/lm2_'.$config_lecteur_audio);
	}
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_playlist_jquery.js').'"></script>'."\n";
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('lm2_player.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<script type="text/javascript" src="'.generer_url_public('lm2_config.js').'"></script>'."\n";
	$flux .= $inline_player;
	$flux .= $lecteur_audio;

	return $flux;
}

# inserer les liens dans la page
function lm2_affichage_final(&$page) {
	// Ajouter javascript/lm2_inlineplayer.js s'il n'y est pas déjà et qu'on a des enclosures dans la page
	if (!strpos($page, 'javascript/lm2_inlineplayer.js')){
		if(strpos($page, 'rel="enclosure"')  OR strpos($page, "rel='enclosure'")){
			$script = recuperer_fond('head/lm2_inlineplayer');
			$page = substr_replace($page, $script, strpos($page, '</body>'), 0);
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

function lm2_pre_liens($texte) {
	
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