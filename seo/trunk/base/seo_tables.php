<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP_SEO
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['seo'] = 'seo';

	return $interface;
}

function seo_declarer_tables_principales($tables_principales){
	//-- Table SEO -----------------------------------------------------
	$seo = array(
		'id_objet' => "int(11) NOT NULL",
		'objet' => "varchar(10) NOT NULL",
		'meta_name' => "varchar(20) NOT NULL",
		'meta_content' => "text NOT NULL"
	);

	$seo_cles = array(
		"PRIMARY KEY" => "id_objet, objet, meta_name"
	);

	$tables_principales['spip_seo'] = array(
		'field' => &$seo,
		'key' => &$seo_cles
	);

	return $tables_principales;
}

?>