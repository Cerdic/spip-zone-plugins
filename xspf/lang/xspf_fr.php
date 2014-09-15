<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// Tous cfg
'description_xspf' => '
<h4>Configuration du plugin xspf</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options de chacun des lecteurs.</p>
En attendant la documentation sur <a href="http://contrib.spip.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'size_anim' => 'Dimensions de l&rsquo;animation',
'width' => 'Largeur',
'height' => 'Hauteur',
'true' => 'Vrai',
'false' => 'Faux',
'repeat' => 'R&eacute;p&eacute;tition',

// Tous les modeles 
'get_player' => 'Vous devez installer le <a href="@url@">module flash correspondant &agrave; votre navigateur</a> pour voir ce contenu.',

// fonds/cfg_xspf
'description_xspf_lecteurs' => '
Lecteurs de <a href="http://musicplayer.sourceforge.net/">XSPF Web Music Player</a>
<ul>
<li>Music Player</li>
<li>Slim Player</li>
<li>Button Player</li>
</ul>
Lecteurs de <a href="http://www.longtailvideo.com/players/">Jeroen Wijering</a>
<ul>
<li><a href="http://www.longtailvideo.com/players/jw-image-rotator/">JW Image Rotator 3.17</a></li>
<li><a href="http://www.longtailvideo.com/players/jw-flv-player/">JW FLV Media Player 4.3</a></li>
</ul>
<p><small>Attention, la licence de ces deux derniers lecteurs est semi-commerciale, c&rsquo;est pourquoi ils ne sont pas inclus dans cette contribution. 
Vous &ecirc;tes libres de les installer selon vos convictions et nous ne pouvons &ecirc;tre tenus responsables du non-respect de ces licences.</small>
</p>',

'wmode' => 'Param&egrave;tre flash Wmode',
'desc_wmode' => 'Option flash de disposition de l&rsquo;animation &agrave; l&rsquo;avant ou l&rsquo;arri&egrave;re plan',
'jwlogo' => 'Logo',
'desc_jwlogo' => 'Utilisez cette variable flash pour mettre un logo en filigrane dans le bon coin sup&eacute;rieur de l&rsquo;affichage. Tous les formats d&rsquo;image sont support&eacute;s, mais les fichiers png en transparence donnent les meilleurs r&eacute;sultats.',

// fonds/cfg_xspf_musicplayer
'description_musicplayer' => '
<h4>Configuration du musicplayer</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur musicplayer.</p>
En attendant la documentation sur <a href="http://contrib.spip.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'conf_msc' => 'Configuration de musicplayer',
'conf_slim' => 'Configuration de slimplayer',
'conf_but' => 'Configuration de buttonplayer',
'autoload' => 'Pr&eacute;chargement',
'desc_autoload' => 'Valeur bool&eacute;enne indiquant si le media doit &ecirc;tre pr&eacute;charg&eacute; (&eacute;vite un temps d&rsquo;attente lorsque l&rsquo;utilisateur d&eacute;marre l&rsquo;&eacute;coute)',

//fonds/cfg_xspf_mediaplayer
'description_mediaplayer' => '
<h4>Configuration du mediaplayer</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur "mediaplayer" de Jeroen Wijering.</p>
En attendant la documentation sur <a href="http://contrib.spip.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/SPIP-Plugin-XSPF-Le-modele"> bloc note de kent1</a>',

