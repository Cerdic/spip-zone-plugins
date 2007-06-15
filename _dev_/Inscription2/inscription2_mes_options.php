<?php
	/**Plugin Inscription 2 avec CFG **/
	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('cfg_options');
	include_spip('base/abstract_sql');
	
	global $tables_principales;
	$table_nom = "spip_auteurs_elargis";
	$desc = spip_abstract_showtable($table_nom, '', true);
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_nom."` (id_auteur bigint(21), PRIMARY KEY (id_auteur))");
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and $cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'statut_rel'  and $cle != 'accesrestreint' and !ereg("^(domaine|categories|zone|newsletter).*$", $cle) and !ereg("^.+_(fiche|table).*$", $cle)){
			if($cle == 'naissance' ){
				$spip_auteurs_elargis[$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
				if (!isset($desc['field'][$cle]))
					spip_query("ALTER TABLE `".$table_nom."` ADD `".$cle."` ".$spip_auteurs_elargis[$cle]);
			}else{
				$spip_auteurs_elargis[$cle] = 'text NOT NULL';
				if (!isset($desc['field'][$cle]))
					spip_query("ALTER TABLE `".$table_nom."` ADD `".$cle."` TEXT NOT NULL");
			}
		}
	}
	$listes = lire_config('plugin/SPIPLISTES');
	if(isset($listes) and !isset($desc['field']['spip_listes_format']))
		spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL");
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	
	$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id_auteur");

	$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	
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
?>