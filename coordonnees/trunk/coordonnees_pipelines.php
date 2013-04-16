<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajout des coordonnées (adresses, mails, numéros)
 * sur la page de visualisation des objets associes
**/
function coordonnees_afficher_fiche_objet($flux) {
	$texte = "";
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	$e = trouver_objet_exec($exec);
	$type = $flux['args']['type'];

	if (!$e['edition'] AND in_array(table_objet_sql($type),lire_config('coordonnees/objets')) ) {
		$texte .= recuperer_fond('prive/squelettes/contenu/coordonnees_fiche_objet', array(
			'objet' => $type,
			'id_objet' => intval($flux['args']['id']),
			),
			array('ajax'=>'coordonnees')
		);
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--afficher_fiche_objet-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Liste des coordonnées d'un auteur sur la page "infos_perso"
**/
function coordonnees_affiche_auteurs_interventions($flux) {
	$texte = "";
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	if ($id_auteur = intval($flux['args']['id_auteur']) AND $exec != 'auteur') {
		$texte .= recuperer_fond('prive/squelettes/contenu/coordonnees_fiche_objet', array(
			'objet' => 'auteur',
			'id_objet' => $id_auteur,
			),
			array('ajax'=>'coordonnees')
		);
	}
	if ($texte) {
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
function coordonnees_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('adresse'=>'*', 'telephone'=>'*', 'email'=>'*'),'*');
	return $flux;
}

?>
