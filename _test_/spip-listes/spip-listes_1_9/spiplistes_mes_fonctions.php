<?php

// Boucles SPIP-listes
global $tables_principales,$exceptions_des_tables,$table_date;

/*$tables_principales['messages']= array(
 'field' => array(
   "id_message" => "bigint(21)",
   "titre" => "varchar(100)",
   "texte" => "blob",
   "type" => "varchar(6)",
   "date_heure" => "datetime"
 ),
 'key' => array("PRIMARY KEY" => "id_message")
);*/

$exceptions_des_tables['messages']['date']='date_heure';
$table_date['messages']='date_heure';
//
// <BOUCLE(MESSAGES)>
//
function boucle_MESSAGES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[] =  "spip_messages AS $id_table";
        $boucle->where[] = array("'='","'type'","'\"nl\"'");
        return calculer_boucle($id_boucle, $boucles);
}

// Filtres SPIP-listes
function supprimer_destinataires($texte) {
 return eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
}


function date_depuis($date) {
	    
	    if (!$date) return;
 	    $decal = date("U") - date("U", strtotime($date));
 	    
	    if ($decal < 0) {
 	        $il_y_a = "date_dans";
 	        $decal = -1 * $decal;
	    } else {
 	        $il_y_a = "spiplistes:date_depuis";
	    }
	    
	    if ($decal < 3600) {
 	        $minutes = ceil($decal / 60);
	        $retour = _T($il_y_a, array("delai"=>"$minutes "._T("date_minutes")));
	    }
	    else if ($decal < (3600 * 24) ) {
	        $heures = ceil ($decal / 3600);
 	        $retour = _T($il_y_a, array("delai"=>"$heures "._T("date_heures")));
 	    }
    else if ($decal < (3600 * 24 * 7)) {
 	        $jours = ceil ($decal / (3600 * 24));
 	        $retour = _T($il_y_a, array("delai"=>"$jours "._T("date_jours")));
	    }
	    else if ($decal < (3600 * 24 * 7 * 4)) {
	        $semaines = ceil ($decal / (3600 * 24 * 7));
 	        $retour = _T($il_y_a, array("delai"=>"$semaines "._T("date_semaines")));
	    }
	    else if ($decal < (3600 * 24 * 30 * 6)) {
 	        $mois = ceil ($decal / (3600 * 24 * 30));
 	        $retour = _T($il_y_a, array("delai"=>"$mois "._T("date_mois")));
 	    }
	    else {
 	        $retour = _T($il_y_a, array("delai"=>" ")).affdate_court($date);
 	    }
 	
 	
 	
 	    return $retour;
}

?>
