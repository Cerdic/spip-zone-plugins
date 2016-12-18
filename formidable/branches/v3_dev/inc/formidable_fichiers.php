<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/flock');
include_spip('inc/documents');
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
 * @param int $id_formulaire
 * @param bool $forcer, pour forcer la création du dossier même si pas de saisie fichiers
 * @return $erreur
 **/ 
function formidable_creer_dossier_formulaire($id_formulaire, $forcer=false) {
	if (!$forcer){
		include_spip('formulaires/formidable');
		$saisies_fichiers = formulaires_formidable_fichiers($id_formulaire); // Récuperer la liste des saisies de type fichier

		if (!is_array($saisies_fichiers) or $saisies_fichiers == array ()) {//pas de saisie fichiers?
			return '';
		}
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

/**
 * Déplace un fichier uploadé de son adresse temporaire vers son adresse définitive. 
 * Crée si besoin les dossiers de stockage.  
 * 
 * @param string $fichier l'adresse temporaire du fichier
 * @param string $nom le nom du fichiera
 * @param int $id_formulaire l'identifiant du formulaire
 * @param string $champ le champ concerné
 * @param int $id_formulaires_reponse l'identifiant de la réponse
 * @return string $nom_definitif le nom définitif du fichier tel que stocké dans son dossier, vide s'il y a eu un souci lors du déplacement (dans ce cas un courriel sera envoyé au webmestre)
 *
 **/
function formidable_deplacer_fichier_emplacement_definitif($fichier, $nom, $id_formulaire, $champ, $id_formulaires_reponse = null){
	
	// déterminer l'extension
	$path_info = pathinfo($nom);
	$basename = $path_info['basename'];
	$extension = $path_info['extension'];

	// d'abord, créer si besoin le dossier pour le formulaire, si on a une erreur, on ne déplace pas le fichier 
	if (formidable_creer_dossier_formulaire($id_formulaire, true) != '') {
		return '';
	}
	// puis on créer le dossier pour la réponse
	$dossier_formulaire =  "formulaire_$id_formulaire";
	$dossier_reponse = "reponse_$id_formulaires_reponse";
	$dossier_reponse = sous_repertoire(_DIR_FICHIERS_FORMIDABLE.$dossier_formulaire."/", $dossier_reponse,false,true);

	// puis le dossier pour le champ
	$dossier_champ = sous_repertoire($dossier_reponse,$champ,false,true);
	$appendice_nom = 0;

	// S'assurer qu'il n'y a pas un fichier du même nom à destination
	$chemin_final = $dossier_champ.$nom;
	$n = 1;
	while (@file_exists($chemin_final)){
		$nom = $basename."_$n.".$extension;
		$chemin_final = $dossier_champ.$nom;
		$n++;
	}
	// On peut déplacer le fichier
	if ($fichier = deplacer_fichier_upload($fichier, $chemin_final,true)){
		return $nom;
	}
	else{
		return '';
	}

}

/** 
 * Fournit à l'utilisateur·trice un fichier qui se trouve normalement dans un endroit inaccessible, par exemple dans config. 
 * La fonction ne vérifie ni l'existence effective du fichier, 
 * ni le droit effectif de l'utilisateur.
 * Ceci doit être fait dans l'action qui appelle cette fonction
 * @param string $chemin le chemin du fichier
 * @param string $f le nom du fichier qui sera envoyé à l'utilisateur·trice.
 *
**/
function formidable_retourner_fichier($chemin, $f) {
			header('Content-Type: '.mime_content_type($chemin));
			header("Content-Disposition: attachment; filename=\"$f\";");
			header("Content-Transfer-Encoding: binary");
			// fix for IE catching or PHP bug issue (inspiré de plugins-dist/dump/action/telecharger_dump.php
			header("Pragma: public");
			header("Expires: 0"); // set expiration time
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			if ($cl = filesize($chemin)) {
				header("Content-Length: " . $cl);
			}
			readfile($chemin);
			exit;
}
