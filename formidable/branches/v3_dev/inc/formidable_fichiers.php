<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/flock');
if (!defined('_DIR_FICHIERS')) { // En attendant que ce soit natif spip
	define ('_DIR_FICHIERS',  _DIR_ETC . 'fichiers/');
}

if (!defined('_DIR_FICHIERS_FORMIDABLE')) { 	
	define ('_DIR_FICHIERS_FORMIDABLE',  _DIR_FICHIERS . 'formidable/');

}
/** 
 * Créer, si le formulaire contient des saisies de type fichiers, un dossier pour stocker les fichiers.
 * Vérifier que ce dossier soit accessible en écriture.
 * Vérifier qu'on ne puisse pas y accéder de l'exterieur.
 *
 * @param $id_formulaire
 * @return $erreur
**/ 
function formidable_creer_dossier_formulaire($id_formulaire) {
	include_spip('formulaires/formidable');
	$saisies_fichiers = formulaires_formidable_fichiers($id_formulaire); // Récuperer la liste des saisies de type fichier

	if (!is_array($saisies_fichiers) or $saisies_fichiers == array ()) {//pas de saisie fichiers?
		return '';
	}
	
	$nom_dossier = "formulaire_$id_formulaire";
	
	// On crée le dossier
	sous_repertoire(_DIR_FICHIERS,'',true,true);
	sous_repertoire(_DIR_FICHIERS_FORMIDABLE,'',true,true);
	$dossier = sous_repertoire(_DIR_FICHIERS_FORMIDABLE, $nom_dossier, false, true);
	if (strpos($dossier, "$nom_dossier/") === False) {
		return _T('formidable:creer_dossier_formulaire_erreur_impossible_creer', 
			array('dossier'=>_DIR_FICHIERS_FORMIDABLE . $nom_dossier)
		);
	}

	// on créer un fichier de test, pour s'assurer 1. Qu'on puisse écrire dans le rep 2. Qu'on ne puisse pas accéder à ce fichier depuis l'exterieur.
	$fichier = $dossier."test.txt"; 
	$ecriture_ok = ecrire_fichier($fichier, "Ce fichier n'est normalement pas lisible de l'extérieur. Si tel est le cas, il y a un souci de confidentialité.",false);
	if ($ecriture_ok == False) {
		return _T('formidable:creer_dossier_formulaire_erreur_impossible_ecrire', 
			array('dossier'=>_DIR_FICHIERS_FORMIDABLE . $nom_dossier)
		);
	}
	
	include_spip('inc/distant');
	$url = url_absolue($fichier);
	if (recuperer_page ($url)) { // si on peut récuperer la page avec un statut http 200, c'est qu'il y a un problème. recuperer_page() est obsolète en 3.1, mais recuperer_url() n'existe pas en 3.0
		return _T('formidable:creer_dossier_formulaire_erreur_possible_lire_exterieur', 
			array('dossier'=>_DIR_FICHIERS_FORMIDABLE . $nom_dossier)
		);
	}

	// Et si tout va bien
	return '';
}