'mediaplayer_exemple' => 'Pr&eacute;visualisation du mod&egrave;le "mediaplayer" (sur tout le site)',
'typefichier' => 'Type de fichiers g&eacute;r&eacute;s',
'desc_typefichier' => 'D&eacute;fini les types de fichiers pris en compte.<br />Les types doivent &ecirc;tre mentionn&eacute;s par leur extension et s&eacute;par&eacute;s par un pipe (par d&eacute;faut &laquo;flv|swf|mp3|jpg|png|gif&raquo;)',
'playliste' => 'Position de la playliste',
'desc_playliste' => 'Peut &ecirc;tre configur&eacute; en bas, &agrave; droite, par dessus ou invisible.',
'right' => '&Agrave; droite',
'over' => 'Par dessus',
'bottom' => 'En bas',
'none' => 'Aucune',
'playlistsize' => 'Taille de la playliste',
'desc_playlistsize' => 'Lorsqu\'elle est plac&eacute;e en dessous, cela correspond &agrave; la hauteur, lorsque la playliste est &agrave; droite, c\'est la largeur.',
'bufferlength' => 'Taille du buffer',
'desc_bufferlength' => 'Nombre de secondes &agrave; charger du fichier avant de lancer la lecture.',
'displayclick' => 'Action du click sur le lecteur',
'desc_displayclick' => 'Peut &ecirc;tre play, link, fullscreen, none, mute, next. Lorsqu\'il est r&eacute;gl&eacute; sur \'none\', le curseur n\'est pas chang&eacute; en main.',
'none' => 'Rien',
'fullscreen' => 'Plein &eacute;cran',
'next' => 'Next',
'mute' => 'Mute',
'play' => 'Lecture',
'icons' => 'Icones',
'desc_icons' => 'Permet d\'afficher ou de cacher l\'icone de lecture et de chargement au milieu du lecteur',
'desc_mute' => 'Met &agrave; z&eacute;ro le volume au chargement. Sauvegard&eacute; dans un cookie.',
'quality' => 'Qualit&eacute;',
'desc_quality' => 'Permet de passer d\'une haute &agrave; une basse qualit&eacute;. Sauvegard&eacute; dans un cookie.',
'stretching' => 'Redimentionnement',
'desc_stretching' => 'D&eacute;fini la m&eacute;thode de redimensionnement des images dans le lecteur. Peut &ecirc;tre \'non\' (pas de redimensionnement), \'exactfit\' (disproportion&eacute;), \'uniform\' (redimensionnement avec barres noires autour) ou \'fill\' (comme \'uniform\', mais en remplissant compl&ecirc;tement le lecteur).',
'uniform' => 'Uniforme',
'fill' => 'Remplissage (sans perte)',
'exactfit' => 'Remplissage exact',
'desc_jwrepeat' => 'Configurez &agrave; \'liste\' pour jouer l\'ensemble de la playliste, &agrave; \'toujours\' pour jouer continuellement le son/video/playliste et &agrave; \'Single\' pour r&eacute;p&eacute;ter continuellement le media choisi dans une playliste.',
'list' => 'Liste',
'allways' => 'Toujours',
'single' => 'Single',
'controlbar' => 'Barre de contr&ocirc;les',
'desc_controlbar' => 'Position de la barre de contr&ocirc;les. Peut &ecirc;tre positionn&eacute;e en bas, par dessus ou aucune.',

// fonds/cfg_xspf_rotator
'description_rotator' => '
<h4>Configuration de rotator</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur "rotator" de Jeroen Wijering.</p>
En attendant la documentation sur <a href="http://contrib.spip.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/SPIP-Plugin-XSPF-Le-modele-rotator"> bloc note de kent1</a>',

