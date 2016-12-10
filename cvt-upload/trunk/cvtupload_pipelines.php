<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cvtupload');

function cvtupload_formulaire_charger($flux) {
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])) {
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

function cvtupload_formulaire_verifier($flux) {
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])) {
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
			
			// On déplace le(s) fichier(s) dans notre dossier tmp de SPIP
			// Et on met à jour les infos par rapport aux anciennes versions
			if (
				isset($_FILES[$champ])
				and $infos = cvtupload_deplacer_fichier($_FILES[$champ], $repertoire_tmp, $flux['args']['form'],$champ)
			){
				if (isset($infos_fichiers[$champ])) {
					$infos_fichiers[$champ] = array_merge($infos_fichiers[$champ], $infos);
				}
				else {
					$infos_fichiers[$champ] = $infos;
				}
			}
		}
		set_request('_fichiers',$infos_fichiers);
		// On utilise ces infos pour générer le HTML et le garder pour charger()
		cvtupload_generer_html($infos_fichiers);
		cvtupload_modifier_files($infos_fichiers);//On modifier $_FILES pour que cela soit transparent pour les traitements futurs
	}
	
	return $flux;
}

function cvtupload_formulaire_fond($flux) {
	// Si ça a déjà été posté (après verifier()) et qu'il y a des champs fichiers déclarés
	if (
		$flux['args']['je_suis_poste']
		and $champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])
	) {
		include_spip('inc/filtres');
		if (
			isset($flux['args']['contexte']['_fichiers_precedents_html'])
			and $fichiers = $flux['args']['contexte']['_fichiers_precedents_html']
		) {
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
							$regexp_par_cle = "#<input[^>]*name=['\"]${champ}(?:\&\#91;|\[)${cle}(?:\&\#93;|\])[^>]*>#i";// cherche les <input name="champ[cle]"> ou <input name="champ#91;cle#93;">
							$regexp_alternative = "#<input[^>]*name=['\"]${champ}[^>]*>#i";
							
							// On commence par chercher si on a un name avec clé numérique explicite
							$flux['data'] = preg_replace(
								$regexp_par_cle,
								$html,
								$flux['data'],
								1, // seul le premier trouvé est remplacé
								$remplacement_effectue
							);
							if ($remplacement_effectue==0) {// Si pas de name avec clef numérique correspondante, on modifie le premier name avec clé implicite
								$flux['data'] = preg_replace(
									$regexp_alternative,
									$html,
									$flux['data'],
									1 // seul le premier trouvé est remplacé
								);
							}
						}
					}
				}
			}
		}
	}
	return $flux;
}
