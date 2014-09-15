<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// All cfg
'description_xspf' => '
<h4>XSPF plugin configuration</h4>
<p>Here you can configure all the options of each different players.</p>
Waitin the full documentation on <a href="http://contrib.spip.net">Spip-Contrib</a>, consult <a href="http://kent1.sklunk.net/spip.php?article96"> kent1\'s notepad</a>',

'size_anim' => 'Animation size',
'width' => 'Width',
'height' => 'Height',
'true' => 'True',
'false' => 'False',
'repeat' => 'Repeat',

// fonds/cfg_xspf
'description_xspf_lecteurs' => '
Players from <a href="http://musicplayer.sourceforge.net/">XSPF Web Music Player</a>
<ul>
<li>Music Player</li>
<li>Slim Player</li>
<li>Button Player</li>
</ul>
Players from <a href="http://www.longtailvideo.com/players/">Jeroen Wijering</a>
<ul>
<li><a href="http://www.longtailvideo.com/players/jw-image-rotator/">JW Image Rotator 3.17</a></li>
<li><a href="http://www.longtailvideo.com/players/jw-flv-player/">JW FLV Media Player 4.3</a></li>
</ul>
<p><small>Warning, the licence of these two lasts players is semi commercial, that\'s why they are not included in this contribution. 
You stay free to install them and we aren\'t responsible if you don\'t respect the licence.</small>
</p>',

'wmode' => 'Wmode Flash param',
'desc_wmode' => 'Use the WMODE parameter to allow layering of Flash content with DHTML layers. The WMODE parameter can be &quot;window&quot; (default), &quot;opaque&quot;, or &quot;transparent&quot;. Using a WMODE value of &quot;opaque&quot; or &quot;transparent&quot; will prevent a Flash movie from playing in the topmost layer and allow you to adjust the layering of the movie within other layers of the HTML document.',
'jwlogo' => 'Logo',
'desc_jwlogo' => 'Set this flashvar to put a watermark logo in the top right corner of the display. All image formats are supported, but transparent PNG files give the best results',

// fonds/cfg_xspf_musicplayer
'description_musicplayer' => '
<h4>Musicplayer\' configuration</h4>
<p>Here you can configure all the options of the &quote;musicplayer&quote; player.</p>
Waiting for the full documentation on <a href="http://contrib.spip.net">Spip-Contrib</a>, you can consul the <a href="http://kent1.sklunk.net/spip.php?article96"> kent1\'s notepad</a>',

'conf_msc' => 'Musicplayer settings',
'conf_slim' => 'Slimplayer settings',
'conf_but' => 'Buttonplayer settings',
'autoload' => 'Autoload',
'desc_autoload' => 'Boolean value indicating if the media should be preloaded.',

//fonds/cfg_xspf_mediaplayer
'description_mediaplayer' => '
<h4>Mediaplayer\'s configuration</h4>
<p>Here you can configure all the options for the Jeroen Wijering\'s mediaplayer.</p>
Waiting for the full documentation on <a href="http://contrib.spip.net">Spip-Contrib</a>, you can consult the <a href="http://kent1.sklunk.net/SPIP-Plugin-XSPF-Le-modele"> kent1\'s notepad</a>',

'mediaplayer_exemple' => 'Previsualization of the "mediaplayer" template (on the whole site)',
'typefichier' => 'File Types used',
'desc_typefichier' => 'Configure the file types used.<br />They should be written with their extension and separated by a pipe (by default &laquo;flv|swf|mp3|jpg|png|gif&raquo;)',
'playliste' => 'Position of the playlist',
'desc_playliste' => 'Can be set to bottom, over, right or none.',
'right' => 'Right',
'over' => 'Over',
'bottom' => 'Bottom',
'none' => 'None',
'playlistsize' => 'Playlist size',
'desc_playlistsize' => 'when below this refers to the height, when right this refers to the width of the playlist. ',
'bufferlength' => 'Buffer length',
'desc_bufferlength' => 'number of seconds of the file that has to be loaded before starting. Set this to a low value to enable instant-start and to a high value to get less mid-stream buffering.',
'displayclick' => 'Action when click on the display',
'desc_displayclick' => 'Can be play, link, fullscreen, none, mute, next. When set to none, the handcursor is also not shown.',
'none' => 'None',
'fullscreen' => 'Fullscreen',
'next' => 'Next',
'mute' => 'Mute',
'play' => 'Play',
'icons' => 'Icons',
'desc_icons' => 'Set this to false to hide the play button and buffering icon in the middle of the video.',
'desc_mute' => 'Mute all sounds on startup. Is saved in a cookie.',
'quality' => 'Quality',
'desc_quality' => 'Enables smoothed playback. This sets the smoothing and deblocking of videos on/off. Is saved in a cookie.',
'stretching' => 'Stretching',
'desc_stretching' => 'Defines how to resize images in the display. Can be none (no stretching), exactfit (disproportionate), uniform (stretch with black borders) or fill (uniform, but completely fill the display).',
'uniform' => 'Uniform',
'fill' => 'Fill',
'exactfit' => 'Exact Fit',
'desc_jwrepeat' => 'Set to list to play the entire playlist once, to always to continously play the song/video/playlist and to single to continue repeating the selected file in a playlist.',
'list' => 'List',
'allways' => 'Allways',
'single' => 'Single',
'controlbar' => 'Controlbar',
'desc_controlbar' => 'Position of the controlbar. Can be set to bottom, over and none.',

