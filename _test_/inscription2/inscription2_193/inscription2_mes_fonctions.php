<?php
include_spip('base/abstract_sql');

function confirmation_inscription2($id, $mode, $cle){
	$q = sql_select("statut, alea_actuel","spip_auteurs","id_auteur = '$id'");
	$q = sql_fetch($q);
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

function form_hidden_env($env){
	$hidden = '';
	foreach(unserialize($env) as $c => $v) {
	    if(!is_array($v)){
	  	 if($c !="fond")
	    $hidden .= "\n<input name='" .
	        entites_html($c) .
	        "' value='" . entites_html($v) .
	        "' type='hidden' />\n";
	    }else{
	    foreach($v as $cc => $vv)
	    $hidden .= "\n<input name='" .
	        entites_html($c) .
	        "[]' value='" . entites_html($vv) .
	        "' type='hidden' />\n";
		}
	}
	return $hidden;
}

?>