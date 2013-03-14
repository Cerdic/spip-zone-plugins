<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

define("_DIR_SIMPLECAL_PRIVE", _DIR_PLUGIN_SIMPLECAL."prive/");

include_spip('inc/simplecal_classement'); // pour la page evenements !

// ------------------------------------
//  Plugin Corbeille - compatibilite
// ------------------------------------
/*
global $corbeille_params;
$corbeille_params["evenements"] = array (
	"statut" => "poubelle",
	"table" => "spip_evenements",
	"tableliee"  => array("spip_mots_evenements"),
);
*/

// 2008-12-23 -> 2009-02-14
// 2009-01
// debut <= 2009-01-31 ET fin >= 2009-01-01



function simplecal_evenements_where($where='', $mode='', $annee='', $mois=''){
	
	// Note : date_fin non obligatoire => like necessaire
	
	if ($annee && $mois) {
		$date_min = $annee."-".$mois."-01";
		$date_max = $annee."-".$mois."-31";
		$req = "((date_debut like '%".$annee."-".$mois."%'";
		$req .= " OR date_fin like '%".$annee."-".$mois."%')";
		$req .= " OR (date_debut <= '$date_max' AND date_fin >= '$date_min'))"; 
	} else if ($annee && !$mois) {
		$date_min = $annee."-01-01";
		$date_max = $annee."-12-31";
		$req = "((date_debut like '%".$annee."%'";
		$req .= " OR date_fin like '%".$annee."%')";
		$req .= " OR (date_debut <= '$date_max' AND date_fin >= '$date_min'))"; 
	} else if ($mode == 'avenir') {
		$req = "(date_debut >= DATE_FORMAT(NOW(),'%Y-%m-%d')";
		$req .= " OR date_fin >= DATE_FORMAT(NOW(),'%Y-%m-%d'))";
	} else {
		$req = "";
	}
	
	if ($where and $req){
		$s = $where." AND ".$req;
	} else if ($where){
		$s = $where;
	} else {
		$s = $req;
	}
	//echo("where=".$where);
	//die("req=".$req);
	
	return $s;
}

?>
