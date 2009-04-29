<?php

$GLOBALS['inscription2_version'] = 0.70;

function inscription2_upgrade(){
	spip_log('INSCRIPTION 2 : installation','inscription2');
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
	include_spip('cfg_options');
	
	//On force le fait d accepter les visiteurs
	$accepter_visiteurs = $GLOBALS['meta']['accepter_visiteurs'];
	if($accepter_visiteurs != 'oui'){
		ecrire_meta("accepter_visiteurs", "oui");
	}
	
	$version_base = $GLOBALS['inscription2_version'];
	$current_version = 0.0;

	//insertion des infos par defaut
	$inscription2_meta = $GLOBALS['meta']['inscription2'];
	
	//Certaines montées de version ont oublié de corriger la meta de I2
	//si ce n'est pas un array alors il faut reconfigurer la meta
	if (!is_array(unserialize($inscription2_meta))) {
		spip_log("INSCRIPTION 2 : effacer la meta inscription2 et relancer l'install","inscription2");
		echo "La configuration du plugin Inscription 2 a &eacute;t&eacute; effac&eacute;e.<br />";
		effacer_meta('inscription2');
		$GLOBALS['meta']['inscription2_version']=0.0;
	}

	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['inscription2_version']) )
		&& (($current_version = $GLOBALS['meta']['inscription2_version'])==$version_base))
	return;
		
	//Si c est une nouvelle installation toute fraiche
	if ($current_version==0.0){
		//inclusion des fonctions pour les requetes sql
		include_spip('base/abstract_sql');
		
		// à passer en sous plugin

		if(!$inscription2_meta){
		ecrire_meta(
			'inscription2',
				serialize(array(
					'nom' => 'on',
					'nom_obligatoire' => 'on',
					'nom_fiche_mod' => 'on',
					'email' => 'on',
					'email_obligatoire' => 'on',
					'nom_famille' => 'on',
					'nom_famille_table' => 'on',
					'prenom' => 'on',
					'prenom_table' => 'on',
					'login' => 'on',
					'login_fiche_mod' => 'on',
					'adresse' => 'on',
					'adresse_fiche_mod' => 'on',
					'code_postal' => 'on',
					'code_postal_fiche_mod' => 'on',
					'ville' => 'on',
					'ville_fiche_mod' => 'on',
					'ville_table' => 'on',
					'telephone' => 'on',
					'telephone_fiche_mod' => 'on',
					'statut_nouveau' => '6forum',
					'statut_interne' => ''
				))
			);
		}
		
		// Creation de la table et des champs
		$verifier_tables = charger_fonction('inscription2_verifier_tables','inc');
		$verifier_tables();
	
		//inserer les auteurs qui existent deja dans la table spip_auteurs en non pas dans la table elargis
		$s = sql_select("a.id_auteur","spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur","b.id_auteur is null");
		while($q = sql_fetch($s)){
			sql_insertq("spip_auteurs_elargis",array('id_auteur' => $q['id_auteur']));
		}
	
		
		/** Inscription 2 (0.70)
		 * Les pays sont maintenant pris dans le plugin Geographie
		 * On ne les installe plus
		 */

		/*
		//les pays
		include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
		$descpays = sql_showtable('spip_pays', '', false);
		if(isset($descpays['field'])){
			sql_drop_table("spip_pays");
		}
		sql_create("spip_geo_pays",
			array("id_pays"=> "SMALLINT NOT NULL AUTO_INCREMENT","pays"=>"varchar(255) NOT NULL"),
			array('PRIMARY KEY' => "id_pays")
		);

		// Attention cette ligne est incorrecte (a corriger si ca doit revenir !)
		// insere 2 "Array" dans la bdd au lieu des pays !
		sql_insertq("spip_geo_pays",array("pays" => $liste_pays));
		*/
		
		echo "Inscription2 installe @ ".$version_base;
		ecrire_meta('inscription2_version',$current_version=$version_base);
	}

	// Si la version installee est inferieur a O.6 on fait l homogeneisation avec spip_geo
	if ($current_version<0.6){
		include_spip('base/abstract_sql');
		include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
		$table_pays = "spip_geo_pays";
		$descpays = sql_showtable($table_pays, '', false);
		
		$descpays_old = sql_showtable('spip_pays', '', false);
		if(isset($descpays_old['field'])){
			sql_drop_table("spip_pays");
		}
		
		if(!isset($descpays['field']['pays'])){
			spip_query("CREATE TABLE spip_geo_pays (id_pays SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY, nom varchar(255) NOT NULL );");
			spip_query("INSERT INTO spip_geo_pays (nom) VALUES (\"".join('"), ("',$liste_pays)."\")");
		}
		
		echo "Inscription2 update @ 0.6<br/>Spip_pays devient spip_geo_pays homogeneite avec spip_geo";
		ecrire_meta('inscription2_version',$current_version=0.6);
	}
		// Si la version installee est inferieur a 0.6 on fait l homogeneisation avec spip_geo
	if ($current_version<0.61){
		include_spip('base/abstract_sql');
		$table_pays = "spip_geo_pays";
		$descpays = sql_showtable($table_pays, '', false);
		
		if((isset($descpays['field']['nom'])) && (!isset($descpays['field']['pays']))){
			sql_alter("TABLE spip_geo_pays CHANGE nom pays varchar(255) NOT NULL");
		}
		
		echo "Inscription2 update @ 0.61<br/>On retablit le champs pays sur la table pays et pas nom";
		ecrire_meta('inscription2_version',$current_version=0.61);
	}
	if ($current_version<0.63){
		include_spip('base/abstract_sql');
		// Suppression du champs id et on remet la primary key sur id_auteur...
		sql_alter("TABLE spip_auteurs_elargis DROP id, DROP INDEX id_auteur, ADD PRIMARY KEY (id_auteur)");
		echo "Inscription2 update @ 0.63<br />On supprime le champs id pour privilegier id_auteur";
		ecrire_meta('inscription2_version',$current_version=0.63);
	}
	if ($current_version<0.65){
		ecrire_meta('inscription2_version',$current_version=0.65);
	}

	/*
	 * Utilisation du plugin Geographie pour les pays
	 */
	if ($current_version<0.70){
		// on fait un upgrade gentil
		// on passe le champs "pays" en "nom"
		// mais le mieux serait de desinstaller la table pour installer celle de geographie
		// on fait l'inverse de la maj 0.61
		include_spip('base/abstract_sql');
		$descpays = sql_showtable("spip_geo_pays", '', false);
		if((isset($descpays['field']['pays'])) && (!isset($descpays['field']['nom']))){
			sql_alter("TABLE spip_geo_pays CHANGE pays nom text DEFAULT '' NOT NULL"); // champ text
		}		
		ecrire_meta('inscription2_version',$current_version=0.70);
	}		
	ecrire_metas();
}


