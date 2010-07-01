<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers-CYM - Apsulis
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporter une vue ou une table au format CSV !
 *
 */
function action_exporter_table_csv_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$table = $securiser_action();
	exporter_table_csv($table);
	return true;
}

/**
 * Permet d'exporter une table SQL sous forme de CSV zippé
 * en garde une copie dans tmp/dump/
 */
function exporter_table_csv($table_ou_vue){
	include_spip('base/abstract_sql');
	$infos = array();
	$champs = array('*');
	$from = $table_ou_vue;
	$where = array('1');
	$infos = sql_allfetsel($champs, $from, $where);

	$partage = _DIR_TMP.'dump/';
	sous_repertoire(_DIR_TMP, 'dump/');
	$fichier = $partage.$from.'.csv';

	$fp = fopen($fichier, 'w');

	// Ecriture de la ligne des labels
	$ligne = '';
	$i = 1;
	foreach($infos[0] as $champs => $valeurs){
		$ligne .= $champs;
		if($i<count($infos[0])){ $ligne .= ',#:#'; $i++; }		
	}
    fputcsv($fp, split(',#:#', $ligne),';');
	// Ecriture des valeurs
	foreach($infos as $champ => $valeur){
		$ligne = '';
		$i = 1;
		foreach($valeur as $champ2 => $valeur2){
			$ligne .= utf8_decode($valeur2);
			if($i<count($valeur)){ $ligne .= ',#:#'; $i++; }
		}
	    fputcsv($fp, split(',#:#', $ligne),';');
	}
	fclose($fp);

	$date = date('_ymd_H').'h';
	$source = $fichier.$date.'.zip';
	$nom = $from.$date.'.zip';

	// On zippe l'export
	include_spip('inc/pclzip');
	$archive = new PclZip($source);
	$v_list = $archive->create($fichier,
		PCLZIP_OPT_REMOVE_PATH, $partage,
		PCLZIP_OPT_ADD_PATH, '');
	if (!$v_list) {
		spip_log("Echec creation du zip ");
		return false;
	}
	spip_unlink($fichier);

	// On lance le téléchargement pour l'utilisateur
	$taille = filesize("$source");
	header('Content-Type: application/force-download; name="'.$nom.'"'); 
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Length: $taille"); 
	header('Content-Disposition: attachment; filename="'.$nom.'"'); 
	header("Expires: 0"); 
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Pragma: no-cache"); 
	readfile("$source");

	return true;
}

?>