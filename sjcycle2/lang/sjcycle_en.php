<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
//CFG/CVT FORM PARAMS
	'cfg_boite_sjcycle' => 'Spip jQuery Cycle plugin configuration',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>jQuery Cycle plugin for SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',

//fieldset <:sjcycle:sjcycle_tooltipfancy_legend:>
	'sjcycle_tooltipfancy_legend' => 'Tooltip & fancybox Parameters',

	'sjcycle_tooltip' => 'Display tooltips',
	'explication_tooltip'=>'On hover, display a tooltip with image title and description. Use the jQuery tooltip plugin',

	'sjcycle_tooltip_carac' => 'Characteristics of the original image',
	'explication_tooltip_carac'=>'Display the characteristics of the original image in the tooltip: width, heigth and size',

	'sjcycle_fancy' => 'FancyBox',
	'explication_fancy'=>'On clic, display the original image in a jQuery fancybox. Need the <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox plugin</a>',

	
//fieldset <:sjcycle:sjcycle_jsparams_legend:>
	'sjcycle_jsparams_legend' => 'jQuery Cycle javascript parameters',
	
	'sjcycle_fx' => 'Effect',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	
	'sjcycle_sync' => 'Synchronisation',
	'explication_sync'=>'Define if in/out transitions should occur simultaneously',
	
	'sjcycle_speed' => 'speed of the transition',
	'explication_speed'=>'speed of the transition in milliseconds',
	
	'sjcycle_timeout' => 'Display time',
	'explication_timeout'=>'Milliseconds between slide transitions (0 to disable auto advance)',
	
	'sjcycle_pause' => 'Pause on hover',
	'explication_pause'=>'Enable pause of the slideshow on hover',
	
	'sjcycle_random' => 'Random slideshow',
	'explication_random'=>'true for random, false for sequence (not applicable to shuffle fx) ',
	
	'sjcycle_prevnext' => 'Prev./Next',
	'explication_prevnext'=>'The prev and next options are used to identify the elements which should be the triggers for prev/next transitions. When used in conjuction with timeout = 0 the effect is a manual slideshow. The values for prev and next can be a DOM element or any valid jQuery selection string. 
		next:   "#s1", or next:   "#next2",  prev:   "#prev2"',
	
	'sjcycle_pager' => 'Pager',
	'explication_pager'=>'_AIDE : The pager option is used for creating full navigation controls. This option instructs the plugin to create navigation elements, one for each slide, and add them to the container identified by the value of the pager option. 
		pager:  "#nav"
		css :
		#nav a { border: 1px solid #ccc; background: #fc0; text-decoration: none; margin: 0 5px; padding: 3px 5px;  }
#nav a.activeSlide { background: #ea0 }
#nav a:focus { outline: none; }',
	
//fieldset <:sjcycle:sjcycle_cssparams_legend:>	
	'sjcycle_cssparams_legend' => 'sjcycle styles parameters',
	'sjcycle_class' => 'Class name css',
	'sjcycle_margin' => 'external Margin',
	'sjcycle_img_margin' => 'Border',
	'sjcycle_background' => 'Border color',
	
//fieldset <:sjcycle:sjcycle_imgparams_legend:>	
	'sjcycle_imgparams_legend' => 'Images treatments SjCycle',
	'sjcycle_img_recadre' => 'Images are automatically resized and cropped in order to preserve the page layout during the insertion of the slideshow within the text.',
	'explication_img_recadre'=>'This treatment is carried out on the images according to<br />
	- their position in the slideshow, combination of the values of alignment "left/center/right" and "top/center/bottom" (ex "left center")<br />
	- their desired final size (width height)<br /><br />
	To know more, consult the explanation on spip.net of the functions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'sjcycle_img_position' => 'Positioning',
	'sjcycle_img_width' => 'Width',
	'sjcycle_img_height' => 'Height',
	'sjcycle_img_background' => 'Background color',

//CFG/CVT FORM INFO
	'sjcycle_valeur_hex' => 'Hexadecimal value or "transparent"',
	'sjcycle_valeur_px' => 'Value in pixels',

//CFG/CVT ERRORS & MESSAGES
	'erreur_config_creer_preview' => 'Caution: the generation of miniatures of the images is currently inactive, please activate it in the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',
	'erreur_config_image_process' => 'Caution: Method of thumbnails creation was not selected, please select of them one din the <a href=".?exec=config_fonctions">advanced functions</a> of the site configuration !',
	
	'sjcycle_message_erreur' => 'Your writing contains errors !',
	'sjcycle_champ_erreur' => 'The value of field "@champ@" is incorrect',
	'sjcycle_reinitialise' =>'Default configuration done',

//Insertion du diapo
	'sjcycle_boite_info' =>'Recopy one of these shortcuts and insert it inside the box "Text", where you wish to locate the slideshow in your article.<br /><br />
		Click on <a href="../plugins/sjcycle/doc.php?art=@art@" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="<:sjcycle:sjcycle_nouvelle_fenetre:>" onclick="javascript:window.open(\'../plugins/sjcycle/doc.php?art=@art@\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="../prive/images/aide.gif" alt="Click to consult the help in a new window" class="aide" title="Click to consult the help and the different parameters in a new window" /></a> to consult the inline help.',
		'sjcycle_nouvelle_fenetre' =>'Click to consult the help in a new window'
);
?>
