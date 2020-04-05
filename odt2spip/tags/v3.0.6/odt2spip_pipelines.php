<?php
/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout une boite de creation d'un article à partir d'un fichier odt
 * dans la colonne gauche des pages exec=rubrique
 * ou
 * ajout d'une boite de remplacement du contenu de l'article à partir d'un fichier odt
 * dans la colonne de gauche des pages exec=article
 *
 * @internal à l'aide du pipeline {@link affiche_gauche}
 * @param Array $flux Le code de la colonne gauche
 * @return Array Le code modifié
 */
function odt2spip_affiche_gauche($flux) {
	if (
		$flux['args']['exec'] == 'rubrique'
		and $id_rubrique = $flux['args']['id_rubrique']
		and autoriser('ecrire')
	) {
		$out = recuperer_fond(
			'prive/squelettes/inclure/document2spip',
			array(
				'objet' => 'rubrique',
				'id_objet' => $id_rubrique,
				'creer_objet' => 'article'
			)
		);
		$flux['data'] .= $out;
	} elseif (
		$flux['args']['exec'] == 'article'
		and $id_article = $flux['args']['id_article']
		and autoriser('modifier', 'article', $id_article)
	) {
		$out = recuperer_fond(
			'prive/squelettes/inclure/document2spip',
			array(
				'objet' => 'article',
				'id_objet' => $id_article
			)
		);
		$flux['data'] .= $out;
	}
	return $flux;
}
