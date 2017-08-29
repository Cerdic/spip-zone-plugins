<?php
/**
 * Utilisations de pipelines par Formulaire upload html5
 *
 * @plugin	   Formulaire upload html5
 * @copyright  2014
 * @author	   Phenix
 * @licence	   GNU/GPL
 * @package	   SPIP\Uploadhtml5\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function uploadhtml5_jquery_plugins($scripts) {
	include_spip('inc/config');
	$config = lire_config('uploadhtml5');
	if ((isset($config['charger_public']) and $config['charger_public']) // Si on doit charger dans l'espace publique
		or test_espace_prive() // Ou que l'on est dans l'espace privé
	) {
		$scripts[] = 'lib/dropzone/dropzone.js'; // Charger Dropzone
	}

	return $scripts;
}

function uploadhtml5_insert_head_css($flux) {
	include_spip('inc/config');
	$config = lire_config('uploadhtml5');
	if ((isset($config['charger_public']) and $config['charger_public']) // Si on doit charger dans l'espace publique
		or test_espace_prive() // Ou que l'on est dans l'espace privé
	) {
		$flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';
	}

	return $flux;
}

function uploadhtml5_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

	$flux .= '<link rel="stylesheet" href="'.find_in_path('prive/css/dropzone_prive.css').'" type="text/css" media="screen" />';

	return $flux;
}

function uploadhtml5_formulaire_fond($flux) {
	include_spip('inc/config');
	$config = lire_config('uploadhtml5');

	// Simplification de variable
	$objet = isset($flux['args']['contexte']['objet']) ? $flux['args']['contexte']['objet'] : '';
	$id_objet = isset($flux['args']['contexte']['id_objet']) ? $flux['args']['contexte']['id_objet'] : 0;

	if ($flux['args']['form'] == 'joindre_document') {

		/**
		 * Si on est pas sur l'espace privé et que les scripts
		 * n'ont pas été charger sur l'espace public,
		 * on ne fait rien au formulaire
		 */
		if (!test_espace_prive() and !$config['charger_public']) {
			return $flux;
		}

		// Récupérer le formulaire d'upload en html5 et lui passer une partie du contexte de joindre_document
		$uploadhtml5 = recuperer_fond(
			'prive/squelettes/inclure/uploadhtml5',
			array(
				'type' => $objet,
				'id' => $id_objet
			)
		);

		// Injecter uloadhtml5 au dessus du formulaire joindre_document.
		$flux['data'] = $uploadhtml5.$flux['data'];
	} elseif ($flux['args']['form'] == 'editer_logo') {

		/**
		 * Si on est pas sur l'espace privé et que les scripts
		 * n'ont pas été charger sur l'espace public,
		 * on ne fait rien au formulaire
		 */
		if (!test_espace_prive() and !$config['charger_public']) {
			return $flux;
		}

		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if (!$chercher_logo($id_objet, id_table_objet($objet))) {
			// Bloc ajax par défaut
			$ajaxReload = 'navigation';

			// Cas spécial: si on édite le logo du site, il faut recharger le contenu et non la navigation
			if ($id_objet == 0 and $objet == 'site') {
				$ajaxReload = 'contenu';
			}

			// Récupérer le formulaire d'upload en html5 et lui passer une partie du contexte
			$uploadhtml5 = recuperer_fond(
				'prive/squelettes/inclure/uploadhtml5_logo',
				array(
					'type' => $objet,
					'id' => $id_objet,
					'ajaxReload' => $ajaxReload
				)
			);

			$config = lire_config('uploadhtml5');
			// Injecter uloadhtml5 au dessus du formulaire joindre_document.
			if (isset($config['remplacer_editer_logo'])
				and $config['remplacer_editer_logo']) {
				$flux['data'] = $uploadhtml5;
			} else {
				$flux['data'] = $uploadhtml5.$flux['data'];
			}
		}
	}

	return $flux;
}

/*
 * function uploadhtml5_ieconfig_metas
 * Pipeline ieconfig pour l'import/export des metas du plugin uploadhtml5
 *
 * @param array $table
 * @return array
 *
 */
function uploadhtml5_ieconfig_metas($table) {
	$table['uploadhtml5_meta']['titre'] = _T('paquet-uploadhtml5:uploadhtml5_nom');
	$table['uploadhtml5_meta']['icone'] = 'prive/themes/spip/images/uploadhtml5-16.png';
	$table['uploadhtml5_meta']['metas_serialize'] = 'uploadhtml5';
	return $table;
}