'rotator_exemple' => 'Pr&eacute;visualisation du mod&egrave;le "rotator" (sur tout le site)',
'enablejs' => 'Enable JS',
'desc_enablejs' => 'Option enablejs<br />Autorise le contr&ocirc;le externe du lecteur par javascript',
'javascriptid' => 'Javacript ID',
'desc_javascriptid' => 'Option javascriptid<br />Donne le nom de l\'&eacute;l&eacute;ment que l\'on peut alors contr&ocirc;ler par javascript. Ici on d&eacute;fini le pr&eacute;fix que l\'on souhaite utiliser et le player aura pour id javascript \'prefix#ID_OBJET\' (par d&eacute;faut \'player#ID_OBJET\')',
'showicons' => 'Montrer les boutons',
'desc_showicons' => 'Si r&eacute;gl&eacute; &agrave; false cache l\'icone d\'activit&eacute et le bouton de lecture au milieu du lecteur',
'transition' => 'Transition',
'desc_transition' => 'Permet de r&eacute;gler la transition &agrave; utiliser entre les images. &laquo;random&raquo; affichera chaque transition al&eacute;atoirement. &laquo;fade&raquo; est d&eacute;fini par d&eacute;faut.',
'overstretch' => 'Redimentionnement',
'desc_overstretch' => 'Configure la mani&egrave;re de redimentionner les images/vid&eacute;os pour qu\'elle remplissent le lecteur. Configur&eacute; &agrave; &quote;true&quote; pour redimentionner proportionnellement pour remplir le lecteur, &quote;fit&quote;pour les redimentionner disproportionnellement et &quote;none&quote; pour garder les dimensions originales.',
'showeq' => 'Montrer l\'equalizer',
'desc_showeq' => 'Afficher l&rsquo;&eacute;qualizer dans la zone de contenu lors de la lecture de mp3',
'shownavigation' => 'Barre de navigation',
'desc_shownavigation' => 'Active/d&eacute;sactive la barre de navigation.',
'audio'=>'Audio',
'desc_audio'=>'Vous pouvez r&eacute;gler cette variable flash avec l&rsquo;adresse URL d&rsquo;un fichier mp3 qui peut servir de piste audio suppl&eacute;mentaire. Utilisez le pour des commentaires d&rsquo;accessibilit&eacute;, pour simplement commmenter une vid&eacute;o, ou avec rotator comme musique de fond.',
'rotatetime' => 'Temps de rotation',
'desc_rotatetime' => 'Utilisez cette variable flash pour r&eacute;gler le temps pendant lequel vous souhaitez afficher une image. La valeur par d&eacute;faut est 5.',
'retailler_images' => 'Retailler les images',
'rotrecadre_width' => 'Retaillage des images (largeur)',
'desc_rotrecadre_width' => 'Largeur (en pixel) &agrave; laquelle spip retaillera automatiquement les images dans la playliste (640 par d&eacute;faut). Mettre 0 pour ne pas retailler.',
'rotrecadre_height' => 'Retaillage des images (hauteur)',
'desc_rotrecadre_height' => 'Hauteur (en pixel) &agrave; laquelle spip retaillera automatiquement les images dans la playliste (0 par d&eacute;faut). Mettre 0 pour ne pas retailler.',
'linkfromdisplay' =>'Lien depuis le lecteur',
'desc_linkfromdisplay' => 'Vous pouvez r&eacute;gler cette variable flash sur  &laquo;Vrai&raquo;  pour rendre cliquable l&rsquo;image(ou la vid&eacute;o) affich&eacute;e dans la zone de contenu provoquant &rsquo;affichage de la page indiqu&eacute;e dans &quot;link&quot;. Par d&eacute;faut un click sur la zone de contenu lira/stopera la lecture du contenu.',

// Les deux player de JW
'shuffle' => 'Lecture al&eacute;atoire (Shuffle)',
'desc_shuffle' => 'Jouer al&eacute;atoirement les fichiers de la liste de lecture',
'volume' => 'Volume',
'desc_volume' => 'Volume du lecteur au chargement. Peut &ecirc;tre de 0 &agrave; 100. Sauvegard&eacute; dans un cookie.',
'backcolor' => 'Couleur d&rsquo;arri&egrave;re plan', 
'desc_backcolor' => 'Couleur d&rsquo;arri&egrave;re plan du lecteur. La couleur par d&eacute;faut des lecteurs est 0xFFFFFF (blanc) et du rotator est 0x000000 (noir).',
'frontcolor' => 'Couleur de contraste',
'desc_frontcolor' => 'Couleur des textes et des boutons du lecteur. La couleur par d&eacute;faut des lecteurs est 0x000000 (noir) et du rotator est 0xFFFFFF (blanc).',
'lightcolor' => 'Couleur de mise en &eacute;vidence',
'desc_lightcolor' => 'Couleur de survol du lecteur par la souris.  La couleur par d&eacute;faut des lecteurs est 0x000000 (noir) et du rotator est 0xCC0000 (rouge).',
'screencolor' => 'Couleur de fond du lecteur',
'desc_screencolor' => 'Couleur de fond du lecteur. La couleur par d&eacute;faut du mediaplayer est 0x000000 (noir) et du rotator est 0xCC0000 (rouge).',
'autostart' => 'Lecture automatique au chargement',
'desc_autostart' => 'Lecture automatique au chargement de la page',
'linktarget'=>'Cible du lien',
'desc_linktarget' =>'Permet de d&eacute;terminer o&ugrave; doit s&rsquo;ouvrir un lien (depuis la zone de contenu ou les boutons). Par d&eacute; le r&eacute;glage est &laquo;_self&raquo;. Indiquez &laquo;_blank&raquo; pour ouvrir le lien dans une nouvelle fen&ecirc;tre. Ins&eacute;rez le nom d&rsquo;un frame pour l&rsquo;y ouvrir.',
'desc_width' => 'As with the height of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels wide it should be.',
'desc_height' => 'As with the width of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels high it should be.',
'external_communication' => 'Communication externe',
'playback_behaviour' => 'Comportement de lecture',
'color_anim' => 'Couleurs de l&rsquo;animation',
'layout' => 'Layout',
'display_appearance' => 'Apparence du lecteur',
'controlbar_appearance' => 'Apparence de la barre de contr&ocirc;le',
'menu' => 'Menu flash',
'desc_menu' => 'Affichage du menu falsh au clic droit sur le lecteur.',

