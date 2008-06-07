<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// description du plugin
'description_xspf' => '
<NEW><h4>Configuration du plugin xspf</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options de chacun des lecteurs.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'description_flvplayer' => '
<NEW><h4>Configuration du  flvplayer</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur "flvplayer" de Jeroen Wijering.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'description_mediaplayer' => '
<NEW><h4>Configuration du mediaplayer</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur "mediaplayer" de Jeroen Wijering.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'description_musicplayer' => '
<NEW><h4>Configuration du musicplayer</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur musicplayer.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'description_rotator' => '
<NEW><h4>Configuration de rotator</h4>
<p>Ici vous pouvez configurer les diff&eacute;rentes options du lecteur "rotator" de Jeroen Wijering.</p>
En attendant la documentation sur <a href="http://www.spip-contrib.net">Spip-Contrib</a>, consultez le <a href="http://kent1.sklunk.net/spip.php?article96"> bloc note de kent1</a>',

'description_xspf_lecteurs' => '
<NEW>Lecteurs de <a href="http://musicplayer.sourceforge.net/">XSPF Web Music Player</a>
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
</p>',

//page d\'affichage de la configuration du plugin
'width' => 'Width',
'desc_width' => 'As with the height of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels wide it should be.',
'height' => 'Height',
'desc_height' => 'As with the width of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels high it should be.',
'displaywidth' => 'Display width',
'desc_displaywidth' => 'Instead of the &quot;displayheight&quot;, you can set &quot;displaywidth&quot; to a size smaller that the SWF width to make the playlist appear at the right side of the display.',
'displayheight' => 'Display height',
'desc_displayheight' => 'This flashvar is used by the players and sets the height of the display. It defaults to the height of the SWF object minus the controlbar (20px), but if you set it to a smaller height, the playlist will show up. If you set it to the height of the player itself (or larger), the controlbar will auto-hide over the video.',
'true' => 'True',
'false' => 'False',
'backcolor' => 'Backgroundcolor',
'desc_backcolor' => 'Backgroundcolor of the player/rotator. The default for the players is 0xFFFFFF (white) and for the rotator 0x000000 (black).',  
'frontcolor' => 'Frontcolor',
'desc_frontcolor' => 'Texts / buttons color of the player/rotator. The default for the players is 0x000000 (black) and for the rotator 0xFFFFFF (white).',
'lightcolor' => 'Lightcolor',
'desc_lightcolor' => 'Rollover/ active color of the player/rotator. The default for the players is 0x000000 (black) and for the rotator 0xCC0000 (red).',
'repeat' => 'Repeat',
'desc_jwrepeat' => ' By default, the players will stop playback after every item to preserve bandwidth (repeat=false). You can set this to &quot;list&quot; to playback all items in a playlist once, or to &quot;true&quot; to continously playback your song/movie/playlist.',
'autostart' => 'Autostart',
'desc_autostart' => 'Set this to &quot;true&quot; to make the player automatically start playing when the page loads. If set to &quot;muted&quot;, the player will autostart with the volume set to 0 and an unmute icon in the display.',
'overstretch' => 'overstretch',
'desc_overstretch' => 'Defines how to stretch images/movies to make them fit the display. &quot;true&quot; will stretch them proportionally to fill the display, &quot;false&quot; will stretch them to fit. &quot;fit&quot; will stretch them disproportionally to fit both height and width. &quot;none&quot; will show all items in their original dimensions. Defaults to &quot;fit&quot; for the players and &quot;false&quot; for the rotator.',
'showdigits' => 'showdigits',
'desc_showdigits' => 'Set this to false if you don\'t want the elapsed/remaining time to display in the controlbar of the players. Quite handy to save some space. Set it to &quot;total&quot; to show the total time instead of the remaining time.',
'showvolume' => 'showvolume',
'desc_showvolume' => 'Set this to false to hide the volume button and save space',
'showicons' => 'showicons',
'desc_showicons' => 'Show or hide the play and activity icons in the middle of the display. Defaults to true for the players and false for the rotator. If set to false, the overlaid controlbar will also hide with the players.',
'showeq' => 'showeq',
'desc_showeq' => 'Set to true to show a fake equalizer in the display. It adds a nice graphical touch when you are playing MP3 files.',
'shuffle' => 'shuffle',
'desc_shuffle' => 'If you use a playlist, the players and rotator will automatically shuffle the entries to prevent boredom. Set this flashvar to &quot;false&quot; to play all items sequentially.',
'allowfullscreen' => 'allowfullscreen',
'desc_allowfullscreen' => 'allowfullscreen',
'thumbsinplaylist' => 'thumbsinplaylist',
'desc_thumbsinplaylist' => 'If you have a playlist that also includes preview images with the <image> element, you can set this flashvar to &quot;true&quot; to show them in the playlist.',
'transition' => 'transition',
'desc_transition' => 'It sets the transition to use between images. &quot;random&quot; will show all transitions randomly. The default is &quot;fade&quot;.',
'shownavigation' => 'shownavigation',
'desc_shownavigation' => 'It enables/disables the navigation bar.',
'wmode' => 'wmode',
'desc_wmode' => 'Use the WMODE parameter to allow layering of Flash content with DHTML layers. The WMODE parameter can be &quot;window&quot; (default), &quot;opaque&quot;, or &quot;transparent&quot;. Using a WMODE value of &quot;opaque&quot; or &quot;transparent&quot; will prevent a Flash movie from playing in the topmost layer and allow you to adjust the layering of the movie within other layers of the HTML document.',
'autoload' => 'autoload',
'desc_autoload' => 'autoload',
'jwlogo' => 'Logo',
'desc_jwlogo' => 'Set this flashvar to put a watermark logo in the top right corner of the display. All image formats are supported, but transparent PNG files give the best results',
'showdownload'=>'showdownload',
'desc_showdownload' => 'Set this to true to show a downloadbutton in the controlbar. The downloadbutton links to the link flashvar.',
'link' =>'Link',
'desc_link' =>'Set here the url to an external URL, downloadeable version of the file, or force-download script you can use for downloading the file. You can assign link-clicks to the display (see below) and the downloadbutton but not yet to every item in a playlist.',
'linkfromdisplay' =>'linkfromdisplay',
'desc_linkfromdisplay' => 'You can set this flashvar to &quot;true&quot; to make a click on the image/video display to result in a jump to the &quot;link&quot; webpage. By default, a click on the display will play/pause the movie.',
'linktarget'=>'linktarget',
'desc_linktarget' =>'The targetframe a link (from the display or playlist buttons) will open into. The default is &quot;_self&quot;. Set it to &quot;_blank&quot; to open links in a new window.',
'autoscroll' => 'autoscroll',
'desc_autoscroll' =>'By default, the playlist area of the players will have a scrollbar if the number of items is too long. If you set this flashvar to &quot;true&quot;, the scrollbar wil disappear and the playlist will scroll automatically, depending upon the mouse position.',
'audio'=>'audio',
'desc_audio'=>'You can set this flashvar to the location of an external mp3 file that should serve as an additional audiotrack. Use this for accessibility commentary, director\'s comments or, with the imagerotator, background music.',
'useaudio'=>'useaudio',
'desc_useaudio' => 'Set this to false to force the additional audiotrack to mute by default.',
'enablejs' => '<NEW>Enable JS',
'desc_enablejs' => '<NEW>Option enablejs<br />Autorise le contr&ocirc;le externe du lecteur par javascript',
'javascriptid' => '<NEW>Javacript ID',
'desc_javascriptid' => '<NEW>Option javascriptid<br />Donne le nom de l\'&eacute;l&eacute;ment que l\'on peut alors contr&ocirc;ler par javascript.<br />Ici on d&eacute;fini le pr&eacute;fix que l\'on souhaite utiliser et le player aura pour id javascript "prefix#ID_OBJET" (par d&eacute;faut "player#ID_OBJET")',
'bufferlength'=>'bufferlength',
'desc_bufferlength'=>'This sets the number of seconds an FLV should be buffered ahead before the player starts it. Set this smaller for fast connections or short videos. Set this bigger for slow connections. The default is 3 seconds.',
'number'=>'A number',
'captions'=>'captions',
'desc_captions'=>'You can set this flashvar to the location of an external textfile with captions. The players support SMIL\'s TimedText format and the SRT format used with ripped DVD\'s. Set this flashvar to &quot;captionate&quot; if your FLV file has Captionate captions embedded. If you use multitrack Captionate captions, you can set this flashvar to &quot;captionate0&quot;, &quot;captionate3&quot; etc. to display a certain track. You can not yet assign captions for every item in a playlist.',
'usecaptions'=>'usecaptions',
'desc_usecaptions'=>'Set this to false to force the captions to hide by default.',

