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
		$rub_parente = sql_getfetsel('id_trad', 'spip_rubriques', 'id_rubrique='.intval($flux['args']['contexte']['id_parent']));
		if (intval($rub_parente) > 0) {
			$rub_traduites = sql_allfetsel('id_rubrique, titre, lang', 'spip_rubriques', 'id_trad='.intval($rub_parente));
			if (count($rub_traduites) > 1) {
				$texte = recuperer_fond('prive/squelettes/inclure/rubriques_traductions', array('traductions' => $rub_traduites, 'id_parent' => $flux['args']['contexte']['id_parent']));
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
