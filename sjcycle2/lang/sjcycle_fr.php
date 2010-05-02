<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
//CFG/CVT FORM PARAMS
	'cfg_boite_sjcycle' => 'Configuration du plugin Spip jQuery Cycle',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>Plugin jQuery Cycle pour SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',

//fieldset <:sjcycle:sjcycle_tooltipfancy_legend:>
	'sjcycle_tooltipfancy_legend' => 'Param&egrave;tres tooltip & fancybox',

	'sjcycle_tooltip' => 'Afficher les infobulles',
	'explication_tooltip'=>'Au survol, affiche une infobulle contenant titre et descriptif de l’image. Utilisation du plugin tooltip de jquery',

	'sjcycle_tooltip_carac' => 'Caractéristiques de l\'originale',
	'explication_tooltip_carac'=>'Afficher les caractéristiques de l\'image originale dans l\'infobulle : largeur, hauteur et poids',

	'sjcycle_fancy' => 'FancyBox',
	'explication_fancy'=>'Au clic, afficher l\'image originale dans une fancybox jquery. Nécessite le plugin <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox</a>',

	
//fieldset <:sjcycle:sjcycle_jsparams_legend:>
	'sjcycle_jsparams_legend' => 'Param&egrave;tres javascript jQuery Cycle',
	
	'sjcycle_fx' => 'Effet',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	
	'sjcycle_sync' => 'Synchronisation',
	'explication_sync'=>'D&eacute;finit si les transitions entre images se produisent simultanément',
	
	'sjcycle_speed' => 'Vitesse de transition',
	'explication_speed'=>'Vitesse de la transition en millisecondes',
	
	'sjcycle_timeout' => 'Temps d\'affichage',
	'explication_timeout'=>'Exprime le temps en millisecondes entre 2 transitions (0 désactive le d&eacute;filement automatique)',
	
	'sjcycle_pause' => 'Pause au survol',
	'explication_pause'=>'Active la mise en pause du diaporama automatique au survol de la souris',
	
	'sjcycle_random' => 'D&eacute;filement al&eacute;atoire',
	'explication_random'=>'Active le d&eacute;filement al&eacute;atoire',
	
	'sjcycle_prevnext' => 'Pr&eacute;c./Suiv.',
	'explication_prevnext'=>'The prev and next options are used to identify the elements which should be the triggers for prev/next transitions. When used in conjuction with timeout = 0 the effect is a manual slideshow. The values for prev and next can be a DOM element or any valid jQuery selection string. 
		next:   "#s1", or next:   "#next2",  prev:   "#prev2"',
	
	'sjcycle_pager' => 'Pagination',
	'explication_pager'=>'_AIDE : The pager option is used for creating full navigation controls. This option instructs the plugin to create navigation elements, one for each slide, and add them to the container identified by the value of the pager option. 
		pager:  "#nav"
		css :
		#nav a { border: 1px solid #ccc; background: #fc0; text-decoration: none; margin: 0 5px; padding: 3px 5px;  }
#nav a.activeSlide { background: #ea0 }
#nav a:focus { outline: none; }',
	
//fieldset <:sjcycle:sjcycle_cssparams_legend:>	
	'sjcycle_cssparams_legend' => 'Param&egrave;tres styles sjcycle',
	'sjcycle_class' => 'Nom de classe css',
	'sjcycle_margin' => 'Marge externe',
	'sjcycle_img_margin' => 'Bordure',
	'sjcycle_background' => 'Couleur de bordure',
	
//fieldset <:sjcycle:sjcycle_imgparams_legend:>	
	'sjcycle_imgparams_legend' => 'Traitements images SjCycle',
	'sjcycle_img_recadre' => 'Les images sont redimensionnées et recadr&eacute;es automatiquement afin de pr&eacute;server la mise en page lors de l\'insertion du diaporama au sein du texte.',
	'explication_img_recadre'=>'Ce traitement se r&eacute;alise sur les images suivant<br />
	- leur position dans le diaporama, combinaison des valeurs d\'alignement "left/center/right" et "top/center/bottom" (ex "left center")<br />
	- leur taille finale souhaitée (largeur hauteur)<br /><br />
	Pour en savoir plus, consulter l\'explication sur spip.net des fonctions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'sjcycle_img_position' => 'Positionnement',
	'sjcycle_img_width' => 'Largeur',
	'sjcycle_img_height' => 'Hauteur',
	'sjcycle_img_background' => 'Couleur de fond',

//CFG/CVT FORM INFO
	'sjcycle_valeur_hex' => 'Valeur hexadecimale',
	'sjcycle_valeur_px' => 'Valeur en pixels',

//CFG/CVT ERRORS & MESSAGES
	'erreur_config_creer_preview' => 'Attention : la génération de miniatures des images est actuellement inactive, veuillez  l\'activer dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',
	'erreur_config_image_process' => 'Attention : Méthode de fabrication des vignettes n\'a pas été choisie, veuillez  en sélectionner une dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',
	
	'sjcycle_message_erreur' => 'Votre saisie contient des erreurs !',
	'sjcycle_champ_erreur' => 'La valeur du champ "@champ@" est incorrecte',
	'sjcycle_reinitialise' =>'Reconfiguration par défaut effectuée',

//Insertion du diapo
	'sjcycle_boite_info' =>'Recopiez l\'un de ces raccourcis et ins&eacute;rez-le &agrave; l’int&eacute;rieur de la case «&nbsp;Texte&nbsp;», l&agrave; o&ugrave; vous d&eacute;sirez situer le diaporama dans votre article.<br /><br />
		Cliquer sur <a href="../plugins/sjcycle/doc.php?art=@art@" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="<:sjcycle:sjcycle_nouvelle_fenetre:>" onclick="javascript:window.open(\'../plugins/sjcycle/doc.php?art=@art@\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="../prive/images/aide.gif" alt="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" class="aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" /></a> pour consulter l\'aide en ligne.',
		'sjcycle_nouvelle_fenetre' =>'Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre'
);
?>
