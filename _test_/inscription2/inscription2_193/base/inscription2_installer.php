<?php

$GLOBALS['inscription2_version'] = 0.61;

function inscription2_upgrade(){
	
	//On force le fait d accepter les visiteurs
	$accepter_visiteurs = lire_meta('accepter_visiteurs');
	if($accepter_visiteurs != 'oui'){
		ecrire_meta("accepter_visiteurs", "oui");
		ecrire_metas();
	}
	
	$version_base = $GLOBALS['inscription2_version'];
	$current_version = 0.0;
	
	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['inscription2_version']) )
		&& (($current_version = $GLOBALS['meta']['inscription2_version'])==$version_base))
	return;
			
	//Si c est une nouvelle installation toute fraiche
	if ($current_version==0.0){
		//inclusion des fonctions pour les requetes sql
		include_spip('base/abstract_sql');
		
		//definition de la table cible
		$table_nom = "spip_auteurs_elargis";
	
		spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY, id_auteur bigint NOT NULL, INDEX id_auteur (id_auteur) );");
	
		//ajout des index
		$desc = spip_abstract_showtable($table_nom, '', false);
		
		if(is_array($desc) and $desc['key']['PRIMARY KEY']!='id'){
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
	
		//insertion des infos par defaut
		$lala = lire_meta('inscription2');
		
		if(!$lala){
		ecrire_meta('inscription2','a:142:{s:3:"nom";s:2:"on";s:15:"nom_obligatoire";s:2:"on";s:13:"nom_fiche_mod";s:2:"on";s:9:"nom_fiche";N;s:9:"nom_table";s:2:"on";s:5:"email";s:2:"on";s:17:"email_obligatoire";s:2:"on";s:15:"email_fiche_mod";N;s:11:"email_fiche";N;s:11:"email_table";N;s:11:"nom_famille";s:2:"on";s:23:"nom_famille_obligatoire";N;s:21:"nom_famille_fiche_mod";N;s:17:"nom_famille_fiche";N;s:17:"nom_famille_table";s:2:"on";s:6:"prenom";s:2:"on";s:18:"prenom_obligatoire";N;s:16:"prenom_fiche_mod";N;s:12:"prenom_fiche";N;s:12:"prenom_table";s:2:"on";s:8:"username";s:2:"on";s:20:"username_obligatoire";N;s:18:"username_fiche_mod";s:2:"on";s:14:"username_fiche";N;s:14:"username_table";N;s:9:"naissance";N;s:21:"naissance_obligatoire";N;s:19:"naissance_fiche_mod";N;s:15:"naissance_fiche";N;s:15:"naissance_table";N;s:4:"sexe";N;s:16:"sexe_obligatoire";N;s:14:"sexe_fiche_mod";N;s:10:"sexe_fiche";N;s:10:"sexe_table";N;s:7:"adresse";s:2:"on";s:19:"adresse_obligatoire";N;s:17:"adresse_fiche_mod";s:2:"on";s:13:"adresse_fiche";N;s:13:"adresse_table";N;s:11:"code_postal";s:2:"on";s:23:"code_postal_obligatoire";N;s:21:"code_postal_fiche_mod";s:2:"on";s:17:"code_postal_fiche";N;s:17:"code_postal_table";N;s:5:"ville";s:2:"on";s:17:"ville_obligatoire";N;s:15:"ville_fiche_mod";s:2:"on";s:11:"ville_fiche";N;s:11:"ville_table";s:2:"on";s:4:"pays";N;s:16:"pays_obligatoire";N;s:14:"pays_fiche_mod";N;s:10:"pays_fiche";N;s:10:"pays_table";N;s:9:"telephone";s:2:"on";s:21:"telephone_obligatoire";N;s:19:"telephone_fiche_mod";s:2:"on";s:15:"telephone_fiche";N;s:15:"telephone_table";N;s:3:"fax";N;s:15:"fax_obligatoire";N;s:13:"fax_fiche_mod";N;s:9:"fax_fiche";N;s:9:"fax_table";N;s:6:"mobile";N;s:18:"mobile_obligatoire";N;s:16:"mobile_fiche_mod";N;s:12:"mobile_fiche";N;s:12:"mobile_table";N;s:11:"commentaire";s:2:"on";s:23:"commentaire_obligatoire";N;s:21:"commentaire_fiche_mod";N;s:17:"commentaire_fiche";N;s:17:"commentaire_table";N;s:10:"profession";N;s:22:"profession_obligatoire";N;s:20:"profession_fiche_mod";N;s:16:"profession_fiche";N;s:16:"profession_table";N;s:7:"societe";N;s:19:"societe_obligatoire";N;s:17:"societe_fiche_mod";N;s:13:"societe_fiche";N;s:13:"societe_table";N;s:11:"url_societe";N;s:23:"url_societe_obligatoire";N;s:21:"url_societe_fiche_mod";N;s:17:"url_societe_fiche";N;s:17:"url_societe_table";N;s:7:"secteur";N;s:19:"secteur_obligatoire";N;s:17:"secteur_fiche_mod";N;s:13:"secteur_fiche";N;s:13:"secteur_table";N;s:8:"fonction";N;s:20:"fonction_obligatoire";N;s:18:"fonction_fiche_mod";N;s:14:"fonction_fiche";N;s:14:"fonction_table";N;s:11:"adresse_pro";N;s:23:"adresse_pro_obligatoire";N;s:21:"adresse_pro_fiche_mod";N;s:17:"adresse_pro_fiche";N;s:17:"adresse_pro_table";N;s:15:"code_postal_pro";N;s:27:"code_postal_pro_obligatoire";N;s:25:"code_postal_pro_fiche_mod";N;s:21:"code_postal_pro_fiche";N;s:21:"code_postal_pro_table";N;s:9:"ville_pro";N;s:21:"ville_pro_obligatoire";N;s:19:"ville_pro_fiche_mod";N;s:15:"ville_pro_fiche";N;s:15:"ville_pro_table";N;s:8:"pays_pro";N;s:20:"pays_pro_obligatoire";N;s:18:"pays_pro_fiche_mod";N;s:14:"pays_pro_fiche";N;s:14:"pays_pro_table";N;s:13:"telephone_pro";N;s:25:"telephone_pro_obligatoire";N;s:23:"telephone_pro_fiche_mod";N;s:19:"telephone_pro_fiche";N;s:19:"telephone_pro_table";N;s:7:"fax_pro";N;s:19:"fax_pro_obligatoire";N;s:17:"fax_pro_fiche_mod";N;s:13:"fax_pro_fiche";N;s:13:"fax_pro_table";N;s:10:"mobile_pro";N;s:22:"mobile_pro_obligatoire";N;s:20:"mobile_pro_fiche_mod";N;s:16:"mobile_pro_fiche";N;s:16:"mobile_pro_table";N;s:11:"publication";N;s:8:"domaines";N;s:6:"divers";N;s:14:"statut_nouveau";s:6:"6forum";s:8:"creation";N;s:10:"statut_int";N;s:14:"statut_interne";s:0:"";}');
			//On ecrit tout de suite les metas comme cela on cree les champs directement derriere
			ecrire_metas();
		}
	
		//ajouts des differents champs ecris dans les metas
		if (is_array(lire_config('inscription2'))){
			foreach(lire_config('inscription2') as $cle => $val) {
				$cle = ereg_replace("_(obligatoire|fiche|table).*$","", $cle);
				if($val!='' and !isset($desc['field'][$cle]) and $cle == 'naissance'){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
					$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
				}elseif($val!='' and !isset($desc['field'][$cle]) and $cle == 'validite'){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
					$desc['field'][$cle] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
				}elseif($val!='' and !isset($desc['field'][$cle]) and $cle == 'pays'){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." int NOT NULL");
					$desc['field'][$cle] = " int NOT NULL";
				}elseif($val!='' and !isset($desc['field'][$cle])  and $cle != 'statut_nouveau' and $cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'statut_relances'  and $cle != 'accesrestreint' and !ereg("^(categories|zone|newsletter).*$", $cle)){
					spip_query("ALTER TABLE ".$table_nom." ADD ".$cle." TEXT NOT NULL");
					$desc['field'][$cle] = "TEXT NOT NULL";
				}
			}
		}

		//Si spip_listes est installe
		if($GLOBALS['meta']['spiplistes_version'] and !isset($desc['field']['spip_listes_format']))
			spip_query("ALTER TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR(8)");
	
		//inserer les auteurs qui existent deja dans la table spip_auteurs en non pas dans la table elargis
		$s = spip_query("SELECT a.id_auteur FROM spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur WHERE b.id_auteur is null");
		while($q = spip_fetch_array($s))
			$a[] = $q['id_auteur'];
		if($a){
			$a = join('), (', $a);
			spip_query("insert into spip_auteurs_elargis (id_auteur) values (".$a.")");
		}
	
		//les pays
		include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
		spip_query("DROP TABLE spip_pays");
		spip_query("CREATE TABLE spip_geo_pays (id_pays SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY, pays varchar(255) NOT NULL );");
		spip_query("INSERT INTO spip_geo_pays (pays) VALUES (\"".join('"), ("',$liste_pays)."\")");
		echo "Inscription2 installe @ ".$version_base;
		ecrire_meta('inscription2_version',$current_version=$version_base);
	}

	// Si la version installee est inferieur a O.6 on fait l homogeneisation avec spip_geo
	if ($current_version<0.6){
		include_spip('base/abstract_sql');
		include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
		$table_pays = "spip_geo_pays";
		$descpays = spip_abstract_showtable($table_pays, '', false);
		
		spip_query("DROP TABLE spip_pays");
		spip_query("CREATE TABLE spip_geo_pays (id_pays SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY, nom varchar(255) NOT NULL );");
		
		if(!isset($descpays['field']['pays'])){
			spip_query("INSERT INTO spip_geo_pays (nom) VALUES (\"".join('"), ("',$liste_pays)."\")");
		}
		
		echo "Inscription2 update @ 0.6<br/>Spip_pays devient spip_geo_pays homogeneite avec spip_geo";
		ecrire_meta('inscription2_version',$current_version=0.6);
	}
		// Si la version installee est inferieur a O.6 on fait l homogeneisation avec spip_geo
	if ($current_version<0.61){
		include_spip('base/abstract_sql');
		$table_pays = "spip_geo_pays";
		$descpays = spip_abstract_showtable($table_pays, '', false);
		
		if((isset($descpays['field']['nom'])) && (!isset($descpays['field']['pays']))){
			spip_query("ALTER TABLE spip_geo_pays CHANGE nom pays varchar(255) NOT NULL");
		}
		
		echo "Inscription2 update @ 0.61<br/>On retablit le champs pays sur la table pays et pas nom";
		ecrire_meta('inscription2_version',$current_version=0.61);
	}
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
		if (!lire_config('plugin/SPIPLISTES') && !lire_config('plugin/ECHOPPE')){
			spip_query('DROP TABLE spip_auteurs_elargis');
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
				inscription2_upgrade();
				break;
			case 'uninstall':
				inscription2_vider_tables();
				break;
		}
	}
?>