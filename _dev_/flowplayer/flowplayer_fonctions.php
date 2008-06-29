<?php

/*
Dans son mes options, choisir pour avoir les js/css
- tout le temps :
define('_FLOW_PLAYER_PARTOUT',true);

- juste sur les pages qui ont le js (valeur par dafaut)
define('_FLOW_PLAYER_PARTOUT',false); 

*/

function flowplayer_head(){
	$flux = "";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/common.css').'" type="text/css" media="projection, screen, tv" />'."\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/multiple-instances.css').'" type="text/css" media="projection, screen, tv" />'."\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/simple-playlist.css').'" type="text/css" media="projection, screen, tv" />'."\n";
	return $flux;
}

function flowplayer_insert_head($flux){
	if (defined('_FLOW_PLAYER_PARTOUT') AND _FLOW_PLAYER_PARTOUT)
		$flux .= flowplayer_head();
	return $flux;
}
function flowplayer_affichage_final($flux){
	if (!defined('_FLOW_PLAYER_PARTOUT') OR !_FLOW_PLAYER_PARTOUT){
		// inserer le head seulement si presente d'un 'flowplayer'
		if ((strpos($flux,'flowplayer')!==FALSE)){
			$flux = str_replace('</head>',flowplayer_head().'</head>',$flux);
		}
	}
	return $flux;
}



?>