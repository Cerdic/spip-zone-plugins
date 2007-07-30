<?php

/*
 * fichier contenant les fonction PHP utiles aux scripts d'administration
 */



function compte_fichier_pb_visite($fichier,
&$visites, &$visites_a, &$referers, &$referers_a, &$articles) {

	// Noter la visite du site (article 0)
	$visites ++;

	$content = array();
	if (lire_fichier($fichier, $content))
		$content = @unserialize($content);
	if (!is_array($content)) return;


	$ip = $content["ip"];
	$debut = $content["debut"];
	$fin = $content["fin"];
	
	$squel = $content["fond"];
	$duree = $fin - $debut;
	$duree_min = floor(($duree/60)*100)/100;
	
	$pages = count($content) - 3;
	
	if (ereg("([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)", $ip, $regs)) {
		$ip_num = ($regs[1] * 256 * 256 * 256) + ($regs[2] * 256 * 256) + ($regs[3] * 256) + $regs[4] ;
	}
	
	$result = spip_query("SELECT code_pays FROM spip_pb_geoip WHERE $ip_num BETWEEN debut_num_ip AND fin_num_ip");
	if ($row = spip_fetch_array ($result)) {
		$pays = $row["code_pays"];
	}
		
	if ($pages > 1) {	// Visite utile
		$p_visites = 1;
		$p_pages_vues = $pages;	
		$p_visites_utiles = 1;
		$p_pages_vues_utiles = $pages;
		$p_duree = $duree;
	} else { // Visite de passage
		$p_visites = 1;
		$p_pages_vues = 1;	
		$p_visites_utiles = 0;
		$p_pages_vues_utiles = 0;
		$p_duree = 0;
	}
	
/*	
	echo "<b>$fichier</b>";
	echo "<ul>";
	echo "<li>IP: $ip / $ip_num";
	echo "<li> Pays: $pays";
	echo "<li>$debut -> $fin";
	echo "<li>Duree: $duree_min minutes";
	echo "<li>Pages vues: $pages";
	echo "<li>$p_visites / $p_pages_vues / $p_visites_utiles / $p_pages_vues_utiles / $p_duree";
	echo "</ul>";
*/	

	$date = date("Y-m-d", time() - 1800);

	spip_query("INSERT IGNORE INTO spip_pb_visites (date) VALUES ('$date')");
	spip_query("UPDATE spip_pb_visites SET visites = visites+$p_visites, pages_vues = pages_vues+$p_pages_vues, visites_utiles = visites_utiles + $p_visites_utiles, pages_vues_utiles = pages_vues_utiles+$p_pages_vues_utiles, duree = duree + $p_duree WHERE date='$date'");


	foreach($squel as $squelette=>$val) {
		$result = spip_query("SELECT * FROM spip_pb_visites_squelettes WHERE date='$date' AND squelette='$squelette'");
		if (spip_num_rows($result) == 0) {
			spip_query("INSERT INTO spip_pb_visites_squelettes (date, squelette) VALUES ('$date', '$squelette')");
		}
		spip_query("UPDATE spip_pb_visites_squelettes SET pages_vues = pages_vues+$val WHERE date='$date' AND squelette='$squelette'");
	}


	$result = spip_query("SELECT * FROM spip_pb_visites_pays WHERE date='$date' AND pays='$pays'");
	if (spip_num_rows($result) == 0) {
		spip_query("INSERT INTO spip_pb_visites_pays (date, pays) VALUES ('$date', '$pays')");
	}
	spip_query("UPDATE spip_pb_visites_pays SET visites = visites+$p_visites, pages_vues = pages_vues+$p_pages_vues, visites_utiles = visites_utiles + $p_visites_utiles, pages_vues_utiles = pages_vues_utiles+$p_pages_vues_utiles, duree = duree + $p_duree WHERE date='$date' AND pays='$pays'");

	@unlink($fichier);

}


function pb_traiter_fichier_geo ($fichier) {
	$row = 1;
	
	$etape = _DIR_TMP."pb_visites_geo_import.txt";
	$sauver = _DIR_PLUGINS."pb_visites/geo/GeoIPsauver.csv";


	$lock = _DIR_SESSIONS . 'pb_visites_traiter.lock';


	if (file_exists($etape)) {
		$debut = join(file($etape));
	} else {
		$debut = 0;
	}


	spip_log("Import PB etape $debut");



	if ($debut == 0) spip_query ("TRUNCATE TABLE `spip_pb_geoip`");
	$handle = fopen($fichier, "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		$row ++;
		if ($num > 0 AND $row >= $debut*10000 AND $row < ($debut+1)*10000) {
			if ($data[1]) {
				$debut_num_ip = $data[2];
				$fin_num_ip = $data[3];
				$code_pays = $data[4];
				$nom_pays = $data[5];
			
				spip_query ("INSERT INTO spip_pb_geoip (debut_num_ip, fin_num_ip, code_pays, nom_pays) VALUES ('$debut_num_ip', '$fin_num_ip', '$code_pays', '$nom_pays')");		
			} else {
				rename($fichier, $sauver);
				@unlink ($fichier);
				unlink ($etape);
			}
		}
	}
	fclose($handle);
	ecrire_fichier($etape,$debut+1);

	if ($debut > 0) {
		return -1;
	}

}

