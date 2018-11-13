<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Age maximum des fichiers dans le dossier temporaire
 **/
if (!defined('_CVTUPLOAD_AGE_MAX')) {
	define('_CVTUPLOAD_AGE_MAX', 6*3600);
}

/**
 * Nombre maximum de fichiers dans le dossier temporaire
 **/
if (!defined('_CVTUPLOAD_MAX_FILES')) {
	define('_CVTUPLOAD_MAX_FILES', 200);
}


include_spip('base/abstract_sql');
/**
 * Chercher si des champs fichiers ont été déclarés dans le fichier formulaires/xxx.php
 * Sert de condition preliminaire pour les pipelines formulaire_charger, formulaire_verifier et formulaire_fond du plugin
 *
 * @param string $form
 *     le nom du formulaire
 * @param array $args
 *     - l'id de l'objet
 *
 * @return array
 *     valeur(s) de l'attribut 'name' du ou des input de type file dans formulaires/xxx.html
 */
function cvtupload_chercher_fichiers($form, $args) {
	$fichiers = array();

	// S'il existe une fonction de fichiers dédiée à ce formulaire
	if ($fonction_fichiers = charger_fonction('fichiers', 'formulaires/'.$form, true)) {
		$fichiers = call_user_func_array($fonction_fichiers, $args);
	}

	// Dans tous les cas on applique le pipeline, si un plugin veut ajouter des choses
	$fichiers = pipeline(
		'formulaire_fichiers',
		array('args'=>array('form'=>$form, 'args'=>$args), 'data'=>$fichiers)
	);

	return $fichiers;
}

/**
 * Génére le HTML de chaque fichier déjà uploadé
 *
 * @param array $infos_fichiers
 * 		Tableau contenant les informations pour chaque champ de fichier
 * @return array
 * 		Retourne un tableau avec pour chaque champ une clé contenant le HTML
 **/
function cvtupload_generer_html($infos_fichiers = null) {
	static $html_fichiers = array();
	// Si on a des infos de fichiers, on va re-générer du HTML
	if ($infos_fichiers and is_array($infos_fichiers)) {
		foreach ($infos_fichiers as $champ => $fichier) {
			// Si c'est un champ unique
			if (isset($fichier['name'])) {
				$html_fichiers[$champ] = recuperer_fond(
					'formulaires/inc-cvtupload-fichier',
					array_merge($fichier, array(
						'crochets' => "[$champ]",
						'champ'    => "$champ",
					))
				);
			} // Sinon c'est un champ multiple
			else {
				$html_fichiers[$champ] = array();
				foreach ($fichier as $cle => $infos) {
					$html_fichiers[$champ][$cle] = recuperer_fond(
						'formulaires/inc-cvtupload-fichier',
						array_merge($infos, array(
							'crochets' => "[$champ][$cle]",
							'champ'    => $champ . "[$cle]",
						))
					);
				}
			}
		}
	}

	return $html_fichiers;
}

/**
 * Déplace un fichier uploadé dans un endroit temporaire et retourne des informations dessus.
 * @param array $fichier
 * 		Le morceau de $_FILES concernant le ou les fichiers
 * @param string $repertoire
 * 		Chemin de destination des fichiers
 * @param string $form
 * 		Formulaire d'où ça vient
 * @param bool $deplacer
 *		Mettre a False pour se contenter de copier
 * @return array
 * 		Retourne un tableau d'informations sur le fichier ou un tableau de tableaux si plusieurs fichiers. Ce tableau est compatible avec l'action "ajouter_un_fichier" de SPIP.
 **/
