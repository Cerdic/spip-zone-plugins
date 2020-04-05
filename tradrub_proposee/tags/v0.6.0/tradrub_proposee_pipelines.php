<?php 
/**
 * Pipelines du plugin tradrub_proposee
 *
 * @author kent1 <kent1@arscenic.info>
 * @package SPIP\Tradrub_proposee\Pipelines
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline formulaire_fond (SPIP)
 *
 * Sur les formulaires d'édition de rubriques et articles :
 * - Lors d'une traduction de l'objet (lier_trad dans l'environnement), proposer comme
 * rubrique de destination les rubriques "traduites" de la rubrique de la version originale
 *
 * @pipeline formulaire_fond
 * @param array $flux
 * @return array
 */
function tradrub_proposee_formulaire_fond($flux) {
	if (intval(_request('lier_trad')) > 0 and in_array($flux['args']['form'], array('editer_rubrique', 'editer_article'))) {
		$rub_parente = sql_fetsel('id_trad, id_secteur', 'spip_rubriques', 'id_rubrique='.intval($flux['args']['contexte']['id_parent']));
		if (intval($rub_parente['id_trad']) > 0) {
			$rub_traduites = sql_allfetsel('id_rubrique, id_secteur, titre, lang', 'spip_rubriques', 'id_trad='.intval($rub_parente['id_trad']));
			if (count($rub_traduites) > 1) {
				$texte = recuperer_fond('prive/squelettes/inclure/rubriques_traductions', array('traductions' => $rub_traduites, 'id_parent' => $flux['args']['contexte']['id_parent'], 'id_secteur' => $rub_parente['id_secteur']));
				$flux['data'] = preg_replace(
					",(<label [^>]*for=[\"']id_parent.*<\/label>),Uims",
					'$1'.$texte,
					$flux['data'],
					1
				);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 *
 * Sur les formulaires d'édition de rubriques et articles, afficher une erreur en fonction de la configuration :
 * - si une traduction est faite dans la même rubrique que l'originale
 * - si une traduction est faite dans le même secteur que l'originale
 *
 * @pipeline formulaire_verifier
 * @param array $flux
 * @return array
 */
function tradrub_proposee_formulaire_verifier($flux) {
	if (!isset($flux['data']['id_parent']) && intval(_request('lier_trad')) > 0 and in_array($flux['args']['form'], array('editer_rubrique', 'editer_article'))) {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		if ($flux['args']['form'] == 'editer_rubrique') {
			$infos = sql_fetsel('id_parent, id_secteur', 'spip_rubriques', 'id_rubrique = '._request('lier_trad'));
		} else {
			$infos = sql_fetsel('id_rubrique as id_parent, id_secteur', 'spip_articles', 'id_article = '._request('lier_trad'));
		}
		if (lire_config('tradrub_proposee/interdit_meme_rubrique') == 'on' 
			&& (intval(_request('id_parent')) == $infos['id_parent'])) {
			$flux['data']['id_parent'] = _T('tradrub_proposee:erreur_interdit_meme_rubrique');
		}
		if (!isset($flux['data']['id_parent']) 
			&& lire_config('tradrub_proposee/interdit_meme_secteur') == 'on' 
			&& (sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique='.intval(_request('id_parent'))) == $infos['id_secteur'])) {
			$flux['data']['id_parent'] = _T('tradrub_proposee:erreur_interdit_meme_secteur');
		}
	}
	return $flux;
}
