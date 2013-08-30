<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_milieu
 * Affiche un bloc l'identification de la boussole a laquelle appartient le site edite
 *
 * @param object $flux
 * @return object
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
