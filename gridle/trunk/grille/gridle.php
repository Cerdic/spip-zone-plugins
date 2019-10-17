<?php
/**
 * Fonctions utiles à la grille « Gridle ».
 *
 * @note
 * Ne pas utiliser ces fonctions directement, faire appel à l'API
 *
 * @plugin    Grille Gridle
 * @copyright 2019
 * @author    Mukt
 * @licence   GNU/GPL
 * @package   SPIP\Gridle\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Description de la grille : nom, breakpoints etc.
 *
 * @return array
 *     Tableau associatif
 */
function grille_gridle_decrire_grille_dist() {

	$grille = array(
		'nom'           => _T('gridle:gridle_nom'),
		'description'   => _T('gridle:gridle_description'),
		'logo'          => 'gridle-32.png',
		'documentation' => 'https://github.com/Coffeekraken/gridle/',
		'demo'          => 'http://gridle.org/demo/demo-flex.html',
		'mobile_first'  => true,
		'nb_colonnes'   => 12,
		'css_public'    => 'css/gridle.min.css',
		'css_prive'     => 'css/gridle.min.css',
		'js'            => array('css/gridle.min.js'),
		'medias' => array(
			'' => array(
				'label'       => _T('noizetier_layout:champ_media_mobile'),
				'image'       => 'images/grid-media-mobile.svg',
				'breakpoints' => array(
					'min' => '',
					'max' => '',
				),
			),
			'tablet' => array(
				'label'       => _T('noizetier_layout:champ_media_tablet'),
				'image'       => 'images/grid-media-tablet.svg',
				'breakpoints' => array(
					'min' => '480px',
					'max' => '',
				),
			),
			'desktop' => array(
				'label'       => _T('noizetier_layout:champ_media_desktop'),
				'image'       => 'images/grid-media-desktop.svg',
				'breakpoints' => array(
					'min' => '768px',
					'max' => '',
				),
			),
			/*
			'large' => array(
				'label'       => _T('noizetier_layout:champ_media_large'),
				'image'       => 'images/grid-media-large.svg',
				'breakpoints' => array(
					'min' => '1440px',
					'max' => '',
				),
			),*/
		),
		// 'classes_base' => array(
		// 	'container' => '',
		// 	'row'       => 'row',
		// 	'column'    => 'gr-12',
		// ),
	);

	return $grille;
}


/**
 * Liste des saisies relatives à la grille.
 *
 * @note
 * Passer id_noisette en paramètre permet de connaître son ordre
 * et donc de définir certaines saisies en fonction.
 *
 * @param int $id_noisette
 *     N° d'une noisette (optionnel)
 * @return array
 *     Description des saisies, rangées par type d'élément de la grille
 *     container => [saisies]
 *     row       => [saisies]
 *     column    => [saisies]
 */
