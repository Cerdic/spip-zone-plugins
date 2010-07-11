<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
//CFG/CVT FORM PARAMS
	'cfg_boite_sjcycle' => 'Configuration du plugin Spip jQuery Cycle.<br /><br />
		Cliquer sur <a href="'._DIR_PLUGIN_SJCYCLE.'doc.php" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" onclick="javascript:window.open(\''._DIR_PLUGIN_SJCYCLE.'doc.php\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="'._DIR_IMG_PACK.'aide.gif" alt="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" class="aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" /></a> pour consulter l\'aide en ligne et les autres param&egrave;tres disponibles localement dans un article.',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>Plugin jQuery Cycle pour SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',

//fieldset <:sjcycle:tooltipfancy_legend:>
	'tooltipfancy_legend' => 'Param&egrave;tres tooltip & fancybox',

	'tooltip' => 'Afficher les infobulles',
	'explication_tooltip'=>'Au survol, affiche une infobulle contenant titre et descriptif de l’image. Utilisation du plugin tooltip de jquery',

	'tooltip_carac' => 'Caractéristiques de l\'originale',
	'explication_tooltip_carac'=>'Afficher les caractéristiques de l\'image originale dans l\'infobulle : largeur, hauteur et poids',

	'fancy' => 'FancyBox',
	'explication_fancy'=>'Au clic, afficher l\'image originale dans une fancybox jquery. Nécessite le plugin <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox</a>',

	
//fieldset <:sjcycle:jsparams_legend:>
	'jsparams_legend' => 'Param&egrave;tres javascript jQuery Cycle',
	
	'fx' => 'Effet',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	
	'sync' => 'Synchronisation',
	'explication_sync'=>'D&eacute;finit si les transitions entre images se produisent simultanément',
	
	'speed' => 'Vitesse de transition',
	'explication_speed'=>'Vitesse de la transition en millisecondes',
	
	'timeout' => 'Temps d\'affichage',
	'explication_timeout'=>'Exprime le temps en millisecondes entre 2 transitions (0 désactive le d&eacute;filement automatique)',
	
	'pause' => 'Pause au survol',
	'explication_pause'=>'Active la mise en pause du diaporama automatique au survol de la souris',
	
	'random' => 'D&eacute;filement al&eacute;atoire',
	'explication_random'=>'Active le d&eacute;filement al&eacute;atoire',
	
//fieldset <:sjcycle:cssparams_legend:>	
	'cssparams_legend' => 'Param&egrave;tres styles SjCycle',
	'div_class' => 'Nom de classe css',
	'div_margin' => 'Marge externe',
	'img_bordure' => 'Largeur de la bordure',
	'div_background' => 'Couleur de bordure',
	
//fieldset <:sjcycle:imgparams_legend:>	
	'imgparams_legend' => 'Traitements images SjCycle',
	'img_recadre' => 'Les images sont redimensionnées et recadr&eacute;es automatiquement afin de pr&eacute;server la mise en page lors de l\'insertion du diaporama au sein du texte.',
	'explication_img_recadre'=>'Ce traitement se r&eacute;alise sur les images suivant<br />
	- leur position dans le diaporama, combinaison des valeurs d\'alignement "left/center/right" et "top/center/bottom" (ex "left center")<br />
	- leur taille finale souhaitée (largeur hauteur)<br /><br />
	Pour en savoir plus, consulter l\'explication sur spip.net des fonctions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'img_position' => 'Positionnement',
	'img_width' => 'Largeur',
	'img_height' => 'Hauteur',
	'img_background' => 'Couleur de fond',
	'explication_img_background' => 'Saisir la couleur de fond au format hexadecimal (#FF0000 par exemple) ou via la palette si le plugin Palette est installé. Pour un fond transparent, saisir "transparent" (auquel cas, les images finales seront au format png).',

//CFG/CVT FORM INFO
	'valeur_hex' => 'Valeur hexadecimale ou "transparent"',
	'valeur_px' => 'Valeur en pixels',

//CFG/CVT ERRORS & MESSAGES
	'erreur_config_creer_preview' => 'Attention : la génération de miniatures des images est actuellement inactive, veuillez  l\'activer dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',
	'erreur_config_image_process' => 'Attention : Méthode de fabrication des vignettes n\'a pas été choisie, veuillez  en sélectionner une dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',

//Insertion du diapo
	'boite_info' =>'Recopiez l\'un de ces raccourcis et ins&eacute;rez-le &agrave; l’int&eacute;rieur de la case «&nbsp;Texte&nbsp;», l&agrave; o&ugrave; vous d&eacute;sirez situer le diaporama dans votre article.<br /><br />
		Cliquer sur <a href="'._DIR_PLUGIN_SJCYCLE.'doc.php?art=@art@" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" onclick="javascript:window.open(\''._DIR_PLUGIN_SJCYCLE.'doc.php?art=@art@\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="'._DIR_IMG_PACK.'aide.gif" alt="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" class="aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" /></a> pour consulter l\'aide en ligne et les param&egrave;tres disponibles.',
	'nouvelle_fenetre' =>'Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre'
);
?>
