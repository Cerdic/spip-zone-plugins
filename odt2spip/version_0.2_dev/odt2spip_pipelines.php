<?php
/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package odt2spip
 * @version $Id$
 *
 */

/**
 * Pipeline ajoutant un lien d'import à la barre de navigation de l'interface privée
 * 
 * @param Array $flux Le code de la barre
 * @return Array Le code modifié
 */
function odt2spip_affiche_droite($flux){
	$id_rubrique = $flux['args']['id_rubrique'];
	if ($flux['args']['exec'] == 'naviguer' AND $id_rubrique > 0) {
		$icone = icone_horizontale(_T("odtspip:importer_fichier"), "#", "",
					_DIR_PLUGIN_ODT2SPIP . "images/odt-24.png", false, 
					"onclick='$(\"#boite_odt2spip\").slideToggle(\"fast\");return false;'");
		$out = recuperer_fond('formulaires/odt2spip', 
					array('id_rubrique' => $id_rubrique, 'icone' => $icone));
		$flux['data'] .= $out;
	}
	return $flux;
}

?>
