<?php
$GLOBALS['inscription2_version'] = 0.5;

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
	
	//création de la nouvelle table spip_auteurs_elargis
	if (!isset($GLOBALS['meta']['inscription2_version']) )
		spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY, id_auteur bigint NOT NULL, INDEX id_auteur (id_auteur) );");
	
	//ajout des index
	$desc = spip_abstract_showtable($table_nom, '', false);
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
	
	//infos par defaut
	$lala = lire_meta('inscription2');
	if(!$lala){
		ecrire_meta('inscription2','a:84:{s:3:"nom";s:2:"on";s:13:"nom_fiche_mod";N;s:9:"nom_fiche";s:2:"on";s:9:"nom_table";s:2:"on";s:5:"email";s:2:"on";s:15:"email_fiche_mod";N;s:11:"email_fiche";s:2:"on";s:11:"email_table";s:2:"on";s:6:"prenom";s:2:"on";s:16:"prenom_fiche_mod";N;s:12:"prenom_fiche";s:2:"on";s:12:"prenom_table";s:2:"on";s:8:"username";s:2:"on";s:18:"username_fiche_mod";N;s:14:"username_fiche";s:2:"on";s:14:"username_table";s:2:"on";s:9:"naissance";N;s:19:"naissance_fiche_mod";N;s:15:"naissance_fiche";N;s:15:"naissance_table";N;s:4:"sexe";N;s:14:"sexe_fiche_mod";N;s:10:"sexe_fiche";N;s:10:"sexe_table";N;s:7:"adresse";s:2:"on";s:17:"adresse_fiche_mod";s:2:"on";s:13:"adresse_fiche";N;s:13:"adresse_table";N;s:11:"code_postal";s:2:"on";s:21:"code_postal_fiche_mod";s:2:"on";s:17:"code_postal_fiche";N;s:17:"code_postal_table";N;s:5:"ville";s:2:"on";s:15:"ville_fiche_mod";s:2:"on";s:11:"ville_fiche";N;s:11:"ville_table";s:2:"on";s:9:"telephone";s:2:"on";s:19:"telephone_fiche_mod";s:2:"on";s:15:"telephone_fiche";N;s:15:"telephone_table";s:2:"on";s:3:"fax";N;s:13:"fax_fiche_mod";N;s:9:"fax_fiche";N;s:9:"fax_table";N;s:6:"mobile";N;s:16:"mobile_fiche_mod";N;s:12:"mobile_fiche";N;s:12:"mobile_table";N;s:10:"profession";N;s:20:"profession_fiche_mod";N;s:16:"profession_fiche";N;s:16:"profession_table";N;s:7:"societe";N;s:17:"societe_fiche_mod";N;s:13:"societe_fiche";N;s:13:"societe_table";N;s:7:"secteur";N;s:17:"secteur_fiche_mod";N;s:13:"secteur_fiche";N;s:13:"secteur_table";N;s:8:"fonction";N;s:18:"fonction_fiche_mod";N;s:14:"fonction_fiche";N;s:14:"fonction_table";N;s:10:"newsletter";N;s:11:"newsletters";N;s:6:"divers";N;s:9:"categorie";N;s:11:"publication";N;s:8:"creation";N;s:15:"statut_relances";N;s:10:"categories";N;s:10:"statut_rel";s:0:"";s:14:"accesrestreint";N;s:5:"zones";N;s:8:"domaines";N;s:4:"pays";s:2:"on";s:14:"pays_fiche_mod";s:2:"on";s:10:"pays_fiche";N;s:10:"pays_table";s:2:"on";s:11:"commentaire";N;s:21:"commentaire_fiche_mod";N;s:17:"commentaire_fiche";N;s:17:"commentaire_table";N;}');
		ecrire_metas();
	}
	//ajouts des différents champs
	if (is_array(lire_config('inscription2'))){
		foreach(lire_config('inscription2') as $cle => $val) {
			$cle = ereg_replace("_(fiche|table).*$","", $cle);
			if($val!='' and !isset($desc['field'][$cle])  and $cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'naissance' and $cle != 'pays' and $cle != 'statut_relances'  and $cle != 'accesrestreint' and !ereg("^(domaine|categories|zone|newsletter).*$", $cle)){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." TEXT NOT NULL");
				$desc['field'][$cle] = "TEXT NOT NULL";
			}if($val!='' and !isset($desc['field'][$cle]) and $cle == 'naissance'){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
				$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			}if($val!='' and !isset($desc['field'][$cle]) and $cle == 'pays'){
				spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." int NOT NULL");
				$desc['field'][$cle] = " int NOT NULL";
			}
		}
	}
	//spip_listes
	if($GLOBALS['meta']['spiplistes_version'] and !isset($desc['field']['spip_listes_format']))
		spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR(8)");
	//inserer les auteurs qui existent déjà dans la table spip_auteurs en non pas dans la table elargis
	$s = spip_query("SELECT a.id_auteur FROM spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur WHERE b.id_auteur is null");
	while($q = spip_fetch_array($s))
		$a[] = $q['id_auteur'];
	if($a){
		$a = join('), (', $a);
		spip_query("insert into spip_auteurs_elargis (id_auteur) values (".$a.")");
	}
	
	//les pays
	include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
	$desc = spip_abstract_showtable('spip_pays', '', false);
	if($desc['field']['pays']=='int NOT NULL') //bug de la version 0.4
		spip_query("DROP TABLE spip_pays");
	if(!$desc){
		spip_query("CREATE TABLE spip_pays (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, pays text NOT NULL );");
		spip_query("INSERT INTO spip_pays (pays) VALUES (\"".join('"), ("',$liste_pays)."\")");
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
			if(isset($desc['field'][$cle])	and $cle != 'id' and $cle != 'id_auteur' and $cle != 'spip_listes_format'){
				$a = spip_query('ALTER TABLE spip_auteurs_elargis DROP COLUMN '.$cle);
				$desc['field'][$cle]='';
			}
		}
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