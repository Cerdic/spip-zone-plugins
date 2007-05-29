<?php

$GLOBALS['inscription2_base_version'] = 0.1;

function inscription2_verifier_base(){

	include_spip('base/abstract_sql');
	//definition de la table cible
	$table_nom = "spip_auteurs_elargis";
	//création de la nouvelle table spip_auteurs_elargis
	spip_query("CREATE TABLE ".$table_nom." (id_auteur bigint(21), PRIMARY KEY (id_auteur));");
	//ajouts des différents champs
	$desc = spip_abstract_showtable($table_nom, '', true);
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and !isset($desc['field'][$cle])  and $cle != 'nom' and $cle != 'email' and $cle != 'login' and $cle != 'naissance' and !ereg("^.+_fiche$", $cle) and !ereg("^.+_fiche_mod$", $cle) and !ereg("^.+_table$", $cle))
			spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." TEXT NOT NULL");
		if($cle == 'naissance')
			spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE");
	}	
}
	
	//supprime les données depuis la table spip_auteurs_ajouts
	function inscription2_vider_tables() {
		include_spip('base/inscription2');
		include_spip('base/abstract_sql');
		effacer_meta('inscription2_base_version');
		//supprime la table spip_auteurs_ajouts
		spip_query("DROP TABLE spip_auteurs_elargis");
	}
	
	function inscription2_install($action){
		$version_base = $GLOBALS['inscription2_base_version'];
		$GLOBALS['meta']['accepter_visiteurs']='oui';
		switch ($action){
			case 'test':
 				return isset($GLOBALS['meta']['inscription2_base_version']);
				break;
			case 'install':
				inscription2_verifier_base();
				break;
			case 'uninstall':
				inscription2_vider_tables();
				break;
		}
	}
?>