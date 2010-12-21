<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */
 

/**
 * Affichage du formulaire de choix Contact/Organisation
 * dans la colonne de vue d'un auteur
**/
function contacts_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'auteur_infos'){
		$flux['data'] .= recuperer_fond('prive/boite/selecteur_contacts_organisations', array(
			'id_auteur'=>$flux['args']['id_auteur']
		), array('ajax'=>true));
	}
	return $flux;
}


/**
 * Affichage des champs de formulaires correspondants
 * aux contacts et aux organisations sur le formulaire d'auteur 
**/
function contacts_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur'
	and $id_auteur = $flux['args']['contexte']['id_auteur']) {

		$contact = recuperer_fond('formulaires/inc-contact', $flux['args']['contexte']);
		$organisation = recuperer_fond('formulaires/inc-organisation', $flux['args']['contexte']);

		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$contact.$organisation, $flux['data']);
	}
	return $flux;
}


/**
 * Chargement des valeurs pour le formulaire d'auteur
 * pour les champs de formulaire ajoutés à destination
 * des contacts et organisations
**/
function contacts_formulaire_charger($flux){
	if ($flux['args']['form'] == 'editer_auteur') {
		
		// test des contacts, sinon des organisations
		$res = sql_fetsel('*', 'spip_contacts', 'id_auteur='.sql_quote($flux['data']['id_auteur']));
		if (!$res) {
			$res =  sql_fetsel('*', 'spip_organisations', 'id_auteur='.sql_quote($flux['data']['id_auteur']));
		}

		// contact ou organisation, on insère dans l'environnement du formulaire
		// les valeurs pour les champs de formulaires,
		// mais avec les cle prefixees de 'co__' pour ne pas confondre
		// avec d'éventuels champs extras sur la table auteurs
		if ($res) {
			unset($res['id_auteur']);
			foreach ($res as $cle=>$valeur) {
				$flux['data']['co__'.$cle] = $valeur;
			}
		}
	}
	return $flux;
}


/**
 * Vérifications des valeurs soumises via le formulaire d'auteur
 * à destination des tables contacts et organisations
**/
function contacts_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'editer_auteur') {
		$id_auteur = $flux['args']['args'][0];
		
		// test des contacts, sinon des organisations
		if (sql_countsel('spip_contacts', 'id_auteur='.sql_quote($id_auteur))) {
			$objet = 'contact';
		} elseif (sql_countsel('spip_organisations', 'id_auteur='.sql_quote($id_auteur))) {
			$objet = 'organisation';
		} else {
			$objet = false;
		}
		
		if ($objet) {
			$flux_extra = $flux;
			$flux_extra['args']['form'] = 'editer_' . $objet;
			$flux_extra['args']['prefixe_champs_extras'] = 'co__';
			// gerer les erreurs sur les champs extras des tables
			// on appelle le même pipeline, mais en indiquant un nom de formulaire different.
			$erreurs = pipeline('formulaire_verifier', $flux_extra);

			if ($erreurs) {
				if (isset($erreurs['message_erreur'])) {
					$flux['data']['message_erreur'] .= $erreurs['message_erreur'];
				}
				if (isset($erreurs['message_ok'])) {
					$flux['data']['message_ok'] .= $erreurs['message_ok'];
				}
				unset($erreurs['message_erreur'], $erreurs['message_ok']);
				$flux['data'] = array_merge($flux['data'], $erreurs);
			}
		}

	}
	return $flux;
}


/**
 * Enregistrement des valeurs soumises via le formulaire d'auteur
 * à destination des tables contacts et organisations
**/
function contacts_formulaire_traiter($flux){
	if ($flux['args']['form'] == 'editer_auteur') {
		$id_auteur = intval($flux['data']['id_auteur']);
		
		// test des contacts, sinon des organisations
		if ($res = sql_fetsel('*', 'spip_contacts', 'id_auteur='.sql_quote($id_auteur))) {
			$objet = 'contact';
		} elseif ($res = sql_fetsel('*', 'spip_organisations', 'id_auteur='.sql_quote($id_auteur))) {
			$objet = 'organisation';
		} else {
			$objet = false;
		}

		// contact ou organisation,
		// on recupère les envois ayant 'co__$cle'
		// et on modifie l'objet en question.
		if ($objet) {
			include_spip('inc/modifier');
			unset($res['id_auteur']);
			$c = array();
			foreach ($res as $cle => $null) {
				if (isset($_REQUEST['co__'.$cle])) {
					$c[$cle] = _request('co__' . $cle);
				}
			}
			$_id_objet = id_table_objet($objet); // id_contact
			$id_objet = $res[$_id_objet]; // 3
			modifier_contenu($objet, $id_objet, array('invalideur' => "id='$_id_objet/$id_objet'"), $c);
		}
	}
	return $flux;
}




/**
 *
 * Insertion dans la vue des auteurs
 * des informations relatives aux contacts et organisations
 */
function contacts_affiche_milieu($flux){
	if ($flux['args']['exec'] == 'auteur_infos') {
		$data  = recuperer_fond('prive/contenu/contact', array('id_auteur' => $flux['args']['id_auteur']));
		$data .= recuperer_fond('prive/contenu/organisation', array('id_auteur' => $flux['args']['id_auteur']));
		$flux['data'] = $data . $flux['data'];
		}
	return $flux;
}


/**
 * Prendre en compte les tables dans la recherche d'éléments. 
 *
 * @param 
 * @return 
**/
function contacts_rechercher_liste_des_champs($tables){
	
	// ajouter la recherche sur contact
	$tables['contact']['id_auteur'] = 12;
	$tables['contact']['nom'] = 4;
	$tables['contact']['prenom'] = 2;
	
	// ajouter la recherche sur organisations
	$tables['organisation']['id_auteur'] = 12;
	$tables['organisation']['nom'] = 4;

	return $tables;
}


/**
 * Autoriser les champs extras sur les objets
 * Contacs et Organisations
**/
function contacts_objets_extensibles($objets){
		return array_merge($objets, array(
			'contact' => _T('contacts:contacts'),
			'organisation' => _T('contacts:organisations'),
		));
}

/**
 * Ajoute une feuille de style pour la v-card
 * Peut être surchargé ensuite
**/
function contacts_insert_head($flux){

	$flux .= '<!-- insertion de la css contacts--><link rel="stylesheet" type="text/css" href="'.find_in_path('contacts.css').'" media="all" />';

	return $flux;
}


?>
