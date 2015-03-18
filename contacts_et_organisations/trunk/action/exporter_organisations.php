<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action exporter une liste d'organisations
 * 
 * La fonction qui s'occupe génère la liste est surchargeable, et chaque annuaire est surchargeable indépendemment
 * 
 * @param null|string $arg
 *     Il est possible de mettre l'identifiant d'un annuaire en argument (ou 0 pour cibler précisemment ce qui n'a pas d'annuaire).
**/
function action_exporter_organisations_dist($arg=null) {
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
		autoriser('exporter', 'organisations', '', '', array('id_annuaire' => $id_annuaire))
		and (
			(!is_null($annuaire) and $fonction = charger_fonction("exporter_organisations_$annuaire", '', true))
			or
			$fonction = charger_fonction('exporter_organisations', '', true)
		)
	) {
		$organisations = $fonction($id_annuaire);
		
		// organisations.csv OU organisations-0.csv OU organisations-truc.csv
		$nom_fichier = 'organisations' . (!is_null($annuaire) ? "-$annuaire" : '');
		if ($organisations and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)){
			$exporter_csv($nom_fichier, $organisations);
			exit();
		}
	}
}

/**
 * Fonction par défaut d'exportation des organisations
 *
 * @param int $id_annuaire
 * @return void
 */
function exporter_organisations_dist($id_annuaire=null) {
	$where = array();
	if (!is_null($id_annuaire)) {
		$where[] = array('=', 'id_annuaire', intval($id_annuaire));
	}
	
	$lignes = array();
	
	if ($organisations = sql_allfetsel('*', 'spip_organisations', $where)) {
		$champs = array('nom', 'statut_juridique', 'activite', 'url_site', 'date_creation', 'descriptif', 'ouvertures', 'tarifs');
		$titres = array(
			_T('contacts:label_nom'),
			_T('contacts:label_statut_juridique'),
			_T('contacts:label_activite'),
			_T('contacts:label_url_site'),
			_T('contacts:label_date_creation'),
			_T('contacts:label_descriptif'),
			_T('contacts:label_ouvertures'),
			_T('contacts:label_tarifs'),
		);
		
		// Si on a des Champs Extras
		if (
			_DIR_PLUGIN_CEXTRAS
			and include_spip('cextras_pipelines')
			and include_spip('inc/cextras')
			and include_spip('inc/saisies')
			and include_spip('inc/filtres')
			and $saisies = champs_extras_saisies_lister_avec_sql(champs_extras_objet('spip_organisations'))
			and is_array($saisies)
		) {
			foreach ($saisies as $saisie) {
				$champs[] = $saisie['options']['nom'];
				$titres[] = sinon($saisie['options']['label'], $saisie['options']['nom']);
			}
		}
		
		// On remplit les lignes
		$lignes[] = $titres;
		foreach ($organisations as $organisation) {
			$ligne = array();
			
			foreach ($champs as $champ) {
				$ligne[] = $organisation[$champ];
			}
			
			$lignes[] = $ligne;
		}
	}
	
	return $lignes;
}

