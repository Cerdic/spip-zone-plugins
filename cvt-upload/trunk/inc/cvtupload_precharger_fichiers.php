<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/cvtupload');

/**
 * Une fonction qui permet de précharger des fichiers.
 * Si la fonction formulaires_formidable_charger
 * a un champ cvtupload_precharger_fichiers dans son tableau de retour, les fichiers sont préchargés.
 * Ce champ est une simple liste de fichiers à précharger
 * @param array $fichiers
 *    la liste des fichiers à précharger, structure sous forme de tableau champ/clé
 *		par exemple array(
 * 				'champ'=>array(
 *						0 => array('chemin'=>chemin_du_fichier,'url'=>url_pour_lire_le_fichier),
 *  					1 => array('chemin'=>chemin_du_fichier,'url'=>url_pour_lire_le_fichier))
 * @param string $form
 *		le formulaire d'où cela vient
 * @return array $infos_fichiers
 *  	un tableau de description de fichiers à la mode cvtupload
 *
 */
function inc_cvtupload_precharger_fichiers_dist($fichiers, $form) {
	// on commence par parcourir les entrées pour structurer selon du pseudo FILES
	$pseudo_files = array();
	foreach ($fichiers as $champ => $valeur) {
		$pseudo_files[$champ] = array(
				'name'=>array(),
				'tmp_name'=>array(),
				'error'=>array(),
				'type'=>array(),
				'size'=>array()
			);
		if (is_array($valeur)) {
			foreach ($valeur as $f => $fichier) {
				$chemin = $fichier['chemin'];
				$pseudo_files[$champ]['tmp_name'][$f] = $chemin;
				$pseudo_files[$champ]['name'][$f] = basename($chemin);
				$pseudo_files[$champ]['error'][$f] = 0;
				$pseudo_files[$champ]['type'][$f] = mime_content_type($chemin);
				$pseudo_files[$champ]['size'][$f] = filesize($chemin);
			}
		} else {
			$chemin = $valeur['chemin'];
			$pseudo_files[$champ] = array();
			$pseudo_files[$champ]['tmp_name'] = $chemin;
			$pseudo_files[$champ]['name'] = basename($chemin);
			$pseudo_files[$champ]['error'] = 0;
			$pseudo_files[$champ]['type'] = mime_content_type($chemin);
			$pseudo_files[$champ]['size'] = filesize($chemin);
		}
	}
	// faire une copie dans tmp/cvtupload
	$repertoire_tmp = sous_repertoire(_DIR_TMP.'cvtupload/');
	$infos_fichiers = array();
	foreach ($pseudo_files as $champ => $pseudo) {
		$infos_fichiers[$champ] = cvtupload_deplacer_fichier($pseudo, $repertoire_tmp, $form, false);

		// ajouter l'url
		if (isset($infos_fichiers[$champ]['tmp_name'])) { // si input simple
			$infos_fichiers[$champ]['url'] = $fichiers[$champ]['url'];
		} else {
			foreach ($infos_fichiers[$champ] as $i => $info) { //si input complexe
				$infos_fichiers[$champ][$i] = array_merge($info, array('url'=>$fichiers[$champ][$i]['url']));
			}
		}
	}

	return $infos_fichiers;
}