function cvtupload_deplacer_fichier($fichier, $repertoire, $form, $deplacer = true) {
	if(!function_exists('deplacer_fichier_upload')){
		include_spip('inc/documents');
	}
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');
	$infos = array();
	// On commence par nettoyer le dossier
	cvtupload_nettoyer_repertoire($repertoire);

	// Si on est sur un upload de type fichier unique, on reformate le tableau pour faire comme si on était en fichiers multiples
	if (!is_array($fichier['name'])) {
		$fichier_unique = true;
		$fichier_nouveau = array();
		foreach ($fichier as $champ => $valeur) {
			$fichier_nouveau[$champ] = array($valeur);
		}
		$fichier = $fichier_nouveau;
	} else {
		$fichier_unique = false;
	}

	foreach ($fichier['name'] as $cle => $nom) {
		if (// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			($nom != null)
			and ($fichier['error'][$cle] == 0)
			// Et qu'on génère bien un nom de fichier aléatoire pour déplacer le fichier
			and $chemin_aleatoire = tempnam($repertoire, $form.'_')
		) {
			$extension = strtolower(pathinfo($fichier['name'][$cle], PATHINFO_EXTENSION));
			if (in_array($extension, array('png','jpg','gif'))) {
				$chemin_aleatoire .= ".$extension";
			}
			// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
			if (deplacer_fichier_upload($fichier['tmp_name'][$cle], $chemin_aleatoire, $deplacer)) {
				$infos[$cle]['tmp_name'] = $chemin_aleatoire;
				$infos[$cle]['name'] = $nom;
				$infos[$cle]['extension'] = $extension;
				// si image on fait une copie avec l'extension pour pouvoir avoir l'image réduite en vignette
				if (in_array($extension, array('png','jpg','gif'))) {
					$infos[$cle]['vignette'] = $chemin_aleatoire;
				} else {
					$infos[$cle]['vignette'] = $vignette_par_defaut($infos[$cle]['extension'], false, true);
				}
				//On récupère le type MIME du fichier aussi
				$infos[$cle]['mime'] = cvt_upload_determiner_mime($fichier['type'][$cle], $infos[$cle]['extension']);
				$infos[$cle]['taille'] = $fichier['size'][$cle];
				// On stocke des infos sur le formulaire
				$infos[$cle]['form'] = $form;
				$infos[$cle]['infos_encodees'] = encoder_contexte_ajax($infos[$cle], $form);
			}
		}
	}

	if ($fichier_unique == true) {
		$infos = $infos[0];
	}
	return $infos;
}

/**
 * Modifier $_FILES pour que le nom et le chemin du fichier temporaire
 * correspondent à ceux qu'on a défini dans cvtupload_deplacer_fichier().
 * Cela permet aux traitements ultérieurs
 * de ne pas avoir à se préoccuper de l'emploi ou non de cvtupload.
 *
 * @param $infos_fichiers
 *  Information sur les fichiers tels que déplacés par cvtupload_deplacer_fichier()
 * @return void
**/
function cvtupload_modifier_files($infos_fichiers) {
	foreach ($infos_fichiers as $champ => $description) {
		if (isset($description['tmp_name'])) {//Upload unique
			 $_FILES[$champ] = array();//On surcharge tout la description $_FILES pour ce champ.  Dans tous les cas les infos ont été stockées dans $description
			 $_FILES[$champ]['name'] = $description['name'];
			 $_FILES[$champ]['tmp_name'] = $description['tmp_name'];
			 $_FILES[$champ]['type'] = $description['mime'];
			 $_FILES[$champ]['error'] = 0; //on fait comme s'il n'y avait pas d'erreur, ce qui n'est pas forcément vrai…
			 $_FILES[$champ]['size'] = $description['taille'];
		} else {//Upload multiple
			//On surcharge tout la description $_FILES pour ce champ. Dans tous les cas les infos ont été stockées dans $description
			if (isset($_FILES[$champ])) {
				$old_FILES_champ = $_FILES[$champ];
			} else {
				$old_FILES_champ = array();
			}
			$_FILES[$champ]['name'] = array();
			$_FILES[$champ]['tmp_name'] = array();
			$_FILES[$champ]['type'] = array();
			$_FILES[$champ]['error'] = array();
			$_FILES[$champ]['size'] = array();
			// Et on re-rempli à partir de $description
			foreach ($description as $fichier_individuel => $description_fichier_individuel) {
				$_FILES[$champ]['name'][$fichier_individuel] = $description_fichier_individuel['name'];
				$_FILES[$champ]['tmp_name'][$fichier_individuel] = $description_fichier_individuel['tmp_name'];
				$_FILES[$champ]['type'][$fichier_individuel] = $description_fichier_individuel['mime'];
				$_FILES[$champ]['error'][$fichier_individuel] = 0; //on fait comme s'il n'y avait pas d'erreur, ce qui n'est pas forcément vrai…
				$_FILES[$champ]['size'][$fichier_individuel] = $description_fichier_individuel['taille'];
			}
			// Si on vient d'envoyer un ou plusieur $champ[] vide, on les rajoute dans notre nouveau $FILES
			if (isset($old_FILES_champ['error']) and is_array($old_FILES_champ['error'])) {
				foreach ($old_FILES_champ['error'] as $id_fichier_individuel => $error_fichier_individuel){
					if ($error_fichier_individuel!=0 and !isset($infos_fichiers[$champ][$id_fichier_individuel])){//Uniquement les erreurs
						$_FILES[$champ]['name'][$id_fichier_individuel] = $old_FILES_champ['name'][$id_fichier_individuel];
						$_FILES[$champ]['tmp_name'][$id_fichier_individuel] = $old_FILES_champ['tmp_name'][$id_fichier_individuel];
						$_FILES[$champ]['type'][$id_fichier_individuel] = $old_FILES_champ['type'][$id_fichier_individuel];
						$_FILES[$champ]['error'][$id_fichier_individuel] = $old_FILES_champ['error'][$id_fichier_individuel];
						$_FILES[$champ]['size'][$id_fichier_individuel] = $old_FILES_champ['size'][$id_fichier_individuel];
					}
				}
			}
			// On remet de l'ordre dans champ dans chaque tableau correspondant à une propriété de $_FILES, histoire d'avoir 0,1,2,3 et pas 3,1,0,2
			foreach ($_FILES[$champ] as $propriete => $valeurs_propriete) {
				ksort($valeurs_propriete);
				$_FILES[$champ][$propriete] = $valeurs_propriete;
			}
		}
	}
}