//fonds/cfg_xspf_rotator
'description_rotator' => '
<h4>Rotator\'s configuration</h4>
<p>Here you can configure all the options for the Jeroen Wijering\'s "rotator".</p>
Waiting for the full documentation on <a href="http://contrib.spip.net">Spip-Contrib</a>, you can consult the <a href="http://kent1.sklunk.net/SPIP-Plugin-XSPF-Le-modele-rotator"> kent1\'s notepad</a>',

'rotator_exemple' => 'Previsualization of the "rotator" template (on the whole site)',
'enablejs' => 'Enable JS',
'desc_enablejs' => 'Option enablejs<br />Autorize external javascript controls to the player.',
'javascriptid' => 'Javacript ID',
'desc_javascriptid' => 'Option javascriptid<br />Give the prefix of the element in the DOM to control it thrue javascript.',
'showicons' => 'Show icons',
'desc_showicons' => 'Set this to false to hide the activity icon and play button in the middle of the display.',
'transition' => 'Transition',
'desc_transition' => 'It sets the transition to use between images. &quot;random&quot; will show all transitions randomly. The default is &quot;fade&quot;.',
'overstretch' => 'Stretching',
'desc_overstretch' => 'Sets how to stretch images/movies to make them fit the display. The default stretches to fit the display. Set this to true to stretch them proportionally to fill the display, fit to stretch them disproportionally and none to keep original dimensions.',
'showeq' => 'Show equalizer',
'desc_showeq' => 'Set to true to show a fake equalizer in the display. It adds a nice graphical touch when you are playing MP3 files.',
'shownavigation' => 'Show navigation',
'desc_shownavigation' => 'It enables/disables the navigation bar.',
'audio'=>'Audio',
'desc_audio'=>'You can set this flashvar to the location of an external mp3 file that should serve as an additional audiotrack. Use this for accessibility commentary, director\'s comments or, with the imagerotator, background music.',
'rotatetime' => 'Rotate time',
'desc_rotatetime' => 'Use this flashvar to set the number of seconds you want an image to display. The default is "5".',
'retailler_images' => 'Resizing pictures',
'rotrecadre_width' => 'Resizing pictures (width)',
'desc_rotrecadre_width' => 'Width (in pixel) which SPIP uses to resize pictures in the playlist (640 by default). Put 0 to disable width resizing.',
'rotrecadre_height' => 'Resizing pictures (height)',
'desc_rotrecadre_height' => 'Height (in pixel) which SPIP uses to resize pictures in the playlist (0 by default). Put 0 to disable height resizing.',
'linkfromdisplay' =>'Link from display',
'desc_linkfromdisplay' => 'You can set this flashvar to &quot;true&quot; to make a click on the image/video display to result in a jump to the &quot;link&quot; webpage. By default, a click on the display will play/pause the movie.',

