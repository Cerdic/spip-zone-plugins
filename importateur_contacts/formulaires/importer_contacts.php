<?php
/**
 * Plugin Importateur de contacts
 * 
 * Formulaire d'imports de contacts
 * 
 * Ce formulaire est en deux étapes :
 * -* Choix de la méthode de récupération des contacts
 * -* Informations nécessaires à l'import (user/pass, liste d'emails...)
 * 
 * @package SPIP\Importateur_Contacts\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire
 * 
 * @param string $retour
 * 	Url de retour
 * @param string $traitement
 * 	Fonction à appeler au traitement du formulaire à la suite de la récupération des contacts
 */
function formulaires_importer_contacts_charger_dist($retour='', $traitement=''){
	/**
	 * On commence par vider la variable de session "contacts"
	 * qui sert à stocker le résultat de la récupération des contacts
	 */ 
	include_spip('inc/session');
	session_set('contacts');
	
	include_spip('inc/importateur_contacts');
	$fournisseurs_choisis = importateur_contacts_lister_fournisseurs_choisis();
	$liste_fournisseurs = array();
	foreach ($fournisseurs_choisis as $nom_fournisseur => $fournisseur){
		$liste_fournisseurs[$nom_fournisseur] = $fournisseur['titre'];
	}
	if(count($fournisseurs_choisis) == 0){
		return array(
					'message_erreur' => _T('importateurcontacts:erreur_aucun_fournisseur_configure'),
					'editable'=>false
				);
	}
	$contexte = array(
		'_etapes' => 2,
		'_fournisseurs_choisis' => $fournisseurs_choisis,
		'_liste_fournisseurs' => $liste_fournisseurs,
		'fournisseur' => '',
		'contacts' => array()
	);
	// On appelle une fonction de vérification correspondant au moteur du fournisseur choisi
	if ($fournisseur = _request('fournisseur')){
		$fournisseur = $fournisseurs_choisis[$fournisseur];
		$moteur_choisi = $fournisseur['moteur_choisi'];
		$fonction_charger_moteur = charger_fonction($moteur_choisi, 'formulaires/importer_contacts/charger');
		if($fonction_charger_moteur)
			$contexte = array_merge($contexte, $fonction_charger_moteur($fournisseur));
	}
	
	return $contexte;
}

/**
 * Vérification de l'étape 2 du formulaire
 * 
 * @param string $retour
 * 	Url de retour
 * @param string $traitement
 * 	Fonction à appeler au traitement du formulaire à la suite de la récupération des contacts
 */
function formulaires_importer_contacts_verifier_2_dist($retour='', $traitement=''){
	include_spip('inc/importateur_contacts');
	$fournisseurs_choisis = importateur_contacts_lister_fournisseurs_choisis();
	$fournisseur = $fournisseurs_choisis[_request('fournisseur')];
	$moteur_choisi = $fournisseur['moteur_choisi'];
	
	// On appelle une fonction de vérification correspondant au moteur du fournisseur choisi
	// Cette fonction doit aussi remplir la variable "contacts"
	$fonction_verif_moteur = charger_fonction($moteur_choisi, 'formulaires/importer_contacts/verifier');
	if($fonction_verif_moteur)
		$erreurs = $fonction_verif_moteur($fournisseur);
	return $erreurs;
}

/**
 * Traitement du formulaire
 * 
 * @param string $retour
 * 	Url de retour
 * @param string $traitement
 * 	Fonction à appeler au traitement du formulaire à la suite de la récupération des contacts
 */
function formulaires_importer_contacts_traiter_dist($retour='', $traitement=''){
	$retours = array();
	
	// Si on a bien récupéré un tableau de contacts (même vide)
	$contacts = _request('contacts');
	if (is_array($contacts)){
		// On peut mettre les contacts en session utilisateur
		include_spip('inc/session');
		session_set('contacts', $contacts);
		
		/**
		 * Si un traitement supplémentaire est demandé pour ce formulaire (en second paramètre)
		 * on applique la fonction de traitement avec comme paramètre le tableau de contacts
		 * récupérés
		 */
		if ($traitement and function_exists($traitement))
			$traitement($contacts);
		
		// Les messages de retours
		if (empty($contacts)) 
			$retours['message_ok'] = _T('importateurcontacts:info_aucun_contact');
		else 
			$retours['message_ok'] = _T('importateurcontacts:info_nb_contacts', array('nb'=>count($contacts)));
	}
	
	if ($retour)
		$retours['redirect'] = $retour;
	
	return $retours;
}

?>
