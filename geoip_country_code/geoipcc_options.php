<?php

// geoipcc_options.php


/**
 * Copyright (c) 2009 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 * */
 
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// pour SPIP 1.9.1
if(!defined('_DIR_PLUGIN_GEOIPCC')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_GEOIPCC',(_DIR_PLUGINS.end($p)).'/');
}

define('_DIR_GEOIPCC_IMAGES', _DIR_PLUGIN_GEOIPCC . 'images/');

define('_GEOIPCC_FICHIER_GZ_DISTANT', 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz');
define('_GEOIPCC_DATA_MAX_LEN', (2*1024*1024)); 

define('_GEOIPCC_FICHIER_DATA', 'GeoIP.dat');
define('_GEOIPCC_CHEMIN_DATA', _DIR_TMP);

define('_GEOIPCC_FICHIER_GZ_LOCAL', _DIR_TMP._GEOIPCC_FICHIER_DATA.'.gz');

define('_GEOIPCC_FICHIER_DATA_LOCAL', _DIR_TMP._GEOIPCC_FICHIER_DATA);

define('_GEOIPCC_PAYS_DEFAULT', 'FR');

// verifier le n jour du mois si le data est correct
define('_GEOIPCC_UPDATE_JOUR_DU_MOIS', 1);
// et a quelle heure de ce jour
define('_GEOIPCC_UPDATE_HEURE_DU_JOUR', 6);

/**
 * Journal (prive_spip.log si espace prive, sinon spip.log)
 * @return boolean
 * @param string $msg
 */
function geoipcc_log ($msg) {
	static $prev;
	static $count = 0;
	static $tag = '[GEOIPCC] ';
	
	$msg = trim($msg);
	if($prev != $msg) {
		if($count) {
			spip_log($tag . '--- last message repeated '.$count.' times ---');
			$count = 0;
		}
		$prev = $msg;
		spip_log($tag . $msg);
	}
	else {
		$count++;
	}
	return(true);
}

/**
 * Si 1 du mois, 6 heures du matin
 * mettre a jour le fichier data
 * @return bool
 * @param bool $forcer[optional]
 */
function geoipcc_charger_data ($forcer = false)
{
	if(
		// force' ? (installation)
		!($faire = $forcer) 
		// ou est-ce le jour de l'update ?
		&& (intval(date('j')) == _GEOIPCC_UPDATE_JOUR_DU_MOIS) 
		&& (intval(date('G')) == _GEOIPCC_UPDATE_HEURE_DU_JOUR))
	{
		if(file_exists(_GEOIPCC_FICHIER_DATA_LOCAL)) 
		{
			$date_fichier = date("YmdG", filectime(_GEOIPCC_FICHIER_DATA_LOCAL));
			$date_now = date("YmdG");
			
			if($faire = ($date_fichier < $date_now)) 
			{
				geoipcc_log('must upgrade data file...');
			}
		}
	}
	
	if($faire)
	{
		geoipcc_log('loading ' . _GEOIPCC_FICHIER_GZ_DISTANT);
		
		if($data = geoipcc_gz_read(_GEOIPCC_DATA_MAX_LEN))
		{
			if($len = strlen($data)) 
			{
				geoipcc_log('uncompressing data file in '._GEOIPCC_FICHIER_DATA_LOCAL);

				if(file_put_contents( _GEOIPCC_FICHIER_DATA_LOCAL, $data)) {
					geoipcc_log('writing data successfull');
					return(true);
				}
				else {
					geoipcc_log('writing data error ');
				}
			}
			else {
				geoipcc_log('file empty or connexion error '._GEOIPCC_FICHIER_GZ_DISTANT);
			}
		}
		else 
		{	
			geoipcc_log('read error '._GEOIPCC_FICHIER_GZ_DISTANT);
		}
		return(false);
	}
	
	return(true);
}

/**
 * Lecture du fichier distant et decompression
 * @return mixed $contents
 * @param string $filename
 */
function geoipcc_gz_read ($filename) 
{
	if(
		($data = file_get_contents (_GEOIPCC_FICHIER_GZ_DISTANT, FILE_BINARY, null, 0, _GEOIPCC_DATA_MAX_LEN))
		&& file_put_contents (_GEOIPCC_FICHIER_GZ_LOCAL, $data)
	) 
	{
		
		if($zd = gzopen(_GEOIPCC_FICHIER_GZ_LOCAL, "r"))
		{
			$contents = gzread($zd, _GEOIPCC_DATA_MAX_LEN);
			gzclose($zd);
		}
	}
	return($contents);
}

/**
 * Appele automatiquement par SPIP
 *   lors de l'installation/desinstallation du plugin
 * Ne pas oublier de cliquer sur la petite valise pour supprimer
 *   mes cc temporaires.
 * @return 
 * @param string $action
 */
function geoipcc_install ($action) 
{
	switch($action) {
		case 'test':
			$result = file_exists(_GEOIPCC_FICHIER_DATA_LOCAL);
			geoipcc_log('TEST: ' . ($result ? 'TRUE' : 'FALSE'));
			return($result);
			break;
		case 'install':
			$result = geoipcc_charger_data(true);
			geoipcc_log('INSTALL: ' . ($result ? 'OK' : 'ERROR'));
			return($result);
			break;
		case 'uninstall':
			if(file_exists(_GEOIPCC_FICHIER_DATA_LOCAL))
			{
				$result = unlink(_GEOIPCC_FICHIER_GZ_LOCAL) && unlink(_GEOIPCC_FICHIER_DATA_LOCAL);
			}
			else {
				$result = true;
			}
			geoipcc_log('UNINSTALL: ' . ($result ? 'OK' : 'ERROR'));
			return($result);
	}
}

/**
 * Renvoie le country code de l'adresse IP
 * @return 
 * @param string $args
 */
function calculer_GEOIP_COUNTRY_CODE ($this_ip = false)
{
	
	static $cc;
	
	if(!$cc) 
	{
		include_spip('geoip.inc');
		
		if(!$this_ip) 
		{
			$this_ip = calculer_GEOIP_IP_VISITEUR();
		}
		
		if(geoipcc_charger_data() && file_exists(_GEOIPCC_FICHIER_DATA_LOCAL))
		{
			if($geoi = geoip_open(_GEOIPCC_FICHIER_DATA_LOCAL, GEOIP_STANDARD))
			{	
				$cc = geoip_country_code_by_addr($geoi, $this_ip);
			}
			// si pays demande' inconnu, appliquer le defaut
			if(!$cc) {
				$cc = _GEOIPCC_PAYS_DEFAULT;
			}
		}
		else 
		{
			geoipcc_log('Fichier ' . _GEOIPCC_FICHIER_DATA_LOCAL . ' manquant. Svp, re-installez le plugin.');
		}
	}
	geoipcc_log('country code for ' . $this_ip . ': '.$cc);
	
	return($cc);	
}

/**
 * Balise #GEOIP_COUNTRY_CODE
 *   Renvoie le country code deduit de l'adresse IP visiteur
 * @return object
 * @param object $p
 */
function balise_GEOIP_COUNTRY_CODE ($p) 
{
	$args = interprete_argument_balise(1, $p);
	
	$p->code = "calculer_GEOIP_COUNTRY_CODE($args)";
	$p->interdire_scripts = false;
	return ($p);
}

function calculer_GEOIP_IP_VISITEUR ()
{
	global $ip; // inc_version.php
	return($ip);
}

/**
 * IP du visiteur
 * @return 
 * @param object $p
 */
function balise_GEOIP_IP_VISITEUR ($p) 
{
	$p->code = "calculer_GEOIP_IP_VISITEUR()";
	$p->interdire_scripts = false;
	return ($p);
}

