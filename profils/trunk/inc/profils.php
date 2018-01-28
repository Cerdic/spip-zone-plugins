<?php
/**
 * Fonctions utilitaires pour les profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Utils
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cherche le profil suivant un id SQL ou un identifiant textuel
 * 
 * @param int|string $id_ou_identifiant_profil
 * 		ID SQL ou identifiant textuel du profil
 * @return array|bool
 * 		Retourne le profil demandé ou false
 */
function profils_chercher_profil($id_ou_identifiant_profil) {
	static $profils = array();
	$profil = false;
	
	// Si on l'a déjà trouvé avant
	if (isset($profils[$id_ou_identifiant_profil])) {
		return $profils[$id_ou_identifiant_profil];
	}
	// Sinon on cherche
	else {
		if (!$id_ou_identifiant_profil) {
			return $profil;
		}
		elseif ($id_ou_identifiant_profil === intval($id_ou_identifiant_profil)) {
			$profil = sql_fetsel('*', 'spip_profils', 'id_profil = '.intval($id_ou_identifiant_profil));
		}
		else {
			$profil = sql_fetsel('*', 'spip_profils', 'identifiant = '.sql_quote($id_ou_identifiant_profil));
		}
	}
	
	if ($profil) {
		$profil['config'] = unserialize($profil['config']);
	}
	
	$profils[$id_ou_identifiant_profil] = $profil;
	return $profil;
}

/**
 * Cherche les saisies d'édition d'un objet ET des champs extras ajoutés
 * 
 * @param string $objet
 * 		Nom de l'objet dont on cherche les saisies
 * @return array
 * 		Retoure un tableau de saisies
 * 
 */
function profils_chercher_saisies_objet($objet) {
	static $saisies = array();
	
	if (isset($saisies[$objet])) {
		return $saisies[$objet];
	}
	else {
		$saisies[$objet] = array();
		
		// Les saisies de base
		if ($saisies_objet = saisies_chercher_formulaire("editer_$objet", array())) {
			$saisies[$objet] += $saisies_objet;
		}
		
		// Les saisies des champs extras
		if (defined('_DIR_PLUGIN_CEXTRAS')) {
			include_spip('cextras_pipelines');
			
			if ($saisies_extra = champs_extras_objet(table_objet_sql($objet))) {
				$saisies[$objet] += $saisies_extra;
			}
		}
	}
	
	return $saisies[$objet];
}

/**
 * Récupérer tous les identifiants des objets liés à un profil
 *
 * @param int $id_auteur=0
 * 		Identifiant d'un auteur précis, sinon visiteur en cours
 * @return array
 * 		Retourne un tableau de tous les identifiants
 */
function profils_chercher_ids_profil($id_auteur=0, $id_ou_identifiant_profil='') {
	$ids = array();
	$ids['id_auteur'] = intval($id_auteur);
	$coordonnes = array();
	
	// Cherchons un utilisateur
	if (!$ids['id_auteur'] and !$ids['id_auteur'] = intval(session_get('id_auteur'))) {
		$ids['id_auteur'] = 'new';
	}
	// Si on a un utilisateur et qu'il n'y a pas de profil déjà fourni
	elseif (!$id_ou_identifiant_profil) {
		$id_ou_identifiant_profil = sql_getfetsel('id_profil', 'spip_auteurs', 'id_auteur = '.$ids['id_auteur']);
	}
	
	// Maintenant on ne continue que si on a trouvé un profil
	if ($profil = profils_chercher_profil($id_ou_identifiant_profil) and $config = $profil['config']) {
		// Si le plugin est toujours là
		if (defined('_DIR_PLUGIN_CONTACTS')) {
			// Est-ce qu'il y a une orga en fiche principale ?
			if ($config['activer_organisation']) {
				// Cherchons une organisation
				if (
					!intval($ids['id_auteur'])
					or !$id_organisation = intval(sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur = '.$ids['id_auteur']))
				) {
					$ids['id_organisation'] = 'new';
				}
				
				// Il peut aussi y avoir un contact physique *lié à l'orga*
				if (
					!intval($ids['id_organisation'])
					or !$liens = objet_trouver_liens(array('contact'=>'*'), array('organisation'=>$ids['id_organisation']))
					or !$contact = $liens[0]
					or !$ids['id_contact'] = intval($contact['id_contact'])
				) {
					$ids['id_contact'] = 'new';
				}
			}
			elseif ($config['activer_contact']) {
				// Cherchons un contact physique
				if (
					!intval($ids['id_auteur'])
					or !$ids['id_contact'] = intval(sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur = '.$ids['id_auteur']))
				) {
					$ids['id_contact'] = 'new';
				}
			}
		}
	}
	
	//~ // Cherchons une adresse liée à l'organisation
	//~ if (
		//~ !intval($id_organisation)
		//~ or !$liens = objet_trouver_liens(array('adresse'=>'*'), array('organisation'=>$id_organisation))
		//~ or !$adresse = $liens[0]
		//~ or !$id_adresse = intval($adresse['id_adresse'])
	//~ ) {
		//~ $id_adresse = 'new';
	//~ }
	
	//~ // Cherchons un email lié au contact
	//~ if (
		//~ !intval($id_contact)
		//~ or !$liens = objet_trouver_liens(array('email'=>'*'), array('contact'=>$id_contact))
		//~ or !$email = $liens[0]
		//~ or !$id_email = intval($email['id_email'])
	//~ ) {
		//~ $id_email = 'new';
	//~ }
	
	//~ // On cherche un numéro lié au contact
	//~ if (
		//~ !intval($id_contact)
		//~ or !$liens = objet_trouver_liens(array('numero'=>'*'), array('contact'=>$id_contact))
		//~ or !$numero = $liens[0]
		//~ or !$id_numero = intval($numero['id_numero'])
	//~ ) {
		//~ $id_numero = 'new';
	//~ }
	
	return $ids;
}

/**
 * Récupérer les informations complètes d'un profil
 *
 * @param int $id_auteur=0
 * 		Identifiant d'un auteur précis, sinon visiteur en cours
 * @return array
 * 		Retourne les informations d'un profil, chaque objet SPIP dans une clé (organisation, contact, telephone, etc).
 */
function profils_recuperer_infos($id_auteur=0, $id_ou_identifiant_profil='') {
	$retour = '';
	$infos = array();
	
	// Récupérer les objets liés au profil utilisateur
	extract(profils_chercher_ids_profil($id_auteur, $id_ou_identifiant_profil));
	
	// On charge les infos du compte SPIP
	$infos['auteur'] = formulaires_editer_objet_charger('auteur', $id_auteur, 0, 0, $retour, '');
	
	// On charge les infos de l'organisation
	if ($id_organisation) {
		$infos['organisation'] = formulaires_editer_objet_charger('organisation', $id_organisation, 0, 0, $retour, '');
		unset($infos['organisation']['_pipeline']);
	}
	
	// On charge les infos du contact avec un préfixe "contact_"
	if ($id_contact) {
		$infos['contact'] = formulaires_editer_objet_charger('contact', $id_contact, intval($id_organisation), 0, $retour, '');
	}
	
	//~ // On charge les infos de l'adresse
	//~ $infos['adresse'] = formulaires_editer_objet_charger('adresse', $id_adresse, 0, 0, $retour, '');
	
	//~ // On charge les infos du numéro
	//~ $infos['numero'] = formulaires_editer_objet_charger('numero', $id_numero, 0, 0, $retour, '');
	
	//~ // On charge les infos de l'email
	//~ $infos['email'] = formulaires_editer_objet_charger('email', $id_email, 0, 0, $retour, '');
	
	return $infos;
}
