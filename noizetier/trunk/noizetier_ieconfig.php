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
		// On affiche la version du noiZetier et le schéma de base de données avec lesquels le fichier d'import
		// à été créé.
		$import = $flux['args']['config']['noizetier'];
		$texte_explication = _T(
			'noizetier:import_resume',
			array('version' => $import['version'], 'schema' => $import['schema']));

		// La configuration : une case suffit car on applique toujours un remplacement et la configuration est
		// toujours présente dans un export.
		$informer_plugin = chercher_filtre('info_plugin');
		$version = $informer_plugin('noizetier', 'version', true);
		$schema = $informer_plugin('noizetier', 'schema');
		if ($schema == $import['schema']) {
			$explication_config = _T(
				'noizetier:import_configuration_explication',
				array('version' => $version, 'schema' => $schema));
		} else {
			$explication_config = _T(
				'noizetier:import_configuration_avertissement',
				array('version' => $version, 'schema' => $schema));
		}

		// Les pages explicites : seuls les bloc exclus peuvent être restaurés. Une case suffit car on applique
		// toujours une fusion des blocs exclus sur les pages de la base ayant le même identifiant.
		// On désactive toutefois l'option si aucune page explicite n'est commune entre les deux listes.
		$disable_pages_explicites = false;
		$select = array('page');
		$where = array('est_virtuelle=' . sql_quote('non'));
		if ($pages_explicites = sql_allfetsel($select,'spip_noizetier_pages', $where)) {
			$pages_explicites = array_map('reset', $pages_explicites);
			if (count(array_intersect($pages_explicites, array_column($import['pages_explicites'], 'page'))) > 0) {
				$explication_pages_explicites = _T('noizetier:import_pages_explicites_explication');
			} else {
				// Aucune page explicite commune entre la base et le fichier d'import.
				$disable_pages_explicites = true;
				$explication_pages_explicites = _T('noizetier:import_pages_explicites_avertissement1');
			}
		} else {
			// Aucune page explicite dans la base, ce n'est pas normal mais on envoie quand un même un avertissement.
			$disable_pages_explicites = true;
			$explication_pages_explicites = _T('noizetier:import_pages_explicites_avertissement2');
		}

		// Les compositions virtuelles : 3 possibilités en radio, remplacement, ajout (avec vérification de
		// l'absence d'une composition identique) et la fusion avec ajout. Si aucune composition virtuelle dans la
		// base seul l'ajout est possible et si aucune composition virtuelle n'est incluse dans l'import on ne propose
		// aucune action.
		$data_compositions = array();
		if ($import['contenu']['compositions_virtuelles']) {
			$data_compositions['ajouter'] = _T('noizetier:import_compositions_virtuelles_ajouter');
			$where = array('est_virtuelle=' . sql_quote('oui'));
			if (!sql_countsel('spip_noizetier_pages', $where)) {
				$explication_compositions = _T('noizetier:import_compositions_virtuelles_avertissement1');
			} else {
				$data_compositions['remplacer'] = _T('noizetier:import_compositions_virtuelles_remplacer');
				$data_compositions['fusionner'] = _T('noizetier:import_compositions_virtuelles_fusionner');
				$explication_compositions = _T('noizetier:import_compositions_virtuelles_explication');
			}
		} else {
			$explication_compositions = _T('noizetier:import_compositions_virtuelles_avertissement2');
		}

		// -- Les noisettes : 2 possibilités en radio, remplacement ou ajout.
		$data_noisettes = array();
		if ($import['contenu']['noisettes']) {
			$pages_base = sql_allfetsel('page','spip_noizetier_pages');
			$pages_base = array_map('reset', $pages_base);

			include_spip('base/objets');
			$importation_noisettes_possible = false;
			foreach ($import['noisettes'] as $_noisette) {
				if ($_noisette['type']) {
					$page_import = $_noisette['composition']
						? $_noisette['type'] . '-' . $_noisette['composition']
						: $_noisette['type'];
					if (in_array($page_import, $pages_base)) {
						$importation_noisettes_possible = true;
						break;
					}
				} else {
					$table_objet = table_objet_sql($_noisette['objet']);
					$id_table_objet = id_table_objet($_noisette['objet']);
					$where = array($id_table_objet. '=' . intval($_noisette['id_objet']));
					if (sql_countsel($table_objet, $where)) {
						$importation_noisettes_possible = true;
						break;
					}
				}
			}

			if ($importation_noisettes_possible) {
				$data_noisettes = array(
					'remplacer' => '<:noizetier:import_noisettes_remplacer:>',
					'ajouter'   => '<:noizetier:import_noisettes_ajouter:>'
				);
				$explication_noisettes = _T('noizetier:import_noisettes_explication');
			} else {
				// Aucune page commune entre la base et le fichier d'import.
				$explication_noisettes = _T('noizetier:import_noisettes_avertissement1');
			}
		} else {
			$explication_noisettes = _T('noizetier:import_noisettes_avertissement2');
		}

		$saisies = array(
			array(
				'saisie'  => 'fieldset',
				'options' => array(
					'nom'   => 'noizetier_export',
					'label' => '<:noizetier:noizetier:>',
					'icone' => 'noizetier-24.png',
				),
				'saisies' => array(
					array(
						'saisie'  => 'explication',
						'options' => array(
							'nom'   => 'noizetier_export_explication',
							'texte' => $texte_explication,
						),
					),
					array(
						'saisie'  => 'case',
						'options' => array(
							'nom'         => 'noizetier_import_config',
							'label'       => '<:noizetier:import_configuration_label:>',
							'label_case'  => '<:noizetier:import_configuration_labelcase:>',
							'explication' => $explication_config
						),
					),
					array(
						'saisie'  => 'case',
						'options' => array(
							'nom'         => 'noizetier_import_pages',
							'label'       => '<:noizetier:import_pages_explicites_label:>',
							'label_case'  => '<:noizetier:import_pages_explicites_labelcase:>',
							'explication' => $explication_pages_explicites,
							'disable'     => $disable_pages_explicites
						),
					),
					array(
						'saisie'  => 'radio',
						'options' => array(
							'nom'         => 'noizetier_import_compositions',
							'label'       => '<:noizetier:import_compositions_virtuelles_label:>',
							'datas'       => $data_compositions,
							'explication' => $explication_compositions
						),
					),
					array(
						'saisie'  => 'radio',
						'options' => array(
							'nom'         => 'noizetier_import_noisettes',
							'label'       => '<:noizetier:import_noisettes_label:>',
							'datas'       => $data_noisettes,
							'explication' => $explication_noisettes
						),
					),
				),
			),
		);
		$flux['data'] = array_merge($flux['data'], $saisies);
	}

	// Import de la configuration
	if (($action == 'import') and isset($flux['args']['config']['noizetier'])) {
		// On récupère les demandes d'importation.
		$importation['configuration'] = _request('noizetier_import_config');
		$importation['pages_explicites'] = _request('noizetier_import_pages');
		$importation['compositions_virtuelles'] = _request('noizetier_import_compositions');
		$importation['noisettes'] = _request('noizetier_import_noisettes');

		// Si au moins l'une est requise on appelle la fonction d'import.
		if ($importation['configuration']
		or $importation['pages_explicites']
		or $importation['compositions_virtuelles']
		or $importation['noisettes']) {
			if (!noizetier_ieconfig_importer($importation, $flux['args']['config']['noizetier'])) {
				$flux['data'] .= _T('noizetier:ieconfig_probleme_import_config').'<br />';
			}
		}
	}

	return $flux;
}


