<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'boite_info' =>'Recopiez l\'un de ces raccourcis et insérez-le à l’intérieur de la case « Texte », là où vous désirez situer le diaporama dans votre article.<br /><br />
		Consultez l\'aide pour connaître les paramètres disponibles.',

	// C
	'cfg_boite_sjcycle' => 'Configuration du plugin Spip jQuery Cycle.<br /><br />
		Consulter l\'aide en ligne pour connaître les autres paramètres disponibles localement dans un article.',
	'cfg_descr_sjcycle' => '<h4>sjcycle</h4>Plugin jQuery Cycle pour SPIP',
	'cfg_titre_sjcycle' => 'Spip jQuery Cycle Plugin',
	
	// E
	'erreur_config_creer_preview' => 'Attention : la génération de miniatures des images est actuellement inactive, veuillez  l\'activer dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',
	'erreur_config_image_process' => 'Attention : Méthode de fabrication des vignettes n\'a pas été choisie, veuillez  en sélectionner une dans les <a href=".?exec=config_fonctions">fonctions avancées</a> de la configuration du site !',
	'explication_afficher_aide'=>'Afficher la boite d\'aide dans la colonne de gauche des pages d\'édition des articles',
	'explication_fancy'=>'Au clic, afficher l\'image originale dans une fancybox jQuery. Nécessite le plugin <a href="http://www.spip-contrib.net/FancyBox,3020" target="_blank">FancyBox</a>',
	'explication_fx'=>'Consultez les exemples en ligne : <a href="http://malsup.com/jquery/cycle/browser.html" target="_blank">jQuery Cycle Plugin</a>',
	'explication_img_background' => 'Saisir la couleur de fond au format hexadecimal (#FF0000 par exemple) ou via la palette si le plugin Palette est installé. Pour un fond transparent, saisir "transparent" (auquel cas, les images finales seront au format png).',
	'explication_img_recadre'=>'Ce traitement se réalise sur les images suivant<br />
	- leur position dans le diaporama, combinaison des valeurs d\'alignement "left/center/right" et "top/center/bottom" (ex "left center")<br />
	- leur taille finale souhaitée (largeur hauteur)<br /><br />
	Pour en savoir plus, consulter l\'explication sur spip.net des fonctions<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_reduire" target="_blank">image_reduire</a>;<br />
	- <a href="http://www.spip.net/fr_article3327.html#image_recadre" target="_blank">image_recadre</a>.',
	'explication_mediabox'=>'Au clic, afficher l\'image originale dans une mediabox jQuery. Nécessite le plugin <a href="http://www.spip-contrib.net/MediaBox" target="_blank">Mediabox</a>',
	'explication_pause'=>'Active la mise en pause du diaporama automatique au survol de la souris',
	'explication_random'=>'Active le défilement aléatoire',
	'explication_speed'=>'Vitesse de la transition en millisecondes',
	'explication_sync'=>'Définit si les transitions entre images se produisent simultanément',
	'explication_timeout'=>'Exprime le temps en millisecondes entre 2 transitions (0 désactive le défilement automatique)',
	'explication_tooltip'=>'Au survol, affiche une infobulle contenant titre et descriptif de l’image. Utilisation du plugin tooltip de jquery',
	'explication_tooltip_carac'=>'Afficher les caractéristiques de l\'image originale dans l\'infobulle : largeur, hauteur et poids',

	// I
	'img_recadre' => 'Les images sont redimensionnées et recadrées automatiquement afin de préserver la mise en page lors de l\'insertion du diaporama au sein du texte.',

	// L
	'label_afficher_aide' => 'Afficher la boite d\'aide',
	'label_div_background' => 'Couleur de bordure',
	'label_div_class' => 'Nom de classe css',
	'label_div_margin' => 'Marge externe',
	'label_fancy' => 'FancyBox',
	'label_fx' => 'Effet',
	'label_img_background' => 'Couleur de fond',
	'label_img_bordure' => 'Largeur de la bordure',
	'label_img_height' => 'Hauteur',
	'label_img_position' => 'Positionnement',
	'label_img_width' => 'Largeur',
	'label_mediabox' => 'Mediabox',
	'label_pause' => 'Pause au survol',
	'label_random' => 'Défilement aléatoire',
	'label_speed' => 'Vitesse de transition',
	'label_sync' => 'Synchronisation',
	'label_timeout' => 'Temps d\'affichage',
	'label_tooltip' => 'Afficher les infobulles',
	'label_tooltip_carac' => 'Caractéristiques de l\'originale',
	'legend_autres' => 'Autres paramètres',
	'legend_cssparams' => 'Paramètres styles SjCycle',
	'legend_imgparams' => 'Traitements images SjCycle',
	'legend_jsparams' => 'Paramètres javascript jQuery Cycle',
	'legend_tooltipfancy' => 'Paramètres tooltip, fancybox et mediabox',	
	
	// N
	'noisette_alea_description' => 'Affiche un diaporama aléatoire jcycle des images du site',
	'noisette_alea_nom_noisette' => 'Diaporama aléatoire',
	'noisette_description' => 'Affiche un diaporama jcycle des images d\'un article',
	'noisette_duree' => 'Durée (ms) :',
	'noisette_fx' => 'Effet :',
	'noisette_hauteur' => 'Hauteur (px) :',
	'noisette_id_sjcycle' => 'Numéro de l\'article contenant les images',
	'noisette_label_afficher_nom_site' => 'Afficher le nom du site sous le logo :',
	'noisette_label_afficher_titre_menu' => 'Afficher le titre :',
	'noisette_largeur' => 'Largeur (px) :',
	'noisette_nb' => 'Nombre d\'images :',
	'noisette_nom_noisette' => 'Diaporama',
	'noisette_sites_description' => 'Affiche un diaporama des logos des sites enregistrés',
	'noisette_sites_nom_noisette' => 'Diaporama des sites',
	'noisette_titre_alea_defaut' => 'Au Hasard',
	'noisette_titre_noisette' => 'Titre :',
	'noisette_titre_sites_defaut' => 'Liens',

	// V
	'valeur_hex' => 'Valeur hexadecimale ou "transparent"',
	'valeur_px' => 'Valeur en pixels',
);
?>