//supprime les donnees depuis la table spip_auteurs_elargis
function inscription2_vider_tables() {
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
	include_spip('cfg_options');
	include_spip('base/abstract_sql');
	
	//supprime la table spip_auteurs_elargis
	if (is_array(lire_config('inscription2'))){
		$clef_passee = array();
		$desc = sql_showtable('spip_auteurs_elargis','', '', true);
		foreach(lire_config('inscription2',array()) as $cle => $val){
			$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $cle);
			if(!in_array($cle,$clef_passee)){
				if(isset($desc['field'][$cle]) and !in_array($cle,$exceptions_des_champs_auteurs_elargis)){
					spip_log("INSCRIPTION 2 : suppression de $cle","inscription2");
					$a = sql_alter('TABLE spip_auteurs_elargis DROP COLUMN '.$cle);
					$desc['field'][$cle]='';
				}
				$clef_passee[] = $cle;
			}
		}
	}
	if (!lire_config('plugin/SPIPLISTES')){
		sql_drop_table('spip_auteurs_elargis');
	}
	if(!lire_config('spip_geo_base_version')){
		sql_drop_table('spip_geo_pays');
		spip_log("INSCRIPTION 2 : suppression de la table spip_geo");
	}
	effacer_meta('inscription2');
	effacer_meta('inscription2_version');
	ecrire_metas();
}

?>
