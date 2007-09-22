<?php
include_spip('base/abstract_sql');

function confirmation_inscription2($id, $mode, $cle){
	$q = spip_query("SELECT statut, alea_actuel FROM spip_auteurs WHERE id_auteur = '$id'");
	$q = spip_fetch_array($q);
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

function inscription2_verifier_tables(){
	//definition de la table cible
	$table_nom = "spip_auteurs_elargis";
	$desc = spip_abstract_showtable($table_nom, '', true);
	spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY, id_auteur bigint NOT NULL, FOREIGN KEY (id_auteur) REFERENCES spip_auteurs (id_auteur));");
	foreach(lire_config('inscription2') as $clef => $val) {
		$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $clef);
		if($cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'statut_nouveau' and $cle != 'statut_int'  and $cle != 'accesrestreint' and !ereg("^(categories|zone|newsletter).*$", $cle) ){
			if($cle == 'naissance' and !isset($desc['field'][$cle]) and _request($clef)!=''){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
					$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			}elseif(_request($clef)!='' and !isset($desc['field'][$cle]) and $cle == 'validite'){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
				$desc['field'][$cle] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
			}elseif(_request($clef)!='' and !isset($desc['field'][$cle]) and $cle == 'pays'){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." int NOT NULL");
				$desc['field'][$cle] = " int NOT NULL";
			}elseif(!isset($desc['field'][$cle]) and _request($clef)!=''){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." text NOT NULL");
					$desc['field'][$cle] = "text NOT NULL";
			}
		}
	}
	$listes = lire_config('plugin/SPIPLISTES');
	if($listes and !isset($desc['field']['spip_listes_format']))
		spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL");
}

// Filtres

function n_to_br($texte){
	$texte = str_replace("\n", "<br />", $texte);
	return $texte;
}

function id_pays_to_pays($id_pays){
$pays = spip_fetch_array(spip_query("select pays from spip_geo_pays where id_pays ='$id_pays'")) ;
return $pays['pays'] ;
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