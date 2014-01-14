<?php
/**
 * Plugin Licence
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 *
 * La $GLOBALS des licences utilisables
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('MES_LICENCES')) define('MES_LICENCES', array());

$GLOBALS['licence_licences'] = array_merge(array (
		"1" => array(
				"name"		=> _T('licence:titre_copyright'), // nom de la licence
				"id"		=> "1", // numero d'identifiacation de la licence
				"icon"		=> "copyright.png", // nom de l'icone de la licence (optionnel),  l'icone devra être placé dans le répertoire img_pack du plugin
				"link"		=> "", // lien documentaire vers la licence (optionnel)
				"description" => _T('licence:description_copyright'), // description un peu plus détaillée de la licence
				"abbr"		=> 'copyright'), // une abbréviation commune à toutes les langues
		"2" => array(
				"name"		=> 	_T('licence:titre_gpl'), // nom de la licence
				"id"		=> "2", // numero d'identifiacation de la licence
				"icon"		=> "gnu-gpl.png",
				"link"		=> _T('licence:lien_gpl'),
				"description" => _T('licence:description_gpl'),
				"abbr"		=> "GPL"),
		"3" => array(
				"name"		=> _T('licence:titre_cc_by'), // nom de la licence
				"id"		=> "3", // numero d'identifiacation de la licence
				"icon"		=> "cc-by.png",
				"link"		=> _T('licence:lien_cc_by'),
				"description" => _T('licence:description_cc_by'),
				"abbr"		=> "cc-by"),
		"4" => array(
				"name"		=> _T('licence:titre_cc_by_nd'), // nom de la licence
				"id"		=> "4", // numero d'identifiacation de la licence
				"icon"		=> "cc-by-nd.png",
				"link"		=> _T('licence:lien_cc_by_nd'),
				"description" => _T('licence:description_cc_by_nd'),
				"abbr"		=> "cc-by-nd"),
		"5" => array(
				"name"		=> _T('licence:titre_cc_by_nc_nd'), // nom de la licence
				"id"		=> "5", // numero d'identifiacation de la licence
				"icon"		=> "cc-by-nc-nd.png",
				"link"		=> _T('licence:lien_cc_by_nc_nd'),
				"description" => _T('licence:description_cc_by_nc_nd'),
				"abbr"		=> "cc-by-nc-nd"),
		"6" => array(
				"name"		=> _T('licence:titre_cc_by_nc'), // nom de la licence
				"id"		=> "6", // numero d'identifiacation de la licence
				"icon"		=> "cc-by-nc.png",
				"link"		=> _T('licence:lien_cc_by_nc'),
				"description" => _T('licence:description_cc_by_nc'),
				"abbr"		=> "cc-by-nc"),
		"7" => array(
				"name"		=> _T('licence:titre_cc_by_nc_sa'), // nom de la licence
				"id"		=> "7", // numero d'identifiacation de la licence
				"icon"		=> "cc-by-nc-sa.png",
				"link"		=> _T('licence:lien_cc_by_nc_sa'),
				"description" => _T('licence:description_cc_by_nc_sa'),
				"abbr"		=> "cc-by-nc-sa"),
		"8" => array(
				"name"		=> _T('licence:titre_cc_by_sa'), // nom de la licence
				"id"		=> "8", // numero d'identifiacation de la licence
				"icon"		=> "cc-by-sa.png",
				"link"		=> _T('licence:lien_cc_by_sa'),
				"description" => _T('licence:description_cc_by_sa'),
				"abbr"		=> "cc-by-sa"),
		"9" => array(
				"name"		=> _T('licence:titre_art_libre'), // nom de la licence
				"id"		=> "9", // numero d'identifiacation de la licence
				"icon"		=> "copyleft.png",
				"link"		=> _T('licence:lien_art_libre'),
				"description" => _T('licence:description_art_libre'),
				"abbr"		=> "lal"),
		"10" => array(
				"name"		=> _T('licence:titre_gfdl'), // nom de la licence
				"id"		=> "10", // numero d'identifiacation de la licence
				"icon"		=> "gnu-gfdl.png",
				"link"		=> _T('licence:lien_gfdl'),
				"description" => _T('licence:description_gfdl'),
				"abbr"		=> "gfdl"),
		"11" => array(
				"name"		=> _T('licence:titre_wtfpl'), // nom de la licence
				"id"		=> "11", // numero d'identifiacation de la licence
				"icon"		=> "wtfpl.png",
				"link"		=> _T('licence:lien_whfpl'),
				"description" => _T('licence:description_wtfpl'),
				"abbr"		=> "wtfpl"),
		"12" => array(
				"name"		=> _T('licence:titre_cc0'), // nom de la licence
				"id"		=> "12", // numero d'identifiacation de la licence
				"icon"		=> "cc-zero-publicdomain.png",
				"link"		=> _T('licence:lien_cc0'),
				"description" => _T('licence:description_cc0'),
				"abbr"		=> "cc0"),
		"99" => array(
				"name"		=> _T('licence:titre_autre'), // nom de la licence
				"id"		=> "99", // numero d'identifiacation de la licence
				"icon"		=> "",
				"link"		=> "",
				"description" => _T('licence:description_autre'),
				"abbr"		=> "???"),
), MES_LICENCES);

?>