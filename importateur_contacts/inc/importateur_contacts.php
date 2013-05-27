<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Liste les moteurs d'importation existants, avec les informations pour les identifier et les fournisseurs qu'ils proposent.
 * 
 * @return array
 *	Retourne un tableau de tableaux décrivant le contenu de chaque moteur trouvé.
 *	Exemple :
 *		array(
 *			'un_moteur' => array(
 *				'titre' => 'Mon moteur',
 *				'url' => 'http://www.sitedumoteur.info',
 *				'fichier' => 'inc/un_fichier_php_a_importer',
 *				'fournisseurs' => array(
 *					'gmail' => array(
 *						'titre' => 'GMail',
 *						'type' => 'webmail',
 *						'domaines' => array(
 *							'gmailcom' => array(
 *								'titre' => 'gmail.com',
 *								'regex' => '/(gmail.com)/i',
 *							),
 *							'googlemailcom' => array(
 *								'titre' => 'googlemail.com',
 *								'regex' => '/(googlemail.com)/i',
 *							)
 *						)
 *					),
 *					'facebook' => array(
 *						'titre' => 'Facebook',
 *						'type' => 'social',
 *						'domaines' => array(
 *							'*' => array(
 *								'titre' => 'Tous',
 *								'regex' => '/(.*)/i',
 *							),
 *						)
 *					),
 *				)
 *			)
 *		)
 */
function importateur_contacts_lister_moteurs(){
	static $moteurs = array();
	
	// Si on l'a déjà fait dans le même hit on retourne
	if (!empty($moteurs)) return $moteurs;
	
	// On récupère la liste des moteurs par les plugins
	$moteurs = pipeline('importateur_contacts_moteurs', $moteurs);
	
	// On ne renvoie que les moteurs qui ont des fournisseurs de contacts
	foreach ($moteurs as $nom => $moteur){
		if (empty($moteur['fournisseurs'])) unset($moteurs[$nom]);
	}
	
	return $moteurs;
}

/*
 * Liste les fournisseurs de contacts existants et leur configuration.
 * 
 * @return array Retourne un tableau de tableaux
 * @see importateur_contacts_lister_moteurs()
 */
function importateur_contacts_lister_fournisseurs(){
	include_spip('inc/config');
	$moteurs = importateur_contacts_lister_moteurs();
	$fournisseurs = array();
	
	foreach ($moteurs as $nom_moteur => $moteur){
		foreach ($moteur['fournisseurs'] as $nom_fournisseur => $fournisseur){
			// S'il n'y est pas déjà on l'ajoute tel quel
			if (!isset($fournisseurs[$nom_fournisseur])){
				$fournisseurs[$nom_fournisseur] = $fournisseur;
			}
			// Sinon on ne teste que certains champs
			else{
				$fournisseurs[$nom_fournisseur]['domaines'] = array_merge($fournisseurs[$nom_fournisseur]['domaines'], $fournisseur['domaines']);
			}
			ksort($fournisseurs[$nom_fournisseur]['domaines']);
			
			// On ajoute le moteur dans la liste des possibles
			$fournisseurs[$nom_fournisseur]['moteurs'][$nom_moteur] = $moteur['titre'];
		}
	}
	ksort($fournisseurs);
	
	// On enregistre la config sur l'activation ou pas du fournisseur et avec quel moteur le cas échéant
	$fournisseurs_choisis = lire_config('importateur_contacts/fournisseurs_choisis');
	foreach ($fournisseurs as $nom_fournisseur => $fournisseur){
		$fournisseurs[$nom_fournisseur]['moteur_choisi'] = !empty($fournisseurs_choisis[$nom_fournisseur]) ? $fournisseurs_choisis[$nom_fournisseur] : null;
	}
	
	return $fournisseurs;
}

/*
 * Liste les fournisseurs de contacts qui ont été activés
 * 
 * @return array Retourne un tableau de tableaux
 * @see importateur_contacts_lister_fournisseurs()
 */
function importateur_contacts_lister_fournisseurs_choisis(){
	static $fournisseurs = array();
	
	// Si on l'a déjà fait on retourne le résultat directement
	if (!empty($fournisseurs)) return $fournisseurs;
	
	$fournisseurs = importateur_contacts_lister_fournisseurs();
	
	// Pour chaque fournisseur, s'il n'est pas activé=s'il n'a pas de moteur choisi, on l'enlève
	foreach ($fournisseurs as $nom_fournisseur => $fournisseur){
		if (empty($fournisseur['moteur_choisi'])){
			unset($fournisseurs[$nom_fournisseur]);
		}
	}
	
	return $fournisseurs;
}

?>