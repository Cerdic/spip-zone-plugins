<?php

/* Fonction pour recuperer la liste des differentes pages, variables globales et 
   elements inseres dans les colonnnes de l'admin de spip gerer par fast_plugin 
*/
function liste_param_fast_plugin($rem,$val){
	$page = array();
	include_spip('inc/cfg_config');
	$page = lire_config("metapack::$val");
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


function test_valid_global_admin_fast_plugin($id_auteur){
	$msg = "Vous n'avez pas acces a cette partie de l'admin - fast plugin";
	if($GLOBALS['unique_admin_fast_plugin']!=$id_auteur) die($msg);
}


?>