<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_sjcycle_saisies_dist() {
	$config = lire_config('sjcycle');

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fjavascriptjcyle',
				'label' => _T('sjcycle:legend_jsparams')
			),
			'saisies' => array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'fx',
						'label' => _T('sjcycle:label_fx'),
						'explication' => _T('sjcycle:explication_fx'),
						'obligatoire' => 'oui',
						'defaut' => $config['fx'],
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'blindX' => 'blindX',
							'blindY' => 'blindY',
							'blindZ' => 'blindZ',
							'cover' => 'cover',
							'curtainX' => 'curtainX',
							'curtainY' => 'curtainY',
							'fade' => 'fade',
							'fadeZoom' => 'fadeZoom',
							'growX' => 'growX',
							'growY' => 'growY',
							'scrollUp' => 'scrollUp',
							'scrollDown' => 'scrollDown',
							'scrollLeft' => 'scrollLeft',
							'scrollRight' => 'scrollRight',
							'scrollHorz' => 'scrollHorz',
							'scrollVert' => 'scrollVert',
							'shuffle' => 'shuffle',
							'slideX' => 'slideX',
							'slideY' => 'slideY',
							'toss' => 'toss',
							'turnUp' => 'turnUp',
							'turnDown' => 'turnDown',
							'turnLeft' => 'turnLeft',
							'turnRight' => 'turnRight',
							'uncover' => 'uncover',
							'wipe' => 'wipe',
							'zoom' => 'zoom'
						)
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'sync',
						'label' => _T('sjcycle:label_sync'),
						'explication' => _T('sjcycle:explication_sync')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'speed',
						'label' => _T('sjcycle:label_speed'),
						'explication' => _T('sjcycle:explication_speed'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'timeout',
						'label' => _T('sjcycle:label_timeout'),
						'explication' => _T('sjcycle:explication_timeout'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'pause',
						'label' => _T('sjcycle:label_pause'),
						'explication' => _T('sjcycle:explication_pause')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'random',
						'label' => _T('sjcycle:label_random'),
						'explication' => _T('sjcycle:explication_random')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'text_prev',
						'label' => _T('sjcycle:label_text_prev'),
						'explication' => _T('sjcycle:explication_text_prev'),
						'placeholder' => _T('sjcycle:prev')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'text_next',
						'label' => _T('sjcycle:label_text_next'),
						'explication' => _T('sjcycle:explication_text_next'),
						'placeholder' => _T('sjcycle:next')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fstylessjcyle',
				'label' => _T('sjcycle:legend_cssparams')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'div_class',
						'label' => _T('sjcycle:label_div_class'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'div_margin',
						'label' => _T('sjcycle:label_div_margin'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'img_bordure',
						'label' => _T('sjcycle:label_img_bordure'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'div_background',
						'label' => _T('sjcycle:label_div_background'),
						'obligatoire' => 'oui'
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fimgparam',
				'label' => _T('sjcycle:legend_imgparams')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'explication',
						'texte' => _T('sjcycle:explication_imgparams')
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'img_position',
						'label' => _T('sjcycle:label_img_position'),
						'obligatoire' => 'oui',
						'datas' => array(
							 'left top' => 'left top',
							 'left center' => 'left center',
							 'left bottom' => 'left bottom',
							 'center top' => 'center top',
							 'center' => 'center',
							 'center bottom' => 'center bottom',
							 'right top' => 'right top',
							 'right center' => 'right center',
							 'right bottom' => 'right bottom'
						)
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'img_width',
						'label' => _T('sjcycle:label_img_width'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'img_height',
						'label' => _T('sjcycle:label_img_height'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'img_background',
						'label' => _T('sjcycle:label_img_background'),
						'explication' => _T('sjcycle:explication_img_background'),
						'obligatoire' => 'oui'
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'ftooltipbox',
				'label' => _T('sjcycle:legend_tooltip_box')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'tooltip',
						'label' => _T('sjcycle:label_tooltip'),
						'explication' => _T('sjcycle:explication_tooltip')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'tooltip_carac',
						'label' => _T('sjcycle:label_tooltip_carac'),
						'explication' => _T('sjcycle:explication_tooltip_carac')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'mediabox',
						'label' => _T('sjcycle:label_mediabox'),
						'explication' => _T('sjcycle:explication_mediabox')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fautres',
				'label' => _T('sjcycle:legend_autres')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'afficher_aide',
						'label' => _T('sjcycle:label_afficher_aide'),
						'explication' => _T('sjcycle:explication_afficher_aide'),
					)
				)
			)
		)
	);
}

function formulaires_configurer_sjcycle_charger() {

	$valeurs = lire_config('sjcycle');

	if (!lire_config('image_process')) {
		$valeurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $valeurs;
	}

	//Generation de miniatures des images inactive
	if (lire_config('creer_preview')!='oui') {
		$valeurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $valeurs;
	}

	return $valeurs;
}
