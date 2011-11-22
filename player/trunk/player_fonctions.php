<?php
/**
 * Plugin Lecteur (mp3)
 * Licence GPL
 * 2007-2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Code JS a inserer dans la page pour faire fonctionner le player
 * @param $player
 * @return string
 */
function player_call_js($player) {
	$flux = "\n"
		. "<!-- Player JS -->\n"
		. '<script type="text/javascript" src="'.find_in_path('javascript/soundmanager/soundmanager2.js').'"></script>'
		. '<script type="text/javascript"><!--' . "\n"
		. 'var musicplayerurl="' . find_in_path('players/' . $player . '/player.swf') . '";'."\n"
		. "var key_espace_stop = true;\n"
		. 'var image_play="'.find_in_path('players/controls/play-16.png').'";'."\n"
		. 'var image_pause="'.find_in_path('players/controls/pause-16.png').'";'."\n"
		. 'soundManager.url = "'.find_in_path('javascript/soundmanager/soundmanager2.swf').'";'."\n"
  	. 'soundManager.nullURL = "'.find_in_path('javascript/soundmanager/null.mp3').'";'."\n"
		. 'var DIR_PLUGIN_PLAYER = "' . _DIR_PLUGIN_PLAYER . '";'
		. "//--></script>\n"
		. '<script type="text/javascript" src="'.find_in_path('javascript/jscroller.js').'"></script>'."\n"
		. '<script type="text/javascript" src="'.find_in_path('javascript/player_enclosure.js').'"></script>'."\n"
		;
	return $flux;
}

/**
 * Code CSS a inserer dans la page pour habiller le player
 * @return string
 */
function player_call_css() {
	$flux = "\n".'<link rel="stylesheet" href="'.direction_css(find_in_path('player.css')).'" type="text/css" media="all" />';
	return $flux;
}

/**
 * inserer systematiquement le CSS dans la page
 * @param string $flux
 * @return string
 */
function player_insert_head_css($flux){
	if (test_espace_prive()
		OR (!defined('_PLAYER_AFFICHAGE_FINAL') OR !_PLAYER_AFFICHAGE_FINAL))
		$flux .= player_call_css();

	return $flux;
}

/**
 * Inserer systematiquement le JS dans la page
 * @param string $flux
 * @return string
 */
function player_insert_head($flux){
	if (test_espace_prive()
		OR (!defined('_PLAYER_AFFICHAGE_FINAL') OR !_PLAYER_AFFICHAGE_FINAL)){
		$player = unserialize($GLOBALS['meta']['player']);
		$player = isset($player['player_mp3'])?$player['player_mp3']:'eraplayer';
		$flux .= player_call_js($player);
	}
	return $flux;
}


/**
 * Inserer JS+CSS dans la page si elle contient un player
 * (a la demande)
 * @param string $flux
 * @return string
 */
function player_affichage_final($flux){
	if (defined('_PLAYER_AFFICHAGE_FINAL') AND _PLAYER_AFFICHAGE_FINAL){
		// inserer le head seulement si presente d'un rel='enclosure'
		// il faut etre pas trop stricte car on peut avoir rel='nofollow encolsure' etc...
		if ((strpos($flux,'enclosure')!==false)){
			// on pourrait affiner la detection avec un preg ?
			$player = unserialize($GLOBALS['meta']['player']);
			$player = isset($player['player_mp3'])?$player['player_mp3']:'eraplayer';
			$ins = player_call_css();
			$ins .= player_call_js($player);

			$p = stripos($flux,"</head>");
			if ($p)
				$flux = substr_replace($flux,$ins,$p,0);
			else
				$flux .= player_head();
		}
	}
	return $flux;
}


/**
 * enclosures
 * ajout d'un rel="enclosure" sur les liens mp3 absolus
 * appele en pipeline apres propre pour traiter les [mon son->http://monsite/mon_son.mp3]
 * peut etre appele dans un squelette apres |liens_absolus
 *
 * @param $texte
 * @return mixed
 */
function player_post_propre($texte) {

	$reg_formats="mp3";
	// plus vite
	if (stripos($texte,".$reg_formats")!==false
	  AND stripos($texte,"<a")!==false){
		$texte = preg_replace_callback(
			",<a(\s[^>]*href=['\"]?(http://[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>,Uims",
			'player_enclose_link',
			$texte
			);
	}

	return $texte;
}

function player_enclose_link($regs){
	$rel = extraire_attribut($regs[0],'rel');
	$rel = ($rel?"$rel ":"")."enclosure";
	return inserer_attribut($regs[0],'rel',$rel);
}

/**
 * Un filtre pour afficher de joli titre a partir du nom du fichier
 * @param $titre
 * @return mixed|string
 */
function joli_titre($titre){
	$titre=basename($titre);
	$titre=preg_replace('/.mp3/','',$titre);
	$titre=preg_replace('/^ /','',$titre);
	$titre = preg_replace("/_/i"," ", $titre );
	$titre = preg_replace("/'/i"," ",$titre );

	return $titre ;
}