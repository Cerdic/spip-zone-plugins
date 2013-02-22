<?php

/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

/**
 * Informations sur les objets où peut s'appliquer les coordonnees
 *
 * @param String $quoi info que l'on veut recuperer (sinon tout le tableau)
 * @return Array Liste d'objet et quelques définitions (titre, exec)
**/
function liste_objets_coordonnees($quoi = '') {
	$liste = array(
		'auteur'       => array('titre'=>_T('ecrire:info_auteurs'),      'exec'=>'auteur_infos'),
		'article'      => array('titre'=>_T('ecrire:info_articles_2'),     'exec'=>'articles'),
		'rubrique'     => array('titre'=>_T('ecrire:info_rubriques'),    'exec'=>'naviguer'),
		'breve'        => array('titre'=>_T('breves:breves'),    'exec'=>'breves_edit'),
		'site'         => array('titre'=>_T('sites:titre_page_sites_tous'),    'exec'=>'sites'),
	);
#	if ( test_plugin_actif('AGENDA') ) // Agenda 2
#		$liste['evenement'] = array('titre'=>_T('agenda:evenements'),    'exec'=>'evenements_edit'); // ca marche, mais comme les evenements sont obligatoirement lies a un article et qu'ils ont des repetitions, il vaut mieux lier le contact directement a l'article
	if ( test_plugin_actif('CONTACTS') ) { // Contacts & Organisations
		$liste['contact'] = array('titre'=>_T('contacts:bouton_contacts'),     'exec'=>'contact');
		$liste['organisation'] = array('titre'=>_T('contacts:bouton_organisations'),'exec'=>'organisation');
	}

	$liste = pipeline('objets_coordonnables', $liste);

	if (!$quoi) {
		return $liste;
	}

	$listeq = array();
	foreach ($liste as $c=>$v) {
		$listeq[$c] = $v[$quoi];
	}
	return $listeq;
}



/**
 * Ajout des informations de coordonnées (adresses, mails, numéros)
 * sur la page de visualisation d'un auteur
**/
function coordonnees_affiche_milieu($flux) {

	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');


		$objet_exec = trouver_objet_exec($exec);

		// pas en édition
		if ($objet_exec['edition']) {
			return $flux;
		}

		// recuperation de l'id
		$_id = $objet_exec['id_table_objet'];
		// type d'objet
		$type = $objet_exec['type'];



	$liste = liste_objets_coordonnees('exec');
	$ok = false;

	if (isset($type) and isset($liste[$type])){
		// c'est un exec que l'on peut afficher
		// verifions qu'il est coche dans la conf
		$conf = unserialize($GLOBALS['meta']['coordonnees']);
		if (is_array($conf['objets']) AND in_array($type, $conf['objets'])) {
			// on doit l'afficher
			// seulement si on a un identifiant
			if (!isset($_id)) {
				$_id = id_table_objet($type);
			}

			if (isset($flux['args'][$_id]) and $id = $flux['args'][$_id]) {
				include_spip('inc/presentation');
				$contexte = array(
					'objet' => $type,
					'id_objet' => $id,
					'id_adresse' => _request('id_adresse'),
					'id_numero' => _request('id_numero'),
					'id_email' => _request('id_email')

				);
				$flux['data'] .= recuperer_fond('prive/boite/coordonnees', $contexte, array('ajax'=>true));
			}
		}
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

?>
