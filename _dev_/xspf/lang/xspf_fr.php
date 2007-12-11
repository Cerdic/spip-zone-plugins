<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// description du plugin
'description_xspf' => '
<h4>Configuration du plugin xspf</h4>
<p>Ici vous pouvez configurer les diff&eacute;rents options de chacun des lecteurs.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>
',

'description_xspf_lecteurs' => '
Lecteurs de <a href="http://musicplayer.sourceforge.net/">XSPF Web Music Player</a>
<ul>
<li>Music Player</li>
<li>Slim Player</li>
<li>Button Player</li>
</ul>
Lecteurs de <a href="http://www.jeroenwijering.com/">Jeroen Wijering</a>
<ul>
<li>Rotator</li>
<li>Media Player</li>
<li>Flash Video Player</li>
</ul>

<p><small>Attention, la licence de ces trois lecteurs est semi-commerciale, c&rsquo;est pourquoi ils ne sont pas inclus dans cette contribution. 
Vous &ecirc;tes libres de les installer selon vos convictions et nous ne pouvons &ecirc;tre tenus responsables du non-respect de ces licences.</small>
</p>
',


//page d\'affichage de la configuration du plugin
'width' => 'Largeur',
'height' => 'Hauteur',
'true' => 'Vrai',
'false' => 'Faux',
'list' => 'Liste',
'backcolor' => 'Couleur d&rsquo;arri&egrave;re plan', 
'frontcolor' => 'Couleur de contraste',
'lightcolor' => 'Couleur de mise en &eacute;vidence',
'repeat' => 'R&eacute;p&eacute;tition',
'autostart' => 'Lecture automatique au chargement',
'overstretch' => 'stretch',
'showdigits' => 'digits',
'showvolume' => 'showvolume',
'showicons' => 'showicons',
'shuffle' => 'shuffle',
'allowfullscreen' => 'allowfullscreen',
'thumbsinplaylist' => 'thumbsinplaylist',
'transition' => 'transition',
'shownavigation' => 'shownavigation',
'wmode' => 'wmode',
'autoload' => 'Pr&eacute; chargement',
'logo' => 'Logo',
'desc_logo' => 'Permet d&rsquo;afficher une image en surimpression, en haut &agrave; droite de la zone de contenu, des lecteurs de Jeroen Wijering',


'desc_width' => 'As with the height of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels wide it should be.',
'desc_height' => 'As with the width of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels high it should be.',
'displaywidth' => 'Largeur de l&rsquo;affichage',
'desc_displaywidth' => 'Instead of the &quot;displayheight&quot;, you can set &quot;displaywidth&quot; to a size smaller that the SWF width to make the playlist appear at the right side of the display.',
'displayheight' => 'Hauteur de l&rsquo;affichage',
'desc_displayheight' => 'This flashvar is used by the players and sets the height of the display. It defaults to the height of the SWF object minus the controlbar (20px), but if you set it to a smaller height, the playlist will show up. If you set it to the height of the player itself (or larger), the controlbar will auto-hide over the video.',
'desc_backcolor' => 'Couleur d&rsquo;arri&egrave;re plan du lecteur. La couleur par d&eacute;faut des lecteurs est 0xFFFFFF (blanc) et du rotator est 0x000000 (noir).',  
'desc_frontcolor' => 'Couleur des textes et des boutons du lecteur. La couleur par d&eacute;faut des lecteurs est 0x000000 (noir) et du rotator est 0xFFFFFF (blanc).',  
'desc_lightcolor' => 'Couleur de survol du lecteur par la souris.  La couleur par d&eacute;faut des lecteurs est 0x000000 (noir) et du rotator est 0xCC0000 (rouge).',
'desc_jwrepeat' => ' Par d&eacute;faut le lecteur s&rsquo;arr&ecirc;te apr&egrave;s la lecture de chaque &eacute;l&eacute;ment de la liste pour pr&eacute;server la bande passante (repeat=false). Vous pouvez le r&eacute;gler sur  &laquo;Liste&raquo; pour jouer une fois l&rsquo;ensemble d&rsquo;une liste de lecture, ou sur &laquo;Vrai&raquo; pour lire continument votre fichier/liste de lecture.',
'desc_autostart' => 'Lecture automatique au chargement de la page',
'desc_overstretch' => 'Defines how to stretch images/movies to make them fit the display. &quot;true&quot; will stretch them proportionally to fill the display, &quot;false&quot; will stretch them to fit. &quot;fit&quot; will stretch them disproportionally to fit both height and width. &quot;none&quot; will show all items in their original dimensions. Defaults to &quot;fit&quot; for the players and &quot;false&quot; for the rotator.',
'desc_showdigits' => 'Afficher la dur&eacute;e du fichier en cours de lecture',
'desc_showvolume' => 'R&eacute;gler sur faux pour cacher le bouton de volume et gagner de la place',
'desc_showicons' => 'Afficher les ic&ocirc;nes des documents joints dans la zone de contenu',
'showeq' => 'showeq',
'desc_showeq' => 'Afficher l&rsquo;&eacute;qualizer dans la zone de contenu lors de la lecture de mp3',
'desc_shuffle' => 'Jouer al&eacute;atoirement les fichiers de la liste de lecture',
'desc_allowfullscreen' => 'Permettre l&rsquo;affichage en plein &eacute;cran',
'desc_thumbsinplaylist' => 'Affichage des vignettes des documents dans la liste de lecture',
'desc_transition' => 'Permet de r&eacute;gler la transition &agrave; utiliser entre les images. &laquo;random&raquo; affichera chaque transition al&eacute;atoirement. &laquo;fade&raquo; est d&eacute;fini par d&eacute;faut.',
'desc_shownavigation' => 'Active/d&eacute;sactive la barre de navigation.',
'desc_wmode' => 'Option flash de disposition de l&rsquo;animation &agrave; l&rsquo;avant ou l&rsquo;arri&egrave;re plan',
'desc_autoload' => 'Valeur bool&eacute;enne indiquant si le media doit &ecirc;tre pr&eacute;charg&eacute; (&eacute;vite un temps d&rsquo;attente lorsque l&rsquo;utilisateur d&eacute;marre l&rsquo;&eacute;coute)',
'jwlogo' => 'Logo',
'desc_jwlogo' => 'Utilisez cette variable flash pour mettre un logo en filigrane dans le bon coin sup&eacute;rieur de l&rsquo;affichage. Tous les formats d&rsquo;image sont support&eacute;s, mais les fichiers png en transparence donnent les meilleurs r&eacute;sultats.',
'size_anim' => 'Dimensions de l&rsquo;animation',
'typefichier' => 'Type de fichiers g&eacute;r&eacute;s',
'desc_typefichier' => 'D&eacute;fini les types de fichiers pris en compte.<br />Les types doivent &ecirc;tre mentionn&eacute;s par leur extension et s&eacute;par&eacute;s par une virgule (par d&eacute;faut &laquo;flv,swf,mp3,jpg,png,gif&raquo;)',
'display_size' => 'Dimensions de la zone de contenu de l&rsquo;animation',
'color_anim' => 'Couleurs de l&rsquo;animation',

