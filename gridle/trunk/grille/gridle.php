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
		'nom'           => _T('gridle:grid_nom'),
		'description'   => _T('gridle:grid_description'),
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
				'label'       => _T('gridle:grid_media_base'),
				'img'         => 'images/grid-media-all.svg',
				'breakpoints' => array(
					'min' => '',
					'max' => '',
				),
			),
			'tablet' => array(
				'label'       => _T('gridle:grid_media_tablet'),
				'img'         => 'images/grid-media-tablet.svg',
				'breakpoints' => array(
					'min' => '480px',
					'max' => '',
				),
			),
			'desktop' => array(
				'label'       => _T('gridle:grid_media_desktop'),
				'img'         => 'images/grid-media-desktop.svg',
				'breakpoints' => array(
					'min' => '768px',
					'max' => '',
				),
			),
			/*
			'large' => array(
				'label'       => _T('gridle:grid_media_large'),
				'img'         => 'images/grid-media-large.svg',
				'breakpoints' => array(
					'min' => '1440px',
					'max' => '',
				),
			),*/
		),
		'classes_base' => array(
			'container' => '',
			'row'       => 'row',
			'column'    => 'gr-12',
		),
	);

	return $grille;
}


/**
 * Liste des saisies relatives à la grille
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
	$saisies     = array();
	$nb_colonnes = intval(noizetier_layout_decrire_grille('nb_colonnes'));
	$medias      = array_keys(noizetier_layout_decrire_grille('medias'));

	// CONTAINER
	$saisies['container'] = array(
		array(
			'saisie' => 'grid_radio',
			'options' => array(
				'nom' => 'grille_container',
				'label' => _T('noizetier_layout:grid_container_label'),
				//'explication' => _T('noizetier_layout:grid_container_explication'),
				// 'li_class' => 'choix_inline',
				'defaut' => '',
				'cacher_option_intro' => 'oui',
				'data' => array(
					''                => array(
						'label' => _T('gridle:grid_null'),
						'img' => 'images/grid-null.svg',
					),
					'container'       => array(
						'label' => _T('gridle:grid_container'),
						'img' => 'images/grid-container.svg',
					),
					'container_fluid' => array(
						'label' => _T('gridle:grid_container_fluid'),
						'img' => 'images/grid-container-fluid.svg',
					),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
	);

	// ROW
	$saisies['row'] = array(
		// Alignement horizontal
		array(
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'      => 'gridle_align_horizontal',
				'label'    => _T('gridle:grid_align_horizontal_label'),
				// 'li_class' => 'choix_inline',
				'defaut'   => '',
				'cacher_option_intro' => 'oui',
				'data' => array(
					'' => array(
						'label' => _T('gridle:grid_null'),
						'img'   => 'images/grid-null.svg',
					),
					'row-align-left' => array(
						'label' => _T('gridle:grid_align_left'),
						'img'   => 'images/grid-align-left.svg',
					),
					'row-align-center'  => array(
						'label' => _T('gridle:grid_align_center'),
						'img'   => 'images/grid-align-center.svg',
					),
					'row-align-right'   => array(
						'label' => _T('gridle:grid_align_right'),
						'img'   => 'images/grid-align-right.svg'
					),
					'row-align-between' => array(
						'label' => _T('gridle:grid_align_between'),
						'img'   => 'images/grid-align-between.svg'
					),
					'row-align-around'  => array(
						'label' => _T('gridle:grid_align_around'),
						'img'   => 'images/grid-align-around.svg'
					),
				),
			),
			'grille' => array(
				'multiple' => false,
			),
		),
		// Alignement vertical
		array(
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'      => 'gridle_align_vertical',
				'label'    => _T('gridle:grid_align_vertical_label'),
				// 'li_class' => 'choix_inline',
				'defaut'   => '',
				'cacher_option_intro' => 'oui',
				'data' => array(
					'' => array(
						'label' => _T('gridle:grid_null'),
						'img'   => 'images/grid-null.svg',
					),
					'row-align-top' => array(
						'label' => _T('gridle:grid_align_top'),
						'img'   => 'images/grid-align-top.svg',
					),
					'row-align-middle' => array(
						'label' => _T('gridle:grid_align_middle'),
						'img'   => 'images/grid-align-middle.svg',
					),
					'row-align-bottom' => array(
						'label' => _T('gridle:grid_align_bottom'),
						'img'   => 'images/grid-align-bottom.svg',
					),
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
				'label'      => _T('gridle:grid_no_gutter_label'),
				'label_case' => _T('gridle:grid_no_gutter_label_case'),
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
				'label'      => _T('gridle:grid_reverse_label'),
				'label_case' => _T('gridle:grid_reverse_label_case'),
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

	// COLUMN
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
	foreach($colonnables as $champ => $classe) {
		// D'abord des valeurs nulles
		switch ($champ) {
			default:
				$data_saisies[$champ][''] = _T('gridle:grid_null');
				break;
		}
		// Ensuite d'autres valeurs éventuelles
		switch ($champ) {
			case 'column':
				$data_saisies[$champ]['gr-adapt'] = _T('gridle:grid_column_adapt');
				$data_saisies[$champ]['gr-grow'] = _T('gridle:grid_column_grow');
				break;
		}
		// Puis les valeurs selon les colonnes/médias
		switch ($champ) {
			default:
				for ($i=1; $i<=$nb_colonnes; $i++) {
					$data_saisies[$champ][$classe.'-'.$i] = $i;
					foreach($medias as $media) {
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
	) {
		$nb_noisettes_conteneur = sql_countsel('spip_noisettes', 'id_conteneur='.sql_quote($noisette['id_conteneur']));
		for ($i=1; $i<=$nb_noisettes_conteneur; $i++) {
			$data_saisies['order'][''] = _T('gridle:grid_null');
			$data_saisies['order']['order-'.$i] = $i;
			foreach($medias as $media) {
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
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_column',
				'label'       => _T('gridle:grid_column_label'),
				'defaut'      => 'gr-12',
				'medias'      => 'oui',
				'obligatoire' => 'oui',
				'li_class'    => 'pleine_largeur',
				'slider'      => 'oui',
				'data'        => $data_saisies['column'],
			),
			'grille' => array(
				'multiple' => true,
				'data'     => $data_grille['column'],
			),
		),
		// Décalage
		/*
		array(
			'saisie' => 'grid_offset',
			'options' => array(
				'nom'      => 'gridle_offfset',
				'label'    => _T('gridle:grid_offset_label'),
				'li_class' => 'pleine_largeur',
			),
		),
		*/
		/*
		// Choix du décalage
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom'        => 'gridle_offset',
				'label'      => _T('gridle:grid_offset_label'),
				'data' => array(
					'push'   => _T('gridle:grid_push_label'),
					'pull'   => _T('gridle:grid_pull_label'),
					'prefix' => _T('gridle:grid_prefix_label'),
					'suffix' => _T('gridle:grid_suffix_label'),
				),
			),
		),
		// Décalage en avant absolu
		array(
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_push',
				'label'       => _T('gridle:grid_push_label'),
				'defaut'      => '',
				'medias'      => 'oui',
				'li_class'    => 'pleine_largeur',
				'slider'      => 'oui',
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
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_pull',
				'label'       => _T('gridle:grid_pull_label'),
				'defaut'      => '',
				'medias'      => 'oui',
				'li_class'    => 'pleine_largeur',
				'slider'      => 'oui',
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
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_prefix',
				'label'       => _T('gridle:grid_prefix_label'),
				'defaut'      => '',
				'medias'      => 'oui',
				'li_class'    => 'pleine_largeur',
				'slider'      => 'oui',
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
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_suffix',
				'label'       => _T('gridle:grid_suffix_label'),
				'defaut'      => '',
				'medias'      => 'oui',
				'li_class'    => 'pleine_largeur',
				'slider'      => 'oui',
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
		array(
			'saisie' => 'grid_radio',
			'options' => array(
				'nom'         => 'gridle_order',
				'label'       => _T('gridle:grid_order_label'),
				// 'explication' => _T('gridle:grid_order_explication'),
				'medias'      => 'oui',
				//'defaut'      => 'order-'.$noisette['rang_noisette'],
				'data'        => $data_saisies['order'],
				'exposer'     => $data_exposer['order'],
			),
			'grille' => array(
				'multiple' => true,
				'data'     => $data_grille['order'],
			),
		),
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

	// COMMUNES
	$saisies['*'] = array(
		// Visibilité
		/*
		array(
			'saisie' => 'grid_visibility',
			'options' => array(
				'nom' => 'grid_visibiity',
				'label' => _T('gridle:grid_visibility_label'),
			),
		),
		*/
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