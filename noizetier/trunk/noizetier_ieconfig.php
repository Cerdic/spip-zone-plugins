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
function noizetier_ieconfig($flux) {

	// On détermine l'action demandée qui peut être : afficher le formulaire d'export ou d'import, construire le
	// tableau d'export ou exécuter l'importation.
	$action = $flux['args']['action'];

	if ($action == 'form_export') {
		// Construire le formulaire d'export :
		// -- on demande le minimum à savoir si l'utilisateur veut inclure dans son export l'ensemble des données
		//    du noiZetier.
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
						'saisie' => 'oui_non',
						'options' => array(
							'nom' => 'noizetier_export_option',
							'label' => '<:noizetier:ieconfig_noizetier_export_option:>',
							'explication' => '<:noizetier:ieconfig_noizetier_export_explication:>',
							'defaut' => '',
						),
					),
				),
			),
		);
		$flux['data'] = array_merge($flux['data'], $saisies);

	} elseif (($action == 'export') and (_request('noizetier_export_option') == 'on')) {
		// Générer le tableau d'export
		$flux['data']['noizetier'] = noizetier_ieconfig_exporter();

	} elseif (($action == 'form_import') and isset($flux['args']['config']['noizetier'])) {
		// Construire le formulaire d'import :
		// -- On affiche un résumé du contenu du fichier d'import
		$import = $flux['args']['config']['noizetier'];
		$texte_explication = _T(
			'noizetier:formulaire_import_resume',
			array('version' => $import['version'], 'schema' => $import['schema']));
		if ($import['contenu']['noisettes']) {
			include_spip('inc/noizetier_conteneur');
			$pages = array();
			foreach ($import['noisettes'] as $_noisette) {
				$conteneur = noizetier_conteneur_decomposer($_noisette['id_conteneur']);
				$pages[] = !empty($conteneur['page'])
					? $conteneur['page']
					: $conteneur['objet'] . $conteneur['id_objet'];
			}
			$pages = array_unique($pages);
			$texte_explication .= '<br />'
				. _T('noizetier:formulaire_liste_pages_config', array('liste' => implode(', ', $pages)));
		}
		if ($import['contenu']['compositions_virtuelles']) {
			$compositions = array();
			foreach ($import['compositions_virtuelles'] as $_composition) {
				$compositions[] = $_composition['page'];
			}
			$texte_explication .= '<br />'
				. _T('noizetier:formulaire_liste_compos_config', array('liste' => implode(', ', $compositions)));
		}

		// -- Construire la saisie permettant de proposer chaque contenu de l'export (configuration, noisettes,
		//    pages explicites et compositions virtuelles).
		$contenu_data = $contenu_defaut = $contenu_disable = array();
		foreach ($import['contenu'] as $_contenu => $_valeur) {
			$contenu_data[$_contenu] = _T("noizetier:formulaire_import_contenu_{$_contenu}");
			if (!$_valeur) {
				$contenu_disable[] = $_contenu;
			} else {
				$contenu_defaut[] = $_contenu;
			}
		}
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
						'saisie' => 'checkbox',
						'options' => array(
							'nom' => 'noizetier_import_contenu',
							'label' => '<:noizetier:formulaire_import_contenu:>',
//							'defaut' => $contenu_defaut,
							'datas' => $contenu_data,
							'disable' => $contenu_disable
						),
					),
					array(
						'saisie' => 'radio',
						'options' => array(
							'nom' => 'noizetier_type_import',
							'label' => '<:noizetier:formulaire_type_import:>',
							'explication' => '<:noizetier:formulaire_type_import_explication:>',
							'defaut' => 'remplacer',
							'datas' => array(
								'fusion' => '<:noizetier:formulaire_import_fusion:>',
								'remplacer' => '<:noizetier:formulaire_import_remplacer:>',
							),
						),
					),
				),
			),
		);
		$flux['data'] = array_merge($flux['data'], $saisies);
	}

	// Import de la configuration
	if (($action == 'import')
	and isset($flux['args']['config']['noizetier'])
	and (_request('noizetier_type_import') != '')) {
		if (!noizetier_ieconfig_importer(_request('noizetier_type_import'), _request('noizetier_import_contenu'), $flux['args']['config']['noizetier'])) {
			$flux['data'] .= _T('noizetier:ieconfig_probleme_import_config').'<br />';
		}
	}

	return $flux;
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

	$export = array();

	// Insérer une en-tête qui permet de connaitre la version du noiZetier utilisé lors de l'export
	$informer_plugin = chercher_filtre('info_plugin');
	$export['version'] = $informer_plugin('noizetier', 'version');
	$export['schema'] = $informer_plugin('noizetier', 'schema');
	$export['contenu'] = array();

	// Exportation de la configuration du plugin rangée dans la meta noizetier.
	// Etant donné que l'on utilise ce pipeline pour les données de production du noiZetier, on exporte aussi
	// sa configuration via ce pipeline et non via le pipeline ieconfig_metas.
	include_spip('inc/config');
	$export['configuration'] = lire_config('noizetier', array());
	$export['contenu']['configuration'] = $export['configuration'] ? 'on' : '';

	// Exportation de la tables spip_noisettes qui contient les noisettes associées aux pages explicites,
	// aux compositions virtuelles et à certains objets précis.
	// -- on supprime l'id_noisette de chaque noisette car il sera recréé lors de l'import.
	include_spip('ncore_fonctions');
	$export['noisettes'] = noisette_repertorier('noizetier', array(), 'id_noisette');
	foreach($export['noisettes'] as $_id => $_noisette) {
		unset($export['noisettes'][$_id]['id_noisette']);
	}
	$export['contenu']['noisettes'] = $export['noisettes'] ? 'on' : '';

	// Exportation de la tables spip_noizetier_pages qui contient les pages explicites et compositions virtuelles.
	$from ='spip_noizetier_pages';

	// -- pour les pages explicites il faut sauvegarder les blocs exclus qui peuvent être modifiés après chargement,
	//    les autres champs n'ont pas d'intérêt à être sauvegardés car ils proviennent du fichier XML/YAML.
	$select = array('page', 'blocs_exclus');
	$where = array('est_virtuelle=' . sql_quote('non'));
	$export['pages_explicites'] = sql_allfetsel($select, $from, $where);
	$export['contenu']['pages_explicites'] = $export['pages_explicites'] ? 'on' : '';

	// -- pour les compositions virtuelles il faut tout sauvegarder (sauf le timestamp 'maj') car elles sont créées
	//    de zéro.
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table = $trouver_table($from);
	$select = array_diff(array_keys($table['field']), array('maj'));
	$where = array('est_virtuelle=' . sql_quote('oui'));
	$export['compositions_virtuelles'] = sql_allfetsel($select, $from, $where);
	$export['contenu']['compositions_virtuelles'] = $export['compositions_virtuelles'] ? 'on' : '';

	// Appel d'un pipeline propre à l'export du noiZetier pour autoriser la modification par des plugins
	// de la structure d'export
	$export = pipeline('noizetier_config_export', $export);

	return $export;
}

/**
 * Importe une configuration de noisettes et de compositions.
 *
 * @param string  $type_import
 * @param string  $import_contenu
 * @param array $config
 *
 * @return bool
 */
function noizetier_ieconfig_importer($type_import, $import_contenu, $config) {
	if ($type_import != 'remplacer') {
		$type_import = 'fusion';
	}
	if ($import_contenu != 'oui') {
		$import_contenu = 'non';
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
	if ($import_contenu == 'oui') {
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
