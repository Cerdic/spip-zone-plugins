<?php

	// player_fonctions.php

	// $LastChangedRevision: 35894 $
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined('_DIR_PLUGIN_PLAYER')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PLAYER',(_DIR_PLUGINS.end($p)."/"));
}

function Player_call_js() {
	$flux = "\n"
		. "<!-- Player JS -->\n"
		. '<script type="text/javascript" src="'.find_in_path('soundmanager/soundmanager2.js').'"></script>'
		. '<script type="text/javascript"><!--' . "\n"
		// . 'var musicplayerurl="'.find_in_path('flash/eraplayer_playlist.swf').'";'."\n"
		. 'var musicplayerurl="' . find_in_path('flash/' . $player_ . '.swf') . '";'."\n"
		. "var key_espace_stop = true;\n"
		. 'var image_play="'.find_in_path('images/playl.gif').'";'."\n"
		. 'var image_pause="'.find_in_path('images/pausel.gif').'";'."\n"
		. 'soundManager.url = "'.find_in_path('soundmanager/soundmanager2.swf').'";'."\n"
  		. 'soundManager.nullURL = "'.find_in_path('soundmanager/null.mp3').'";'."\n"
		. 'var videoNullUrl = "null.flv";'."\n"
		. 'var DIR_PLUGIN_PLAYER = "' . _DIR_PLUGIN_PLAYER . '";'
		. "//--></script>\n"
		. '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'javascript/jscroller.js"></script>'."\n"
		. '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'player_enclosure.js"></script>'."\n"
		;
	return $flux;
}

function Player_call_css() {
	$flux = "\n".'<link rel="stylesheet" href="'.direction_css(find_in_path('player.css')).'" type="text/css" media="all" />';
	return $flux;
}

function Player_head(){
	
	$player_ = ($p = $GLOBALS['meta']['player']) ? $p : _PLAYER_MP3_LECTEUR_DEFAULT;
	
	$flux =	Player_call_js();
	$flux .= Player_call_css();

	return $flux;
}

function Player_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		if (!defined('_PLAYER_AFFICHAGE_FINAL') OR !_PLAYER_AFFICHAGE_FINAL)
		{
			$flux .= Player_call_css();
		}
	}
	return $flux;
}

function Player_insert_head($flux){
	if (!defined('_PLAYER_AFFICHAGE_FINAL') OR !_PLAYER_AFFICHAGE_FINAL)
	{
		$flux = Player_insert_head_css($flux);
		$flux .= Player_call_js();
	}
	return $flux;
}

function Player_affichage_final($flux){
	if (defined('_PLAYER_AFFICHAGE_FINAL') AND _PLAYER_AFFICHAGE_FINAL){
		// inserer le head seulement si presente d'un rel='enclosure'
		if ((strpos($flux,'rel="enclosure"')!==FALSE)
		  OR (strpos($flux,'playliste_video')!==FALSE)){
			$flux = str_replace('</head>', Player_head().'</head>', $flux);
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
	$titre=preg_replace('/.mp3/','',$titre);
	$titre=preg_replace('/^ /','',$titre);
	$titre = preg_replace("/_/i"," ", $titre );
	$titre = preg_replace("/'/i"," ",$titre );

	return $titre ;
}


// CP 20080321
// balise a' placer dans le modele
// donne la ligne FlashVars
function balise_PLAYER_FLV_FLASHVVARS ($p) {
	
	static $player_flv_flashvars = null;

	$id_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	
	// #PLAYER_FLV_FLASHVVARS hors boucle ? ne rien faire !
	if (!$type = $p->boucles[$id_boucle]->type_requete) {
		$p->code = "''";
	}
	else {
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
	$p->interdire_scripts = false;
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
	$p->interdire_scripts = false;
	return($p);
}

function balise_PLAYER_VIDEOS_DIR ($p) {

	$p->code = "'/videos/'";
	$p->interdire_scripts = false;

	return($p);
	
}

function player_meta_prefs_item ($ii) {
	
	static $prefs;
	
	if($prefs == null)
	{
		lire_metas();
		$prefs = unserialize($GLOBALS['meta'][_PLAYER_META_PREFERENCES]);
		$prefs = $prefs['player_video_prefs'];
	}
	return($ii && isset($prefs[$ii]) ? $prefs[$ii] : null);
}

function balise_PLAYER_META_GET ($p) {

	if($key = trim(interprete_argument_balise(1, $p))) {
		$p->code = "player_meta_prefs_item($key)";
	}
		
	return($p);
}