// --------------------------------------------------------------------
// ------------------------- API IMPORT/EXPORT ------------------------
// --------------------------------------------------------------------

/**
 * Retourne le tableau d'export du noiZetier contenant toujours sa configuration, les blocs exclus des pages
 * explicites, la description complète des compositions virtuelles et la description complète des noisettes.
 *
 * @return array
 *         Tableau d'export pour le pipeline ieconfig_exporter.
 **/
function noizetier_ieconfig_exporter() {

	$export = array();

	// Insérer une en-tête qui permet de connaitre la version du noiZetier utilisé lors de l'export
	$informer_plugin = chercher_filtre('info_plugin');
	$export['version'] = $informer_plugin('noizetier', 'version', true);
	$export['schema'] = $informer_plugin('noizetier', 'schema');
	$export['contenu'] = array();

	// Exportation de la configuration du plugin rangée dans la meta noizetier.
	// Etant donné que l'on utilise ce pipeline pour les données de production du noiZetier, on exporte aussi
	// sa configuration via ce pipeline et non via le pipeline ieconfig_metas.
	include_spip('inc/config');
	$export['configuration'] = lire_config('noizetier', array());
	$export['contenu']['configuration'] = $export['configuration'] ? 'on' : '';

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

	// Exportation de la tables spip_noisettes qui contient les noisettes associées aux pages explicites,
	// aux compositions virtuelles et à certains objets précis.
	// -- on supprime l'id_noisette de chaque noisette car il sera recréé lors de l'import.
	include_spip('ncore_fonctions');
	$export['noisettes'] = noisette_repertorier('noizetier', array(), 'id_noisette');
	foreach($export['noisettes'] as $_id => $_noisette) {
		unset($export['noisettes'][$_id]['id_noisette']);
	}
	$export['contenu']['noisettes'] = $export['noisettes'] ? 'on' : '';

	// Appel d'un pipeline propre à l'export du noiZetier pour autoriser la modification par des plugins
	// de la structure d'export
	$export = pipeline('noizetier_config_export', $export);

	return $export;
}

/**
 * Importe tout ou partie d'un fichier d'export ieconfig contenant les données du noiZetier.
 *
 * @param array $importation
 *        Tableau associatif des demandes d'importation issues du formulaire ieconfig. Les index et les valeurs
 *        possibles sont :
 *        - `configuration` : vaut `on` pour importer ou null sinon
 *        - `pages_explicites` : vaut `on` pour importer ou null sinon
 *        - `compositions_virtuelles` : vaut `remplacer`, `ajouter` ou `fusionner` pour importer ou null sinon.
 *        - `noisettes` : vaut `remplacer` ou `ajouter` pour importer ou null sinon.
 * @param array $contenu_import
 *        Tableau des données du noiZetier issues du fichier d'import.
 *
 * @return bool
 */
