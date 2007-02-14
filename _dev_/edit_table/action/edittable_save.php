<?php
//
// action/edittable_save.php
//

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function action_edittable_save_dist(){
	$sql_save = "UPDATE "._request('table')." SET";
	$res_description_table = spip_query("DESC "._request('table').";");
	$description_table = mysql_fetch_array($res_description_table);
	while ($description_table = mysql_fetch_array($res_description_table)){
		//if ($cle == 'Field'){
			$sql_save .= " ".$description_table['Field']."='"._request($description_table['Field'])."',";
		//}
	} 
	$sql_save = substr($sql_save, 0, strlen($sql_save)-1);
	$sql_save .= " WHERE "._request('colonne_cle')."='".addslashes(_request('valeur_cle'))."';";
	$res_sql = spip_query($sql_save);
	
	//echo $sql_save;
	
	
	$redirect = _request('redirect').'&sql_command='.$sql_save;
	//$redirect = parametre_url(urldecode(_request('redirect')),'id_digg', $id_digg,'&');
	redirige_par_entete($redirect);
	
}

?>
