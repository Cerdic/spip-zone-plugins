<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
//CFG/CVT FORM PARAMS
	'cfg_boite_sjcycle' => 'Spip jQuery Cycle plugin configuration.<br /><br />
		Click on <a href="'._DIR_PLUGIN_SJCYCLE.'doc.php" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Click to consult the help in a new window" onclick="javascript:window.open(\''._DIR_PLUGIN_SJCYCLE.'doc.php\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="'._DIR_IMG_PACK.'aide.gif" alt="Click to consult the help in a new window" class="aide" title="Click to consult the help and the different parameters in a new window" /></a> to consult the inline help and other parameters.',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>jQuery Cycle plugin for SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',

//fieldset <:sjcycle:tooltipfancy_legend:>
	'tooltipfancy_legend' => 'Tooltip & fancybox Parameters',

	'tooltip' => 'Display tooltips',
	'explication_tooltip'=>'On hover, display a tooltip with image title and description. Use the jQuery tooltip plugin',

	'tooltip_carac' => 'Characteristics of the original image',
	'explication_tooltip_carac'=>'Display the characteristics of the original image in the tooltip: width, heigth and size',

	'fancy' => 'FancyBox',
	'explication_fancy'=>'On clic, display the original image in a jQuery fancybox. Need the <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox plugin</a>',

	
//fieldset <:sjcycle:jsparams_legend:>
	'jsparams_legend' => 'jQuery Cycle javascript parameters',
	
	'fx' => 'Effect',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	
	'sync' => 'Synchronisation',
	'explication_sync'=>'Define if in/out transitions should occur simultaneously',
	
	'speed' => 'speed of the transition',
	'explication_speed'=>'speed of the transition in milliseconds',
	
	'timeout' => 'Display time',
	'explication_timeout'=>'Milliseconds between slide transitions (0 to disable auto advance)',
	
	'pause' => 'Pause on hover',
	'explication_pause'=>'Enable pause of the slideshow on hover',
	
	'random' => 'Random slideshow',
	'explication_random'=>'true for random, false for sequence (not applicable to shuffle fx) ',
	
//fieldset <:sjcycle:cssparams_legend:>	
	'cssparams_legend' => 'SjCycle styles parameters',
	'div_class' => 'Class name css',
	'div_margin' => 'external Margin',
	'img_bordure' => 'Border',
	'div_background' => 'Border color',
	
//fieldset <:sjcycle:imgparams_legend:>	
	'imgparams_legend' => 'Images treatments SjCycle',
	'img_recadre' => 'Images are automatically resized and cropped in order to preserve the page layout during the insertion of the slideshow within the text.',
	'explication_img_recadre'=>'This treatment is carried out on the images according to<br />
	- their position in the slideshow, combination of the values of alignment "left/center/right" and "top/center/bottom" (ex "left center")<br />
	- their desired final size (width height)<br /><br />
	To know more, consult the explanation on spip.net of the functions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'img_position' => 'Positioning',
	'img_width' => 'Width',
	'img_height' => 'Height',
	'img_background' => 'Background color',
	'explication_img_background' => 'Type the background color in hexa format or with the palette if the Palette Plugin is available. To force transparent background, type "transparent" (in which case, the final images will be with in png format)',

//CFG/CVT ERRORS & MESSAGES
	'erreur_config_creer_preview' => 'Caution: the generation of miniatures of the images is currently inactive, please activate it in the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',
	'erreur_config_image_process' => 'Caution: Method of thumbnails creation was not selected, please select of them one din the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',

//Insertion du diapo
	'boite_info' =>'Recopy one of these shortcuts and insert it inside the box "Text", where you wish to locate the slideshow in your article.<br /><br />
		Click on <a href="'._DIR_PLUGIN_SJCYCLE.'doc.php?art=@art@" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Click to consult the help in a new window" onclick="javascript:window.open(\''._DIR_PLUGIN_SJCYCLE.'doc.php?art=@art@\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="'._DIR_IMG_PACK.'aide.gif" alt="Click to consult the help in a new window" class="aide" title="Click to consult the help and the different parameters in a new window" /></a> to consult the inline help and other parameters.',
	'nouvelle_fenetre' =>'Click to consult the help in a new window'
);
?>
