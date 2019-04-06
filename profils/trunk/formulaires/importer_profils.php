<?php
/**
 * Gestion du formulaire de d'importer de profils depuis un tableur
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_importer_profils_saisies_dist($id_profil) {
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'fichier',
				'type' => 'file',
				'label' => 'CSV',
				'obligatoire' => 'oui',
				'pleine_largeur' => 'oui',
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'envoyer_notification',
				'label_case' => _T('profil:envoyer_notification_label_case'),
				'pleine_largeur' => 'oui',
			),
		),
		'options' => array(
			'texte_submit' => _T('bouton_upload'),
			'inserer_debut' => '<h3 class="titrem">'._T('profil:importer_titre').'</h3>'
		),
	);
	
	return $saisies;
}

function formulaires_importer_profils_traiter_dist($id_profil){
	$retours = array();
	$jobs = 0;
	$jobs_erreurs = 0;
	
	$fichier = $_FILES['fichier']['tmp_name'];
	$importer_csv = charger_fonction('importer_csv', 'inc/');
	$envoyer_notification = _request('envoyer_notification') ? true : false;
	
	// Si on récupère bien un tableau
	if (is_array($profils = $importer_csv($fichier, true))){
		foreach ($profils as $profil){
			// On cherche rapidement un truc pertinent à mettre
			if (isset($profil['auteur_nom']) and $profil['auteur_nom']) {
				$nom = $profil['auteur_nom'];
			}
			elseif (isset($profil['auteur_email']) and $profil['auteur_email']) {
				$nom = $profil['auteur_email'];
			}
			elseif (isset($profil['organisation_nom']) and $profil['organisation_nom']) {
				$nom = $profil['organisation_nom'];
			}
			elseif (isset($profil['contact_nom']) and $profil['contact_nom']) {
				$nom = $profil['contact_nom'];
			}
			else {
				$nom = '???';
			}
			// On ajoute la tâche d'import
			if (job_queue_add(
				'importer_csv_profil',
				'Importer le profil "'.$nom.'"',
				array($id_profil, $profil, $envoyer_notification),
				'inc/'
			) > 0){
				$jobs++;
			}
			else{
				$jobs_erreurs++;
			}
		}
	}
	
	if (!$jobs_erreurs){
		$retours['message_ok'] = "Lancement de $jobs importations de profils dans la liste des travaux.";
	}
	else{
		$retours['message_erreur'] = "$jobs importations lancées avec succès MAIS $jobs_erreurs n’ont pas pu être lancées !";
	}
	
	return $retours;
}
