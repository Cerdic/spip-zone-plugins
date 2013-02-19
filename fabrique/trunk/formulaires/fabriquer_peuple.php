<?php

/**
 * Gestion du formulaire permettant de créer un fichier de peuplement
 * d'une table SQL avec les données d'une table existante
 *
 * @package SPIP\Fabrique\Formulaires
 */

// sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire de fabrication de peuplement de table SQL
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_fabriquer_peuple_charger_dist() {
	$contexte = array(
		'table' => '',
		'compression' => '',
		'table_destination' => '',
	);
	return $contexte;
}

/**
 * Traitement du formulaire de fabrication de peuplement de table SQL
 *
 * @return array
 *     Retour des traitements
 */
function formulaires_fabriquer_peuple_traiter_dist(){

	$table = _request('table');
	$compression = _request('compression');
	$table_destination = _request('table_destination');

	// 'spip_articles' ou
	// 'autreconnect:spip_articles'
	list($connect, $table) = explode(':', $table);
	if (!$table) {
		$table = $connect;
		$connect = '';
	}

	if (!$table_destination) {
		$table_destination = 'spip_' . str_replace('spip_', '', $table);
		set_request('table_destination', $table_destination);
	}

	// ou placer notre contenu ?
	include_spip('fabrique_fonctions');
	$destination = fabrique_destination();

	$import = 'importer_' . $table_destination . '.php';
	$donnees_compressees = 'importer_' . $table_destination . '_donnees.gz';
	$chemin              = $destination . $import;
	$chemin_compression  = $destination . $donnees_compressees;

	// calcul du squelette et copie a destination du contenu.
	$contenu = recuperer_fond(FABRIQUE_SKEL_SOURCE . 'base/importer_table.php', array(
		'table' => $table,
		'table_destination' => $table_destination,
		'connecteur' => $connect,
		'compression' => $compression,
		'chemin_compression' => $chemin_compression,
		'donnees_compressees' => $donnees_compressees,
	));


	ecrire_fichier($chemin, $contenu);

	$nb = sql_countsel($table, '', '', '', $connect);
	$taille = filesize($chemin);

	$message = 'fabrique:fichier_importation_cree_dans';
	if ($compression) {
		$taille += filesize($chemin_compression);
		$message = 'fabrique:fichiers_importations_compresses_cree_dans';
	}

	$res = array(
		'editable'=>'oui',
		'message_ok' => _T($message, array(
			'dir' => $destination,
			'import' => $import,
			'donnees_compressees' => $donnees_compressees,
			'lignes' => $nb,
			'taille' => taille_en_octets($taille),
		)),
	);
	return $res;
}



?>
