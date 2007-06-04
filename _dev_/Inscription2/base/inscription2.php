<?php
	/**Plugin Inscription 2 avec CFG **/
	
	if (!defined("_ECRIRE_INC_VERSION")) return;

	global $tables_principales;
	$table_nom = "spip_auteurs_elargis";
	spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (id_auteur bigint(21), PRIMARY KEY (id_auteur))");
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and $cle != 'nom' and $cle != 'email' and $cle != 'username' and !ereg("^.+_fiche$", $cle) and !ereg("^.+_fiche_mod$", $cle) and !ereg("^.+_table$", $cle)){
			if($cle == 'naissance' ){
				$spip_auteurs_elargis[$cle] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
				if (!isset($desc['field'][$cle]))
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE");
			}else{
				$spip_auteurs_elargis[$cle] = 'text NOT NULL';
				if (!isset($desc['field'][$cle]))
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." TEXT NOT NULL");
			}
		}
	}
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	
	$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id_auteur");

	$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);

?>