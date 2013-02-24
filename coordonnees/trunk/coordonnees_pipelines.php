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

	$liste = lister_tables_objets_sql(); // tableau de donnees de TOUTES les tables...
	$deliste = array('spip_adresses', 'spip_emails','spip_numeros', 'spip_pays', 'spip_documents', 'spip_messages'); // ...donc on retire ceux du plugin coordonnees ! ...ainsi que ceux de : pays requis (revoir sa declaration pour ne pas avoir a faire ceci), documents (comme pays n'a pas de page de vue) et messages (ca fait etrange, tout comme les mots-cles et groupes de mots-cles...)
	for($i=0; $i<6; $i++) {
		// http://stackoverflow.com/questions/12633877/how-to-unset-multiple-variables
		unset($liste[$deliste[$i]]);
	} /// @ToDo: on peut passer directement la liste a unset a partir de PHP 4.0.1
	foreach ($liste as $tab=>$inf) {
		if (!$liste[$tab]['principale'] OR !$liste[$tab]['editable']) {
			// on ne prendra pas en compte les objets non editables...
			// (ceci vire donc spip_forum spip_petitions spip_signatures spip_syndic_articles spip_depots spip_plugins spip_paquets etc.)
			unset($liste[$tab]);
		} else {
			$type = $liste[$tab]['type'];
			$liste[$type] = $liste[$tab]; // le plugin-ci utilise comme cle le type d'objet alors que le tableau renvoye a comme cle le nom de table, donc on recree l'entree...
			unset($liste[$tab]); // ...et on supprime l'ancienne entree histoire de ne pas allourdir le tableau en memoire
			$liste[$type]['titre'] = _T($liste[$type]['texte_objet']); // on rajoute le titre traduit pour etre affiche par la configuration (je ne sais pas utiliser directement "texte_objet")
		}
	}

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
 * sur la page de visualisation d'un objet
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
	// liste des exec de visualisation pour les objets declares
	$liste = liste_objets_coordonnees('url_voir');

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
