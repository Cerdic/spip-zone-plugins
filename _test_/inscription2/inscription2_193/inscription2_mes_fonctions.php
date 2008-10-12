<?php
include_spip('base/abstract_sql');

function confirmation_inscription2($id, $mode, $cle){
	$q = sql_fetsel("statut, alea_actuel","spip_auteurs","id_auteur = '$id'");
	$statuts_autorises = array( 
 		"aconfirmer", 
	 	"6forum" 
 	 ); 
 		        	         
 	if(in_array($q['statut'],$statuts_autorises) and $mode == 'conf' and $cle ==  $q['alea_actuel']){ 		
	 	return 'pass';
	}elseif($q['statut'] == 'aconfirmer' and $mode == 'sup' and $cle ==  $q['alea_actuel']){
		return 'sup';
	}else
		return 'rien';
}

// Filtres
function n_to_br($texte){
	$texte = str_replace("\n", "<br />", $texte);
	return $texte;
}

function id_pays_to_pays($id_pays){
	if($id_pays != 0){
		$pays = sql_getfetsel('pays', 'spip_geo_pays', 'id_pays ='.$id_pays) ;
		return $pays;
	}
	else return;
}
?>