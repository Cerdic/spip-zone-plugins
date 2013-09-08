<?php
/**
 * Ce fichier contient les cas d'utilisation des pipelines d'affichage.
 *
 * @package SPIP\BOUSSOLE\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Affichage, dans la fiche d'un objet site, d'un bloc identifiant
 * la boussole à laquelle appartient le site édité.
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux
 * 		Données de contexte fournies au pipeline
 * @return array
 * 		Données de contexte complétées par la fonction
 */
function boussole_affiche_milieu($flux){
	if (($flux['args']['exec'] == 'site') AND $flux['args']['id_syndic']) {
		$id_syndic = $flux['args']['id_syndic'];
		$info = recuperer_fond('prive/squelettes/inclure/site_boussole', array('id_syndic'=>$id_syndic));

		if ($info){
			if ($p = strpos($flux['data'],'<!--affiche_milieu-->'))
				$flux['data'] = substr_replace($flux['data'], $info, $p, 0);
			else
				$flux['data'] .= $info;
		}
	}

	return $flux;
}

?>