function noizetier_ieconfig_importer($importation, $contenu_import) {

	// Initialisation de la sortie
	$retour = true;

	// On appelle le pipeline pour éventuellement modifier le contenu à importer.
	$contenu_import = pipeline('noizetier_config_import', $contenu_import);

	// La configuration
	if ($importation['configuration']) {
		// On remplace la configuration actuelle par celle du fichier d'import.
		include_spip('inc/config');
		ecrire_config('noizetier', $contenu_import['configuration']);
	}

	// Les pages explicites
	if ($importation['pages_explicites']) {
		// On fusionne les blocs exclus de la configuration avec ceux des pages explicites de la base.
		// -- On récupère toutes les pages de la base avec leur blocs exclus
		$select = array('page', 'blocs_exclus');
		$where = array('est_virtuelle=' . sql_quote('non'));
		$pages_explicites_base = sql_allfetsel($select,'spip_noizetier_pages', $where);
		// -- on structure les blocs exclus du fichier d'import sous la forme [page] = blocs exclus
		$blocs_exclus_import = array_column($contenu_import['pages_explicites'], 'blocs_exclus', 'page');
		// -- on compare les pages de la base et celles de l'import et on met à jour systématiquement
		//    les pages communes (même identifiant).
		foreach ($pages_explicites_base as $_page_explicite) {
			if (isset($blocs_exclus_import[$_page_explicite['page']])) {
				// Remplacement des blocs exclus de la page actuelle par ceux du fichier d'import.
				$modification = array('blocs_exclus' => $blocs_exclus_import[$_page_explicite['page']]);
				$where = array('page=' . sql_quote($_page_explicite['page']));
				sql_updateq('spip_noizetier_pages', $modification, $where);
			}
		}
	}

	// Les compositions virtuelles
	if ($importation['compositions_virtuelles']) {
		if ($importation['compositions_virtuelles'] == 'remplacer') {
			// On vide d'abord la table spip_noizetier_pages de toutes le compositions virtuelles du noiZetier.
			$where = array('est_virtuelle=' . sql_quote('oui'));
			if (!sql_delete('spip_noizetier_pages', $where)) {
				$retour = false;
			}
		}

		if ($retour) {
			// On collecte les compositions virtuelles actuellement en base.
			$select = array('page');
			$where = array('est_virtuelle=' . sql_quote('oui'));
			$compositions_base = sql_allfetsel($select, 'spip_noizetier_pages', $where);
			if ($compositions_base) {
				$compositions_base = array_map('reset', $compositions_base);
			}

			// Suivant le mode d'importation et l'existence ou pas de la composition en base on ajoute ou
			// on met à jour la composition virtuelle ou on ne fait rien.
			foreach ($contenu_import['compositions_virtuelles'] as $_composition) {
				if (in_array($_composition['page'], $compositions_base)) {
					if ($importation['compositions_virtuelles'] == 'fusionner') {
						$where = 'page=' . sql_quote($_composition['page']);
						$modifications = $_composition;
						unset($modifications['page']);
						sql_updateq('spip_noizetier_pages', $modifications, $where);
					}
				} else {
					sql_insertq('spip_noizetier_pages', $_composition);
				}
			}
		}
	}

	// Les noisettes
	$importation['noisettes'] = array();
	if ($importation['noisettes']) {
		if ($importation['noisettes'] == 'remplacer') {
			// On vide d'abord la table spip_noisettes de toutes les noisettes du noiZetier.
			$where = array('plugin=' . sql_quote('noizetier'));
			if (!sql_delete('spip_noisettes', $where)) {
				$retour = false;
			}
		}

		if ($retour) {
			// Liste des pages génériques disponibles dans la base.
			$pages_base = sql_allfetsel('page','spip_noizetier_pages');
			$pages_base = array_map('reset', $pages_base);

			$champs_modifiables = array_flip(array('parametres', 'encapsulation', 'css'));
			foreach ($contenu_import['noisettes'] as $_noisette) {
				$page_noisette = $_noisette['type']
					? ($_noisette['composition'] ? $_noisette['type'] . '-' . $_noisette['composition'] : $_noisette['type'])
					: $_noisette['objet'] . $_noisette['id_objet'];
				if (!$_noisette['objet'] and in_array($page_noisette, $pages_base)) {
					// La noisette à importer est bien associée à une page présente dans la base.
					// On la rajoute en fin du bloc concerné.
					$id_noisette = noisette_ajouter(
						'noizetier',
						$_noisette['type_noisette'],
						unserialize($_noisette['conteneur']),
						0);
					// La noisette a été ajoutée de façon générique. Pour terminer l'importation il faut aussi
					// mettre à jour les données paramétrables : parametres, encapsulation et css.
					if ($id_noisette) {
						$modifications = array_intersect_key($_noisette, $champs_modifiables);
						noisette_parametrer('noizetier', $id_noisette, $modifications);
					}
				}
			}
		}
	}

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur('noizetier-import-config');

	return $retour;
}
