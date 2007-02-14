<?php
//
// action/edittable_save.php
//

if (!defined("_ECRIRE_INC_VERSION")) return;



function edittable_save_dist(){
	$sql_save = "UPDATE "._request('table')." SET";
	$res_description_table = "DESC "._request('table').";";
	$description_table = spip_fetch_array($res_description_table);
	
	$sql_save .= "WHERE ".."='"..";";
	$res_sql = spip_query($sql_save);
	
}

?>
