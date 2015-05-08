<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

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
		// S'il n'y a pas déjà une action de configurée, on en force une pour avoir un hash unique par visiteur
		if (!$contexte['_action'])
			$contexte['_action'] = array('cvtupload', 'cvtupload');
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
		include_spip('inc/charsets');
		
		$hash = cvtupload_hash();
		
		//Si le répertoire temporaire n'existe pas encore, il faut le créer.
		$repertoire_tmp = sous_repertoire(_DIR_TMP.'cvtupload/');
		$repertoire_tmp = sous_repertoire($repertoire_tmp, $hash.'/');
		
		// On parcourt les champs déclarés comme étant des fichiers
		$infos_fichiers = session_get($hash.'_fichiers') ? session_get($hash.'_fichiers') : array();
		foreach ($champs_fichiers as $champ){
			if ($_FILES[$champ]){
				$infos = cvtupload_deplacer_fichier($_FILES[$champ], $repertoire_tmp);
				if (isset($infos_fichiers[$champ]))
					$infos_fichiers[$champ] = array_merge($infos_fichiers[$champ], $infos);
				else
					$infos_fichiers[$champ] = $infos;
			}
		}
		set_request('_fichiers', $infos_fichiers);
		session_set($hash.'_fichiers', $infos_fichiers);
	}
	
	return $flux;
}

function cvtupload_formulaire_fond($flux){
	// Si ça a déjà été posté (après verifier()) et qu'il y a des champs fichiers déclarés
	if ($flux['args']['je_suis_poste']
		and $champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])
	){
		$fichiers = _request('_fichiers');
		foreach ($champs_fichiers as $champ){
			// Si le visiteur a bien réussi a charger un ou plusieurs fichiers dans ce champ
			if (isset($fichiers[$champ])){
				include_spip('inc/filtres_images');
				// Si c'est un champ unique
				if (isset($fichiers[$champ]['name'])){
					$flux['data'] = preg_replace(
						"#<input[^>]*name=['\"]${champ}[^>]*>#i",
						image_reduire($fichiers[$champ]['vignette'],32).' '.$fichiers[$champ]['name'],
						$flux['data']
					);
				}
				// Sinon c'est un multiple
				else{
					foreach($fichiers[$champ] as $cle=>$fichier){
						$flux['data'] = preg_replace(
							"#<input[^>]*name=['\"]${champ}[^>]*>#i",
							image_reduire($fichier['vignette'],32).' '.$fichier['name'],
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

/*
 * Déplace un fichier uploadé dans un endroit temporaire et retourne des informations dessus
 *
 * @param array $fichier Le morceau de $_FILES concernant le ou les fichiers
 * @param string $$repertoire Chemin de destination des fichiers
 * @return array Retourne un tableau d'informations sur le fichier ou un tableau de tableaux si plusieurs fichiers. Ce tableau est compatible avec l'action "ajouter_un_fichier" de SPIP.
 */
function cvtupload_deplacer_fichier($fichier, $repertoire){
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');
	$infos = array();
	if (is_array($fichier['name'])){
		foreach ($fichier['name'] as $cle=>$nom){
			// On commence par transformer le nom du fichier pour éviter les conflits
			$nom = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($nom))));
			// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			if (($nom != null) and ($fichier['error'][$cle] == 0)) {
				//On vérifie qu'un fichier ne porte pas déjà le même nom, sinon on lui donne un nom aléatoire + nom original
				if (file_exists($repertoire.$nom))
					$nom = $nom.'_'.rand();
				// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
				if (deplacer_fichier_upload($fichier['tmp_name'][$cle], $repertoire.$nom, true)) {
					$infos[$cle]['tmp_name'] = $repertoire.$nom;
					$infos[$cle]['name'] = $nom;
					// On en déduit l'extension et du coup la vignette
					$infos[$cle]['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name'][$cle]));
					$infos[$cle]['vignette'] = $vignette_par_defaut($infos[$cle]['extension'], false, true);
					//On récupère le type MIME du fichier aussi
					$infos[$cle]['mime'] = $fichier['type'][$cle];
				}
			}
		}
	}
	else{
		// On commence par transformer le nom du fichier pour éviter les conflits
		$nom = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($fichier['name']))));
		// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
		if (($nom != null) && ($fichier['error'] == 0)) {
			//On vérifie qu'un fichier ne porte pas déjà le même nom, sinon on lui donne un nom aléatoire + nom original
			if (file_exists($repertoire.$nom))
				$nom = $nom.'_'.rand();
			// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
			if (deplacer_fichier_upload($fichier['tmp_name'], $repertoire.$nom, true)) {
				$infos['tmp_name'] = $repertoire.$nom;
				$infos['name'] = $nom;
				// On en déduit l'extension et du coup la vignette
				$infos['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name']));
				$infos['vignette'] = $vignette_par_defaut($infos['extension'], false, true);
				//On récupère le type MIME du fichier aussi
				$infos['mime'] = $fichier['type'];
			}
		}
	}
	
	return $infos;
}

?>
