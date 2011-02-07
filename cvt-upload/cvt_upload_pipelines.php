<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function cvt_upload_formulaire_charger($flux){
	$contexte =& $flux['data'];
	
	if ($contexte['_champs_fichiers'] and is_array($contexte['_champs_fichiers'])){
		//$contexte['_pipelines']['editer_contenu_cvt_upload'] = array('champs_fichiers' => $contexte['_champs_fichiers']);
		foreach ($contexte['_champs_fichiers'] as $champ){
			$contexte['_hidden'] .= '<input type="hidden" name="_champs_fichiers[]" value="'.$champ.'" />';
		}
		// S'il n'y a pas déjà une action de configurée, on en force une pour avoir un hash unique par visiteur
		if (!$contexte['_action'])
			$contexte['_action'] = array('cvt_upload');
	}
	
	return $flux;
}

function cvt_upload_formulaire_verifier($flux){
	$erreurs =& $flux['data'];
	
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = _request('_champs_fichiers') and is_array($champs_fichiers)){
		include_spip('inc/filtres');
		include_spip('inc/documents');
		include_spip('inc/getdocument');
		include_spip('inc/charsets');
		//Si le répertoire temporaire n'existe pas encore, il faut le créer.
		$repertoire_tmp = _DIR_TMP.'cvt_upload/';
		if (!is_dir($repertoire_tmp)) mkdir($repertoire_tmp);
		$repertoire_tmp .= _request('hash').'/';
		if (!is_dir($repertoire_tmp)) mkdir($repertoire_tmp);
		
		// On parcourt les champs déclarés comme étant des fichiers
		$infos_fichiers = _request('_infos_fichiers');
		foreach ($champs_fichiers as $champ){
			if ($_FILES[$champ]){
				$infos = cvt_upload_deplacer_fichier($_FILES[$champ], $repertoire_tmp);
				if (isset($infos_fichiers[$champ]))
					$infos_fichiers[$champ] = array_merge($infos_fichiers[$champ], $infos);
				else
					$infos_fichiers[$champ] = $infos;
			}
		}
		if ($erreurs)
			$erreurs = array_merge($erreurs, $infos_fichiers);
	}
	//var_dump($erreurs);
	
	return $flux;
}

function cvt_upload_formulaire_traiter($flux){
	// On supprime tous les fichiers maintenant que les traitements sont faits
	if ($champs_fichiers = _request('_champs_fichiers') and is_array($champs_fichiers)){
		$repertoire = _DIR_TMP.'cvt_upload/'._request('hash').'/';
		if (is_dir($repertoire)){
			$infos_fichiers = _request('_infos_fichiers');
			foreach ($champs_fichiers as $champ){
				if ($infos_fichiers[$champ]['nom']){
					unlink($repertoire.$infos_fichiers[$champ]['nom']);
				}
				else foreach ($infos_fichiers[$champ] as $fichier){
					unlink($repertoire.$fichier['nom']);
				}
			}
			// On supprime le répertoire
			rmdir($repertoire);
		}
	}
	
	return $flux;
}

function cvt_upload_editer_contenu_cvt_upload($flux){
	return $flux;
}

/*
 * Déplace un fichier uploadé dans un endroit temporaire et retourne des informations dessus
 *
 * @param array $fichier Le morceau de $_FILES concernant le fichier
 * @return array Retourne un tableau d'informations sur le fichier
 */
function cvt_upload_deplacer_fichier($fichier, $repertoire){
	$infos = array();
	if (is_array($fichier['name'])){
		foreach ($fichier['name'] as $cle=>$nom){
			// On commence par transformer le nom du fichier pour éviter les conflits
			$nom = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($nom))));
			// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			if (($nom != null) && ($fichier['error'][$cle] == 0)) {
				//On vérifie qu'un fichier ne porte pas déjà le même nom, sinon on lui donne un nom aléatoire + nom original
				if (file_exists($repertoire.$nom))
					$nom = $nom.'_'.rand();
				// Déplacement du fichier vers le dossier de réception temporaire + récupération d'infos
				if (deplacer_fichier_upload($fichier['tmp_name'][$cle], $repertoire.$nom, true)) {
					$infos[$cle]['nom'] = $nom;
					// On en déduit l'extension et du coup la vignette
					$infos[$cle]['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name'][$cle]));
					$infos[$cle]['vignette'] = vignette_par_defaut($infos[$cle]['extension'], false, true);
					//On récupère le tye MIME du fichier aussi
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
				$infos['nom'] = $nom;
				// On en déduit l'extension et du coup la vignette
				$infos['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $fichier['name']));
				$infos['vignette'] = vignette_par_defaut($infos['extension'], false, true);
				//On récupère le tye MIME du fichier aussi
				$infos['mime'] = $fichier['type'];
			}
		}
	}
	
	return $infos;
}

?>