function grille_gridle_lister_saisies_dist($id_noisette = 0) {

	include_spip('inc/noizetier_layout');
	include_spip('inc/filtres');
	$saisies     = array();
	$nb_colonnes = intval(noizetier_layout_decrire_grille('nb_colonnes'));
	$medias      = array_keys(noizetier_layout_decrire_grille('medias'));

	// =========
	// CONTAINER
	// =========
	$saisies['container'] = array(
		array(
			'saisie' => 'medias_radio',
			'options' => array(
				'nom' => 'grille_container',
				'label' => _T('noizetier_layout:champ_container_label'),
				'explication' => _T('noizetier_layout:champ_container_explication'),
				'defaut' => '',
				'cacher_option_intro' => 'oui',
				'data' => array(
					''                => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-null.svg'), '" />', _T('noizetier_layout:champ_valeur_null')),
					'container'       => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-container.svg'), '" />', _T('noizetier_layout:champ_container')),
					'container_fluid' => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-container-fluid.svg'), '" />', _T('noizetier_layout:champ_container_fluid')),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
	);

	// ===
	// ROW
	// ===
	$saisies['row'] = array(
		// Toujours indiquer que c'est une ligne
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom'    => 'row',
				'defaut' => 'row',
			),
			'grille' => array(
				'multiple' => false,
			),
		),
		// Alignement horizontal
		array(
			'saisie' => 'medias_radio',
			'options' => array(
				'nom'         => 'gridle_align_horizontal',
				'label'       => _T('noizetier_layout:champ_align_horizontal_label'),
				'explication' => _T('noizetier_layout:champ_align_horizontal_row_explication'),
				'defaut'      => '',
				// 'cacher_option_intro' => 'oui',
				'data' => array(
					''                  => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-null.svg'), '" />', _T('noizetier_layout:champ_valeur_null')),
					'row-align-left'    => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-left.svg'), '" />', _T('noizetier_layout:champ_align_left')),
					'row-align-center'  => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-center.svg'), '" />', _T('noizetier_layout:champ_align_center')),
					'row-align-right'   => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-right.svg'), '" />', _T('noizetier_layout:champ_align_right')),
					'row-align-between' => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-between.svg'), '" />', _T('noizetier_layout:champ_align_between')),
					'row-align-around'  => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-around.svg'), '" />', _T('noizetier_layout:champ_align_around')),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
		// Alignement vertical
		array(
			'saisie' => 'medias_radio',
			'options' => array(
				'nom'         => 'gridle_align_vertical',
				'label'       => _T('noizetier_layout:champ_align_vertical_label'),
				'explication' => _T('noizetier_layout:champ_align_vertical_row_explication'),
				'defaut'      => '',
				// 'cacher_option_intro' => 'oui',
				'data' => array(
					''                 => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-null.svg'), '" />', _T('noizetier_layout:champ_valeur_null')),
					'row-align-top'    => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-top.svg'), '" />', _T('noizetier_layout:champ_align_top')),
					'row-align-middle' => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-middle.svg'), '" />', _T('noizetier_layout:champ_align_middle')),
					'row-align-bottom' => concat('<img alt="" width="50" height="50" src="'.find_in_path('images/grid-align-bottom.svg'), '" />', _T('noizetier_layout:champ_align_bottom')),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
		// Gouttière
		array(
			'saisie' => 'case',
			'options' => array(
				'nom'        => 'gridle_no_gutter',
				'label'      => _T('noizetier_layout:champ_gutter_label'),
				'label_case' => _T('noizetier_layout:champ_gutterless_label_case'),
				'valeur_oui' => 'row-no-gutter',
			),
			'grille' => array(
				'multiple' => false,
				'data' => array(
					'row-no-gutter',
				),
			),
		),
		// Ordre inverse
		array(
			'saisie' => 'case',
			'options' => array(
				'nom'        => 'gridle_reverse',
				'label'      => _T('noizetier_layout:champ_direction_label'),
				'label_case' => _T('noizetier_layout:champ_direction_reverse_label_case'),
				'valeur_oui' => 'row-reverse',
			),
			'grille' => array(
				'multiple' => false,
				'data' => array(
					'row-reverse',
				),
			),
		),
	);

	// ======
	// COLUMN
	// ======
	// Construire certains datas en fonction des médias et du nombre de colonnes
	$data_grille  = array(); // Liste complète des classes (avec @media)
	$data_saisies = array(); // Liste réduite
	$data_exposer = array(); // Pour exposer certaines valeurs
	$colonnables  = array(
		'column' => 'gr',
		'push'   => 'push',
		'pull'   => 'pull',
		'prefix' => 'prefix',
		'suffix' => 'suffix',
	);
	foreach ($colonnables as $champ => $classe) {
		// D'abord des valeurs nulles si besoin
		/*
		switch ($champ) {
			default:
				$data_saisies[$champ][''] = _T('noizetier_layout:champ_valeur_null');
				break;
		}*/
		// Ensuite d'autres valeurs éventuelles
		switch ($champ) {
			case 'column':
				$data_saisies[$champ]['gr-adapt'] = _T('noizetier_layout:champ_width_shrink');
				$data_saisies[$champ]['gr-grow'] = _T('noizetier_layout:champ_width_grow');
				break;
		}
		// Puis les valeurs selon les colonnes/médias
		switch ($champ) {
			default:
				for ($i=1; $i<=$nb_colonnes; $i++) {
					$data_saisies[$champ][$classe.'-'.$i] = $i;
					foreach ($medias as $media) {
						$data_grille[$champ][] = $classe.'-'.$i.($media?'@'.$media:'');
					}
				}
				break;
		}
	}
	// Ordre : trouver le rang de la noisette et le nombre de siblings
	if (
		intval($id_noisette)
		and $noisette = sql_fetsel('rang_noisette,id_conteneur', 'spip_noisettes', 'id_noisette='.intval($id_noisette))
		and (intval($nb_noisettes_conteneur = sql_countsel('spip_noisettes', 'id_conteneur='.sql_quote($noisette['id_conteneur']))) > 1)
	) {
		for ($i=1; $i<=$nb_noisettes_conteneur; $i++) {
			$data_saisies['order'][''] = _T('noizetier_layout:champ_valeur_null');
			$data_saisies['order']['order-'.$i] = $i;
			foreach ($medias as $media) {
				$value = 'order-'.$i.($media?'@'.$media:'');
				$data_grille['order'][] = $value;
				if ($i == $noisette['rang_noisette']) {
					$data_exposer['order'][] = $value;
				}
			}
		}
	}

	$saisies['column'] = array(
		// Largeur
		array(
			'saisie' => 'medias_selection',
			'options' => array(
				'nom'         => 'gridle_column',
				'label'       => _T('noizetier_layout:champ_width_label'),
				'explication' => _T('noizetier_layout:champ_width_explication'),
				'defaut'      => 'gr-12',
				'medias'      => 'oui',
				'obligatoire' => 'oui',
				// 'conteneur_class' => 'pleine_largeur',
				// 'slider'      => 'oui',
				'data'        => $data_saisies['column'],
			),
			'grille' => array(
				'multiple' => true,
				'data'     => $data_grille['column'],
			),
		),
		/*
		// Choix du type de décalage
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom'   => 'gridle_offset',
				'label' => _T('noizetier_layout:champ_offset_label'),
				'data'  => array(
					// ''       => _T('noizetier_layout:champ_valeur_null'),
					'prefix' => _T('noizetier_layout:champ_offset_push_label'),
					'suffix' => _T('noizetier_layout:champ_offset_pull_label'),
					'push'   => _T('noizetier_layout:champ_offset_push_absolute_label'),
					'pull'   => _T('noizetier_layout:champ_offset_pull_absolute_label'),
				),
			),
		),
		// Décalage en avant absolu
		array(
			'saisie' => 'medias_selection',
			'options' => array(
				'nom'         => 'gridle_push',
				'label'       => _T('noizetier_layout:champ_offset_push_absolute_label'),
				'explication' => _T('noizetier_layout:champ_offset_push_absolute_explication'),
				'defaut'      => '',
				'medias'      => 'oui',
				// 'conteneur_class' => 'pleine_largeur',
				// 'slider'      => 'oui',
				'data'        => $data_saisies['push'],
				'afficher_si' => '@gridle_offset@ == "push"'
			),
			'grille' => array(
				'multiple' => true,
				'data'     => $data_grille['push'],
			),
		),
		// Décalage en arrière absolu
		array(
			'saisie' => 'medias_selection',
			'options' => array(
				'nom'         => 'gridle_pull',
				'label'       => _T('noizetier_layout:champ_offset_pull_absolute_label'),
				'explication' => _T('noizetier_layout:champ_offset_pull_absolute_explication'),
				'defaut'      => '',
				'medias'      => 'oui',
				// 'conteneur_class' => 'pleine_largeur',
				// 'slider'      => 'oui',
				'data'        => $data_saisies['pull'],
				'afficher_si' => '@gridle_offset@ == "pull"'
			),
			'grille' => array(
				'multiple' => true,
				'medias'   => true,
				'data'     => $data_grille['pull'],
			),
		),
		// Décalage en avant relatif
		array(
			'saisie' => 'medias_selection',
			'options' => array(
				'nom'         => 'gridle_prefix',
				'label'       => _T('noizetier_layout:champ_offset_push_label'),
				'explication' => _T('noizetier_layout:champ_offset_push_explication'),
				'defaut'      => '',
				'medias'      => 'oui',
				// 'conteneur_class' => 'pleine_largeur',
				// 'slider'      => 'oui',
				'data'        => $data_saisies['prefix'],
				'afficher_si' => '@gridle_offset@ == "prefix"'
			),
			'grille' => array(
				'multiple' => true,
				'medias'   => true,
				'data'     => $data_grille['prefix'],
			),
		),
		// Décalage en arrière relatif
		array(
			'saisie' => 'medias_selection',
			'options' => array(
				'nom'         => 'gridle_suffix',
				'label'       => _T('noizetier_layout:champ_offset_pull_label'),
				'explication' => _T('noizetier_layout:champ_offset_pull_explication'),
				'defaut'      => '',
				'medias'      => 'oui',
				// 'conteneur_class' => 'pleine_largeur',
				// 'slider'      => 'oui',
				'data'        => $data_saisies['suffix'],
				'afficher_si' => '@gridle_offset@ == "suffix"'
			),
			'grille' => array(
				'multiple' => true,
				'medias'   => true,
				'data'     => $data_grille['suffix'],
			),
		),
		*/
		// Ordre
		$data_saisies['order'] ?
			array(
				'saisie' => 'medias_radio',
				'options' => array(
					'nom'         => 'gridle_order',
					'label'       => _T('noizetier_layout:champ_order_label'),
					'explication' => _T('noizetier_layout:champ_order_explication').' '._T('noizetier_layout:champ_order_explication_expose'),
					'medias'      => 'oui',
					//'defaut'      => 'order-'.$noisette['rang_noisette'],
					'data'        => $data_saisies['order'],
					'exposer'     => $data_exposer['order'],
				),
				'grille' => array(
					'multiple' => true,
					'data'     => $data_grille['order'],
				),
			)
			: array(),
		/*
		// Centrer
		array(
			'saisie' => 'case',
			'options' => array(
				'nom'        => 'gridle_centered',
				'label'      => _T('gridle:grid_align_label'),
				'label_case' => _T('gridle:grid_align_horizontal_label_case'),
				'valeur_oui' => 'gr-centered',
			),
			'grille' => array(
				'multiple' => false,
			),
		),
		*/
	);

	// ====
	// TOUT
	// ====
	$saisies['*'] = array(
		// Visibilité
		array(
			'saisie' => 'medias_radio',
			'options' => array(
				'nom' => 'grid_visibility',
				'label' => _T('noizetier_layout:champ_visibility_label'),
				'medias' => true,
				'data' => array(
					''        => _T('noizetier_layout:champ_valeur_null'),
					'hidden'  => _T('noizetier_layout:champ_visibility_hidden'),
					'visible' => _T('noizetier_layout:champ_visibility_visible'),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
	);

	return $saisies;
}


/**
 * Créer la variante d'une classe pour un média
 *
 * Par exemple gr-6 => gr-6@desktop
 *
 * @param string $classe
 *     Classe à modifier
 * @param string $media
 *     Le media
 * @return string
 *     La classe modifiée
 */
function grille_gridle_creer_classe_media_dist($classe, $media) {

	$classe_media = $classe;
	if ($classe and $media) {
		$classe_media = $classe . '@' . $media;
	}

	return $classe_media;
}