<?php


function sa_header_prive($flux){
	$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_SA."js/spip_ajax.js'></script>";
	$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_SA."js/jquery-ui-1.7.2.js'></script>";
	$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_SA."js/datepicker-fr.js'></script>";
	
	$flux .= "<link rel='stylesheet' media='all' href='"._DIR_PLUGIN_SA."css/sunny/jquery-ui-1.7.2.custom.css' />";
	$flux .= "<link rel='stylesheet' media='all' href='"._DIR_PLUGIN_SA."css/spip_ajax_ui.css' />";
	return $flux;
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
	
	// si les droits ne sont pas renseigne on bloque le processus 
	if (!$droit) return false;
	
	// si allowed on test les droits de l'auteur
	if ($allowed && in_array($id,$allowed))return true;
	
	// pour les admin restreint 
	if ($statut_type != 1 && $statut_auteur=="0minirezo") $statut_auteur = 'admin_restreint';

	$acces = array(	"tous" => 0,"admin_restreint" => 1,"admin" => 2 ,"aucun" => 4);
	$type_acces = array("1comite" => 0,"admin_restreint" => 1,"0minirezo" => 2 );
	
	
	if($type_acces[$statut_auteur] >= $acces[$droit]) return true;
	
	return false;
	
}



function get_droit_spip_ajax(){
	include_spip('inc/cfg_config');
	$tab = lire_config("metapack::spip_ajax");
	if (count($tab)==0) return array();
	return $tab;
}


?>