/**
 * Nettoyer $_FILES pour effacer les entrées dont on a vérifié qu'elle ne répondaient pas à certains critères
 *
 * @param string $champ
 *	Le nom du champ concerné dans $_FILES
 * @param string[]|string $erreurs
 * 	Si un upload multiple, un tableau des $erreurs avec comme clés les numéros des fichiers à supprimer dans $_FILES[$champ]
 * 	Si un upload unique, une chaîne, qui si non vide, indique qu'il faut effacer le $_FILE[$champ]
 * @return void
**/
function cvtupload_nettoyer_files_selon_erreurs($champ, $erreurs) {
	if (is_array($erreurs)) { // cas d'upload multiple
		foreach ($erreurs as $cle => $erreur) {
			foreach ($_FILES[$champ] as $propriete => $valeur) {
				unset($_FILES[$champ][$propriete][$cle]);
			}
		}
	} elseif ($erreurs!='') { // cas d'upload unique avec erreur
		unset($_FILES[$champ]);
	}
}

/**
 * Détermine un MIME lorsque les informations de PHP sont imprécises.
 * Par exemple PHP considère qu'un fichier .tex est de MIME application/octet-stream
 * Ce qui n'est absolument pas utilse
 * @param string $mime_suppose
 * @param string $extension
 * @return string $mime_trouve
**/
function cvt_upload_determiner_mime($mime_suppose, $extension) {
	if (!in_array($mime_suppose, array('text/plain', '', 'application/octet-stream'))) { // si on a un mime précis, on le renvoie, tout simplement
		return $mime_suppose;
	}
	$mime_spip = sql_getfetsel('mime_type', 'spip_types_documents', 'extension='.sql_quote($extension));
	if ($mime_spip) {
		return $mime_spip;
	} else {
		return $mime_suppose;
	}
}

/**
 * Nettoyer un répertoire suivant l'age et le nombre de ses fichiers
 *
 * @param string $repertoire
 * 		Répertoire à nettoyer
 * @param int $age_max
 * 		Age maxium des fichiers en seconde
 * @param int $max_files
 * 		Nombre maximum de fichiers dans le dossier
 * @return void
 **/
function cvtupload_nettoyer_repertoire($repertoire, $age_max = _CVTUPLOAD_AGE_MAX, $max_files = _CVTUPLOAD_MAX_FILES) {
	include_spip('inc/flock');

	// Si on entre bien dans le répertoire
	if ($ressource_repertoire = opendir($repertoire)) {
		$fichiers = array();

		// On commence par supprimer les plus vieux
		while ($fichier = readdir($ressource_repertoire)) {
			if (!in_array($fichier, array('.', '..', '.ok'))) {
				$chemin_fichier = $repertoire.$fichier;

				if (is_file($chemin_fichier) and !jeune_fichier($chemin_fichier, $age_max)) {
					supprimer_fichier($chemin_fichier);
				} else {
					$fichiers[@filemtime($chemin_fichier).'_'.rand()] = $chemin_fichier;
				}
			}
		}

		// On trie les fichiers par ordre de leur date
		ksort($fichiers);

		// Puis s'il reste trop de fichiers, on supprime le surplus
		$nb_fichiers = count($fichiers);
		if ($nb_fichiers > $max_files) {
			$nb_a_supprimer = $nb_fichiers - $max_files - 1;

			while ($nb_a_supprimer) {
				$fichier = array_shift($fichiers);
				supprimer_fichier($fichier);
				$nb_a_supprimer--;
			}
		}
	}
}
