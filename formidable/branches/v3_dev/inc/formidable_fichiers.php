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
if (!defined('_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL')) {
	define ('_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL',24*3600); // Combien de temps un lien par email dans fichier est valable (en seconde)
}
if (!defined('_FORMIDABLE_EFFACEMENT_FICHIERS_EMAIL')) {
	define ('_FORMIDABLE_EFFACEMENT_FICHIERS_EMAIL', _FORMIDABLE_EXPIRATION_FICHIERS_EMAIL); // Au bout de combien de temps efface-t-on les fichiers enregistrés lorsque le traitement est uniquement email? 
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
 * @param string $nom le nom du fichier 
 * @param string $mime le mime du fichier
 * @param string $extension l'extension du fichier 
 * @param string $champ le champ concerné
 * @return string $nom_definitif le nom définitif du fichier tel que stocké dans son dossier, vide s'il y a eu un souci lors du déplacement (dans ce cas un courriel sera envoyé au webmestre)
 *
 **/
function formidable_deplacer_fichier_emplacement_definitif($fichier, $nom, $mime, $extension, $champ, $options){
	if (isset($options['id_formulaire'])) {
		$id_formulaire = $options['id_formulaire'];
		$dossier_formulaire =  "formulaire_$id_formulaire";
	}
	else {// si c'est pas set, c'est qu'il y a une erreur
		return '';
	}

	if (isset($options['id_formulaires_reponse'])) {
		$dossier_reponse = "reponse_".$options['id_formulaires_reponse'];
	} elseif (isset($options['timestamp'])){
		$dossier_reponse = "reponse_".$options['timestamp'];
	} else { // si ni timestamp, ni id_formulaires_reponse => erreur
		return '';
	}
	// déterminer le basename
	$basename = pathinfo($nom, PATHINFO_BASENAME);

	// sécurité : si la combinaison extension/mime_type est inconnu de SPIP (spip_documents_type), on zip. 
	// On n'utilise volontairement pas verifier/fichiers.php, dès fois que celui-ci évolue dans le future
	$res = sql_select('mime_type','spip_types_documents','mime_type='.sql_quote($mime).' and extension='.sql_quote($extension));
	if (sql_count($res) == 0) {
		$zipper = True;
		$nom_dans_zip = $nom;
		// pas de fichier nom de zip commencant par point
		while (strpos($basename,'.') === 0){
			$basename = substr($basename,1);		
		}
		$nom = "$basename.zip";
	} else {
		$zipper = False;	
	}
	if (!isset($options['timestamp'])) { // si on enregistre la réponse en base

		// d'abord, créer si besoin le dossier pour le formulaire, si on a une erreur, on ne déplace pas le fichier 
		if (formidable_creer_dossier_formulaire($id_formulaire, true) != '') {
			return '';
		}
		
		// puis on créer le dossier pour la réponse
		$dossier_reponse = sous_repertoire(_DIR_FICHIERS_FORMIDABLE.$dossier_formulaire."/", $dossier_reponse,false,true);

		// puis le dossier pour le champ
		$dossier_champ = sous_repertoire($dossier_reponse,$champ,false,true);
		$appendice_nom = 0;
	} else { // si on enregistre sous forme de timestamp
		sous_repertoire(_DIR_FICHIERS,'',true,true);
		sous_repertoire(_DIR_FICHIERS_FORMIDABLE,'',true,true);
		$dossier = sous_repertoire(_DIR_FICHIERS_FORMIDABLE, 'timestamp', false, true);
		$dossier = sous_repertoire($dossier, $options['timestamp'],false,true);
		$dossier_champ = sous_repertoire($dossier,$champ,false,true);
	}
	// S'assurer qu'il n'y a pas un fichier du même nom à destination
	$chemin_final = $dossier_champ.$nom;
	$n = 1;
	$basename_sans_extension = substr_replace($basename,"",-strlen($extension)-1);//la constante PATHINFO_FILENAME n'est qu'à partir de PHP 5.2, or SPIP 3 peut fonctionne en PHP 5.1
	while (@file_exists($chemin_final)){
		$nom = $basename_sans_extension."_$n.".$extension;
		$chemin_final = $dossier_champ.$nom;
		$n++;
	}
	if (!$zipper) { // si on ne zippe pas, c'est simple
		if ($fichier = deplacer_fichier_upload($fichier, $chemin_final,true)) {
			return $nom; 
		} else {
			return '';
		}
	} else { // si on doit zipper, c'est plus complexe
		include_spip('inc/pclzip');
		$zip = new PclZip($chemin_final);
		// mettre à jour le fichier dans le dossier cvtupload
		if (!$tmp_dir = tempnam($dossier_champ, 'tmp_upload')) {
			return '';
		}
		spip_unlink($tmp_dir);
		@mkdir($tmp_dir);
		$old_fichier = $fichier;
		if (!$fichier = deplacer_fichier_upload($fichier,$tmp_dir."/".$nom_dans_zip,false)) {
			return '';
		}
		$zip_final = $zip -> create($fichier, 
			PCLZIP_OPT_REMOVE_PATH, $tmp_dir,
			PCLZIP_OPT_ADD_PATH, '');
		if (!$zip_final){
			return '';
		} else {
			spip_unlink($old_fichier);
			supprimer_repertoire($tmp_dir);
			return $nom;
		}
	}

	return $nom;
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
/**
 * Déplacer un fichier temporaire à son emplacement définif. 
 * Produire un tableau de description des fichiers déplacés.
 * Le tout à partir de la description d'une saisies 'fichiers'
 * @param array $saisie la description de la saisie fichiers
 * @param array $options des options, dépendante du type de traitement, qui permettent d'indiquer où l'on déplace le fichier
 * return array un tableau de "vue" de la saisie
**/ 
function formidable_deplacer_fichiers_produire_vue_saisie($saisie, $options) {
	$nb_fichiers_max = $saisie['options']['nb_fichiers']; // on va parcourir $_FILES en nous limitant aux nombres de fichiers définies par la saisie, pour éviter les éventuelles ajout supplémentaire de fichiers par modif du html
	$champ = $saisie['options']['nom'];
	if (!isset($_FILES[$champ])) {//précaution
		return null;
	}
	$description_fichiers = array();
	$mon_file = $_FILES[$champ];
	$i = 0;
	while ($i < $nb_fichiers_max) {
		if ($mon_file['error'][$i] == 0) { // la saisie fichiers est forcément structurée sous la forme d'un tableau, on peut donc vérifier qu'il n'y a pas d'erreur facilement
			$description = array(); // tableau pour stocker la description de ce fichier

			// les infos qu'on peut récuperer directement de $files
			$description['taille'] = $mon_file['size'][$i];
			$description['mime'] = $mon_file['type'][$i];

			// l'adresse du nouveau fichier, sans le chemin
			if ($nouveau_nom = formidable_deplacer_fichier_emplacement_definitif(
				$mon_file['tmp_name'][$i],
				$mon_file['name'][$i],
				$mon_file['type'][$i],
				pathinfo($mon_file['name'][$i], PATHINFO_EXTENSION),	
				$champ,
				$options 
				)
			) {
					$description['nom'] = $nouveau_nom;
					$description['extension'] = pathinfo($nouveau_nom, PATHINFO_EXTENSION); 
			} else {
				$description['erreur'] = _T('formidable:erreur_deplacement_fichier', array('nom'=>$mon_file['name'][$i]));
				$description['nom'] = $mon_file['name'][$i];
				$description['tmp_name'] = $mon_file['tmp_name'][$i];
			}
			$description_fichiers[] = $description;//on ajoute la description au tableau global

		}
		$i++;
	}
	return $description_fichiers;
}
/**
 * Efface les fichiers d'un formulaire
 * @param $str $id_formulaire
 * @return int 1 ou 0 selon que l'on a effacé ou non un répertoire
**/
function formidable_effacer_fichiers_formulaire($id_formulaire){
	$chemin = _DIR_FICHIERS_FORMIDABLE."formulaire_$id_formulaire";
	if (file_exists($chemin)) {// par sécurité
		if (supprimer_repertoire($chemin)){
			return 1;
		} else {
			return 0;
		}
	}
	return 0;
}

/**
 * efface les fichiers d'une réponse formidable
 * @param $str $id_formulaire
 * @param $str $id_formulaires_reponse
 * @return int 1 ou 0 selon que l'on a effacé ou non un répertoire
**/
function formidable_effacer_fichiers_reponse($id_formulaire, $id_formulaires_reponse){
	$chemin = _DIR_FICHIERS_FORMIDABLE."formulaire_$id_formulaire/reponse_$id_formulaires_reponse";
	if (file_exists($chemin)) {// par sécurité
		if (supprimer_repertoire($chemin)){
			return 1;
		} else {
			return 0;
		}
	}
	return 0;
}

/** Efface les fichiers d'un champ pour les réponses d'un formulaire
 * @param str $id_formulaire
 * @param array|str $reponses
 * @param str $champ
**/
function formidable_effacer_fichiers_champ($id_formulaire, $reponses, $champ) {
	if ($champ != '') { // on devrait pas arriver ici avec un $champ vide, mais prenons nos précaution
	
		if (!is_array($reponses)) {
			$reponses = array($reponses);
		}

		$rep_vide = array(".",'..','.ok'); // si scandire retourne cela où inférieur, alors le dossier est vide 
		foreach ($reponses as $rep) {
			$chemin_reponse = _DIR_FICHIERS_FORMIDABLE."formulaire_$id_formulaire/reponse_$rep";
			$chemin_champ = $chemin_reponse."/".$champ;

			if (file_exists($chemin_champ)) {
				supprimer_repertoire($chemin_champ);
				if (count(array_diff(scandir($chemin_reponse), $rep_vide)) == 0) { // si jamais il ne reste plus aucun fichiers pour cette réponse, on peut effacer le repertoire de celle-ci
					supprimer_repertoire($chemin_reponse);
				}
			}

		}
	}	
}

/** Efface les fichiers des réponses par email
 * lorsque la constante _FORMIDABLE_EFFACEMENT_FICHIERS_EMAIL est différent de 0 et que le temps est écoulé
 * @return int nombre de dossiers effacés
 **/
function formidable_effacer_fichiers_email() {
	if (_FORMIDABLE_EFFACEMENT_FICHIERS_EMAIL == 0) {
		return 0;
	}
	$dossiers_effaces = 0;
	$chemin = _DIR_FICHIERS_FORMIDABLE."timestamp";
	$timestamp = time();
	foreach (scandir($chemin) as $dossier){
		if (strval(intval($dossier))!=$dossier) { // on ne traite que les dossiers qui ont comme nom un entier
			continue;
		}
		if ($timestamp - intval($dossier) >= _FORMIDABLE_EFFACEMENT_FICHIERS_EMAIL) {
			$chemin_complet = "$chemin/$dossier";
			var_dump($chemin_complet);
			if (supprimer_repertoire($chemin_complet)) {
				$dossiers_effaces++;
			}	
		}
	}
	return $dossiers_effaces;
}

/**
 * Générer une url d'action pour la récupération d'un fichier lié à une réponse
 * @param int|str $id_formulaire
 * @param int|str $id_formulaires_reponse
 * @param str $saisie
 * @param str $fichier
 **/
function formidable_generer_url_action_recuperer_fichier($id_formulaire, $id_formulaires_reponse, $saisie, $fichier) {
	$param = array(
		'formulaire' => $id_formulaire,
		'reponse' => $id_formulaires_reponse,
		'saisie' => $saisie,
		'fichier' => $fichier
	);

	// Pour les utilisateurs non authentifiés, on se base sur le cookier
	$nom_cookie = formidable_generer_nom_cookie($id_formulaire);
	if (isset($_COOKIE[$nom_cookie])) {
		include_spip('inc/securiser_action');
		$param['cookie'] = sha1($_COOKIE[$nom_cookie].secret_du_site());
	}

	$param = serialize($param);
	$securiser_action = charger_fonction('securiser_action','inc');
	return $securiser_action('formidable_recuperer_fichier',$param,'',      false);
}
