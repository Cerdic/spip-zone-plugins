<?php
/**
 * Importer en masse des références de projets
 *
 * @plugin     InfoSites
 * @copyright  2017-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/InfoSites/Formulaire
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_importer_projets_references_charger_dist() {
	// Contexte du formulaire.
	$contexte = array(
		'fichier' => null,
		'premiere_ligne' => null,
	);

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_importer_projets_references_verifier_dist() {
	$erreurs = array();
	if (isset($_FILES['fichier']) and isset($_FILES['fichier']['tmp_name']) and empty($_FILES['fichier']['tmp_name'])) {
		$erreurs['fichier'] = _T('info_obligatoire');
	}
	if (isset($_FILES['fichier']) and isset($_FILES['fichier']['tmp_name']) and !empty($_FILES['fichier']['tmp_name'])) {
		if ($_FILES['fichier']['error'] > 0) {
			switch ($_FILES['fichier']['error']) {
				case 1:
					$erreurs['fichier'] .= "<br/>Le poids du fichier chargé excède la valeur attendue.";
					break;
				case 2:
					$erreurs['fichier'] .= "<br/>Le poids du fichier chargé excède la valeur attendue.";
					break;
				case 3:
					$erreurs['fichier'] .= "<br/>Une erreur technique a été rencontré, votre fichier n'a pu être téléchargé complètement.";
					break;
				case 4:
					$erreurs['fichier'] .= "<br/>Une erreur technique a été rencontré, votre fichier n'a pu être téléchargé.";
					break;
				default:
					$erreurs['fichier'] .= "<br/>Une erreur technique a été rencontré lors du chargement de votre fichier.";
			}
		}
		$extension_valides = array('csv');
		$extension_upload = strtolower(substr(strrchr($_FILES['fichier']['name'], '.'), 1));
		if (!in_array($extension_upload, $extension_valides)) {
			$erreurs['fichier'] = "Le fichier n'est pas au format csv";
		}
	}

	return $erreurs;
}

function formulaires_importer_projets_references_traiter_dist() {
	$result =  array(
		'editable' => true,
		'message_ko' => '',
		'redirect' => '',
	);

	//Traitement du formulaire.
	$dir_tmp_reference = _DIR_TMP . "references_csv";
	if (!is_dir($dir_tmp_reference)) {
		include_spip('inc/flock');
		sous_repertoire(_DIR_TMP, 'references_csv');
	}
	$extension_file = strtolower(substr(strrchr($_FILES['fichier']['name'], '.'), 1));
	$base_name = preg_replace("/\.$extension_file$/", '', basename($_FILES['fichier']['name']));
	$base_name = info_sites_nom_machine($base_name);
	$destination_csv = $dir_tmp_reference . '/' . $base_name . '.' . strtolower($extension_file);

	// On déplace le fichier uploadé
	$uploaded_file_csv = move_uploaded_file($_FILES['fichier']['tmp_name'], $destination_csv);
	// On garde la valeur de configuration de la première ligne. Est-ce que le fichier contient en première ligne le nom des colonnes ? 'on' ou false
	$premiere_ligne = _request('premiere_ligne');
	if ($uploaded_file_csv) {
		include_spip('base/abstract_sql');
		include_spip('inc/utils');
		$compteur = 0;
		if (($handle = fopen($destination_csv, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				// Ne pas importer la première ligne
				if ($premiere_ligne == 'on' and $compteur == 0) {
					spip_log('Pas d\'importation de la première ligne pour le fichier ' . $_FILES['fichier']['name']);
				} else {
					// regarder si l'url est déjà enregistré dans la table spip_projets_references.
					$count = sql_countsel('spip_projets_references', "url_site='$data[1]'");

					if ($count == 0) {
						// L'url ne se trouve pas dans la BDD, alors on l'insère
						sql_insertq('spip_projets_references', array(
							'nom' => $data[0],
							'url_site' => $data[1],
							'organisation' => $data[2],
						));
						spip_log($data[1] . " a été inséré dans la table spip_projets_references", 'info_spip');
					} elseif ($count === false) {
						spip_log($data[1] . " n'a pu être inséré dans la table spip_projets_references suite à un problème technique.", 'info_spip');
					} else {
						spip_log($data[1] . " se trouve déjà dans la table spip_projets_references", 'info_spip');
					}
				}
				// incrémentation du compteur de ligne.
				$compteur++;
			}
		}
		// Donnée de retour.
		$result =  array(
			'editable' => true,
			'message_ok' => '',
			'redirect' => '',
		);
	}

	return $result;
}
