<?php

	// inc/player_flv_config.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

// CP-20080321 : cr�ation de la table


/*
	Documentation sur :
	- player_flv_js.swf -> http://flv-player.net/players/js/documentation/
	- player_flv_multi.swf -> http://flv-player.net/players/multi/documentation/
	- player_flv_maxi -> http://flv-player.net/players/maxi/documentation/
	- player_flv.swf -> http://flv-player.net/players/normal/documentation/
	- player_flv_mini.swf -> http://flv-player.net/players/mini/documentation/
*/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

function player_flv_config () {

	// la grosse table commune � tous les profils
	$player_flv_config = array(
		  'flv' // Les URLs des fichiers vid�os FLV � charger, s�par�s par des |
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
		, 'buffer' // Le nombre de secondes pour la m�moire tampon. Par d�faut � 5.
		  => array(
			'type' => "list"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buffer")
			, 'values' => array(5, 10, 20, 30, 60)
			, 'default' => '5'
		  )
		, 'buffermessage' // Le message de la m�moire tampon. Par d�faut � Buffering _n_, _n_ indiquant le pourcentage.
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_buffermessage")
			, 'default' => ''
		  )
		, 'title' // Les titres s�par�s par des |
		  => array(
			'type' => "text"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_title")
			, 'default' => ''
		  )
		, 'titlesize' // La taille de la police du titre. Par d�faut � 20.
		  => array(
			'type' => "list"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_titlesize")
			, 'values' => array(10, 20, 30)
			, 'default' => '20'
		  )
		, 'titlecolor' // La couleur du titre. Par d�faut � ffffff.
		  => array(
			'type' => "color"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_titlecolor")
			, 'default' => 'ffffff'
		  )
		, 'margin' // La marge de la vid�o par rapport au Flash (utile pour les skins)
		  => array(
			'type' => "list"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_margin")
			, 'values' => array(0, 8, 16, 24, 32)
			, 'default' => '8'
		  )
		, 'srt' // 1 pour utiliser les sous-titres SRT (le fichier doit �tre au m�me endroit que la vid�o et avoir le m�me nom que le fichier vid�o mais avec l'extension .srt)
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srt")
			, 'default' => '0'
		  )
		, 'srtsize' // La taille du texte des sous-titres. Par d�faut � 11.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_srtsize")
			, 'values' => array(8, 9, 10, 11, 12, 13, 14)
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
		, 'autoload' // 1 pour lancer le chargement et afficher la premi�re image de la vid�o
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_autoload")
			, 'default' => '1'
		  )
		, 'autonext' // 0 pour ne pas lire automatiquement la vid�o suivante.
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_autonext")
			, 'default' => '0'
		  )
		, 'shuffle' // 1 pour lire al�atoirement.
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
			, 'values' => array('0' => _T(_PLAYER_LANG."label_showtime_0"), '1' => _T(_PLAYER_LANG."label_showtime_1"), '2' => _T(_PLAYER_LANG."label_showtime_2"))
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
		, 'showopen' // 0 pour cacher le bouton OPEN. 2 pour afficher la playlist au d�marrage.
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_showopen")
			, 'values' => array(0, 1, 2)
			, 'default' => '0'
		  )
		, 'showplayer' // Affichage de la barre des boutons : autohide, always ou never
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showplayer")
			, 'values' => array('autohide', 'always', 'never')
			, 'default' => 'autohide'
		  )
		, 'showfullscreen' // 1 pour afficher le bouton pour le plein �cran (n�cessite Flash Player 9.0.16.60 ou sup�rieur)
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
			, 'default' => '1'
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
			, 'default' => '320'
		  )
		, 'height' // Forcer la hauteur du lecteur
		  => array(
			'type' => "int"
		  	, 'class' => "mini normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_height")
			, 'default' => '240'
		  )
		, 'startimage' // Les images de titre s�par�es par des |
		  => array(
			'type' => "url"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_startimage")
			, 'default' => ''
		  )
		, 'skin' // L'URL du fichier JPEG (non progressif) � charger
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
		, 'bgcolor1' // La premi�re couleur du d�grad� du fond
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_bgcolor1")
			, 'default' => 'ffcccc'
		  )
		, 'bgcolor2' // La seconde couleur du d�grad� du fond
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
		, 'slidercolor1' // La premi�re couleur du d�grad� de la barre
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_slidercolor1")
			, 'default' => '00ffff'
		  )
		, 'slidercolor2' // La seconde couleur du d�grad� de la barre
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
		, 'scrollbarcolor' // La couleur de la barre de d�filement.
		  => array(
			'type' => "color"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarcolor")
			, 'default' => 'ff0000'
		  )
		, 'scrollbarovercolor' // La couleur de la barre de d�filement au survol.
		  => array(
			'type' => "color"
		  	, 'class' => "normal maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarovercolor")
			, 'default' => '00ffff'
		  )
		, 'currentflvcolor' // La couleur de la vid�o s�lectionn�e.
		  => array(
			'type' => "color"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_currentflvcolor")
			, 'default' => '00ff00'
		  )
		, 'onclick' // L'URL de la destination au click sur la vid�o. Par d�faut � playpause qui signifie que la vid�o fait play ou pause au click. Pour ne rien faire, il faut mettre none.
		  => array(
			'type' => "url"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_onclick")
			, 'default' => 'playpause'
		  )
		, 'onclicktarget' // La cible de l'URL au click sur la vid�o. Par d�faut � _self. Pour ouvrir une nouvelle fen�tre, mettez _blank.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_onclicktarget")
			, 'values' => array('_self', '_blank')
			, 'default' => '_self'
		  )
		, 'playertimeout' // Le d�lai en milliseconde avant que le lecteur se cache (quand il est en mode autohide bien s�r. Par d�faut � 1500.
		  => array(
			'type' => "int"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_playertimeout")
			, 'default' => '1500'
		  )
		, 'videodelay' // La dur�e d'affichage du titre au changement de vid�o, en milliseconde. Par d�faut � 0.
		  => array(
			'type' => "int"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_videodelay")
			, 'default' => '0'
		  )
		, 'shortcut' // 0 pour d�sactiver les raccourcis clavier.
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
			, 'values' => array(0, 25, 50, 100, 150, 175, 200)
			, 'default' => '100'
		  )
		, 'videobgcolor' // La couleur du fond de la vid�o quand il n'y a pas de vid�o.
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
		, 'playonload' // 0 pour ne pas jouer la vid�o au chargement (de la vid�o).
		  => array(
		  	'type' => "boolean"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_playonload")
			, 'default' => '0'
		  )
		, 'scrollbarsize' // La taille de la barre de d�filement (4 par d�faut)
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_scrollbarsize")
			, 'values' => array(4, 8, 16)
			, 'default' => '4'
		  )
		, 'showtitlebackground' // Affichage du fond du titre: auto, always ou never
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showtitlebackground")
			, 'values' => array('auto', 'always', 'never')
			, 'default' => 'auto'
		  )
		, 'playeralpha' // La transparence du fond du lecteur entre 0 et 100.
		  => array(
			'type' => "list"
		  	, 'class' => "multi"
			, 'label' => _T(_PLAYER_LANG."label_playeralpha")
			, 'values' => array(0, 20, 40, 60, 80, 100)
			, 'default' => '100'
		  )
		, 'ondoubleclick' // Action sur le double click: none, fullscreen, playpause, ou l'url � ouvrir.
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_ondoubleclick")
			, 'default' => 'fullscreen'
		  )
		, 'ondoubleclicktarget' // La cible de l'URL au double click sur la vid�o. Par d�faut � _self. Pour ouvrir une nouvelle fen�tre, mettez _blank.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_ondoubleclicktarget")
			, 'values' => array('_self', '_blank')
			, 'default' => '_self'
		  )
		, 'showmouse' // Affichage de la souris : always, autohide, never.
		  => array(
			'type' => "list"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_showmouse")
			, 'values' => array('always', 'autohide', 'never')
			, 'default' => 'autohide'
		  )
		, 'top1' // Charger une image par dessus la vid�o et la placer � une coordonn�e x et y (par exemple url|x|y)
		  => array(
			'type' => "text"
		  	, 'class' => "maxi multi"
			, 'label' => _T(_PLAYER_LANG."label_top1")
			, 'default' => ''
		  )
		, 'showiconplay' // 1 pour afficher l'icone PLAY au milieu de la vid�o.
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
			, 'values' => array(0, 20, 40, 60, 80, 100)
			, 'default' => '100'
		  )
		, 'showtitleandstartimage' // 1 pour afficher le titre et l'image de d�part en m�me temps.
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