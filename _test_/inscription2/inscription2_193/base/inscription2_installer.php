<?php

$GLOBALS['inscription2_version'] = 0.61;

function inscription2_upgrade(){
	
    include_spip('cfg_options');
	
	//On force le fait d accepter les visiteurs
	$accepter_visiteurs = $GLOBALS['meta']['accepter_visiteurs'];
	if($accepter_visiteurs != 'oui'){
		ecrire_meta("accepter_visiteurs", "oui");
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
		$desc = sql_showtable($table_nom, '', false);
		
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
		$lala = $GLOBALS['meta']['inscription2'];
		
		if(!$lala){
		    ecrire_config(
		        'inscription2',
		        array(
		            'nom' => 'on',
		            'nom_obligatoire' => 'on',
		            'nom_fiche_mod' => 'on',
		            'nom_fiche' => 'on',
		            'nom_table' => 'on',
		            'email' => 'on',
		            'email_obligatoire' => 'on',
		            'email_fiche_mod' => NULL,
		            'email_fiche' => NULL,
		            'email_table' => NULL,
		            'nom_famille' => 'on',
		            'nom_famille_obligatoire' => NULL,
		            'nom_famille_fiche_mod' => NULL,
		            'nom_famille_fiche' => NULL,
		            'nom_famille_table' => 'on',
		            'prenom' => 'on',
		            'prenom_obligatoire' => NULL,
		            'prenom_fiche_mod' => NULL,
		            'prenom_fiche' => NULL,
		            'prenom_table' => 'on',
		            'login' => 'on',
		            'login_obligatoire' => NULL,
		            'login_fiche_mod' => 'on',
		            'login_fiche' => NULL,
		            'login_table' => NULL,
		            'naissance' => NULL,
		            'naissance_obligatoire' => NULL,
		            'naissance_fiche_mod' => NULL,
		            'naissance_fiche' => NULL,
		            'naissance_table' => NULL,
		            'sexe' => NULL,
		            'sexe_obligatoire' => NULL,
		            'sexe_fiche_mod' => NULL,
		            'sexe_fiche' => NULL,
		            'sexe_table' => NULL,
		            'adresse' => 'on',
		            'adresse_obligatoire' => NULL,
		            'adresse_fiche_mod' => 'on',
		            'adresse_fiche' => NULL,
		            'adresse_table' => NULL,
		            'code_postal' => 'on',
		            'code_postal_obligatoire' => NULL,
		            'code_postal_fiche_mod' => 'on',
		            'code_postal_fiche' => NULL,
		            'code_postal_table' => NULL,
		            'ville' => 'on',
		            'ville_obligatoire'  => NULL,
		            'ville_fiche_mod' => 'on',
		            'ville_fiche' => NULL,
		            'ville_table' => 'on',
		            'pays' => NULL,
		            'pays_obligatoire' => NULL,
		            'pays_fiche_mod' => NULL,
		            'pays_fiche' => NULL,
		            'pays_table' => NULL,
		            'telephone' => 'on',
		            'telephone_obligatoire' => NULL,
		            'telephone_fiche_mod' => 'on',
		            'telephone_fiche' => NULL,
		            'telephone_table' => NULL,
		            'fax' => NULL,
		            'fax_obligatoire' => NULL,
		            'fax_fiche_mod' => NULL,
		            'fax_fiche' => NULL,
		            'fax_table' => NULL,
		            'mobile' => NULL,
		            'mobile_obligatoire' => NULL,
		            'mobile_fiche_mod' => NULL,
		            'mobile_fiche' => NULL,
		            'mobile_table' => NULL,
		            'commentaire' => 'on',
		            'commentaire_obligatoire' => NULL,
		            'commentaire_fiche_mod' => NULL,
		            'commentaire_fiche' => NULL,
		            'commentaire_table' => NULL,
		            'profession' => NULL,
		            'profession_obligatoire' => NULL,
		            'profession_fiche_mod' => NULL,
		            'profession_fiche' => NULL,
		            'profession_table' => NULL,
		            'societe' => NULL,
		            'societe_obligatoire' => NULL,
		            'societe_fiche_mod' => NULL,
		            'societe_fiche' => NULL,
		            'societe_table' => NULL,
		            'url_societe' => NULL,
		            'url_societe_obligatoire' => NULL,
		            'url_societe_fiche_mod' => NULL,
		            'url_societe_fiche' => NULL,
		            'url_societe_table' => NULL,
		            'secteur' => NULL,
		            'secteur_obligatoire' => NULL,
		            'secteur_fiche_mod' => NULL,
		            'secteur_fiche' => NULL,
		            'secteur_table' => NULL,
		            'fonction' => NULL,
		            'fonction_obligatoire' => NULL,
		            'fonction_fiche_mod' => NULL,
		            'fonction_fiche' => NULL,
		            'fonction_table' => NULL,
		            'adresse_pro' => NULL,
		            'adresse_pro_obligatoire' => NULL,
		            'adresse_pro_fiche_mod' => NULL,
		            'adresse_pro_fiche' => NULL,
		            'adresse_pro_table' => NULL,
		            'code_postal_pro' => NULL,
		            'code_postal_pro_obligatoire' => NULL,
		            'code_postal_pro_fiche_mod' => NULL,
		            'code_postal_pro_fiche' => NULL,
		            'code_postal_pro_table' => NULL,
		            'ville_pro' => NULL,
		            'ville_pro_obligatoire' => NULL,
		            'ville_pro_fiche_mod' => NULL,
		            'ville_pro_fiche' => NULL,
		            'ville_pro_table' => NULL,
		            'pays_pro' => NULL,
		            'pays_pro_obligatoire' => NULL,
		            'pays_pro_fiche_mod' => NULL,
		            'pays_pro_fiche' => NULL,
		            'pays_pro_table' => NULL,
		            'telephone_pro' => NULL,
		            'telephone_pro_obligatoire' => NULL,
		            'telephone_pro_fiche_mod' => NULL,
		            'telephone_pro_fiche' => NULL,
		            'telephone_pro_table' => NULL,
		            'fax_pro' => NULL,
		            'fax_pro_obligatoire' => NULL,
		            'fax_pro_fiche_mod' => NULL,
		            'fax_pro_fiche' => NULL,
		            'fax_pro_table' => NULL,
		            'mobile_pro' => NULL,
		            'mobille_pro_obligatoire' => NULL,
		            'mobile_pro_fiche_mod' => NULL,
		            'mobile_pro_fiche' => NULL,
		            'mobile_pro_table' => NULL,
		            'publication' => NULL,
		            'domaines' => NULL,
		            'divers' => NULL,
		            'statut_nouveau' => '6forum',
		            'creation' => NULL,
		            'statut_int' => NULL,
		            'statut_interne' => ''		            
		        )
	        );
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
		$descpays = sql_showtable($table_pays, '', false);
		
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
		$descpays = sql_showtable($table_pays, '', false);
		
		if((isset($descpays['field']['nom'])) && (!isset($descpays['field']['pays']))){
			spip_query("ALTER TABLE spip_geo_pays CHANGE nom pays varchar(255) NOT NULL");
		}
		
		echo "Inscription2 update @ 0.61<br/>On retablit le champs pays sur la table pays et pas nom";
		ecrire_meta('inscription2_version',$current_version=0.61);
	}
	if (version_compare($GLOBALS['spip_version_code'],'1.9300','<')) ecrire_metas();
}


	//supprime les données depuis la table spip_auteurs_ajouts
	function inscription2_vider_tables() {
		include_spip('base/abstract_sql');
		//supprime la table spip_auteurs_ajouts
		$desc = sql_showtable('spip_auteurs_elargis', '', true);
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
		if (version_compare($GLOBALS['spip_version_code'],'1.9300','<')) ecrire_metas();
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