'showdownload'=>'showdownload',
'desc_showdownload' => 'R&eacute;glez ce param&egrave;tre sur &laquo;Vrai&raquo; pour afficher un bouton de t&eacute;l&eacute;chargement dans la barre de controle. Le bouton de t&eacute;l&eacute;chargement est li&eacute; &agrave; la variable flash nomm&eacute;e link ci-dessous',
'link' =>'Link',
'desc_link' =>'Ins&eacute;rez ici l&rsquo;adresse URL d&rsquo;une version t&eacute;l&eacute;chargeable du fichier ou d&rsquo;un script de t&eacute;l&eacute;chargement forc&eacute; du fichier. Vous pouvez assigner des liens cliquables &agrave; la zone de contenu (display ci-dessous) et au bouton de t&eacute;l&eacute;chargement, mais pas encore aux &eacute;l&eacute;ment de la liste de lecture.',
'linkfromdisplay' =>'linkfromdisplay',
'desc_linkfromdisplay' => 'Vous pouvez r&eacute;gler cette variable flash sur  &laquo;Vrai&raquo;  pour rendre cliquable l&rsquo;image(ou la vid&eacute;o) affich&eacute;e dans la zone de contenu provoquant &rsquo;affichage de la page indiqu&eacute;e dans &quot;link&quot;. Par d&eacute;faut un click sur la zone de contenu lira/stopera la lecture du contenu.',
'linktarget'=>'linktarget',
'desc_linktarget' =>'Permet de d&eacute;terminer o&ugrave; doit s&rsquo;ouvrir un lien (depuis la zone de contenu ou les boutons). Par d&eacute; le r&eacute;glage est &laquo;_self&raquo;. Indiquez &laquo;_blank&raquo; pour ouvrir le lien dans une nouvelle fen&ecirc;tre. Ins&eacute;rez le nom d&rsquo;un frame pour l&rsquo;y ouvrir.',
'autoscroll' => 'autoscroll',
'desc_autoscroll' =>'Par d&eacute;faut la liste de lecture pr&eacute;sente, lorsqu&rsquo;elle est trop longue, une barre de d&eacute;filement. Si vous r&eacute;glez cette variable flash sur &laquo;Vrai&raquo;, la barre de d&eacute;filement dispara&icirc;tra et la liste de lecture d&eacute;filera automatiquement en fonction de la position de la souris.',
'audio'=>'audio',
'desc_audio'=>'Vous pouvez r&eacute;gler cette variable flash avec l&rsquo;adresse URL d&rsquo;un fichier mp3 qui peut servir de piste audio suppl&eacute;mentaire. Utilisez le pour des commentaires d&rsquo;accessibilit&eacute;, pour simplement commmenter une vid&eacute;o, ou avec rotator comme musique de fond.',
'useaudio'=>'useaudio',
'desc_useaudio' => 'R&eacute;glez le sur &laquo;Faux&raquo; pour que par d&eacute;faut la piste audio suppl&eacute;mentaire soit forc&eacute;e au silence.',
'enablejs' => 'Enable JS',
'desc_enablejs' => 'Option enablejs<br />Autorise le contr&ocirc;le externe du lecteur par javascript',
'javascriptid' => 'Javacript ID',
'desc_javascriptid' => 'Option javascriptid<br />Donne le nom de l\'&eacute;l&eacute;ment que l\'on peut alors contr&ocirc;ler par javascript. Ici on d&eacute;fini le pr&eacute;fix que l\'on souhaite utiliser et le player aura pour id javascript "prefix#ID_OBJET" (par d&eacute;faut "player#ID_OBJET")',
'bufferlength'=>'bufferlength',
'desc_bufferlength'=>'This sets the number of seconds an FLV should be buffered ahead before the player starts it. Set this smaller for fast connections or short videos. Set this bigger for slow connections. The default is 3 seconds.',
'number'=>'Un nombre',
'captions'=>'sous-titres',
'desc_captions'=>'Utilisez cette variable flash en y entrant l&rsquo;adresse URL d&rsquo;un fichier texte contenant des sous-titres. Les lecteurs supportent le format SMIL TimedText et le format SRT. R&eacute;glez cette variable sur &laquo;captionate&raquo; si votre fichier FLV contient des sous-titres embarqu&eacute;s. Si vous utilisez plusieurs pistes, vous pouvez r&eacute;gler cette variable en &laquo;captionate0&raquo;, &laquo;captionate3&raquo; etc. pour afficher une piste en particulier. On ne peut pas encore assigner des sous-titres &agrave; chaque &eacute;l&eacute;ment de la liste de lecure.',
'usecaptions'=>'usecaptions',
'desc_usecaptions'=>'R&eacute;glez sur &laquo;Faux&raquo; pour forcer les sous-titres &agrave; &ecirc;tre masqu&eacute;s par d&eacute;faut.',
'rotatetime' => 'rotatetime',
'desc_rotatetime' => 'Utilisez cette variable flash pour r&eacute;gler le temps pendant lequel vous souhaitez afficher une image. La valeur par d&eacute;faut est 5.',
'opt_lec' => 'Options de lecture',
'opt_aff' => 'Options d&rsquo;affichage',
'opt_link' => 'Options des liens',
'opt_audio' => 'Piste audio suppl&eacute;mentaire',
'opt_javascript' => 'Options javascript',
'conf_jw_mpl' => 'Configuration de mediaplayer',
'conf_jw_flp' => 'Configuration de flash video player',
'conf_jw_rot' => 'Configuration de rotator',
'conf_msc' => 'Configuration de musicplayer',
'conf_slim' => 'Configuration de slimplayer',
'conf_but' => 'Configuration de buttonplayer',
'wmode' => 'Param&egrave;tre flash Wmode',
'jw_logo' => 'Configuration g&eacute;n&eacute;rale pour les lecteurs de Jeroen Wijering',
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
<p><a href="http://www.jeroenwijering.com/?item=JW_Media_Player">http://www.jeroenwijering.com/?item=JW_Media_Player</a></p>',
'jw_rotator' => '<NEW><p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_Image_Rotator">http://www.jeroenwijering.com/?item=JW_Image_Rotator</a></p>',
'jw_flv_player' => '<NEW><p>The JW FLV Player can be used standalone, without the need for the Flash authoring tool. The player allows you to show your videos more controlled and to a broader audience than with Quicktime, Windows Media or Real Media. 
It supports playback of a single Flash video file, RTMP streams or RSS, XSPF and ATOM playlists (with advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_FLV_Player">http://www.jeroenwijering.com/?item=JW_FLV_Player</a></p>',

'jw_media_player_install' => '<NEW><p>Pour installer JW Media Player, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_media_player.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 1.9.3 de SPIP.</p>',
'jw_rotator_install' => '<NEW><p>Pour installer JW Image Rotator, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_flv_player.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 1.9.3 de SPIP.</p>',
'jw_flv_player_install' => '<NEW><p>Pour installer JW Flv Player, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_image_rotator.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 1.9.3 de SPIP.</p>',

);
?>
