<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_sjcycle_saisies_dist(){
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
						'explication' => _T('sjcycle:explication_sync'),
						'defaut' => $config['sync']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'speed',
						'label' => _T('sjcycle:label_speed'),
						'explication' => _T('sjcycle:explication_speed'),
						'obligatoire' => 'oui',
						'defaut' => $config['speed']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'timeout',
						'label' => _T('sjcycle:label_timeout'),
						'explication' => _T('sjcycle:explication_timeout'),
						'obligatoire' => 'oui',
						'defaut' => $config['timeout']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'pause',
						'label' => _T('sjcycle:label_pause'),
						'explication' => _T('sjcycle:explication_pause'),
						'defaut' => $config['pause']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'random',
						'label' => _T('sjcycle:label_random'),
						'explication' => _T('sjcycle:explication_random'),
						'defaut' => $config['random']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'prev',
						'label' => _T('sjcycle:label_prev'),
						'explication' => _T('sjcycle:explication_prev'),
						'defaut' => $config['prev']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'text_prev',
						'label' => _T('sjcycle:label_text_prev'),
						'explication' => _T('sjcycle:explication_text_prev'),
						'defaut' => $config['text_prev']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'next',
						'label' => _T('sjcycle:label_next'),
						'explication' => _T('sjcycle:explication_next'),
						'defaut' => $config['next']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'text_next',
						'label' => _T('sjcycle:label_text_next'),
						'explication' => _T('sjcycle:explication_text_next'),
						'defaut' => $config['text_next']
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
						'obligatoire' => 'oui',
						'defaut' => $config['div_class']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'div_margin',
						'label' => _T('sjcycle:label_div_margin'),
						'obligatoire' => 'oui',
						'defaut' => $config['div_margin']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'img_bordure',
						'label' => _T('sjcycle:label_img_bordure'),
						'obligatoire' => 'oui',
						'defaut' => $config['img_bordure']
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'div_background',
						'label' => _T('sjcycle:label_div_background'),
						'obligatoire' => 'oui',
						'defaut' => $config['div_background']
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
                  'defaut' => $config['img_position'],
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
						'obligatoire' => 'oui',
						'defaut' => $config['img_width']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'img_height',
						'label' => _T('sjcycle:label_img_height'),
						'obligatoire' => 'oui',
						'defaut' => $config['img_height']
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'img_background',
						'label' => _T('sjcycle:label_img_background'),
						'explication' => _T('sjcycle:explication_img_background'),
						'obligatoire' => 'oui',
						'defaut' => $config['img_background']
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
						'explication' => _T('sjcycle:explication_tooltip'),
						'defaut' => $config['tooltip']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'tooltip_carac',
						'label' => _T('sjcycle:label_tooltip_carac'),
						'explication' => _T('sjcycle:explication_tooltip_carac'),
						'defaut' => $config['tooltip_carac']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'mediabox',
						'label' => _T('sjcycle:label_mediabox'),
						'explication' => _T('sjcycle:explication_mediabox'),
						'defaut' => $config['mediabox']
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
						'defaut' => $config['afficher_aide']
					)
				)
			)
		)
	);
}

function formulaires_configurer_sjcycle_charger(){

	$erreurs = array();
		
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $erreurs;
	}
	
	//Generation de miniatures des images inactive
	if (lire_config('creer_preview')!='oui') {
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $erreurs;
	}

	return $erreurs;
}

?>