<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

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
	define('_CVTUPLOAD_MAX_FILES', 2);
}

function cvtupload_chercher_fichiers($form, $args){
	if ($fonction_fichiers = charger_fonction('fichiers', 'formulaires/'.$form, true)
		and $fichiers = call_user_func_array($fonction_fichiers, $args)
		and is_array($fichiers)
		and $fichiers = pipeline(
			'formulaire_fichiers',
			array('args'=>array('form'=>$form, 'args'=>$args), 'data'=>$fichiers)
		)
		and is_array($fichiers)
	){
		return $fichiers;
	}

	return false;
}

function cvtupload_hash(){
	include_spip('inc/session');
	return session_get('hash_env').'_'._request('hash');
}

function cvtupload_formulaire_charger($flux){
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])){
		$contexte =& $flux['data'];
		
		// On déclare le champ qui contiendra les infos en hidden
		$contexte['cvtupload_fichiers_precedents'] = array();
		
		// On met dans le contexte le HTML pour les fichiers précédemment postés
		if ($html_fichiers = cvtupload_generer_html()) {
			$contexte['_fichiers_precedents_html'] = $html_fichiers;
		}
	}
	
	return $flux;
}

function cvtupload_formulaire_verifier($flux){
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])){
		$erreurs =& $flux['data'];
		include_spip('inc/filtres');
		include_spip('inc/documents');
		include_spip('inc/getdocument');
		include_spip('inc/flock');
		
		//Si le répertoire temporaire n'existe pas encore, il faut le créer.
		$repertoire_tmp = sous_repertoire(_DIR_TMP.'cvtupload/');
		
		// On récupère les anciens fichiers déjà postés
		$infos_fichiers_precedents = _request('cvtupload_fichiers_precedents');
		$infos_fichiers = array();
		
		// Les demandes de suppression
		$supprimer_fichier = _request('cvtupload_supprimer_fichier');
		
		// On parcourt les champs déclarés comme étant des fichiers
		foreach ($champs_fichiers as $champ){
			// On commence par ne récupérer que les anciennes informations
			// Si ce champ de fichier est multiple, on décode chaque champ
			if (is_array($infos_fichiers_precedents[$champ])) {
				foreach ($infos_fichiers_precedents[$champ] as $cle=>$fichier) {
					if ($infos_decodees = decoder_contexte_ajax($fichier, $flux['args']['form'])) {
						$infos_fichiers[$champ][$cle] = $infos_decodees;
						$infos_fichiers[$champ][$cle]['infos_encodees'] = encoder_contexte_ajax($infos_decodees, $flux['args']['form']);
						
						// Si suppression
						if (isset($supprimer_fichier[$champ][$cle])) {
							supprimer_fichier($infos_fichiers[$champ][$cle]['tmp_name']);
							unset($infos_fichiers[$champ][$cle]);
							// On génère une erreur pour réafficher le form de toute façon
							$erreurs["rien_$champ_$cle"] = 'rien';
						}
					}
				}
			}
			// Si le champ est unique, on décode juste le champ
			elseif ($infos_decodees = decoder_contexte_ajax($infos_fichiers_precedents[$champ], $flux['args']['form'])) {
				$infos_fichiers[$champ] = $infos_decodees;
				$infos_fichiers[$champ]['infos_encodees'] = encoder_contexte_ajax($infos_decodees, $flux['args']['form']);
				
				// Si suppression
				if (isset($supprimer_fichier[$champ])) {
					supprimer_fichier($infos_fichiers[$champ]['tmp_name']);
					unset($infos_fichiers[$champ]);
					// On génère une erreur pour réafficher le form de toute façon
					$erreurs["rien_$champ"] = 'rien';
				}
			}
			
			// On déplace le fichier dans notre dossier tmp de SPIP
			// Et on met à jour les infos par rapport aux anciennes
			if (
				$_FILES[$champ]
				and $infos = cvtupload_deplacer_fichier($_FILES[$champ], $repertoire_tmp, $flux['args']['form'])
			){
				if (isset($infos_fichiers[$champ])) {
					$infos_fichiers[$champ] = array_merge($infos_fichiers[$champ], $infos);
				}
				else {
					$infos_fichiers[$champ] = $infos;
				}
			}
		}
		
		// On utilise ces infos pour générer le HTML et le garder pour charger()
		cvtupload_generer_html($infos_fichiers);
	}
	
	return $flux;
}

function cvtupload_formulaire_fond($flux){
	// Si ça a déjà été posté (après verifier()) et qu'il y a des champs fichiers déclarés
	if (
		$flux['args']['je_suis_poste']
		and $champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])
	) {
		include_spip('inc/filtres');
		$fichiers = $flux['args']['contexte']['_fichiers_precedents_html'];
		
		foreach ($champs_fichiers as $champ) {
			// Si le visiteur a bien réussi a charger un ou plusieurs fichiers dans ce champ
			if (isset($fichiers[$champ])) {
				// Si c'est un champ unique
				if (!is_array($fichiers[$champ])) {
					$flux['data'] = preg_replace(
						"#<input[^>]*name=['\"]${champ}[^>]*>#i",
						$fichiers[$champ],
						$flux['data']
					);
				}
				// Sinon c'est un multiple
				else {
					foreach ($fichiers[$champ] as $cle=>$html) {
						$flux['data'] = preg_replace(
							"#<input[^>]*name=['\"]${champ}[^>]*>#i",
							$html,
							$flux['data'],
							1 // seul le premier trouvé est remplacé
						);
					}
				}
			}
		}
	}
	
	return $flux;
}

function cvtupload_formulaire_traiter($flux){
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])){
		$hash = cvtupload_hash();
		// On supprime le répertoire unique comportant les fichiers du visiteur
		$repertoire = _DIR_TMP.'cvtupload/'.$hash.'/';
		supprimer_repertoire($repertoire);
		session_set($hash.'_fichiers', null);
	}
	
	return $flux;
}

/**
 * Génére le HTML de chaque fichier déjà uploadés
 * 
 * @param array $infos_fichiers
 * 		Talbeau contenant les informations pour chaque champ de fichier
 * @return array
 * 		Retourne un tableau avec pour chaque champ une clé contenant le HTML
 **/
function cvtupload_generer_html($infos_fichiers=null){
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
function cvtupload_deplacer_fichier($fichier, $repertoire, $form){
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');
	$infos = array();
	
	// On commence par nettoyer le dossier
	cvtupload_nettoyer_repertoire($repertoire);
	
	if (is_array($fichier['name'])){
		foreach ($fichier['name'] as $cle=>$nom){
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
	else{
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
function cvtupload_nettoyer_repertoire($repertoire, $age_max=_CVTUPLOAD_AGE_MAX, $max_files=_CVTUPLOAD_MAX_FILES) {
	include_spip('inc/flock');
	
	// Si on entre bien dans le répertoire
	if ($ressource_repertoire = opendir($repertoire)) {
		$fichiers = array();
		
		// On commence par supprimer les plus vieux
		while ($fichier = readdir($ressource_repertoire)) {
			if (!in_array($fichier, array('.', '..', '.ok'))) {
				$chemin_fichier = $repertoire.$fichier;
				
				if (is_file($fichier) and !jeune_fichier($chemin_fichier, $age_max)) {
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
