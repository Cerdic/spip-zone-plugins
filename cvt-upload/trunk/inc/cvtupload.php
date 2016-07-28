<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

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
 * Génére le HTML de chaque fichier déjà uploadés
 * 
 * @param array $infos_fichiers
 * 		Talbeau contenant les informations pour chaque champ de fichier
 * @return array
 * 		Retourne un tableau avec pour chaque champ une clé contenant le HTML
 **/
function cvtupload_generer_html($infos_fichiers = null) {
	static $html_fichiers = array();
	
	// Si on a des infos de fichiers, on va re-générer du HTML
	if ($infos_fichiers and is_array($infos_fichiers)) {
		foreach ($infos_fichiers as $champ=>$fichier) {
			// Si c'est un champ unique
			if (isset($fichier['name'])) {
				$html_fichiers[$champ] = recuperer_fond(
					'formulaires/inc-cvtupload-fichier',
					array_merge($fichier, array('crochets'=>"[$champ]"))
				);
			}
			// Sinon c'est un champ multiple
			else {
				foreach ($fichier as $cle=>$infos) {
					$html_fichiers[$champ][$cle] = recuperer_fond(
						'formulaires/inc-cvtupload-fichier',
						array_merge($infos, array('crochets'=>"[$champ][$cle]"))
					);
				}
			}
		}
	}
	
	return $html_fichiers;
}

/**
 * Déplace un fichier uploadé dans un endroit temporaire et retourne des informations dessus
 *
 * @param array $fichier
 * 		Le morceau de $_FILES concernant le ou les fichiers
 * @param string $repertoire
 * 		Chemin de destination des fichiers
 * @param string $prefix
 * 		Formulaire d'où ça vient
 * @return array
 * 		Retourne un tableau d'informations sur le fichier ou un tableau de tableaux si plusieurs fichiers. Ce tableau est compatible avec l'action "ajouter_un_fichier" de SPIP.
 **/
function cvtupload_deplacer_fichier($fichier, $repertoire, $form) {
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');
	$infos = array();
	
	// On commence par nettoyer le dossier
	cvtupload_nettoyer_repertoire($repertoire);
	
	if (is_array($fichier['name'])) {
		foreach ($fichier['name'] as $cle=>$nom) {
			// On commence par transformer le nom du fichier pour éviter les conflits
			$nom = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($nom))));
			if (
				// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
				($nom != null)
				and ($fichier['error'][$cle] == 0)
				// Et qu'on génère bien un nom de fichier aléatoire pour déplacer le fichier
				and $chemin_aleatoire = tempnam($repertoire, $form.'_')
			) {
				// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
				if (deplacer_fichier_upload($fichier['tmp_name'][$cle], $chemin_aleatoire, true)) {
					$infos[$cle]['tmp_name'] = $chemin_aleatoire;
					$infos[$cle]['name'] = $nom;
					// On en déduit l'extension et du coup la vignette
					$infos[$cle]['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name'][$cle]));
					$infos[$cle]['vignette'] = $vignette_par_defaut($infos[$cle]['extension'], false, true);
					//On récupère le type MIME du fichier aussi
					$infos[$cle]['mime'] = $fichier['type'][$cle];
					$infos[$cle]['form'] = $form;
					$infos[$cle]['infos_encodees'] = encoder_contexte_ajax($infos[$cle], $form);
				}
			}
		}
	}
	else {
		// On commence par transformer le nom du fichier pour éviter les conflits
		$nom = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($fichier['name']))));
		if (
			// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			($nom != null)
			and ($fichier['error'] == 0)
			// Et qu'on génère bien un nom de fichier aléatoire pour déplacer le fichier
			and $chemin_aleatoire = tempnam($repertoire, $form.'_')
		) {
			// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
			if (deplacer_fichier_upload($fichier['tmp_name'], $chemin_aleatoire, true)) {
				$infos['tmp_name'] = $chemin_aleatoire;
				$infos['name'] = $nom;
				// On en déduit l'extension et du coup la vignette
				$infos['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name']));
				$infos['vignette'] = $vignette_par_defaut($infos['extension'], false, true);
				//On récupère le type MIME du fichier aussi
				$infos['mime'] = $fichier['type'];
				$infos['form'] = $form;
				$infos['infos_encodees'] = encoder_contexte_ajax($infos, $form);
			}
		}
	}
	
	return $infos;
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
				}
				else {
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
