<?php

/* Fonction pour recuperer la liste des differentes pages gerer par fast_plugin */
function liste_param_spip_ajax($val){
	$page = get_droit_spip_ajax();
	if(count($page)==0)return;
	$choix = "";
	if (isset($_POST["cfg_id"])) $choix = $_POST["cfg_id"];
	$retour = "";
	
	foreach ($page as $cle=>$valeur) {
		$select = "";
		if ($choix==$cle) $select ="selected";
		$retour .="<option value='$cle' $select >$cle</option>";
	}
	return $retour;
}




function test_valid_global_admin_spip_ajax($id_auteur){
	if(_request("exec")=="admin_plugin" or (_request("exec")=="cfg" && _request("cfg")!="spip_ajax"))return;
	$msg = "Vous n'avez pas acces a cette partie de l'admin - spip ajax";
	if($GLOBALS['unique_admin_spip_ajax']!=$id_auteur) die($msg);
}




?>