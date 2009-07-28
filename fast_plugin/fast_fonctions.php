<?php

/* Fonction pour recuperer la liste des differentes pages gerer par fast_plugin */
function liste_param_fast_plugin($val){
	$page = get_fast_plugin();
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

/* Fonction pour recuperer la liste des differentes variables globales */
/*  accessible dans les squelettes avec #CONFIG{php::global_vars/test/var0} */
function liste_global_vars($val){
	include_spip('inc/cfg_config');
	$tab = lire_config("php::global_vars");
	if(count($page)==0)return;
	$choix = "";
	if (isset($_POST["cfg_id"])) $choix = $_POST["cfg_id"];
	$retour = "";
	foreach ($tab as $cle=>$val) {
		$select = "";
		if ($choix==$cle) $select ="selected";
		$retour .="<option value='$cle' $select >$cle</option>";
	}
	return $retour;
}

function set_valid_global_admin($id_auteur){
	$msg = "Vous n'avez pas acces a cette partie de l'admin";
	if($GLOBALS['unique_admin_fast_plugin']!=$id_auteur) die($msg);
}

?>