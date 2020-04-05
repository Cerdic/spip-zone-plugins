<?php

	// inc/player_flv_config.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

// CP-20080321 : creation de la table
if (!defined("_ECRIRE_INC_VERSION")) return;


/*
	Documentation sur :
	- player_flv_js.swf -> http://flv-player.net/players/js/documentation/
	- player_flv_multi.swf -> http://flv-player.net/players/multi/documentation/
	- player_flv_maxi -> http://flv-player.net/players/maxi/documentation/
	- player_flv.swf -> http://flv-player.net/players/normal/documentation/
	- player_flv_mini.swf -> http://flv-player.net/players/mini/documentation/
*/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

function player_array_set_key_from_value () {
	$result = array();
	foreach(func_get_args() as $value) {
		$result[$value] = $value;
	}
	return($result);
}

function player_flv_config () {

	// la grosse table commune a tous les profils
	$player_flv_config = array(
		  'flv' // Les URLs des fichiers videos FLV a charger, separes par des |
		  => array(
			'type' => "url"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_flv")
			, 'default' => ''
		  )
		, 'config' // L'URL du fichier texte de configuration, par exemple flv_config_multi.txt
		  => array(
			'type' => "url"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_config")
			, 'default' => ''
		  )
		, 'configxml' // L'URL du fichier XML de configuration, par exemple flv_config_multi.xml
		  => array(
			'type' => "url"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_configxml")
			, 'default' => ''
		  )
		, 'buffer' // Le nombre de secondes pour la memoire tampon. Par defaut a 5.
		  => array(
			'type' => "list"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buffer")
			, 'values' => player_array_set_key_from_value(5, 10, 20, 30, 60)
			, 'default' => '5'
		  )
		, 'buffermessage' // Le message de la memoire tampon. Par defaut a Buffering _n_, _n_ indiquant le pourcentage.
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buffermessage")
			, 'default' => ''
		  )
		, 'title' // Les titres separes par des |
		  => array(
			'type' => "text"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_title")
			, 'default' => ''
		  )
		, 'titlesize' // La taille de la police du titre. Par defaut a 20.
		  => array(
			'type' => "list"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_titlesize")
			, 'values' => player_array_set_key_from_value(10, 20, 30)
			, 'default' => '20'
		  )
		, 'titlecolor' // La couleur du titre. Par defaut a ffffff.
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_titlecolor")
			, 'default' => 'ffffff'
		  )
		, 'margin' // La marge de la video par rapport au Flash (utile pour les skins)
		  => array(
			'type' => "list"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_margin")
			, 'values' => player_array_set_key_from_value(0, 1, 2, 4, 8, 16, 24, 32)
			, 'default' => '8'
		  )
		, 'srt' // 1 pour utiliser les sous-titres SRT (le fichier doit etre au meme endroit que la video et avoir le meme nom que le fichier video mais avec l'extension .srt)
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srt")
			, 'default' => '0'
		  )
		, 'srtsize' // La taille du texte des sous-titres. Par defaut a 11.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srtsize")
			, 'values' => player_array_set_key_from_value(8, 9, 10, 11, 12, 13, 14)
			, 'default' => '11'
		  )
		, 'srtcolor' // La couleur du texte des sous-titres
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srtcolor")
			, 'default' => 'ffffff'
		  )
		, 'srtbgcolor' // La couleur de fond des sous-titres
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srtbgcolor")
			, 'default' => '000000'
		  )
		, 'autoplay' // 1 pour lire automatiquement
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_autoplay")
			, 'default' => '0'
		  )
		, 'autoload' // 1 pour lancer le chargement et afficher la premiere image de la video
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_autoload")
			, 'default' => '1'
		  )
		, 'autonext' // 0 pour ne pas lire automatiquement la video suivante.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_autonext")
			, 'default' => '0'
		  )
		, 'shuffle' // 1 pour lire aleatoirement.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_shuffle")
			, 'default' => '0'
		  )
		, 'showstop' // 1 pour afficher le bouton STOP
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showstop")
			, 'default' => '1'
		  )
		, 'showvolume' // 1 pour afficher le bouton VOLUME
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showvolume")
			, 'default' => '1'
		  )
		, 'showtime' // 1 pour afficher le bouton TIME, 2 pour l'afficher avec le temps restant
		  => array(
			'type' => "list"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showtime")
			, 'values' => array('0' => _T(_PLAYER_LANG."label_showtime_0")
					, '1' => _T(_PLAYER_LANG."label_showtime_1")
					, '2' => _T(_PLAYER_LANG."label_showtime_2"))
			, 'default' => '0'
		  )
		, 'showprevious' // 1 pour afficher le bouton PREVIOUS.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_showprevious")
			, 'default' => '0'
		  )
		, 'shownext' // 1 pour afficher le bouton NEXT.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_shownext")
			, 'default' => '0'
		  )
		, 'showopen' // 0 pour cacher le bouton OPEN. 2 pour afficher la playlist au demarrage.
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_showopen")
			, 'values' => player_array_set_key_from_value(0, 1, 2)
			, 'default' => '0'
		  )
		, 'showplayer' // Affichage de la barre des boutons : autohide, always ou never
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showplayer")
			, 'values' => player_array_set_key_from_value('autohide', 'always', 'never')
			, 'default' => 'autohide'
		  )
		, 'showfullscreen' // 1 pour afficher le bouton pour le plein ecran (necessite Flash Player 9.0.16.60 ou superieur)
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showfullscreen")
			, 'default' => '1'
		  )
		, 'showswitchsubtitles' // 1 pour afficher le bouton qui affiche/cache les sous-titres
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showswitchsubtitles")
			, 'default' => '0'
		  )
		, 'loop' // 1 pour boucler
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_loop")
			, 'default' => '0'
		  )
		, 'width' // Forcer la largeur du lecteur
		  => array(
			'type' => "int"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_width")
//			, 'default' => '320'
			, 'default' => ''
		  )
		, 'height' // Forcer la hauteur du lecteur
		  => array(
			'type' => "int"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_height")
//			, 'default' => '240'
			, 'default' => ''
		  )
		, 'startimage' // Les images de titre separees par des |
		  => array(
			'type' => "url"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_startimage")
			, 'default' => ''
		  )
		, 'skin' // L'URL du fichier JPEG (non progressif) a charger
		  => array(
			'type' => "url"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_skin")
			, 'default' => ''
		  )
		, 'playercolor' // La couleur du lecteur (pas du flash)
		  => array(
			'type' => "color"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_playercolor")
			, 'default' => '7b3740'
		  )
		, 'loadingcolor' // La couleur de la barre de chargement
		  => array(
			'type' => "color"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_loadingcolor")
			, 'default' => '4b4ff7'
		  )
		, 'bgcolor' // La couleur de fond
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_bgcolor")
			, 'default' => 'cccccc'
		  )
		, 'bgcolor1' // La premiere couleur du degrade du fond
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_bgcolor1")
			, 'default' => 'ffcccc'
		  )
		, 'bgcolor2' // La seconde couleur du degrade du fond
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_bgcolor2")
			, 'default' => 'ffffff'
		  )
		, 'buttoncolor' // La couleur des boutons
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buttoncolor")
			, 'default' => 'ffffff'
		  )
		, 'buttonovercolor' // La couleur des boutons au survol
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buttonovercolor")
			, 'default' => '00ffcc'
		  )
		, 'slidercolor1' // La premiere couleur du degrade de la barre
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_slidercolor1")
			, 'default' => '00ffff'
		  )
		, 'slidercolor2' // La seconde couleur du degrade de la barre
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_slidercolor2")
			, 'default' => 'autohide'
			, 'default' => 'ccffff'
		  )
		, 'sliderovercolor' // La couleur de la barre au survol
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_sliderovercolor")
			, 'default' => '00cccc'
		  )
		, 'scrollbarcolor' // La couleur de la barre de defilement.
		  => array(
			'type' => "color"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarcolor")
			, 'default' => 'ff0000'
		  )
		, 'scrollbarovercolor' // La couleur de la barre de defilement au survol.
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarovercolor")
			, 'default' => '00ffff'
		  )
		, 'currentflvcolor' // La couleur de la video selectionnee.
		  => array(
			'type' => "color"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_currentflvcolor")
			, 'default' => '00ff00'
		  )
		, 'onclick' // L'URL de la destination au click sur la video. Par defaut a playpause qui signifie que la video fait play ou pause au click. Pour ne rien faire, il faut mettre none.
		  => array(
			'type' => "url"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_onclick")
			, 'default' => 'playpause'
		  )
		, 'onclicktarget' // La cible de l'URL au click sur la video. Par defaut a _self. Pour ouvrir une nouvelle fenetre, mettez _blank.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_onclicktarget")
			, 'values' => player_array_set_key_from_value('_self', '_blank')
			, 'default' => '_self'
		  )
		, 'ondoubleclick' // Action sur le double click: none, fullscreen, playpause, ou l'url a ouvrir.
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_ondoubleclick")
			, 'default' => 'fullscreen'
		  )
		, 'ondoubleclicktarget' // La cible de l'URL au double click sur la video. Par defaut a _self. Pour ouvrir une nouvelle fenetre, mettez _blank.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_ondoubleclicktarget")
			, 'values' => player_array_set_key_from_value('_self', '_blank')
			, 'default' => '_self'
		  )
		, 'playertimeout' // Le delai en milliseconde avant que le lecteur se cache (quand il est en mode autohide bien sur. Par defaut a 1500.
		  => array(
			'type' => "int"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_playertimeout")
			, 'default' => '1500'
		  )
		, 'videodelay' // La duree d'affichage du titre au changement de video, en milliseconde. Par defaut a 0.
		  => array(
			'type' => "int"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_videodelay")
			, 'default' => '0'
		  )
		, 'shortcut' // 0 pour desactiver les raccourcis clavier.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_shortcut")
			, 'default' => '0'
		  )
		, 'volume' // Le volume initial, entre 0 et 200.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_volume")
			, 'values' => player_array_set_key_from_value(0, 25, 50, 100, 150, 175, 200)
			, 'default' => '100'
		  )
		, 'videobgcolor' // La couleur du fond de la video quand il n'y a pas de video.
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_videobgcolor")
			, 'default' => '000000'
		  )
		, 'playlisttextcolor' // La couleur du texte de la playlist.
		  => array(
			'type' => "color"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_playlisttextcolor")
			, 'default' => 'cccccc'
		  )
		, 'playonload' // 0 pour ne pas jouer la video au chargement (de la video).
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_playonload")
			, 'default' => '0'
		  )
		, 'scrollbarsize' // La taille de la barre de defilement (4 par defaut)
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarsize")
			, 'values' => player_array_set_key_from_value(4, 8, 16)
			, 'default' => '4'
		  )
		, 'showtitlebackground' // Affichage du fond du titre: auto, always ou never
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showtitlebackground")
			, 'values' => player_array_set_key_from_value('auto', 'always', 'never')
			, 'default' => 'auto'
		  )
		, 'playeralpha' // La transparence du fond du lecteur entre 0 et 100.
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_playeralpha")
			, 'values' => player_array_set_key_from_value(0, 20, 40, 60, 80, 100)
			, 'default' => '100'
		  )
		, 'showmouse' // Affichage de la souris : always, autohide, never.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showmouse")
			, 'values' => player_array_set_key_from_value('always', 'autohide', 'never')
			, 'default' => 'autohide'
		  )
		, 'top1' // Charger une image par dessus la video et la placer a une coordonnee x et y (par exemple url|x|y)
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_top1")
			, 'default' => ''
		  )
		, 'showiconplay' // 1 pour afficher l'icone PLAY au milieu de la video.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showiconplay")
			, 'default' => '1'
		  )
		, 'iconplaycolor' // La couleur de l'icone PLAY.
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_iconplaycolor")
			, 'default' => 'ffffff'
		  )
		, 'iconplaybgcolor' // La couleur de fond de l'icone PLAY.
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_iconplaybgcolor")
			, 'default' => '000000'
		  )
		, 'iconplaybgalpha' // La transparence du fond de l'icone PLAY entre 0 et 100.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_iconplaybgalpha")
			, 'values' => player_array_set_key_from_value(0, 20, 40, 60, 80, 100)
			, 'default' => '100'
		  )
		, 'showtitleandstartimage' // 1 pour afficher le titre et l'image de depart en meme temps.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showtitleandstartimage")
			, 'default' => '1'
		  )
	);
	
	return($player_flv_config);
}
?>