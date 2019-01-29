<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Pipeline ieconfig pour l'import/export des données de configuration du plugin et de certaines données de production.
 *
 * @param array $flux
 *
 * @return array
 */
function taxonomie_ieconfig($flux) {

	// On détermine l'action demandée qui peut être : afficher le formulaire d'export ou d'import, construire le
	// tableau d'export ou exécuter l'importation.
	$action = $flux['args']['action'];

	if ($action == 'form_export') {
		// Construire le formulaire d'export :
		// -- on demande le minimum à savoir si l'utilisateur veut inclure dans son export l'ensemble des données
		//    de Taxonomie.
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom'   => 'taxonomie_fieldset',
					'label' => '<:taxonomie:titre_page_taxonomie:>',
					'icone' => 'taxon-16.png',
				),
				'saisies' => array(
					array(
						'saisie' => 'oui_non',
						'options' => array(
							'nom' => 'taxonomie_export_option',
							'label' => '<:taxonomie:export_option:>',
							'explication' => '<:taxonomie:export_explication:>',
							'defaut' => '',
						),
					),
				),
			),
		);
		$flux['data'] = array_merge($flux['data'], $saisies);

	} elseif (($action == 'export') and (_request('taxonomie_export_option') == 'on')) {
		// Générer le tableau d'export
		$flux['data']['taxonomie'] = taxonomie_ieconfig_exporter();

	} elseif (($action == 'form_import') and isset($flux['args']['config']['taxonomie'])) {
		// Construire le formulaire d'import :
		// On affiche la version de Taxonomie et le schéma de base de données avec lesquels le fichier d'import
		// à été créé.
		$import = $flux['args']['config']['taxonomie'];
		$texte_explication = _T(
			'taxonomie:import_resume',
			array('version' => $import['version'], 'schema' => $import['schema']));

		// La configuration : une case suffit car on applique toujours un remplacement et la configuration est
		// toujours présente dans un export.
		$informer_plugin = chercher_filtre('info_plugin');
		$version = $informer_plugin('taxonomie', 'version', true);
		$schema = $informer_plugin('taxonomie', 'schema');
		$plugin = $informer_plugin('taxonomie', 'nom');
		if ($schema == $import['schema']) {
			$explication_config = _T(
				'taxonomie:import_configuration_explication',
				array('version' => $version, 'schema' => $schema));
		} else {
			$explication_config = _T(
				'taxonomie:import_configuration_avertissement',
				array('version' => $version, 'schema' => $schema));
		}

		$saisies = array(
			array(
				'saisie'  => 'fieldset',
				'options' => array(
					'nom'   => 'taxonomie_export',
					'label' => $plugin,
					'icone' => 'taxonomie-24.png',
				),
				'saisies' => array(
					array(
						'saisie'  => 'explication',
						'options' => array(
							'nom'   => 'taxonomie_export_explication',
							'texte' => $texte_explication,
						),
					),
					array(
						'saisie'  => 'case',
						'options' => array(
							'nom'         => 'taxonomie_import_config',
							'label'       => '<:taxonomie:import_configuration_label:>',
							'label_case'  => '<:taxonomie:import_configuration_labelcase:>',
							'explication' => $explication_config
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
		$importation['configuration'] = _request('taxonomie_import_config');
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
 * Retourne le tableau d'export du plugin Taxonomie contenant toujours sa configuration et les taxons nécessitant d'être
 * sauvegardés car non créés via les fichiers ITIS.
 * Les taxons concernés sont :
 * - les taxons du règne au genre, importés via les fichiers ITIS puis édités manuellement;
 * - les taxons ascendants d'une espèce (entre le genre et l'espèce non compris), non inclus dans un fichier ITIS
 *   et insérés lors de la création d'une espèce;
 * - les taxons de type espèce et descendants créés manuellement.
 *
 * @return array
 *         Tableau d'export pour le pipeline ieconfig_exporter.
 **/
function taxonomie_ieconfig_exporter() {

	$export = array();

	// Insérer une en-tête qui permet de connaitre la version du plugin Taxonomie utilisé lors de l'export
	$informer_plugin = chercher_filtre('info_plugin');
	$export['version'] = $informer_plugin('taxonomie', 'version', true);
	$export['schema'] = $informer_plugin('taxonomie', 'schema');
	$export['contenu'] = array();

	// Exportation de la configuration du plugin rangée dans la meta taxonomie uniquement.
	// Etant donné que l'on utilise ce pipeline pour les données de production de Taxonomie, on exporte aussi
	// sa configuration via ce pipeline et non via le pipeline ieconfig_metas.
	include_spip('inc/config');
	$export['configuration'] = lire_config('taxonomie');
	$export['contenu']['configuration'] = $export['configuration'] ? 'on' : '';

	// Les metas de chargement de chaque règne ne sont pas exportées mais on identifie quand même la liste des règnes
	// insérés dans la base. Les taxons seront ensuite exportés par règne pour permettre un import plus ciblé.
	include_spip('taxonomie_fonctions');
	include_spip('inc/taxonomie');
	$export['regnes'] = array();
	$regnes = regne_lister();
	foreach ($regnes as $_regne) {
		if (regne_existe($_regne, $meta_regne)) {
			$export['regnes'][] = $_regne;
		}
	}
	$export['contenu']['regnes'] = $export['regnes'] ? 'on' : '';

	// Exportation de la table spip_taxons des taxons nécessitant d'être sauvegardés.
	if ($export['contenu']['regnes']) {
		// Récupération de la description de la table spip_taxons afin de connaitre la liste des colonnes.
		include_spip('base/objets');
		$from ='spip_taxons';
		$description_table = lister_tables_objets_sql($from);
		$select = array_diff(array_keys($description_table['field']), array('id_taxon', 'maj'));

		// Pour faciliter l'import et aussi mieux le cibler les taxons exportés sont rangés par règne (index au nom
		// du règne). Ensuite, on sépare aussi les taxons édités (index [taxons][edites]), les taxons créés en tant
		// qu'ascendant d'une espèce (index [taxons][crees]) et les espèces créées manuellement (index [especes]).
		foreach ($export['regnes'] as $_regne) {
			// Extraction des taxons du règne au genre édités manuellement par les utilisateurs ou créés lors d'un
			// ajout d'espèce.
			// On sauvegarde les champs éditables uniquement des édités et tous les champs pour les autres.
			$export[$_regne]['taxons'] = taxon_preserver($_regne);
			$export['contenu']['taxons']['edites'][$_regne] = $export[$_regne]['taxons']['edites'] ? 'on' : '';
			$export['contenu']['taxons']['crees'][$_regne] = $export[$_regne]['taxons']['crees'] ? 'on' : '';

			// Extraction des espèces et descendants.
			$export[$_regne]['especes'] = array();
			$where = array(
				'regne=' . sql_quote($_regne),
				'importe=' . sql_quote('non'),
				'espece=' . sql_quote('oui')
			);
			$export[$_regne]['especes'] = sql_allfetsel($select, $from, $where);
			$export['contenu']['especes'][$_regne] = $export[$_regne]['especes'] ? 'on' : '';
		}
	}

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
function taxonomie_ieconfig_importer($importation, $contenu_import) {

	// Initialisation de la sortie
	$retour = true;

	// On appelle le pipeline pour éventuellement modifier le contenu à importer.
	$contenu_import = pipeline('noizetier_config_import', $contenu_import);

	// On récupère la liste des blocs par défaut des pages du site pour filtrer des blocs non autorisés
	// provenant éventuellement de l'import. Cette liste sert pour les pages explicites et les compositions virtuelles.
	include_spip('inc/noizetier_bloc');
	$blocs_defaut = noizetier_bloc_lister_defaut();

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
				// Remplacement des blocs exclus de la page actuelle par ceux du fichier d'import. On filtre
				// les blocs éventuellement non autorisés sur le site.
				$modification = array(
					'blocs_exclus' => array_intersect($blocs_exclus_import[$_page_explicite['page']], $blocs_defaut)
				);
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
				// On filtre les blocs exclus avec la liste des blocs par défaut du site.
				$composition = $_composition;
				$composition['blocs_exclus'] = array_intersect($composition['blocs_exclus'], $blocs_defaut);

				// On détermine l'opération à faire ou pas.
				if (in_array($composition['page'], $compositions_base)) {
					if ($importation['compositions_virtuelles'] == 'fusionner') {
						$where = 'page=' . sql_quote($composition['page']);
						unset($composition['page']);
						sql_updateq('spip_noizetier_pages', $composition, $where);
					}
				} else {
					sql_insertq('spip_noizetier_pages', $composition);
				}
			}
		}
	}

	// Les noisettes
	if ($importation['noisettes']) {
		if ($importation['noisettes'] == 'remplacer') {
			// On vide d'abord la table spip_noisettes de toutes les noisettes du noiZetier.
			$where = array('plugin=' . sql_quote('noizetier'));
			if (sql_delete('spip_noisettes', $where) === false) {
				$retour = false;
			}
		}

		if ($retour) {
			// Liste des pages génériques disponibles dans la base.
			$pages_base = sql_allfetsel('page','spip_noizetier_pages');
			$pages_base = array_map('reset', $pages_base);

			// Nombre de noisettes par conteneur. On récupère l'ensemble des conteneurs y compris les noisettes
			// conteneur mais seuls les blocs Z du noiZetier sont utiles.
			$select = array('id_conteneur', 'count(*) as nb_noisettes');
			$where = array('plugin=' . sql_quote('noizetier'));
			$group_by = array('id_conteneur');
			$nb_noisettes_base = sql_allfetsel($select, 'spip_noisettes', $where, $group_by);
			if ($nb_noisettes_base) {
				$nb_noisettes_base = array_column($nb_noisettes_base, 'nb_noisettes', 'id_conteneur');
			}

			// On insère les noisettes du fichier d'import appartenant à des pages ou des objets disponibles dans la
			// base. Dans le fichier d'export, les noisettes conteneur sont classées avant les autres noisettes et
			// suivant une profondeur croissante de façon à être créées quand les noisettes imbriquées le nécessiteront.
			// Cette opération se fait en deux passes pour gérer le fait que les noisettes conteneur vont
			// changer d'id ce qui change leur identifiant de conteneur :
			// - Passe 1 : si la noisette est à insérer on l'ajoute dans le conteneur sans se préoccuper du changement
			//             d'id de conteneur pour les noisettes conteneur. On stocke toutes les informations nécessaires
			//             à la passe 2 comme le nouvel id des noisettes conteneur.
			include_spip('base/objets');
			include_spip('inc/ncore_conteneur');
			include_spip('inc/ncore_noisette');
			$noisettes_conteneur = $noisettes_imbriquees = array();
			foreach ($contenu_import['noisettes'] as $_id_noisette_ancien => $_noisette) {
				// On vérifie qu'il faut bien importer la noisette
				$noisette_a_importer = false;
				if ($_noisette['type']) {
					$page_import = $_noisette['composition']
						? $_noisette['type'] . '-' . $_noisette['composition']
						: $_noisette['type'];
					if (in_array($page_import, $pages_base)) {
						$noisette_a_importer = true;
					}
				} else {
					$table_objet = table_objet_sql($_noisette['objet']);
					$id_table_objet = id_table_objet($_noisette['objet']);
					$where = array($id_table_objet. '=' . intval($_noisette['id_objet']));
					if (sql_countsel($table_objet, $where)) {
						$noisette_a_importer = true;
					}
				}

				if ($noisette_a_importer) {
					// La noisette à importer est bien associée à une page ou un objet de la base.
					// Les noisettes ne sont pas triées dans l'ordre d'insertion pour un conteneur donné,
					// il faut donc se baser sur le rang dans le fichier d'import. Pour une noisette appartenant à un
					// conteneur noisette on reprend le rang tel que mais pour une noisette incluse dans un bloc Z il
					// faut recalculer le rang en tenant compte des noisettes déjà incluses dans la base.
					$rang = $_noisette['rang_noisette'];
					$conteneur = unserialize($_noisette['conteneur']);
					$conteneur_est_noisette = conteneur_est_noisette('noizetier', $conteneur);
					if (!$conteneur_est_noisette) {
						$rang_max = !empty($nb_noisettes_base[$_noisette['id_conteneur']])
							? $nb_noisettes_base[$_noisette['id_conteneur']]
							: 0;
						$rang += $rang_max;
					}
					$id_noisette_nouveau = noisette_ajouter(
						'noizetier',
						$_noisette['type_noisette'],
						$conteneur,
						$rang);
					// La noisette a été ajoutée de façon générique (paramètres par défaut). Pour finaliser l'importation
					// il faut aussi mettre à jour les données paramétrables : parametres, encapsulation et css.
					if ($id_noisette_nouveau) {
						$champs_modifiables = array('parametres');
						if ($_noisette['est_conteneur'] != 'oui') {
							$champs_modifiables = array_merge($champs_modifiables, array('parametres', 'encapsulation', 'css'));
						}
						$modifications = array_intersect_key($_noisette, array_flip($champs_modifiables));
						noisette_parametrer('noizetier', $id_noisette_nouveau, $modifications);
					}

					// Pour conclure il faut stocker les informations nécessaires à la passe suivante:
					// - les noisettes imbriquées dans un conteneur noisette et les référence de ce conteneur
					// - la nouvelle valeur de l'id_noisette des noisettes conteneur
					if ($conteneur_est_noisette) {
						// Il faut se rappeler de la noisette car il faudra changer son conteneur (2 champs) lors de la
						// deuxième passe. On stocke
						$noisettes_imbriquees[$id_noisette_nouveau] = array(
							'type_noisette' => $conteneur['type_noisette'],
							'id_noisette' => $conteneur['id_noisette']
						);
					}
					if ($_noisette['est_conteneur'] == 'oui') {
						// La noisette est un conteneur. On constitue un tableau permettant de calculer son nouvel
						// identifiant induit par son nouvel id de noisette. Le tableau est indexé par son ancien id.
						$noisettes_conteneur[$_id_noisette_ancien] = $id_noisette_nouveau;
					}
				}
			}

			// - Passe 2 : On reprend les noisettes venant d'être insérées dans une noisette conteneur et
			//             on rétablit le bon conteneur (id et tableau sérialisé), la profondeur et les informations
			//             du bloc Z accueillant les noisettes.
			if ($noisettes_imbriquees) {
				foreach ($noisettes_imbriquees as $_id_noisette_nouveau => $_conteneur_ancien) {
					// Détermination du conteneur
					$nouveau_conteneur = $_conteneur_ancien;
					$nouveau_conteneur['id_noisette'] = $noisettes_conteneur[$_conteneur_ancien['id_noisette']];

					// Détermination de la profondeur et des caractéristiques du bloc Z de plus haut niveau.
					// Le conteneur est une noisette, qui a été insérée précédemment, on la lit.
					$select = array('type', 'composition', 'objet',	'id_objet',	'bloc', 'profondeur');
					$where = array(
						'id_noisette=' . intval($nouveau_conteneur['id_noisette']),
						'plugin=' . sql_quote('noizetier')
					);
					$modifications = sql_fetsel($select, 'spip_noisettes', $where);

					// On finalise les modifications
					$modifications['profondeur'] += 1;
					$modifications['conteneur'] = serialize($nouveau_conteneur);
					$modifications['id_conteneur'] = conteneur_identifier('noizetier', $nouveau_conteneur);

					// On met à jour le contenu de la noisette en base.
					$where = array('plugin=' . sql_quote('noizetier'), 'id_noisette=' . intval($_id_noisette_nouveau));
					sql_updateq('spip_noisettes', $modifications, $where);
				}
			}
		}
	}

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur('noizetier-import-config');

	return $retour;
}
