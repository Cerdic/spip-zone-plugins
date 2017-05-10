<?php
/**
 * Utilisations de pipelines par Massicot
 *
 * @plugin	   Massicot
 * @copyright  2015
 * @author	   Michel @ Vertige ASBL
 * @licence	   GNU/GPL
 * @package	   SPIP\Massicot\Pipelines
 */

/**
 * Insérer le plugin jquery de selection du cadre
 *
 * @pipeline jquery_plugins
 * @param  array $scripts  Les scripts qui seront insérés dans la page
 * @return array	   La liste des scripts complétée
 */
function massicot_jquery_plugins($scripts) {

	if (test_espace_prive()) {
		$scripts[] = 'lib/jquery.imgareaselect.js/jquery.imgareaselect.dev.js';
		$scripts[] = 'javascripts/formulaireMassicoterImage.js';
	}

	return $scripts;
}

/**
 * Ajoute le plugins jqueryui Slider
 *
 * @pipeline jqueryui_plugins
 * @param  array $scripts  Plugins jqueryui à charger
 * @return array	   Liste des plugins jquerui complétée
 */
function massicot_jqueryui_plugins($scripts) {

	if (version_compare($GLOBALS['spip_version_branche'], '3.2', '<') and test_espace_prive()) {
		$scripts[] = 'jquery.ui.slider';
	}
	return $scripts;
}

/**
 * Ajouter un brin de CSS
 *
 * @pipeline header_prive
 * @param  array $flux Données du pipeline
 * @return array	   Données du pipeline
 */
function massicot_header_prive($flux) {
	if (test_espace_prive()) {
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' .
			  find_in_path('css/massicot.css') . '" />';

		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' .
			  find_in_path('lib/jquery.imgareaselect.js/distfiles/css/imgareaselect-default.css') . '" />';
	}
	return $flux;
}

/**
 * Ajouter une action "recadrer" sur les documents
 *
 * @pipeline editer_document_actions
 * @param  array $flux Données du pipeline
 * @return array	   Données du pipeline
 */
function massicot_document_desc_actions($flux) {

	$flux['data'] .= recuperer_fond(
		'prive/squelettes/inclure/lien_recadre',
		$flux['args']
	);

	return $flux;
}

/**
 * Supprimer les traitements lorsqu'on remplace l'image d'un document
 *
 * @pipeline post_edition
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_post_edition($flux) {

	if (($flux['args']['type'] === 'document')
	    and isset($flux['data']['fichier'])) {
		include_spip('base/abstract_sql');
		include_spip('action/editer_liens');

		$id_document = $flux['args']['id_objet'];

		$massicotages = objet_trouver_liens(
			array('massicotage' => '*'),
			array('document' => $id_document)
		);

		$id_massicotages = array();

		foreach ($massicotages as $cle => $valeur) {
			$id_massicotages[] = $valeur['id_massicotage'];
		}

		sql_delete(
			'spip_massicotages',
			sql_in('id_massicotage', $id_massicotages)
		);
		sql_delete(
			'spip_massicotages_liens',
			sql_in('id_massicotage', $id_massicotages)
		);
	}

	return $flux;
}

/**
 * Supprimer les traitements lorsqu'on supprime un logo
 *
 * @pipeline formulaire_traiter
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_formulaire_traiter($flux) {

	if (($flux['args']['form'] === 'editer_logo')
	    and (_request('supprimer_logo_on'))) {
		include_spip('base/abstract_sql');
		include_spip('action/editer_liens');

		$objet = $flux['args']['args'][0];
		$id_objet = $flux['args']['args'][1];

		$massicotages = objet_trouver_liens(
			array('massicotage' => '*'),
			array($objet => $id_objet)
		);

		$id_massicotages = array();

		foreach ($massicotages as $cle => $valeur) {
			$id_massicotages[] = $valeur['id_massicotage'];
		}

		sql_delete(
			'spip_massicotages',
			sql_in('id_massicotage', $id_massicotages)
		);
		sql_delete(
			'spip_massicotages_liens',
			sql_in('id_massicotage', $id_massicotages)
		);
	}

	return $flux;
}

/**
 * Ajouter un lien pour recadrer les vignettes des documents
 *
 * @pipeline editer_contenu_objet
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_editer_contenu_objet($flux) {

	$html = $flux['data'];
	$args = $flux['args'];

	if ($args['type'] === 'illustrer_document') {
		include_spip('base/abstract_sql');

		if ($id_vignette = sql_getfetsel(
			'id_vignette',
			'spip_documents',
			'id_document='.intval($args['id'])
		)) {
			$href = generer_url_ecrire(
				'massicoter_image',
				'objet=document&id_objet=' . $id_vignette . '&redirect=' . urlencode(self())
			);
			$lien = '<a href="' . $href . '"><strong>' . _T('massicot:massicoter') . '</strong></a>';

			$repere = '<span class=\'image_loading\'>';
			$flux['data'] = str_replace($repere, $lien . $repere, $html);
		}
	}

	return $flux;
}

/**
 * Appliquer le recadrage sur l'image affichée dans le formulaire illustrer_document
 *
 * @pipeline editer_contenu_objet
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_formulaire_charger($flux) {

	if (($flux['args']['form'] === 'illustrer_document')
			and $id_vignette) {
		$parametres = massicot_get_parametres(
			'document',
			$flux['data']['id_vignette']
		);

		$flux['data']['vignette'] = massicoter_fichier(
			$flux['data']['vignette'],
			$parametres
		);
	}

	return $flux;
}
