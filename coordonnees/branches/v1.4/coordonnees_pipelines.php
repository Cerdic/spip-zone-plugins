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
		'breve'     => array('titre'=>_T('ecrire:info_breves_03'),    'exec'=>'breves_edit'),
		'site'     => array('titre'=>_T('ecrire:titre_page_sites_tous'),    'exec'=>'sites'),
		'mot'     => array('titre'=>_T('ecrire:mots_clef'),    'exec'=>'mots_edit'),
		'groupe_mots'     => array('titre'=>_T('spip:icone_mots_cles'),    'exec'=>'mots_tous'),
#		'message'     => array('titre'=>_T('spip:icone_messagerie_personnelle'),    'exec'=>'message'),
	);
#	if ( test_plugin_actif('AGENDA') ) // Agenda 2
#		$liste['evenement'] = array('titre'=>_T('agenda:evenements'),    'exec'=>'evenements_edit'); // ca marche, mais comme les evenements sont obligatoirement lies a un article et qu'ils ont des repetitions, il vaut mieux lier le contact directement a l'article
	if ( test_plugin_actif('CONTACT') ) { // Contacts & Organisations
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

	$liste = liste_objets_coordonnees('exec');

	$liste = array_flip($liste);
	if (isset($liste[$exec])) {
		$type = $liste[$exec];
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
					"id_$type" => _request("id_$type"),
				);
				$flux['data'] .= recuperer_fond('prive/boite/coordonnees', $contexte, array('ajax'=>true));
			}
		}
	}

	return $flux;

}


/**
 * Ajout des objets 'adresse' et 'numero'
 * à la liste des objets pouvant recevoir des champs extras
**/
function coordonnees_objets_extensibles($objets){
	return array_merge($objets, array(
		'adresse' => _T('coordonnees:adresses'),
		'numero' => _T('coordonnees:numeros'),
	));
}

?>
