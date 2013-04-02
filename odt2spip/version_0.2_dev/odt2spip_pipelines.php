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

/**
 * Ajout une boite de creation d'un article à partir d'un fichier odt
 * dans la colonne gauche des pages exec=rubrique
 *
 * @internal à l'aide du pipeline {@link affiche_gauche}
 * @param Array $flux Le code de la colonne gauche
 * @return Array Le code modifié
 */
function odt2spip_affiche_gauche($flux){
//echo '<br><br>depart pipe<br>';
	if ($flux['args']['exec']=='rubrique'
	  AND $id_rubrique = $flux['args']['id_rubrique']
	  AND autoriser('publierdans','rubrique',$flux['args']['id_rubrique'])){
		$out = recuperer_fond('formulaires/odt2spip', array('id_rubrique' => $id_rubrique));
		$flux['data'] .= $out;
	}
	return $flux;
}

?>