'size_anim' => 'Animation size',
'typefichier' => '<NEW>Type de fichiers g&eacute;r&eacute;s',
'desc_typefichier' => '<NEW>D&eacute;fini les types de fichiers pris en compte.<br />Les types doivent &ecirc;tre mentionn&eacute;s par leur extension et s&eacute;par&eacute;s par une virgule (par d&eacute;faut &laquo;flv,swf,mp3,jpg,png,gif&raquo;)',
'display_size' => 'Display size',
'color_anim' => 'Animation colors',
'rotatetime' => 'rotatetime',
'desc_rotatetime' => 'Use this flashvar to set the number of seconds you want an image to display. The default is "5".',

'opt_lec' => 'Playing options',
'opt_aff' => 'Display options',
'opt_link' => 'Link options',
'opt_audio' => 'Audio options',
'opt_javascript' => '<NEW>Options javascript',
'conf_jw_mpl' => 'Mediaplayer settings',
'conf_jw_flp' => 'Flash video player settings',
'conf_jw_rot' => 'Rotator settings',
'conf_msc' => 'Musicplayer settings',
'conf_slim' => 'Slimplayer settings',
'conf_but' => 'Buttonplayer settings',
'wmode' => '<NEW>Param&egrave;tre flash Wmode',
'jw_logo' => 'General setting for Jeroen Wijering\'s players',
'opt_avancees' => 'Advanced settings',
'prerolllocation' =>'preroll location',
'prerolllink' =>'preroll link',
'postrolllocation' =>'postroll location',
'postrolllink' =>'postroll link',
'desc_prerolllocation' =>'Adresse du fichier de pr&eacute;face',
'desc_prerolllink' =>'Lien depuis le fichier de pr&eacute;face',
'desc_postrolllocation' =>'Adresse du fichier de postface',
'desc_postrolllink' =>'Lien depuis le fichier de postface',

