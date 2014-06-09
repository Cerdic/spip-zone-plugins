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
if (!defined("_ECRIRE_INC_VERSION")) return;

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
function odt2spip_affiche_gauche($flux){
	if ($flux['args']['exec']=='rubrique'
	  AND $id_rubrique = $flux['args']['id_rubrique']
	  AND autoriser('ecrire')){
		$out = recuperer_fond('formulaires/odt2spip', array('id_rubrique' => $id_rubrique, 'exec' => 'rubrique'));
		$flux['data'] .= $out;
	}
	if ($flux['args']['exec']=='article'
	  AND $id_article = $flux['args']['id_article']
	  AND autoriser('ecrire')){
		$out = recuperer_fond('formulaires/odt2spip', array('id_article' => $id_article));
		$flux['data'] .= $out;
	}
	elseif ($flux['args']['exec']=='article' 
		AND $id_article = $flux['args']['id_article']
		AND autoriser('modifier', 'article', $id_article)){
		$out = recuperer_fond('formulaires/odt2spip', array('id_article' => $id_article, 'exec' => 'article'));
		$flux['data'] .= $out;
	}
	return $flux;
}

?>
