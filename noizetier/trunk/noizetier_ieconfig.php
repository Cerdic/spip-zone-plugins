<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Pipeline ieconfig pour l'import/export de configuration.
 *
 * @param array $flux
 *
 * @return array
 */
function noizetier_ieconfig($flux)
{
	$action = $flux['args']['action'];

	// Formulaire d'export
	if ($action == 'form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'noizetier_export',
					'label' => '<:noizetier:noizetier:>',
					'icone' => 'noizetier-24.png',
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'noizetier_export_explication',
							'texte' => '<:noizetier:ieconfig_noizetier_export_explication:>',
						),
					),
					array(
						'saisie' => 'oui_non',
						'options' => array(
							'nom' => 'noizetier_export_option',
							'label' => '<:noizetier:ieconfig_noizetier_export_option:>',
							'defaut' => '',
						),
					),
				),
			),
		);
		$flux['data'] = array_merge($flux['data'], $saisies);
	}

	// Tableau d'export
	if ($action == 'export' && _request('noizetier_export_option') == 'on') {
		include_spip('noizetier_fonctions');
		$flux['data']['noizetier'] = noizetier_ieconfig_exporter();
	}

	// Formulaire d'import
	if ($action == 'form_import' && isset($flux['args']['config']['noizetier'])) {
		$texte_explication = '';
		if (isset($flux['args']['config']['noizetier']['noisettes'])) {
			$texte_explication .= _T('noizetier:formulaire_liste_pages_config');
			$pages = array();
			foreach ($flux['args']['config']['noizetier']['noisettes'] as $noisette) {
				$pages[] = $noisette['type'].'-'.$noisette['composition'];
			}
			$pages = array_unique($pages);
			foreach ($pages as $page) {
				$texte_explication .= '<br />&raquo; '.rtrim($page, '-');
			}
		}
		if (isset($flux['args']['config']['noizetier']['noizetier_compositions'])) {
			$texte_explication .= '<br />'._T('noizetier:formulaire_liste_compos_config');
			foreach ($flux['args']['config']['noizetier']['noizetier_compositions'] as $type => $compositions) {
				foreach ($compositions as $composition => $compo) {
					$texte_explication .= '<br />&raquo; '.$type.'-'.$composition;
				}
			}
		}
		if (isset($flux['args']['config']['noizetier']['noizetier_compositions'])) {
			$saisies = array(
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'noizetier_export',
						'label' => '<:noizetier:noizetier:>',
						'icone' => 'noizetier-24.png',
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'noizetier_export_explication',
								'texte' => $texte_explication,
							),
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_type_import',
								'label' => '<:noizetier:formulaire_type_import:>',
								'explication' => '<:noizetier:formulaire_type_import_explication:>',
								'defaut' => '',
								'option_intro' => '<:noizetier:ieconfig_ne_pas_importer:>',
								'datas' => array(
									'fusion' => '<:noizetier:formulaire_import_fusion:>',
									'remplacer' => '<:noizetier:formulaire_import_remplacer:>',
								),
							),
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_import_compos',
								'label' => '<:noizetier:formulaire_import_compos:>',
								'defaut' => 'oui',
								'cacher_option_intro' => 'oui',
								'datas' => array(
									'oui' => '<:noizetier:oui:>',
									'non' => '<:noizetier:non:>',
								),
							),
						),
					),
				),
			);
		} else {
			$saisies = array(
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'noizetier_export',
						'label' => '<:noizetier:noizetier:>',
						'icone' => 'noizetier-24.png',
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'noizetier_export_explication',
								'texte' => $texte_explication,
							),
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_type_import',
								'label' => '<:noizetier:formulaire_type_import:>',
								'explication' => '<:noizetier:formulaire_type_import_explication:>',
								'defaut' => '',
								'option_intro' => '<:noizetier:ieconfig_ne_pas_importer:>',
								'datas' => array(
									'fusion' => '<:noizetier:formulaire_import_fusion:>',
									'remplacer' => '<:noizetier:formulaire_import_remplacer:>',
								),
							),
						),
						array(
							'saisie' => 'hidden',
							'options' => array(
								'nom' => 'noizetier_import_compos',
								'defaut' => 'non',
							),
						),
					),
				),
			);
		}
		$flux['data'] = array_merge($flux['data'], $saisies);
	}

	// Import de la configuration
	if ($action == 'import' && isset($flux['args']['config']['noizetier']) && _request('noizetier_type_import') != '') {
		include_spip('noizetier_fonctions');
		if (!noizetier_ieconfig_importer(_request('noizetier_type_import'), _request('noizetier_import_compos'), $flux['args']['config']['noizetier'])) {
			$flux['data'] .= _T('noizetier:ieconfig_probleme_import_config').'<br />';
		}
	}

	return($flux);
}