'jw_media_player' => '<p>The JW Media Player supports playback of a single media file of any format the Adobe Flash Player can handle (MP3, FLV, SWF, JPG, PNG and GIF). 
It also supports RSS, XSPF and ATOM playlist (with mixed mediatypes and advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_Media_Player">http://www.jeroenwijering.com/?item=JW_Media_Player</a></p>',
'jw_rotator' => '<p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_Image_Rotator">http://www.jeroenwijering.com/?item=JW_Image_Rotator</a></p>',
'jw_flv_player' => '<p>The JW FLV Player can be used standalone, without the need for the Flash authoring tool. The player allows you to show your videos more controlled and to a broader audience than with Quicktime, Windows Media or Real Media. 
It supports playback of a single Flash video file, RTMP streams or RSS, XSPF and ATOM playlists (with advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_FLV_Player">http://www.jeroenwijering.com/?item=JW_FLV_Player</a></p>',

'jw_media_player_install' => '<p>The JW Media Player supports playback of a single media file of any format the Adobe Flash Player can handle (MP3, FLV, SWF, JPG, PNG and GIF). 
It also supports RSS, XSPF and ATOM playlist (with mixed mediatypes and advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_Media_Player">http://www.jeroenwijering.com/?item=JW_Media_Player</a></p>',
'jw_rotator_install' => '<p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_Image_Rotator">http://www.jeroenwijering.com/?item=JW_Image_Rotator</a></p>',
'jw_flv_player_install' => '<p>The JW FLV Player can be used standalone, without the need for the Flash authoring tool. The player allows you to show your videos more controlled and to a broader audience than with Quicktime, Windows Media or Real Media. 
It supports playback of a single Flash video file, RTMP streams or RSS, XSPF and ATOM playlists (with advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_FLV_Player">http://www.jeroenwijering.com/?item=JW_FLV_Player</a></p>',

'jw_media_player_install' => '<p>Pour installer JW Media Player, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/mediaplayer-3-16.zip">ici</a> et d&eacute;compressez un répertoire du m&ecirc;me nom que l\'archive dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',
'jw_rotator_install' => '<NEW><p>Pour installer JW Image Rotator, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_image_rotator.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',

'js_necessaire' => 'D&#233;sol&#233;, mais le javascript est n&#233;ecessaire dans la version actuelle. Merci de le r&#233;activer pour afficher le contenu multimedia ',
);
?>
