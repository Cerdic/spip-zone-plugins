<?php


function sa_header_prive($flux){
	$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_SA."js/spip_ajax.js'></script>";
	return $flux;
}



function get_droit_spip_ajax(){
	return array("test"=>array("statut"=>"admin_restreint"),
				 "demo"=>array("statut"=>"aucun","allowed"=>"2")
				);
}


// fonction gerant les autorisations pour le plugin 
// fast plugin
function autoriser_sa_acces_dist($faire, $type, $fichier){

	// les infos sur l'auteur
	$statut_auteur = $GLOBALS['connect_statut'];
	$statut_type = $GLOBALS['connect_toutes_rubriques']; 
	$id = $GLOBALS['auteur_session']['id_auteur'];
	
	
	$inc = get_droit_spip_ajax();
	$droit = $inc[$fichier]["statut"];
	$allowed = $inc[$fichier]["allowed"];
	if ($allowed) $allowed = explode(',',$allowed);
	
	// si allowed on test les droits de l'auteur
	if ($allowed){
		if(in_array($id,$allowed)) return true;
	}
	
	// si les droits ne sont pas renseigne 
	// on bloque le processus idem si droit='aucun' et allowed pas renseigne
	if (!$droit) return false;
	if ($droit=='aucun' && !$allowed) return false;
	
	
	// si les droits sont pour tous c'est bon
	if ($droit=='tous') return true;

	
	// les droits admin
	if ($droit=='admin' && $statut_auteur=="0minirezo" && $statut_type) return true;
	if ($droit=='admin_restreint' && $statut_auteur=="0minirezo" && $statut_type) return true;

	// les droits admin_restreint
	if ($droit=='admin_restreint' && $statut_auteur=="0minirezo") return true;
	
	return false;
	
}


?>