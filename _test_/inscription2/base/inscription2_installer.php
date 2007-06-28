<?php
$GLOBALS['inscription2_version'] = 0.3;

function inscription2_verifier_base(){
	include_spip('base/abstract_sql');
	$accepter_visiteurs = lire_meta('accepter_visiteurs');
	if($accepter_visiteurs != 'oui'){
		ecrire_meta("accepter_visiteurs", "oui");
		ecrire_metas();
	}
	
	$version_base = $GLOBALS['inscription2_version'];
	//definition de la table cible
	$table_nom = "spip_auteurs_elargis";
	if (!isset($GLOBALS['meta']['inscription2_version']) ){
		//création de la nouvelle table spip_auteurs_elargis
		spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY, id_auteur bigint NOT NULL, INDEX id_auteur (id_auteur) );");
	}
	$desc = spip_abstract_showtable($table_nom, '', true);
	if($desc['key']['PRIMARY KEY']!='id'){
		spip_query("ALTER TABLE ".$table_nom." DROP PRIMARY KEY");
		if(!isset($desc['fields']['id']))
			spip_query("ALTER TABLE ".$table_nom." ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY");
		else 
			spip_query("ALTER TABLE ".$table_nom." ADD PRIMARY KEY (id)");
	}
	if($desc['key']['KEY id_auteur'])
		spip_query("ALTER TABLE ".$table_nom." DROP INDEX id_auteur, ADD INDEX id_auteur (id_auteur)");
	else
		spip_query("ALTER TABLE ".$table_nom." ADD INDEX id_auteur (id_auteur)");
	//ajouts des différents champs
	$desc = spip_abstract_showtable($table_nom, '', true);
	if (is_array(lire_config('inscription2'))){
		foreach(lire_config('inscription2') as $cle => $val) {
			$cle = ereg_replace("_(fiche|table).*$","", $cle);
			if($val!='' and !isset($desc['field'][$cle])  and $cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'naissance' and $cle != 'statut_relances'  and $cle != 'accesrestreint' and !ereg("^(domaine|categories|zone|newsletter).*$", $cle)){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." TEXT NOT NULL");
				$desc['field'][$cle] = "TEXT NOT NULL";
			}if($val!='' and !isset($desc['field'][$cle]) and $cle == 'naissance'){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
				$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			}
		}
	}
	if($GLOBALS['meta']['spiplistes_version'] and !isset($desc['field']['spip_listes_format']))
		spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR(8)");
	$s = spip_query("SELECT a.id_auteur FROM spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur WHERE b.id_auteur is null");
	while($q = spip_fetch_array($s))
		$a[] = $q['id_auteur'];
	if($a){
		$a = join('), (', $a);
		spip_query("insert into spip_auteurs_elargis (id_auteur) values (".$a.")");
	}
	
	ecrire_meta('inscription2_version',$version_base);
	ecrire_metas();
}

	
	//supprime les données depuis la table spip_auteurs_ajouts
	function inscription2_vider_tables() {
		include_spip('base/abstract_sql');
		//supprime la table spip_auteurs_ajouts
		$desc = spip_abstract_showtable('spip_auteurs_elargis', '', true);
		foreach(lire_config('inscription2') as $cle => $val){
			if(isset($desc['field'][$cle])	and $cle != 'id' and $cle != 'id_auteur' and $cle != 'spip_listes_format')
				$a = spip_query('ALTER TABLE spip_auteurs_elargis DROP COLUMN '.$cle);
		}
		spip_query("DROP TABLE spip_auteurs_elargis");
		effacer_meta('inscription2');
		effacer_meta('inscription2_version');
		ecrire_metas();
	}
	
	function inscription2_install($action){
		$version_base = $GLOBALS['inscription2_version'];
		switch ($action){
			case 'test':
 				return (isset($GLOBALS['meta']['inscription2_version']) AND ($GLOBALS['meta']['inscription2_version']>=$version_base));
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