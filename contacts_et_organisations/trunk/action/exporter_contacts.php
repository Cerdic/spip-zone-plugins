<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action exporter une liste d'contacts
 * 
 * La fonction qui s'occupe génère la liste est surchargeable, et chaque annuaire est surchargeable indépendemment
 * 
 * @param null|string $arg
 *     Il est possible de mettre l'identifiant d'un annuaire en argument (ou 0 pour cibler précisemment ce qui n'a pas d'annuaire).
**/
function action_exporter_contacts_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// On regarde s'il y a un annuaire (ou 0)
	$id_annuaire = null;
	if ($arg !== '') {
		$id_annuaire = intval($arg);
	}
	
	// On détermine un nom d'annuaire
	if (!is_null($id_annuaire)) {
		if ($id_annuaire === 0) {
			$annuaire = '0';
		}
		elseif ($identifiant = sql_getfetsel('identifiant', 'spip_annuaires', 'id_annuaire = '.$id_annuaire)) {
			$annuaire = $identifiant;
		}
	}
	
	if (
		autoriser('exporter', 'contacts', '', '', array('id_annuaire' => $id_annuaire))
		and (
			(!is_null($annuaire) and $fonction = charger_fonction("exporter_contacts_$annuaire", '', true))
			or
			$fonction = charger_fonction('exporter_contacts', '', true)
		)
	) {
		$contacts = $fonction($id_annuaire);
		
		// contacts.csv OU contacts-0.csv OU contacts-truc.csv
		$nom_fichier = 'contacts' . (!is_null($annuaire) ? "-$annuaire" : '');
		if ($contacts and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)){
			$exporter_csv($nom_fichier, $contacts);
			exit();
		}
	}
}

/**
 * Fonction par défaut d'exportation des contacts
 *
 * @param int $id_annuaire
 * @return void
 */
function exporter_contacts_dist($id_annuaire=null) {
	$where = array();
	if (!is_null($id_annuaire)) {
		$where[] = array('=', 'id_annuaire', intval($id_annuaire));
	}
	
	$lignes = array();
	
	if ($contacts = sql_allfetsel('*', 'spip_contacts', $where)) {
		$champs = array('civilite', 'prenom', 'nom', 'fonction', 'date_naissance', 'descriptif');
		$titres = array(
			_T('contacts:label_civilite'),
			_T('contacts:label_prenom'),
			_T('contacts:label_nom'),
			_T('contacts:label_fonction'),
			_T('contacts:label_date_naissance'),
			_T('contacts:label_descriptif'),
		);
		
		// Si on a des Champs Extras
		if (
			_DIR_PLUGIN_CEXTRAS
			and include_spip('cextras_pipelines')
			and include_spip('inc/cextras')
			and include_spip('inc/saisies')
			and include_spip('inc/filtres')
			and $saisies = champs_extras_saisies_lister_avec_sql(champs_extras_objet('spip_contacts'))
			and is_array($saisies)
		) {
			foreach ($saisies as $saisie) {
				$champs[] = $saisie['options']['nom'];
				$titres[] = sinon($saisie['options']['label'], $saisie['options']['nom']);
			}
		}
		
		// On remplit les lignes
		$lignes[] = $titres;
		foreach ($contacts as $organisation) {
			$ligne = array();
			
			foreach ($champs as $champ) {
				$ligne[] = $organisation[$champ];
			}
			
			$lignes[] = $ligne;
		}
	}
	
	return $lignes;
}

