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
	$e = trouver_objet_exec($flux['args']['exec']);
	$type = $flux['args']['type'];

	if (!$e['edition'] AND in_array(table_objet_sql($type),lire_config('coordonnees/objets'))) {
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
 * Ajout de l'objet 'adresse'
 * à la liste des objets pouvant recevoir des champs extras
**/
function coordonnees_objets_extensibles($objets){
	return array_merge($objets, array(
		'adresse' => _T('coordonnees:adresses'),
		'numero' => _T('coordonnees:numeros'),
		'email' => _T('coordonnees:emails'),
	));
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
