<?php

	// player_fonctions.php

	// $LastChangedRevision:$
	// $LastChangedBy:$
	// $LastChangedDate:$

if (!defined('_DIR_PLUGIN_PLAYER')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PLAYER',(_DIR_PLUGINS.end($p)."/"));
}

function Player_head(){
	$flux = "";
	$flux .= '<script type="text/javascript" src="'.find_in_path('soundmanager/soundmanager2.js').'"></script>';
	$flux .= '<script type="text/javascript"><!--'."\n"
	. 'var musicplayerurl="'.find_in_path('flash/eraplayer_playlist.swf').'";'."\n"
	. 'var image_play="'.find_in_path('images/playl.gif').'";'."\n"
	. 'var image_pause="'.find_in_path('images/pausel.gif').'";'."\n"
	. 'soundManager.url = "'.find_in_path('soundmanager/soundmanager2.swf').'";'."\n"
  	. 'soundManager.nullURL = "'.find_in_path('soundmanager/null.mp3').'";'."\n"
	. 'var videoNullUrl = "'._DIR_PLUGIN_PLAYER.'null.flv";'."\n"
	. 'var DIR_PLUGIN_PLAYER = "'._DIR_PLUGIN_PLAYER.'";'
	. "//--></script>\n";
	
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'javascript/jscroller.js"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'player_enclosure.js"></script>'."\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('player.css').'" type="text/css" media="all" />'."\n";
	return $flux;
}

function Player_insert_head($flux){
	if (!defined('_PLAYER_AFFICHAGE_FINAL') OR !_PLAYER_AFFICHAGE_FINAL)
		$flux .= Player_head();
	return $flux;
}
function Player_affichage_final($flux){
	if (defined('_PLAYER_AFFICHAGE_FINAL') AND _PLAYER_AFFICHAGE_FINAL){
		// inserer le head seulement si presente d'un rel='enclosure'
		if ((strpos($flux,'rel="enclosure"')!==FALSE)){
			$flux = str_replace('</head>',Player_head().'</head>',$flux);
		}
	}
	return $flux;
}


/**
 * enclosures
 * ajout d'un rel="enclosure" sur les liens mp3 absolus
 * appele en pipeline apres propre pour traiter les [mon son->http://monsite/mon_son.mp3]
 * peut etre appele dans un squelette apres |liens_absolus
 */
 
 function Player_post_propre($texte) {

	$reg_formats="mp3";

	$texte = preg_replace(
		",<a(\s[^>]*href=['\"]?(http:\/\/[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>(.*)</a>,Uims",
		'<a$1 rel="enclosure">$4</a>', 
		$texte);
	
	return $texte;
}

function joli_titre($titre){
$titre=basename($titre);
$titre=ereg_replace('.mp3','',$titre);
$titre=ereg_replace('^ ','',$titre);
$titre = eregi_replace("_"," ", $titre );
$titre = eregi_replace("'"," ",$titre );

return $titre ;
}

// CP 20080321
// balise a' placer dans le modele
// donne la ligne FlashVars
function balise_PLAYER_FLV_FLASHVVARS ($p) {
	
	static $player_flv_flashvars = null;

/* pour debug. Dump du parametre 
spip_log("######################################");
if(!function_exists('dump_me')) {
	function dump_me($p, $m = "###: ") {
		foreach($p as $k => $v) {
			spip_log($m . $k . " = ".$v);
			if(is_array($v) || is_object($v)) {
				dump_me($v, $m . $k.": ");
			}
		}
	}
}
dump_me($p);
/**/

	$id_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	
	// #PLAYER_FLV_FLASHVVARS hors boucle ? ne rien faire !
	if (!$type = $p->boucles[$id_boucle]->type_requete) {
		$p->code = "''";
	} else {
	// sinon, renvoyer les Flashvars sur une seule ligne

		if(!$player_flv_flashvars) {
		
			$player_flv_lecteurs = unserialize(_PLAYER_FLV_LECTEURS);
	
			$player_config = unserialize($GLOBALS['meta'][_PLAYER_META_PREFERENCES]);
			
			include_spip('inc/player_flv_config');
			// la grosse table commune a tous les profils
			$player_flv_config = player_flv_config();
	
			$result = array();
			$player_key = $player_config['player_key'];
			
			// n'envoyer que ce qui est necessaire au profil configure en admin
			// mini demande beaucoup moins de variables que multi
			foreach($player_flv_config as $key => $value) {
				if(
					in_array($player_key, explode(' ', $value['class']))
					&& !empty($player_config['player_video_prefs'][$key])
				) {
					$result[] = $key."=".$player_config['player_video_prefs'][$key];
				}
			}
			$player_flv_flashvars = implode('&amp;', $result);
		}
		
		$p->code = "'$player_flv_flashvars'";
	}
	$p->interdire_scripts = true;
	return($p);
}


// CP 20080321
// balise a' placer dans le modele
// donne le nom du fichier player flv demande a' la config
function balise_PLAYER_FLV_PLAYER ($p) {

	$id_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	
	// #PLAYER_FLV_PLAYER hors boucle ? ne rien faire !
	if (!$type = $p->boucles[$id_boucle]->type_requete) {
		$p->code = "''";
	} else {
	// sinon, renvoyer le nom du swf

		$player_config = unserialize($GLOBALS['meta'][_PLAYER_META_PREFERENCES]);
		$result = $player_config['player_video'];
		if(!$result){
			$result = 'player_flv_maxi.swf'; 
		}
		$p->code = "'$result'";
	}
	$p->interdire_scripts = true;
	return($p);
}

?>
