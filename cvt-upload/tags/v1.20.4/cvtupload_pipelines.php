<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/cvtupload');

/**
 * Charger les informations qui correspondent aux champs fichiers du formulaire
 * @param array $flux
 * @return array mixed
 */
function cvtupload_formulaire_charger($flux) {
	// S'il y a des champs fichiers de déclarés
	if ($champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])) {
		$contexte =& $flux['data'];
		// On déclare le champ qui contiendra les infos en hidden
		$contexte['cvtupload_fichiers_precedents'] = array();

		// On met dans le contexte le HTML pour les fichiers précédemment postés
		// cvtupload_generer_html() a prepare son contenu lors de son appel depuis verifier()
		$forcer = _request('_cvtupload_precharger_fichiers_forcer');
		if ($html_fichiers = cvtupload_generer_html()
		  and $_fichiers = _request('_fichiers')) {
			$contexte['_fichiers_precedents_html'] = $html_fichiers;
		}
		elseif (isset($flux['data']['cvtupload_precharger_fichiers'])
		  and ($flux['args']['je_suis_poste']==false or $forcer == true)) {
			$precharger_fichiers = charger_fonction('cvtupload_precharger_fichiers', 'inc');
			$contexte['_fichiers_precedents_html'] = cvtupload_generer_html($precharger_fichiers($flux['data']['cvtupload_precharger_fichiers'],$flux['args']['form']));
		}
	}

	return $flux;
}

/**
 * Verifier les contenus uploades sur les champs fichiers
 * @param array $flux
 * @return array
 */
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
		foreach ($champs_fichiers as $champ) {
			// On commence par ne récupérer que les anciennes informations
			// Si ce champ de fichier est multiple, on décode chaque champ
			if (isset($infos_fichiers_precedents[$champ])) {
				if (is_array($infos_fichiers_precedents[$champ])) {
					foreach ($infos_fichiers_precedents[$champ] as $cle => $fichier) {
						if ($infos_decodees = decoder_contexte_ajax($fichier, $flux['args']['form'])) {
							$infos_fichiers[$champ][$cle] = $infos_decodees;
							$infos_fichiers[$champ][$cle]['infos_encodees'] = encoder_contexte_ajax($infos_decodees, $flux['args']['form']);

							// Si suppression ou un autre fichier uploadé en remplacement
							if (isset($supprimer_fichier[$champ][$cle])
								or (
									isset($_FILES[$champ]['name'][$cle])
									and $_FILES[$champ]['error'][$cle] === UPLOAD_ERR_OK
								)
							) {
								supprimer_fichier($infos_fichiers[$champ][$cle]['tmp_name']);
								$name = $infos_fichiers[$champ][$cle]['name'];
								unset($infos_fichiers[$champ][$cle]);
								if (!count($infos_fichiers[$champ])) {
									unset($infos_fichiers[$champ]);
								}
								if (isset($supprimer_fichier[$champ][$cle])) {
									// On génère une erreur pour réafficher le form de toute façon
									$erreurs["$champ"] = _T('cvtupload:erreur_fichier_supprime', array('nom' => $name));
								}
							}
						}
					}
				} // Si le champ est unique, on décode juste le champ
				elseif ($infos_decodees = decoder_contexte_ajax($infos_fichiers_precedents[$champ], $flux['args']['form'])) {
					$infos_fichiers[$champ] = $infos_decodees;
					$infos_fichiers[$champ]['infos_encodees'] = encoder_contexte_ajax($infos_decodees, $flux['args']['form']);

					// Si suppression ou un autre fichier uploadé en remplacement
					if (isset($supprimer_fichier[$champ])
						or (
							isset($_FILES[$champ]['name'])
							and $_FILES[$champ]['error'] === UPLOAD_ERR_OK
						)
					) {
						supprimer_fichier($infos_fichiers[$champ]['tmp_name']);
						$name = $infos_fichiers[$champ]['name'];
						unset($infos_fichiers[$champ]);
						if (isset($supprimer_fichier[$champ])) {
							// On génère une erreur pour réafficher le form de toute façon
							$erreurs["$champ"] = _T('cvtupload:erreur_fichier_supprime', array('nom' => $name));
						}
					}
				}
			}

			// On déplace le(s) fichier(s) dans notre dossier tmp de SPIP
			// Et on met à jour les infos par rapport aux anciennes versions
			if (isset($_FILES[$champ])
				and $infos = cvtupload_deplacer_fichier($_FILES[$champ], $repertoire_tmp, $flux['args']['form'], $champ)
			) {
				if (isset($infos_fichiers[$champ])) {
					$infos_fichiers[$champ] = $infos_fichiers[$champ] + $infos;//ne pas utiliser array_merge, car sinon cela réindexe le tableau, et cela nous perturbe pour le déplacement de $_FILES
					ksort($infos_fichiers[$champ]);
				} else {
					$infos_fichiers[$champ] = $infos;
				}
			}
		}
		set_request('_fichiers', $infos_fichiers);
		// On utilise ces infos pour générer le HTML et le garder pour charger()
		cvtupload_generer_html($infos_fichiers);
		cvtupload_modifier_files($infos_fichiers);//On modifier $_FILES pour que cela soit transparent pour les traitements futurs
	}

	return $flux;
}

/**
 * Nettoyer le FILES s'il y a des erreurs dans les fichiers
 * 
 * @param array $flux
 * @return array
 */
function cvtupload_saisies_verifier($flux) {
	// On supprime de $_FILES les fichiers envoyés qui ne passent pas le test de vérification
	include_spip('inc/cvtupload');
	if (isset($flux['args']['erreurs_fichiers']) and is_array($flux['args']['erreurs_fichiers'])) {
		foreach ($flux['args']['erreurs_fichiers'] as $champ => $erreurs) {
			cvtupload_nettoyer_files_selon_erreurs($champ, $erreurs);
		}
	}
	
	return $flux;
}

/**
 * Injecter le html de presentation du fichier deja uploade avant chaque input file
 * @param array $flux
 * @return array mixed
 */
function cvtupload_formulaire_fond($flux) {
	// Si ça a déjà été posté (après verifier()) et qu'il y a des champs fichiers déclarés
	if (($flux['args']['je_suis_poste'] or isset($flux['args']['contexte']['cvtupload_precharger_fichiers']))
		and $champs_fichiers = cvtupload_chercher_fichiers($flux['args']['form'], $flux['args']['args'])
	) {
		include_spip('inc/filtres');
		if (isset($flux['args']['contexte']['_fichiers_precedents_html'])
			and $fichiers = $flux['args']['contexte']['_fichiers_precedents_html']
		) {
			foreach ($champs_fichiers as $champ) {
				// Si le visiteur a bien réussi a charger un ou plusieurs fichiers dans ce champ
				if (isset($fichiers[$champ])) {
					if (!is_array($fichiers[$champ])) {// Si c'est un champ unique
						$flux['data'] = preg_replace(
							"#<input[^>]*name=['\"]${champ}[^>]*>#i",
							$fichiers[$champ],
							$flux['data']
						);
					} else { // Sinon c'est un multiple
						foreach ($fichiers[$champ] as $cle => $html) {
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

/**
 * Ajouter la CSS dans le head du site public
 * @param string $flux
 * @param bool $prive
 * @return string
 */
function cvtupload_insert_head_css($flux, $prive = false) {
	if (!$prive) {
		$css = timestamp(find_in_path('css/cvtupload.css'));

		$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
	}
	return $flux;
}
