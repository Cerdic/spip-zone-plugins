<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function orr_affiche_milieu($flux) {
	$texte = "";
	$e     = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les orr_reservations
	if (!$e['edition'] AND in_array($e['type'], array('orr_reservation'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet'        => $e['type'],
			'id_objet'     => $flux['args'][$e['id_table_objet']]
		));
	}
	
	// orr_autorisations sur orr_ressources
	if (!$e['edition'] AND in_array($e['type'], array('orr_ressource'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'orr_autorisations',
			'objet'        => $e['type'],
			'id_objet'     => $flux['args'][$e['id_table_objet']]
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
 * Ajout de liste sur la vue d'un auteur
 */
function orr_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/orr_reservations', array(
			'id_auteur' => $id_auteur,
			'titre'     => _T('orr_reservation:info_orr_reservations_auteur')
		), array('ajax' => true));

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
function orr_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('orr_reservation'=>'*'),'*');
	return $flux;
}
/**
 * insertion de date.js
 **/
function orr_jquery_plugins($scripts){
    $scripts[] = "javascript/date.js";
    return $scripts;
}

/**
 * insertion du css
 **/
function orr_insert_head_css($flux){
	$css   = find_in_path('orr.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;
}

?>
