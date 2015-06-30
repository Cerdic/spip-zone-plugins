<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporte des champs extras
 *
 * Crée un fichier PHP contenant des informations relatives (array)
 * aux saisies utilisées par les champs extras sur un ou plusieurs objets
 *
 * Paramètres d'action :
 *
 * - tous                       Tous les champs extras de tous les objets
 * - objet/{type}/tous          Tous les champs extras de l'objet {type}. {@example: `objet/auteur/tous`}
 * - objet/{type}/champ/{nom}   Le champ {nom} de l'objet {type}. {@example: `objet/auteur/champ/date_naissance`}
 * 
**/
function action_iextras_exporter_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	list($quoi, $objet, $quoi_objet, $champ) = array_pad(explode('/', $arg), 4, null);

	// actions possibles
	if (!in_array($quoi, array(
		'tous',
		'objet'))){
			include_spip('inc/minipres');
			echo minipres(_T('iextras:erreur_action',array("action" => $quoi)));
			exit;
	}

	if ($quoi == 'tous') {
		return iextras_exporter_tous();
	}

	if ($quoi_objet == 'tous') {
		return iextras_exporter_objet_tous($objet);
	}

	return iextras_exporter_objet_champ($objet, $champ);
}


/**
 * Exporte tous les champs extras
**/
function iextras_exporter_tous() {
	include_spip('inc/iextras');
	$tables = lister_tables_objets_sql();
	$champs = array();
	foreach ($tables as $table => $desc) {
		if ($liste = iextras_champs_extras_definis($table)) {
			$champs[$table] = $liste;
		}
	}

	return iextras_envoyer_export($champs, 'tous');
}


/**
 * Exporte tous les champs extras d'un objet
 *
 * @param string $objet
**/
function iextras_exporter_objet_tous($objet) {
	include_spip('inc/iextras');
	$champs = array();
	$table = table_objet_sql($objet);

	if ($liste = iextras_champs_extras_definis($table)) {
		$champs[$table] = $liste;
	}

	return iextras_envoyer_export($champs, $objet);
}


/**
 * Exporte un champs extras d'un objet
 *
 * @param string $objet
 * @param string $champ
**/
function iextras_exporter_objet_champ($objet, $champ) {
	include_spip('inc/iextras');
	$champs = array();
	$table = table_objet_sql($objet);

	if ($liste = iextras_champs_extras_definis($table)) {
		include_spip('inc/saisies');
		$liste = saisies_lister_par_nom($liste);
		if (!empty($liste[$champ])) {
			$champs[$table] = array();
			$champs[$table][] = $liste[$champ];
		}
	}

	return iextras_envoyer_export($champs, "$objet-$champ");
}


/**
 * Exporte un contenu (description de champs extras) au format YAML
 *
 * Envoie les données au navigateur !
 *
 * @param array $export
 * @param string $nom_fichier
**/
function iextras_envoyer_export($export, $nom_fichier) {
	// On envode en yaml
	include_spip('inc/yaml');
	$export = yaml_encode($export);

	$date = date("Ymd-His");
	Header("Content-Type: text/x-yaml;");
	Header("Content-Disposition: attachment; filename=champs_extras_export-$date-$nom_fichier.yaml");
	Header("Content-Length: " . strlen($export));
	echo $export;
	exit;
}
