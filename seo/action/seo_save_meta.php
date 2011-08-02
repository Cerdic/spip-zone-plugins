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

function action_seo_save_meta_dist() {
	include_spip('inc/abstract_sql');

	$id_object   = _request('id_object');
	$type_object = _request('type_object');
	$meta_tag 	 = is_array(_request('meta_tag')) ? _request('meta_tag') : array();
	
	if(!autoriser('modifier', $type_object, $id_object)) {
		echo "Error :(";
		return;
	}
	
	sql_delete("spip_seo", "id_object = $id_object AND type_object = '$type_object'");
	
	foreach ($meta_tag as $name => $content) {
		sql_insertq('spip_seo', array('id_object' => $id_object, 'type_object' => $type_object, 'meta_name' => $name, 'meta_content' => $content));
	} 
	
}

?>
