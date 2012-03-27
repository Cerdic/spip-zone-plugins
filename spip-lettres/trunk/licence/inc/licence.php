<?php
/*
 * Plugin Licence
 * (c) 2007-2011 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['licence_licences'] = array (
			"1" 	=> array(
				# nom de la licence
				"name" 	=> _T('licence:titre_copyright'),
				# numero d'identifiacation de la licence
				"id"		=> "1",
				# nom de l'icone de la licence (optionnel)
				# l'icone devra être placé dans le répertoire img_pack du plugin
				"icon"		=> "copyright-24.png",
				# Lien documentaire vers la licence (optionnel)
				"link"		=> "",
				# Description un peu plus détaillée de la licence
				"description" 	=> _T('licence:description_copyright'),
				# Une abbréviation commune à toutes les langues
				"abbr" => 'copyright'),
			"2" 			=> array(
				"name" 		=> 	_T('licence:titre_gpl'),
				"id"		=> "2",
				"icon"		=> "gnu-gpl.png",
				"link"		=> _T('licence:lien_gpl'),
				"description" => _T('licence:description_gpl'),
				"abbr" => "GPL"),
			"3" 			=> array(
				"name" 		=> _T('licence:titre_cc_by'),
				"id"		=> "3",
				"icon"		=> "cc-by.png",
				"link"		=> _T('licence:lien_cc_by'),
				"description" => _T('licence:description_cc_by'),
				"abbr" => "cc-by"),
			"4" 			=> array(
				"name" 		=> _T('licence:titre_cc_by_nd'),
				"id"		=> "4",
				"icon"		=> "cc-by-nd.png",
				"link"		=> _T('licence:lien_cc_by_nd'),
				"description" => _T('licence:description_cc_by_nd'),
				"abbr" 		=> "cc-by-nd"),
			"5" 			=> array(
				"name" 		=> _T('licence:titre_cc_by_nc_nd'),
				"id"		=> "5",
				"icon"		=> "cc-by-nc-nd.png",
				"link"		=> _T('licence:lien_cc_by_nc_nd'),
				"description" => _T('licence:description_cc_by_nc_nd'),
				"abbr" 		=> "cc-by-nc-nd"),
			"6" 			=> array(
				"name" 		=> _T('licence:titre_cc_by_nc'),
				"id"		=> "6",
				"icon"		=> "cc-by-nc.png",
				"link"		=> _T('licence:lien_cc_by_nc'),
				"description" => _T('licence:description_cc_by_nc'),
				"abbr" 		=> "cc-by-nc"),
			"7" 			=> array(
				"name" 		=> _T('licence:titre_cc_by_nc_sa'),
				"id"		=> "7",
				"icon"		=> "cc-by-nc-sa.png",
				"link"		=> _T('licence:lien_cc_by_nc_sa'),
				"description" => _T('licence:description_cc_by_nc_sa'),
				"abbr" 		=> "cc-by-nc-sa"),
			"8" 			=> array(
				"name" 		=> _T('licence:titre_cc_by_sa'),
				"id"		=> "8",
				"icon"		=> "cc-by-sa.png",
				"link"		=> _T('licence:lien_cc_by_sa'),
				"description" => _T('licence:description_cc_by_sa'),
				"abbr" 		=> "cc-by-sa"),
			"9" 			=> array(
				"name" 		=> _T('licence:titre_art_libre'),
				"id"		=> "9",
				"icon"		=> "copyleft-24.png",
				"link"		=> _T('licence:lien_art_libre'),
				"description" => _T('licence:description_art_libre'),
				"abbr" 		=> "lal"),
			"10" 			=> array(
				"name" 		=> _T('licence:titre_gfdl'),
				"id"		=> "10",
				"icon"		=> "gnu-gfdl.png",
				"link"		=> _T('licence:lien_gfdl'),
				"description" => _T('licence:description_gfdl'),
				"abbr" 		=> "gfdl"),
);

?>