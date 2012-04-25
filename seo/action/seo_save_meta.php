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

function action_seo_save_meta_dist() {
	include_spip('inc/abstract_sql');

	$id_objet   = _request('id_objet');
	$objet = _request('objet');
	$meta_tag 	 = is_array(_request('meta_tag')) ? _request('meta_tag') : array();
	
	if(!autoriser('modifier', $objet, $id_objet)) {
		echo "Error :(";
		return;
	}
	
	sql_delete("spip_seo", "id_objet = ".intval($id_objet)." AND objet =".sql_quote($objet));
	
	foreach ($meta_tag as $name => $content) {
		sql_insertq('spip_seo', array('id_objet' => $id_objet, 'objet' => $objet, 'meta_name' => $name, 'meta_content' => $content));
	} 
	
}

?>
