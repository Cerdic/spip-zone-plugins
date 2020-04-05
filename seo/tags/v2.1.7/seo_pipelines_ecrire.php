<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP\SEO\Pipelines
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function seo_metas_editable($objet) {
	include_spip('inc/config');
	if (lire_config('seo/meta_tags/activate', 'off') == 'yes'
		&& lire_config('seo/meta_tags/activate_editing', 'off') == 'yes') {
		$table_sql = table_objet_sql($objet);
		if (in_array($table_sql, lire_config('seo/meta_tags/editable_tables', array('spip_articles', 'spip_rubriques')))) {
			return true;
		}
	}
	return false;
}
/**
 * Insertion dans le pipeline afficher_contenu_objet (SPIP)
 *
 * Afficher les meta-tags en bas du contenu de l'objet
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifé
 */
function seo_afficher_contenu_objet($flux) {
	$flux['data'] .= recuperer_fond('prive/objets/seo-metas', array('objet' => $flux['args']['type'], 'id_objet' => $flux['args']['id_objet']));
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger
 *
 * Charger les valeurs des meta-tags pour la saisie dans l'objet
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function seo_formulaire_charger($flux) {
	if (strncmp($flux['args']['form'], 'editer_', 7) == 0
		and $objet = substr($flux['args']['form'], 7)
		and seo_metas_editable($objet)) {
		$valeurs = array(
			'meta_title'=>'',
			'meta_description'=>'',
			'meta_keywords'=>'',
			'meta_copyright'=>'',
			'meta_author'=>'',
			'meta_robots'=>'',
		);
		if ($id_objet = intval($flux['args']['args'][0])) {
			$metas = sql_select('*', 'spip_seo', 'id_objet ='.intval($id_objet).' AND objet ='.sql_quote($objet));
			while ($meta = sql_fetch($metas)) {
				$valeurs['meta_'.$meta['meta_name']] = $meta['meta_content'];
			}
		}
		$flux['data'] = array_merge($flux['data'], $valeurs);
	}
	return $flux;
}

/**
 * Enregistrer les valeurs des meta-tags apres la saisie dans l'objet
 * @param array $flux
 * @return array
 */
function seo_formulaire_traiter($flux) {
	if (strncmp($flux['args']['form'], 'editer_', 7) == 0
		and $objet = substr($flux['args']['form'], 7)
		and _request('seo_metas')
		and $id_table_objet=id_table_objet($objet)
		and isset($flux['data'][$id_table_objet])
		and $id_objet=$flux['data'][$id_table_objet]) {
		$editer_seo = charger_fonction('editer_seo', 'action');
		$err = $editer_seo($objet, $id_objet, 'meta_');

		if ($err) {
			if (!isset($flux['data']['message_erreur'])) {
				$flux['data']['message_erreur'] = '';
			}
			$flux['data']['message_erreur'] .= ' ' ._L('Vous n\'avez pas le droit de modifier les meta-tags : '.$err);
			if (isset($flux['data']['redirect'])) {
				unset($flux['data']['redirect']);
			}
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline formulaire_fond (SPIP)
 *
 * Ajouter la saisie des meta-tags dans le form de saisie de l'objet
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function seo_formulaire_fond($flux) {

	if (isset($flux['args']['args']['type'])
		and $objet = $flux['args']['args']['type']
		and $flux['args']['form']=="editer_$objet"
		and seo_metas_editable($objet)) {
		$ins = recuperer_fond('formulaires/inc-editer-seo', $flux['args']['contexte']);
		if ($p=strpos($flux['data'], $i = '<!--extra-->')
			or $p=strrpos($flux['data'], $i = '</ul>')) {
			$p = $p + strlen($i);
			$flux['data'] = substr_replace($flux['data'], $ins, $p, 0);
		}
	}
	return $flux;
}
