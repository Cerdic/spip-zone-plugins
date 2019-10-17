<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'champ_grille_lien_label' => 'Site web',
	'champ_grille_demo_label' => 'Page de démonstration',
	'champ_grille_documentation_label' => 'Documentation',
	'champ_grille_breakpoints_label' => 'Breakpoints',
	'champ_grille_nb_colonnes_label' => 'Nombre de colonnes',
	'champ_grille_mobile_first_label' => 'Mobile-first',
	'champ_grille_media_label' => 'Média',
	'champ_grille_media_min_label' => 'À partir de :',
	'champ_grille_media_max_label' => 'Jusqu’à :',
	//
	'champ_cfg_inclure_css_public_label' => 'CSS',
	'champ_cfg_inclure_css_public_label_case' => 'Charger la feuille de style sur le site public',
	'champ_cfg_inclure_css_public_explication' => 'Décochez cette option si votre thème inclus d’office la feuille de style correspondant à la grille CSS utilisée.',
	'champ_cfg_activer_container_label' => 'Conteneurs internes.',
	'champ_cfg_activer_container_label_case' => 'Activer la gestion des conteneurs internes.',
	'champ_cfg_activer_container_explication' => 'Chaque noisette peut optionnellement avoir un conteneur interne en vue de limiter la largeur de son contenu. Cette option n’est utile que si le bloc où elle se trouve n’est pas déjà limité en largeur par le thème CSS.',
	//
	'champ_fieldset_affichage_label' => 'Affichage',
	'champ_fieldset_container_label' => 'Largeur interne',
	'champ_fieldset_container_legend' => 'Agencement : largeur maximale',
	'champ_fieldset_container_explication' => 'Largeur maximale interne de la noisette.',
	'champ_fieldset_row_label' => 'Ligne',
	'champ_fieldset_row_legend' => 'Agencement : ligne',
	'champ_fieldset_row_explication' => 'Agencement des noisettes enfantes.',
	'champ_fieldset_column_label' => 'Colonne',
	'champ_fieldset_column_legend' => 'Agencement : colonne',
	'champ_fieldset_column_explication' => 'Agencement de la colonne.',
	//
	'champ_align_label' => 'Alignement',
	'champ_align_horizontal_label' => 'Alignement horizontal',
	'champ_align_horizontal_row_explication' => 'Alignement horizontal des noisettes enfantes.',
	'champ_align_horizontal_column_explication' => 'Alignement horizontal de cette noisette.',
	'champ_align_horizontal_column_label_case' => 'Centrer horizontalement',
	'champ_align_vertical_label' => 'Alignement vertical',
	'champ_align_vertical_row_explication' => 'Alignement vertical des noisettes enfantes.',
	'champ_align_vertical_column_explication' => 'Alignement vertical de cette noisette.',
	'champ_align_left' => 'Gauche',
	'champ_align_right' => 'Droite',
	'champ_align_center' => 'Centre',
	'champ_align_between' => 'Distribuer entre',
	'champ_align_around' => 'Distribuer autour',
	'champ_align_top' => 'Haut',
	'champ_align_middle' => 'Milieu',
	'champ_align_bottom' => 'Bas',
	//
	'champ_width_label' => 'Largeur',
	'champ_width_explication' => 'Largeur de cette colonne.',
	'champ_width_grow' => 'Extensible',
	'champ_width_grow_explication' => 'S’étend autant que possible.',
	'champ_width_shrink' => 'Rétrécible',
	'champ_width_shrink_explication' => 'Se rétrécit autant que possible.',
	'champ_width_full_label' => 'Pleine largeur',
	'champ_width_full_label_case' => 'Mettre en pleine largeur',
	'champ_width_full_explication' => 'Force la noisette à prendre toute la largeur de la page, quelquesoit son emplacement.',
	//
	'champ_order_label' => 'Ordre',
	'champ_order_explication' => 'Modifier l’ordre d’affichage.',
	'champ_order_explication_expose' => 'En surligné = ordre initial.',
	//
	'champ_gutter_label' => 'Gouttière',
	'champ_gutterless_label_case' => 'Pas de gouttière',
	'champ_direction_label' => 'Direction',
	'champ_direction_reverse_label_case' => 'Inverser la direction',
	//
	'champ_media_all' => 'Tous',
	'champ_media_mobile' => 'Mobiles',
	'champ_media_mobile_up' => 'Mobiles et +',
	'champ_media_tablet' => 'Tablettes',
	'champ_media_tablet_up' => 'Tablettes et +',
	'champ_media_desktop' => 'Bureaux',
	'champ_media_desktop_up' => 'Bureaux et +',
	'champ_media_desktop_large' => 'Bureaux larges',
	'champ_media_desktop_large_up' => 'Bureaux larges et +',
	//
	'champ_container_label' => 'Largeur interne',
	'champ_container_explication' => 'Limiter la largeur du <strong>contenu</strong> de la noisette, sans changer sa largeur propre.',
	'champ_container_edito' => 'Largeur adaptée à du texte',
	'champ_container' => 'Largeur moyenne',
	'champ_container_small' => 'Largeur petite',
	'champ_container_large' => 'Largeur grande',
	'champ_container_fluid' => 'Largeur fluide',
	//
	'champ_valeur_null' => '∅',
	'champ_valeur_aucune' => 'Aucune',
	'champ_valeur_aucun' => 'Aucun',
	//
	'champ_visibility_label' => 'Visibilité',
	'champ_visibility_visible' => 'Visible',
	'champ_visibility_hidden' => 'Caché',
	//
	'champ_offset_label' => 'Décalage',
	'champ_offset_push_label' => 'Décaler en avant',
	'champ_offset_push_explication' => 'Décaler en avant cette noisette et les suivantes.',
	'champ_offset_pull_label' => 'Décaler en arrière',
	'champ_offset_pull_explication' => 'Décaler en arrière cette noisettes et les suivantes.',
	'champ_offset_push_absolute_label' => 'Décaler en avant (absolu)',
	'champ_offset_push_absolute_explication' => 'Décaler en avant, indépendamment des autres noisettes.',
	'champ_offset_pull_absolute_label' => 'Décaler en arrière (absolu)',
	'champ_offset_pull_absolute_explication' => 'Décaler en arrière, indépendamment des autres noisettes.',

	// M
	'message_aucune_grille' => 'Aucune Grille CSS n’est activée',

	// T
	'titre_grille' => 'Grille CSS',
	'titre_cfg_parametrages' => 'Configuration',
);
