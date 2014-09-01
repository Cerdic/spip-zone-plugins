<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'boite_info' =>'Recopy one of these shortcuts and insert it inside the box "Text", where you wish to locate the slideshow in your article.<br /><br />Consult the inline help to know other parameters.<br /><br />',
	'boite_info_explication' => 'You can also combine these parameters.',
	'boite_info_explication_docs' =>'You can choose documents by appointing them as this:',
	'boite_info_exemple_docs' => '<code>&lt;articleN|cycle<strong>|docs=AA,BB,CC</strong>&gt;</code>',
	'boite_info_explication_docs_2' => '<small>where AA, BB and CC are identifiers of the images.</small>',
	'boite_info_explication_largeur' =>'You can choose the maximum width of the slide show, for example:',
	'boite_info_exemple_largeur' => '<code>&lt;articleN|cycle<strong>|largeurmax=200</strong>&gt;</code>',
	'boite_info_explication_float' =>'You can choose to place the slide show to the left or to the right, as a label:',
	'boite_info_exemple_float' => '<code>&lt;articleN|cycle<strong>|left</strong>&gt;</code> or <code>&lt;articleN|cycle<strong>|right</strong>&gt;</code>',
	'boite_info_titre' => 'Slideshow',

	// C
	'configurer_titre' => 'Cycle2 Configuration',

	// E
	'erreur_config_creer_preview' => 'Caution: the generation of miniatures of the images is currently inactive, please activate it in the <a href=".?exec=configurer_avancees">advanced functions</a> of the site configuration !',
	'erreur_config_image_process' => 'Caution: Method of thumbnails creation was not selected, please select of them one din the <a href=".?exec=configurer_avancees">advanced functions</a> of the site configuration !',
	'explication_afficher_aide'=>'Display the help box in the left column on the edition pages of articles',
	'explication_fx' => 'See exemples :  : <a href="http://jquery.malsup.com/cycle2/" target="_blank">Cycle2 Plugin</a>. <br /><code>&lt;articleN|cycle<strong>|fx=scrollHorz</strong>&gt;</code>',
	'explication_caption' => 'Show a legend for every image. Target a html block by its class or its identifier css, either by default "<strong>.cycle-caption</strong>".<br /><code>&lt;articleN|cycle<strong>|caption=.cycle-caption</strong>&gt;</code>',
	'explication_captiontemplate' => 'Space to have the discount/total number,"<strong>{{alt}}</strong>" to have the titles of the images in legend, or still for example "<strong>Slide {{slideNum}} : {{alt}}</strong>"". <a href="http://jquery.malsup.com/cycle2/demo/caption.php">See examples.</a><br /><code>&lt;articleN|cycle<strong>|captiontemplate={{alt}}</strong>&gt;</code>',
	'explication_delay'=>'Waiting time before the departure of the slide show (in milliseconds).<br /><code>&lt;articleN|cycle<strong>|delay=200</strong>&gt;</code>',
	'explication_largeurmax' => 'All the images will be cut in width in this value, in pixels. Slideshows will be in proportional size, adapting themselves to the width defined by the interface of the site, but in the limit defined here.<br /><code>&lt;articleN|cycle<strong>|largeurmax=150</strong>&gt;</code>',
	'explication_hauteurmax' => 'By leaving the empty field, the images will always be for the possible maximum width. Otherwise all the images will be cut in height in this value, in pixels. If the width of image is not enough large, the configured background color will be visible. The value of the background color "transparent" will return on the other hand a white background.<br /><code>&lt;articleN|cycle<strong>|hauteurmax=450</strong>&gt;</code>',
	
	'explication_autoheight' => 'Choose here the way that determine the height of the slide show. By default (the field empty), the height of the first image serves as value, " calc " will use the height of the highest image, and "container" will adjust the height of the slide-show as high as the current image. It is possible to force the ratio height / width with a double value, for example "600:400". The value " false " will prevent the script from managing the height automatically.<br /><code>&lt;articleN|cycle<strong>|autoheight=600:400</strong>&gt;</code> or <code>&lt;articleN|cycle<strong>|autoheight=calc</strong>&gt;</code>',
	
	'explication_timeout' => 'Temps d\'affichage pour chaque image (en millisecondes). Choisir "0" pour faire un diaporama manuel (cf les boutons "précédent" et "suivant" pour activer le défilement manuel).<br /><code>&lt;articleN|cycle<strong>|timeout=4000</strong>&gt;</code>',
	
	'explication_speed' => 'Temps de transition entre chaque image (en millisecondes).<br /><code>&lt;articleN|cycle<strong>|speed=1000</strong>&gt;</code>',
	
	'explication_backgroundcolor' => 'Une valeur de couleur hexadécimale avec le "#", ex "#C5E41C". La valeur "transparent" rétabli la transparence.',
	
	'explication_palette' => 'Avec le plugin Palette, commencez par taper le "#" avant de choisir la couleur.<br /><code>&lt;articleN|cycle<strong>|backgroundcolor=#C5E41C</strong>&gt;</code>',
	
	
	'explication_tilevertical' => 'Jouer les effets de Glissement par bandes (tileSlide et tileBlind) horizontalement.<br /><code>&lt;articleN|cycle<strong>|tilevertical=false</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|tilevertical=true</strong>&gt;</code>',
	'explication_next' => 'Cibler un bloc html par sa classe ou son identifiant. Par défaut "<strong>.cycle-next</strong>" place une flèche à droite au survol de l\'image.<br /><code>&lt;articleN|cycle<strong>|next=.cycle-next</strong>&gt;</code>',
	'explication_prev' => 'Cibler un bloc html par sa classe ou son identifiant. Par défaut "<strong>.cycle-prev</strong>" place une flèche gauche au survol de l\'image.<br /><code>&lt;articleN|cycle<strong>|prev=.cycle-prev</strong>&gt;</code>',
	'explication_pauseonhover' => '<code>&lt;articleN|cycle<strong>|pauseonhover=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|pauseonhover=false</strong>&gt;</code>',
	'explication_pauseonhovercontent' => 'Laisser vide pour ne rien afficher.<br /> <code>&lt;articleN|cycle<strong>|pauseonhovercontent=pause</strong>&gt;</code>',
	'explication_allowwrap' => 'À propos des boutons "précédent" et "suivant" : à la fin du diaporama, ne pas revenir au début, ou bien au début, ne pas suivre à la fin. N\'empêche pas le diaporama automatique de tourner en continu.<br /><code>&lt;articleN|cycle<strong>|allowwrap=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|allowwrap=false</strong>&gt;</code>',
	'explication_reverse' => 'Joue le diaporama à l\'envers.<br /><code>&lt;articleN|cycle<strong>|reverse=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|reverse=false</strong>&gt;</code>',
	'explication_random' => '<code>&lt;articleN|cycle<strong>|random=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|random=false</strong>&gt;</code>',
	'explication_paused' => '<code>&lt;articleN|cycle<strong>|paused=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|paused=false</strong>&gt;</code>',
	'explication_pager' => 'Cibler un bloc html qui contiendra la pagination en nommant sa classe ou son identifiant css. Par défaut : "<strong>.cycle-pager</strong>". <code>&lt;articleN|cycle<strong>|pager=.cycle-pager</strong>&gt;</code>',
	'explication_mediabox' => 'Ouvre l\'image d\'origine suivant <a href=".?exec=configurer_mediabox">vos paramétrages de la mediabox</a>.',
	'explication_tooltip' => 'Au survol, affiche une infobulle contenant titre et descriptif de l’image. Utilisation du plugin Tooltip de jQuery.',
	'explication_tooltip_carac' => 'Afficher les caractéristiques de l\'image originale dans l\'infobulle : largeur, hauteur et poids.',
	'explication_carouselvisible' => 'Nombre de diapositives affichées simultanément (carousel-visible).<br /><code><strong>|carouselvisible=3</strong></code>',
	'explication_carouseloffset' => 'Décalage (en pixels) de la première diapositive (carousel-offset).<br /><code><strong>|carouseloffset=20</strong></code>',
	'explication_carouselslidedimension' => 'Largeur (carousel horizontal) ou hauteur (carousel vertical) d\'une diapositive (carousel-slide-dimension).<br /><code><strong>|carouselslidedimension=130</strong></code>',
	'explication_carouselvertical' => '(carousel-vertical)<br /><code><strong>|carouselvertical=true</strong></code> ou <code><strong>|carouselvertical=false</strong></code>',
	'explication_carouselfluid' => 'Adapter le carousel à la mise en page, seulement si horizontal (carousel-fluid).<br /><code><strong>|carouselfluid=true</strong></code> ou <code><strong>|carouselfluid=false</strong></code>',
	'explication_option_carousel' => 'La taille des images doit être réglée avec <a href="#largeurmax">le paramètre "largeurmax"</a> et <a href="#hauteurmax">le paramètre "hauteurmax"</a> si besoin.',
	'explication_overlay' => 'Un calque noir/transparent sur le bas de l\'image pour afficher une légende avec Titre et Description de l\'image.<br /><code>&lt;articleN|cycle<strong>|overlay=.cycle-overlay</strong>&gt;</code>',
	'explication_overlaytemplate' => 'Par défaut le titre et la description de l\'image, vous pouvez personnaliserle contenu suivant <a href="http://jquery.malsup.com/cycle2/demo/overlay.php">les exemples.</a>',
	'explication_sync' => 'L\'arrivée d\'une image est simultanée avec le départ de la précédente.<br /><code>&lt;articleN|cycle<strong>|sync=true</strong>&gt;</code> ou <code>&lt;articleN|cycle<strong>|sync=false</strong>&gt;</code>',
	
	'explication_backgroundcolor' => 'Type the background color in hexa format or with the palette if the Palette Plugin is available. To force transparent background, type "transparent" (in which case, the final images will be with in png format)',
	'explication_pauseonhover'=>'<code>&lt;articleN|cycle<strong>|pauseonhover=true</strong>&gt;</code> or <code>&lt;articleN|cycle<strong>|pauseonhover=false</strong>&gt;</code>',
	'explication_random'=>'<code>&lt;articleN|cycle<strong>|random=true</strong>&gt;</code> or <code>&lt;articleN|cycle<strong>|random=false</strong>&gt;</code>',	
	'explication_speed'=>'speed of the transition in milliseconds',
	'explication_timeout'=>'Milliseconds between slide transitions (0 to disable auto advance)',
	'explication_tooltip'=>'On hover, display a tooltip with image title and description. Use the jQuery tooltip plugin',
	'explication_tooltip_carac'=>'Display the characteristics of the original image in the tooltip: width, heigth and size',

	// L
	'label_afficher_aide' => 'Display the help box',
	'label_fx' => 'Effect',
	'label_backgroundcolor' => 'Background color',	
	'label_mediabox' => 'Mediabox',
	'label_pauseonhover' => 'Pause on hover',
	'label_random' => 'Random slideshow',
	'label_speed' => 'speed of the transition',
	'label_next' => 'Button <strong>"next"</strong>',
	'label_prev' => 'Button <strong>"previous"</strong>',
	'label_timeout' => 'Display time',
	'label_tooltip' => 'Display tooltips',
	'label_tooltip_carac' => 'Characteristics of the original image',
	'legend_parametres_suplementaires' => 'Other parameters',
	'legend_tooltip_box' => 'Tooltip & mediabox Parameters',

	// N
	'noisette_alea_description' => 'Display a random slideshow',
	'noisette_alea_nom_noisette' => 'Random slideshow',
	'noisette_description' => 'Display a slideshow with the images of an article',
	'noisette_duree' => 'Duration (ms)&nbsp;:',
	'noisette_fx' => 'Effect&nbsp;:',
	'noisette_hauteur' => 'Height (px)&nbsp;:',
	'noisette_id_sjcycle' => 'Number of the article with the images',
	'noisette_label_afficher_nom_site' => 'View the site name under the logo&nbsp;:',
	'noisette_label_afficher_titre_menu' => 'Display title&nbsp;:',
	'noisette_largeur' => 'Width (px)&nbsp;:',
	'noisette_nom_noisette' => 'Slideshow',
	'noisette_nb' => 'Number of images&nbsp;:',
	'noisette_sites_description' => 'Displays a slideshow of the logos of registered web sites',
	'noisette_sites_nom_noisette' => 'Websites Slideshow',
	'noisette_titre_alea_defaut' => 'Randomly',
	'noisette_titre_noisette' => 'Title&nbsp;:',
	'noisette_titre_sites_defaut' => 'Links',
	'non' => 'no',
	
	// O
	'oui' => 'yes',
	
	// P

	// T
	'titre_menu' => 'jQuery Cycle',
);