// Les deux player de JW
'shuffle' => 'Shuffle',
'desc_shuffle' => 'If you use a playlist, the players and rotator will automatically shuffle the entries to prevent boredom. Set this flashvar to &quot;false&quot; to play all items sequentially.',
'volume' => 'Volume',
'desc_volume' => 'Startup volume of the player. Can be 0 to 100. Is saved in a cookie.',
'backcolor' => 'Background color',
'desc_backcolor' => 'Background color of the player/rotator. The default for the mediaplayer is 0xFFFFFF (white) and for the rotator 0x000000 (black).',  
'frontcolor' => 'Front color',
'desc_frontcolor' => 'Texts / buttons color of the player/rotator. The default for the mediaplayer is 0x000000 (black) and for the rotator 0xFFFFFF (white).',
'lightcolor' => 'Light color',
'desc_lightcolor' => 'Rollover/ active color of the player/rotator. The default for the mediaplayer is 0x000000 (black) and for the rotator 0xCC0000 (red).',
'screencolor' => 'Background color of the display',
'desc_screencolor' => 'Background color of the display. The default for the mediaplayer is 0x000000 (black) and for the rotator 0xCC0000 (red).',
'autostart' => 'Autostart',
'desc_autostart' => 'Set this to &quot;true&quot; to make the player automatically start playing when the page loads. If set to &quot;muted&quot;, the player will autostart with the volume set to 0 and an unmute icon in the display.',
'linktarget'=>'Link target',
'desc_linktarget' =>'The targetframe a link (from the display or playlist buttons) will open into. The default is &quot;_self&quot;. Set it to &quot;_blank&quot; to open links in a new window.',
'desc_width' => 'As with the height of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels wide it should be.',
'desc_height' => 'As with the width of the player/rotator, this variable is already set with a default embed code. However, sometimes (notably on IE), this won\'t get through (so you get a messed-up display). Then use this flashvar to tell the player/rotator how many pixels high it should be.',
'external_communication' => 'External communication',
'playback_behaviour' => 'Playback behaviour',
'color_anim' => 'Animation colors',
'layout' => 'Layout',
'display_appearance' => 'Display appearance',
'controlbar_appearance' => 'Controlbar appearance',
'menu' => 'Flash menu',
'desc_menu' => 'Display the flash menu when right clicking on the player.',

//page d\'affichage de la configuration du plugin

'allowfullscreen' => 'Allow fullscreen',
'desc_allowfullscreen' => 'Allow Fullscreen mode',
'link' =>'Link',
'desc_logo' => 'Set this flashvar to put a watermark logo in the top right corner of the display. All image formats are supported, but transparent PNG files give the best results',
'desc_link' =>'Set here the url to an external URL, downloadeable version of the file, or force-download script you can use for downloading the file. You can assign link-clicks to the display (see below) and the downloadbutton but not yet to every item in a playlist.',
'number'=>'A number',
'display_size' => 'Display size',
'opt_lec' => 'Playing options',
'opt_aff' => 'Display options',
'opt_link' => 'Link options',
'opt_audio' => 'Audio options',
'opt_javascript' => '<NEW>Options javascript',
'conf_jw_mpl' => 'Mediaplayer settings',
'conf_jw_flp' => 'Flash video player settings',
'conf_jw_rot' => 'Rotator settings',
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
<p><a href="http://www.longtailvideo.com/players/jw-flv-player/">http://www.longtailvideo.com/players/jw-flv-player/</a></p>',
'jw_rotator' => '<p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.longtailvideo.com/players/jw-image-rotator/">http://www.longtailvideo.com/players/jw-image-rotator/</a></p>',
'jw_flv_player' => '<p>The JW FLV Player can be used standalone, without the need for the Flash authoring tool. The player allows you to show your videos more controlled and to a broader audience than with Quicktime, Windows Media or Real Media. 
It supports playback of a single Flash video file, RTMP streams or RSS, XSPF and ATOM playlists (with advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.jeroenwijering.com/?item=JW_FLV_Player">http://www.jeroenwijering.com/?item=JW_FLV_Player</a></p>',
'jw_media_player_install' => '<p>The JW Media Player supports playback of a single media file of any format the Adobe Flash Player can handle (MP3, FLV, SWF, JPG, PNG and GIF). 
It also supports RSS, XSPF and ATOM playlist (with mixed mediatypes and advertisement possibilities), a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript/actionscript API.</p>
<p><a href="http://www.longtailvideo.com/players/jw-flv-player/">http://www.longtailvideo.com/players/jw-flv-player/</a></p>',
'jw_rotator_install' => '<p>The JW Image Rotator enables you to show a couple of photos in sequence, with fluid transitions between them. 
It supports rotation of an RSS, XSPF or ATOM playlist with JPG, GIF and PNG images, a wide range of flashvars (settings) for tweaking both behavior and appearance and an extensive, documented javascript and actionscript API.</p>
<p><a href="http://www.longtailvideo.com/players/jw-image-rotator/">http://www.longtailvideo.com/players/jw-image-rotator/</a></p>',
'jw_media_player_install' => '<p>Pour installer JW Media Player, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/mediaplayer-3-16.zip">ici</a> et d&eacute;compressez un répertoire du m&ecirc;me nom que l\'archive dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',
'jw_rotator_install' => '<NEW><p>Pour installer JW Image Rotator, t&eacute;l&eacute;chargez le <a href="http://www.jeroenwijering.com/upload/jw_image_rotator.zip">ici</a> et d&eacute;compressez le tel quel dans un dossier lib/ &agrave; la racine du site (en pr&eacute;vision de la verion 2.0 de SPIP.</p>',
'js_necessaire' => 'D&#233;sol&#233;, mais le javascript est n&#233;ecessaire dans la version actuelle. Merci de le r&#233;activer pour afficher le contenu multimedia ',
);
?>
