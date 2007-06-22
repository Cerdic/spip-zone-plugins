<?php
include_spip('base/abstract_sql');

function confirmation_inscription2($id, $mode, $cle){
	$q = spip_query("SELECT statut, alea_actuel FROM spip_auteurs WHERE id_auteur = '$id'");
	$q = spip_fetch_array($q);
	if($q['statut'] == 'aconfirmer' and $mode == 'conf' and $cle ==  $q['alea_actuel']){
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
		$cle = ereg_replace("_(fiche|table).*", "", $clef);
		if($cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'statut_rel'  and $cle != 'accesrestreint' and !ereg("^(domaine|categories|zone|newsletter).*$", $cle) ){
			echo $cle.' ';
			if($cle == 'naissance' and !isset($desc['field'][$cle]) and $_POST[$clef]!=''){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
					$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			}
			elseif(!isset($desc['field'][$cle]) and $_POST[$clef]!=''){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." text NOT NULL");
					$desc['field'][$cle] = "text NOT NULL";
			}
		}
	}
	$listes = lire_config('plugin/SPIPLISTES');
	if(isset($listes) and !isset($desc['field']['spip_listes_format']))
		spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL");
	
}

?>