// --------------------------------------------------------------------
// ------------------------- API IMPORT/EXPORT ------------------------
// --------------------------------------------------------------------

/**
 * Retourne le tableau des noisettes et des compositions du noizetier pour les exports.
 *
 * @return
 **/
function noizetier_ieconfig_exporter() {
	$data = array();

	// On calcule le tableau des noisettes
	// TODO : a modifier pour les objets non ?
	$data['noisettes'] = sql_allfetsel(
		'type, composition, bloc, type_noisette, parametres, css',
		'spip_noisettes',
		'1',
		'',
		'type, composition, bloc, rang'
	);

	// On remet au propre les parametres
	foreach ($data['noisettes'] as $cle => $noisette) {
		$data['noisettes'][$cle]['parametres'] = unserialize($noisette['parametres']);
	}

	// On recupere les compositions du noizetier
	// TODO : a supprimer car elles sont dans la table des pages
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (is_array($noizetier_compositions) and count($noizetier_compositions) > 0) {
		$data['noizetier_compositions'] = $noizetier_compositions;
	}

	$data = pipeline('noizetier_config_export', $data);

	return $data;
}

/**
 * Importe une configuration de noisettes et de compositions.
 *
 * @param string  $type_import
 * @param string  $import_compos
 * @param array $config
 *
 * @return bool
 */
function noizetier_ieconfig_importer($type_import, $import_compos, $config) {
	if ($type_import != 'remplacer') {
		$type_import = 'fusion';
	}
	if ($import_compos != 'oui') {
		$import_compos = 'non';
	}

	$config = pipeline('noizetier_config_import', $config);

	// On s'occupe deja des noisettes
	$noisettes = $config['noisettes'];
	include_spip('base/abstract_sql');
	if (is_array($noisettes) and count($noisettes) > 0) {
		$noisettes_insert = array();
		$rang = 1;
		$page = '';

		if ($type_import == 'remplacer') {
			sql_delete('spip_noisettes', '1');
		}

		foreach ($noisettes as $noisette) {
			$type = $noisette['type'];
			$composition = $noisette['composition'];
			if ($type.'-'.$composition != $page) {
				$page = $type.'-'.$composition;
				$rang = 1;
				if ($type_import == 'fusion') {
					$rang = sql_getfetsel('rang_noisette', 'spip_noisettes', 'type='.sql_quote($type).' AND composition='.sql_quote($composition), '', 'rang DESC') + 1;
				}
			} else {
				$rang = $rang + 1;
			}
			$noisette['rang_noisette'] = $rang;
			$noisette['parametres'] = serialize($noisette['parametres']);
			$noisettes_insert[] = $noisette;
		}

		$ok = sql_insertq_multi('spip_noisettes', $noisettes_insert);
	}

	// On s'occupe des compositions du noizetier
	// TODO : à modifier car les compositions sont dans la table des pages
	if ($import_compos == 'oui') {
		include_spip('inc/meta');
		$compos_importees = $config['noizetier_compositions'];
		if (is_array($compos_importees) and count($compos_importees) > 0) {
			if ($type_import == 'remplacer') {
				effacer_meta('noizetier_compositions');
			} else {
				$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			}

			if (!is_array($noizetier_compositions)) {
				$noizetier_compositions = array();
			}

			foreach ($compos_importees as $type => $compos_type) {
				foreach ($compos_type as $composition => $info_compo) {
					$noizetier_compositions[$type][$composition] = $info_compo;
				}
			}

			ecrire_meta('noizetier_compositions', serialize($noizetier_compositions));
			ecrire_metas();
		}
	}

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur('noizetier-import-config');

	return $ok;
}
