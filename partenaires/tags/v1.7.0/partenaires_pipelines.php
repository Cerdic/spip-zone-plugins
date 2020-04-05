<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function partenaires_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);
	
	// partenaires sur les objets sélectionnées
	$config=lire_config('partenaires',array());
	$objets_partenaires=isset($config['objets'])?$config['objets']:array();
	if (!$e['edition'] AND in_array($e['type'], $objets_partenaires)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'partenaires',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}



	// partenaires_types sur les partenaires
	if (!$e['edition'] AND in_array($e['type'], array('partenaire'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'partenaires_types',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function partenaires_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('partenaires_type'=>'*'),'*');
	return $flux;
}

?>