function pb_traiter_les_visites () {
	include_spip('base/abstract_sql');

	$result = spip_query("SHOW COLUMNS FROM spip_pb_visites");
 
	while ($row = spip_fetch_array($result)) {
		$count ++;
		$existe[$row["Field"]] = true;

	}
	if ($count < 1) { //Creer spip_pb_visites
		spip_query( "CREATE TABLE `spip_pb_visites` (`date` DATE NOT NULL , `visites` INT( 10 ) NOT NULL , `pages_vues` INT( 10 ) NOT NULL , `visites_utiles` INT( 10 ) NOT NULL , `pages_vues_utiles` INT( 10 ) NOT NULL , `duree` INT( 10 ) NOT NULL , `maj` TIMESTAMP NOT NULL , PRIMARY KEY ( `date` ) )");
		spip_query( "CREATE TABLE `spip_pb_visites_pays` (`date` DATE NOT NULL , `pays` VARCHAR(3), `visites` INT( 10 ) NOT NULL , `pages_vues` INT( 10 ) NOT NULL , `visites_utiles` INT( 10 ) NOT NULL , `pages_vues_utiles` INT( 10 ) NOT NULL , `duree` INT( 10 ) NOT NULL , `maj` TIMESTAMP NOT NULL )");
		spip_query( "ALTER TABLE `spip_pb_visites_pays` ADD INDEX ( `date` , `pays`)" );
		spip_query( "CREATE TABLE `spip_pb_geoip` (`debut_num_ip` INT(10) NOT NULL, `fin_num_ip` INT(10) NOT NULL, `code_pays` VARCHAR(3), `nom_pays` VARCHAR(20) )" );
		spip_query( "ALTER TABLE `spip_pb_geoip` ADD INDEX ( `debut_num_ip` , `fin_num_ip` , `code_pays` )" );
	}
	// Tester pour creation spip_pb_visites_squelettes (deuxieme passe, car mise-ˆ-jour du plugin)
	$count = 0;
	$result = spip_query("SHOW COLUMNS FROM spip_pb_visites_squelettes");
	while ($row = spip_fetch_array($result)) {
		$count ++;
	}
	if ($count < 1) { // Creer spip_pb_visites_squelettes
		spip_query( "CREATE TABLE `spip_pb_visites_squelettes` (`date` DATE NOT NULL , `squelette` VARCHAR(100), `pages_vues` INT( 10 ) NOT NULL , `maj` TIMESTAMP NOT NULL )");
		spip_query( "ALTER TABLE `spip_pb_visites_squelettes` ADD INDEX ( `date` , `squelette`)" );
	}



	// RECUPERER LE FICHIER DES VILLES (AUTOMATIQUE) TOUS LES 3 JOURS
	$fichier_distant = "http://www.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip";
	$fichier_geo_zip = _DIR_PLUGINS."pb_visites/geo/GeoIPCountryCSV.zip";
	$folder = _DIR_PLUGINS."pb_visites/geo";
	$telecharger = false;

	if (!file_exists($fichier_geo_zip)) {
		$telecharger = true;
	}
	else {
		$date_init = time() -  60 * 60 * 24 * 3;
		if (@filemtime($fichier_geo_zip) < $date_init) $telecharger = true;
	}
	
		
	if ($telecharger) {
		spip_log ("telechargement du fichier GeoIPCountry");
		// Ramener le fichier en local
		include_spip("inc/distant");

		$contenu = recuperer_page($fichier_distant, false, false, 5048576);
		if (!$contenu) return false;
		ecrire_fichier($fichier_geo_zip, $contenu);
		
		// Decompacter
		include_spip("inc/pclzip");
		$archive = new PclZip($fichier_geo_zip);
		$list = $archive->extract(PCLZIP_OPT_PATH, $folder, PCLZIP_OPT_REMOVE_ALL_PATH);
		return -1;
	}

	$fichier_geo = _DIR_PLUGINS."pb_visites/geo/GeoIPCountryWhois.csv";
	if (file_exists($fichier_geo)) {
		return pb_traiter_fichier_geo ($fichier_geo);
	}

	// Initialisations
	$visites = ''; # visites du site
	$visites_a = array(); # tableau des visites des articles
	$referers = array(); # referers du site
	$referers_a = array(); # tableau des referers des articles
	$articles = array(); # articles vus dans ce lot de visites

	$squelettes = array(); # squelettes vus dans ce lot

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 sessions datant d'au moins 30 minutes
	$sessions = preg_files(sous_repertoire(_DIR_TMP, 'pb_visites'));

	$compteur = 100000;
	$date_init = time()-30*60;
//	$date_init = time();
	
	foreach ($sessions as $item) {
		if (@filemtime($item) < $date_init) {
			spip_log("traite la session $item");

			compte_fichier_pb_visite($item,
				$visites, $visites_a, $referers, $referers_a, $articles);
			//@unlink($item);
			if (--$compteur <= 0)
				break;
		}
		#else spip_log("$item pas vieux");
	}

	if (!$visites) return;
	spip_log("analyse $visites visites");


}



function cron_pb_visites_traiter() {
	$ret = pb_traiter_les_visites();
	return $ret;
}


?>
