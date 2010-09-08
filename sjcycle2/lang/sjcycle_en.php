<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'boite_info' =>'Recopy one of these shortcuts and insert it inside the box "Text", where you wish to locate the slideshow in your article.<br /><br />
	Consult the inline help to know other parameters.',

	// C
	'cfg_boite_sjcycle' => 'Spip jQuery Cycle plugin configuration.<br /><br />
	Consult the inline help to know other parameters.',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>jQuery Cycle plugin for SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',

	// E
	'erreur_config_creer_preview' => 'Caution: the generation of miniatures of the images is currently inactive, please activate it in the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',
	'erreur_config_image_process' => 'Caution: Method of thumbnails creation was not selected, please select of them one din the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',
	'explication_fancy'=>'On clic, display the original image in a jQuery fancybox. Need the <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox plugin</a>',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	'explication_img_background' => 'Type the background color in hexa format or with the palette if the Palette Plugin is available. To force transparent background, type "transparent" (in which case, the final images will be with in png format)',
	'explication_img_recadre'=>'This treatment is carried out on the images according to<br />
	- their position in the slideshow, combination of the values of alignment "left/center/right" and "top/center/bottom" (ex "left center")<br />
	- their desired final size (width height)<br /><br />
	To know more, consult the explanation on spip.net of the functions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'explication_mediabox'=>'On clic, display the original image in a jQuery mediabox. Need the <a href="http://www.spip-contrib.net/MediaBox" target="_blank">Mediabox plugin</a>',
	'explication_pause'=>'Enable pause of the slideshow on hover',
	'explication_random'=>'true for random, false for sequence (not applicable to shuffle fx) ',	
	'explication_speed'=>'speed of the transition in milliseconds',
	'explication_sync'=>'Define if in/out transitions should occur simultaneously',
	'explication_timeout'=>'Milliseconds between slide transitions (0 to disable auto advance)',
	'explication_tooltip'=>'On hover, display a tooltip with image title and description. Use the jQuery tooltip plugin',
	'explication_tooltip_carac'=>'Display the characteristics of the original image in the tooltip: width, heigth and size',

	// I
	'img_recadre' => 'Images are automatically resized and cropped in order to preserve the page layout during the insertion of the slideshow within the text.',

	// L
	'label_div_background' => 'Border color',
	'label_div_class' => 'Class name css',
	'label_div_margin' => 'External margin',
	'label_fancy' => 'FancyBox',
	'label_fx' => 'Effect',
	'label_img_background' => 'Background color',	
	'label_img_bordure' => 'Border width',
	'label_img_height' => 'Height',
	'label_img_position' => 'Positioning',
	'label_img_width' => 'Width',
	'label_mediabox' => 'Mediabox',
	'label_pause' => 'Pause on hover',
	'label_random' => 'Random slideshow',
	'label_speed' => 'speed of the transition',
	'label_sync' => 'Synchronisation',
	'label_timeout' => 'Display time',
	'label_tooltip' => 'Display tooltips',
	'label_tooltip_carac' => 'Characteristics of the original image',
	'legend_cssparams' => 'SjCycle styles parameters',
	'legend_imgparams' => 'Images treatments SjCycle',
	'legend_jsparams' => 'jQuery Cycle javascript parameters',
	'legend_tooltipfancy' => 'Tooltip & fancybox Parameters',

	// N
	'nouvelle_fenetre' =>'Click to consult the help in a new window',

	// V
	'valeur_hex' => 'Hexadecimal value or "transparent"',
	'valeur_px' => 'Value in pixels',
);
?>
