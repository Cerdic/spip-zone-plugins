<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'boite_info' =>'Dans vos textes, ce raccourci insère un diaporama de toutes les images liées à cet article :',
	'boite_info_explication' => 'Vous pouvez aussi combiner ces paramètres.',
	'boite_info_explication_docs' =>'Vous pouvez choisir les documents en les désignant comme ceci :',
	'boite_info_exemple_docs' => '&lt;sjcycleXX<strong>|docs=AA,BB,CC</strong>&gt;',
	'boite_info_explication_docs_2' => '<small>où AA,BB et CC sont les n° identifiants des images.</small>',
	'boite_info_explication_largeur' =>'Vous pouvez choisir la largeur maximum du diaporama, par exemple :',
	'boite_info_exemple_largeur' => '&lt;sjcycleXX<strong>|largeurmax=200&gt;</strong>',
	'boite_info_explication_float' =>'Vous pouvez choisir de placer le diaporama à gauche ou à droite, comme une vignette :',
	'boite_info_exemple_float' => '&lt;sjcycleXX<strong>|left</strong>&gt; ou &lt;sjcycleXX<strong>|right</strong>&gt;',
	'boite_info_titre' =>'Diaporama',

	// C
	'configurer_titre' => 'Configurer Cycle2',
	
	// E
	'erreur_config_creer_preview' => 'Attention : la génération de miniatures des images est actuellement inactive, veuillez  l\'activer dans les <a href=".?exec=configurer_avancees">fonctions avancées</a> de la configuration du site !',
	'erreur_config_image_process' => 'Attention : La méthode de fabrication des vignettes n\'a pas été choisie, veuillez  en sélectionner une dans les <a href=".?exec=configurer_avancees">fonctions avancées</a> de la configuration du site !',
	'explication_caption' => 'Afficher une légende pour chaque image. Cibler un bloc html par sa classe ou son identifiant css, ou bien par défaut "<strong>.cycle-caption</strong>".<br />&lt;sjcycleXX<strong>|caption=.cycle-caption</strong>&gt;',
	'explication_captiontemplate' => 'Vide pour avoir le décompte/le nombre total, "<strong>{{alt}}</strong>" pour avoir les titres des images en légende, ou encore par exemple "<strong>Diapositive {{slideNum}} : {{alt}}</strong>"".<br />&lt;sjcycleXX<strong>|captiontemplate={{alt}}</strong>&gt;',
	'explication_largeurmax' => 'Toutes les images seront retaillées en largeur à cette valeur, en pixels. Les diaporamas étant en taille proportionnelle, ils s\'adapteront à la largeur définie par l\'interface du site, mais dans la limite définie ici.<br />&lt;sjcycleXX<strong>|largeurmax=150</strong>&gt;',
	'explication_timeout' => 'Temps d\'affichage pour chaque image (en millisecondes).<br />&lt;sjcycleXX<strong>|timeout=4000</strong>&gt;',
	'explication_speed' => 'Temps de transition entre chaque image (en millisecondes).<br />&lt;sjcycleXX<strong>|speed=1000</strong>&gt;',
	'explication_backgroundcolor' => 'Une valeur de couleur héxadécimale avec le "#", ex "#C5E41C". La valeur "transparent" rétabli la transparence.',
	'explication_palette' => 'Avec le plugin Palette, commencez par taper le "#" avant de choisir la couleur.<br />&lt;sjcycleXX<strong>|backgroundcolor=#C5E41C</strong>&gt;',
	'explication_fx' => '&lt;sjcycleXX<strong>|fx=scrollHorz</strong>&gt;',
	'explication_next' => 'Cibler un bloc html par sa classe ou son identifiant. Par défaut "<strong>.cycle-next</strong>" place une flèche à droite au survol de l\'image.<br />&lt;sjcycleXX<strong>|next=.cycle-next</strong>&gt;',
	'explication_prev' => 'Cibler un bloc html par sa classe ou son identifiant. Par défaut "<strong>.cycle-prev</strong>" place une flèche gauche au survol de l\'image.<br />&lt;sjcycleXX<strong>|prev=.cycle-prev</strong>&gt;',
	'explication_pauseonhover' => '&lt;sjcycleXX<strong>|pauseonhover=true</strong>&gt; ou &lt;sjcycleXX<strong>|pauseonhover=false</strong>&gt;',
	'explication_random' => '&lt;sjcycleXX<strong>|random=true</strong>&gt; ou &lt;sjcycleXX<strong>|random=false</strong>&gt;',
	'explication_paused' => '&lt;sjcycleXX<strong>|paused=true</strong>&gt; ou &lt;sjcycleXX<strong>|paused=false</strong>&gt;',
	'explication_pager' => 'Cibler un bloc html qui contiendra la pagination en nommant sa classe ou son identifiant css. Par défaut : "<strong>.cycle-pager</strong>". &lt;sjcycleXX<strong>|pager=.cycle-pager</strong>&gt;',

	// L
	'label_afficher_aide' => 'Afficher un bloc d\'aide à la rédaction sur la page d\'édition d\'un article',
	'label_afficher_aide_oui' => 'oui',
	'label_backgroundcolor' => 'Couleur de fond',
	'label_caption' => 'Légende',
	'label_captiontemplate' => 'Format de la légende',
	'label_largeurmax' => 'Largeur maximum',
	'label_timeout' => 'Affichage',
	'label_speed' => 'Transition',
	'label_pauseonhover' => 'Pause au survol',
	'label_pauseonhover_true' => 'oui',
	'label_random' => 'Ordre aléatoire',
	'label_random_true' => 'oui',
	'label_fx' => 'Effet de transition',
	'label_fx_fade' => 'Fondu (fade)',
	'label_fx_fadeout' => 'Fondu simultané (fadeout)',
	'label_fx_scrollHorz' => 'Glissement horizontal (scrollHorz)',
	'label_fx_scrollVert' => 'Glissement vertical (scrollVert)',
	'label_fx_flipHorz' => 'Retournement horizontal (flipHorz)',
	'label_fx_flipVert' => 'Retournement vertical (flipVert)',
	'label_fx_shuffle' => 'Remplacement - paquet de carte (shuffle)',
	'label_fx_tileSlide' => 'Glissement par bandes en longueur (tileSlide)',
	'label_fx_tileBlind' => 'Glissement par bandes en largeur (tileBlind)',
	'label_fx_none' => 'Aucun (none)',
	'label_next' => 'Bouton "suivant"',
	'label_prev' => 'Bouton "précédent"',
	'label_paused' => 'Départ arrêté',
	'label_paused_true' => 'oui',
	'label_pager' => 'Pagination',
	'legend_parametres_suplementaires' => 'Paramètres supplémentaires',
	
	// P
	'parametres_diaporama' => 'Paramétrage général des diaporamas du site, peut être corrigé à la rédaction lors de l\'inclusion d\'un diaporama.<br /><small>&lt;sjcycleXX<strong>|right|largeurmax=250</strong>&gt;</small>',
	'pre' => 'précédent',
	
	// T
	'titre_menu' => 'Cycle2',
);
?>