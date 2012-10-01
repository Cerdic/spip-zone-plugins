<?php

/***************************************************************************\
 * Plugin Duplicator pour Spip 2.0
 * Licence GPL (c) 2010 - Apsulis
 * Duplication de rubriques et d'articles
 *
\***************************************************************************/


function duplicator_boite_infos($flux){

	$type = $flux['args']['type'];

	if(autoriser("webmestre")){
		if (lire_config('duplicator/duplic_rubrique')){
			if (($id = intval($flux['args']['id'])) && ($type=='rubrique')) {
			$contexte = array('id_rubrique'=>$id);
			$flux["data"] .= recuperer_fond("inclure/duplicator_rubrique", $contexte);
			}
		}
		if (lire_config('duplicator/duplic_article')){
			if (($id = intval($flux['args']['id'])) && ($type=='article')) {
			$contexte = array('id_article'=>$id);
			$flux["data"] .= recuperer_fond("inclure/duplicator_article", $contexte);
			}
		}
	}

	return $flux;
}
?>