//page d'affichage de la configuration du plugin

'allowfullscreen' => 'Plein &eacute;cran',
'desc_allowfullscreen' => 'Permettre l&rsquo;affichage en plein &eacute;cran.',
'list' => 'Liste',
'logo' => 'Logo',
'desc_logo' => 'Permet d&rsquo;afficher une image en surimpression, en haut &agrave; droite de la zone de contenu, des lecteurs de Jeroen Wijering',
'jw_logo' => 'Configuration g&eacute;n&eacute;rale pour les lecteurs de Jeroen Wijering',
'display_size' => 'Dimensions de la zone de contenu de l&rsquo;animation',
'link' =>'Link',
'desc_link' =>'Ins&eacute;rez ici l&rsquo;adresse URL d&rsquo;une version t&eacute;l&eacute;chargeable du fichier ou d&rsquo;un script de t&eacute;l&eacute;chargement forc&eacute; du fichier. Vous pouvez assigner des liens cliquables &agrave; la zone de contenu (display ci-dessous) et au bouton de t&eacute;l&eacute;chargement, mais pas encore aux &eacute;l&eacute;ment de la liste de lecture.',
'number'=>'Un nombre',
'opt_lec' => 'Options de lecture',
'opt_aff' => 'Options d&rsquo;affichage',
'opt_link' => 'Options des liens',
'opt_audio' => 'Piste audio suppl&eacute;mentaire',
'opt_javascript' => 'Options javascript',
'conf_jw_mpl' => 'Configuration de mediaplayer',
'conf_jw_flp' => 'Configuration de flash video player',
'conf_jw_rot' => 'Configuration de rotator',
'opt_avancees' => 'Options avanc&eacute;es',
'prerolllocation' =>'Emplacement preroll',
'prerolllink' =>'Lien sur preroll',
'postrolllocation' =>'Emplacement postroll',
'postrolllink' =>'Lien sur postroll',
'desc_prerolllocation' =>'Adresse du fichier de pr&eacute;face. <br /> Il vous est possible d&rsquo;afficher une vid&eacute;o avant le d&eacute;but de la lecture de la playlist',
'desc_prerolllink' =>'Lien depuis le fichier de pr&eacute;face',
'desc_postrolllocation' =>'Adresse du fichier de postface. <br /> Il vous est possible d&rsquo;afficher une vid&eacute;o apr&egrave; la fin de la lecture de la playlist',
'desc_postrolllink' =>'Lien depuis le fichier de postface',
'jw_media_player' => '<NEW><p>The JW Media Player supports playback of a single media file of any format the Adobe Flash Player can handle (MP3, FLV, SWF, JPG, PNG and GIF). 
It also supports RSS, XSPF and ATOM playlist (with mixed mediatypes and advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.longtailvideo.com/players/jw-flv-player/">http://www.longtailvideo.com/players/jw-flv-player/</a></p>',
'jw_rotator' => '<NEW><p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.longtailvideo.com/players/jw-image-rotator/">http://www.longtailvideo.com/players/jw-image-rotator/</a></p>',
'jw_media_player_install' => '<p>Pour installer JW Media Player, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/imagerotator-3-16.zip">ici</a> et d&eacute;compressez un répertoire du m&ecirc;me nom que l\'archive dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',
'jw_rotator_install' => '<p>Pour installer JW Image Rotator, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_image_rotator.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',
'js_necessaire' => 'D&#233;sol&#233;, mais le javascript est n&#233;ecessaire dans la version actuelle. Merci de le r&#233;activer pour afficher le contenu multimedia ',
);
?>