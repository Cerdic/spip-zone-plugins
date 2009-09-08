<?php

function genespip_install(){
    genespip_verifier_base();
}

function genespip_verifier_base(){
    $version_base =  $GLOBALS['genespip_version'];; //version actuelle
    $current_version = 0.0;
    
    if (   (!isset($GLOBALS['meta']['genespip_base_version']) )
        | (($current_version = $GLOBALS['meta']['genespip_base_version'])!=$version_base)){
		
		include_spip('base/genespip');
		
        if ($current_version==0.0){  
            include_spip('base/create');
            include_spip('base/abstract_sql');
            creer_base();
            $date_init=date("Y-m-d H:i:s");
            $action2=spip_query("INSERT INTO spip_genespip_type_evenements (type_evenement , clair_evenement) VALUES ('BIRT', 'Naissance'), ('DEAT', 'D&eacute;c&egrave;s'), ('MARR', 'Mariage')");
            $action3=spip_query("INSERT INTO spip_genespip_lieux (ville, pays) VALUES ('','xx')");
			
            ecrire_meta('genespip_base_version',$current_version=$version_base);
        }
		
        if ($current_version<0.1){
			//Création table spip_genespip_Lieu
            spip_query("CREATE TABLE spip_genespip_lieux (id_lieu INT NOT NULL auto_increment , ville TEXT NOT NULL , code_departement INT NOT NULL , departement TEXT NOT NULL , region TEXT NOT NULL , pays TEXT NOT NULL, PRIMARY KEY (id_lieu)) TYPE=MyISAM") or die ("Requête spip_genespip_lieu invalide");
            $action3=spip_query("INSERT INTO spip_genespip_lieux (ville, pays) VALUES ('','xx')");
            echo "Base lieux cr&eacute;&eacute;e.<br />";
			//récupération des lieux (naissance, deces, mariage)
            $result = spip_query("SELECT naissancelieu, naissancedep, naissancepays FROM spip_genespip_individu where poubelle<>1 group by naissancelieu");
            while ($naissance = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_lieux (ville , code_departement, pays) VALUES ('".$naissance['naissancelieu']."', '".$naissance['naissancedep']."', '".$naissance['naissancepays']."')");
            }
            $result = spip_query("SELECT ville, deceslieu, decesdep, decespays FROM spip_genespip_individu, spip_genespip_lieux where spip_genespip_mariage.deceslieu<>spip_genespip_lieux.ville group by deceslieu");
            while ($deces = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_lieux (ville , code_departement, pays) VALUES ('".$deces['deceslieu']."', '".$deces['decesdep']."', '".$deces['decespays']."')");
            }
            $result = spip_query("SELECT ville, marlieu, mardep, marpays FROM spip_genespip_mariage, spip_genespip_lieux where spip_genespip_mariage.marlieu<>spip_genespip_lieux.ville group by marlieu");
            while ($mar = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_lieux (ville , code_departement, pays) VALUES ('".$mar['marlieu']."', '".$mar['mardep']."', '".$mar['marpays']."')");
            }
			//Création table spip_genespip_type_evenement
            spip_query("CREATE TABLE spip_genespip_type_evenements (id_type_evenement INT NOT NULL auto_increment ,type_evenement TEXT NOT NULL ,clair_evenement TEXT NOT NULL ,PRIMARY KEY (id_type_evenement)) TYPE=MyISAM") or die ("Requête spip_genespip_type_evenement invalide");
            echo "Base type Evenement cr&eacute;&eacute;e.<br />";
            spip_query("INSERT INTO spip_genespip_type_evenements (type_evenement , clair_evenement) VALUES ('BIRT', 'Naissance'), ('DEAT', 'D&eacute;c&egrave;s'), ('MARR', 'Mariage')");
			//Création table spip_genespip_evenement
            spip_query("CREATE TABLE spip_genespip_evenements (id_evenement INT NOT NULL auto_increment ,id_individu INT NOT NULL ,id_type_evenement INT NOT NULL ,date_evenement DATE NOT NULL , precision_date TEXT NOT NULL ,id_lieu INT NOT NULL DEFAULT '1',id_epoux INT NOT NULL ,date_update DATETIME NOT NULL ,PRIMARY KEY (id_evenement)) TYPE=MyISAM") or die ("Requête spip_genespip_evenement invalide");
            echo "Base Evenement cr&eacute;&eacute;e.<br />";
            $result = spip_query("SELECT id_individu, naissance, id_lieu FROM spip_genespip_individu, spip_genespip_lieux where poubelle<>1 and ville=naissancelieu group by id_individu") or die ("Requête insert evenement naissance invalide");
            while ($naissance = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_evenements (id_individu , id_type_evenement, date_evenement, id_lieu) VALUES ('".$naissance['id_individu']."', '1', '".$naissance['naissance']."', '".$naissance['id_lieu']."')");
            }
            $result = spip_query("SELECT id_individu, deces, id_lieu FROM spip_genespip_individu, spip_genespip_lieux where poubelle<>1 and ville=deceslieu group by id_individu") or die ("Requête insert evenement deces invalide");
            while ($deces = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_evenements (id_individu , id_type_evenement, date_evenement, id_lieu) VALUES ('".$deces['id_individu']."', '2', '".$deces['deces']."', '".$deces['id_lieu']."')");
            }
            $result = spip_query("SELECT individu, mar, epoux, id_lieu FROM spip_genespip_mariage, spip_genespip_lieux where ville=marlieu group by id_mariage") or die ("Requête insert evenement mariage invalide");
            while ($mar = spip_fetch_array($result)) {
				spip_query("INSERT INTO spip_genespip_evenements (id_individu , id_type_evenement, date_evenement, id_lieu, id_epoux) VALUES ('".$mar['individu']."', '3', '".$mar['mar']."', '".$mar['id_lieu']."', '".$mar['epoux']."')");
            }
			//Nettoyage champs spip_genespip_lieux non utilisés
            $result = spip_query("SELECT * FROM spip_genespip_lieux order by ville");
            while ($lieux = spip_fetch_array($result)) {
				$resultnb = spip_query("SELECT * FROM spip_genespip_evenements where id_lieu=".$lieux['id_lieu']);
				if(mysql_num_rows($resultnb)==0){
					spip_query("DELETE FROM spip_genespip_lieux WHERE id_lieu = ".$lieux['id_lieu']);
				}
			}
			//Ajout du champ limitation dans spip_genespip_individu
            spip_query("ALTER TABLE spip_genespip_individu ADD limitation INT(3) NOT NULL ");
			//Suppression des anciens champs de spip_genespip_individu
            spip_query("ALTER TABLE spip_genespip_individu DROP naissance ,DROP naissancelieu ,DROP naissancedep ,DROP naissancepays ,DROP deces ,DROP deceslieu ,DROP decesdep ,DROP decespays");
            echo "Suppression des champs inutile de la table individu<br />";
			//Suppression table spip_genespip_mariage
            spip_query("DROP TABLE spip_genespip_mariage");
            echo "Suppression de la table mariage remplac&eacute; par la table &eacute;v&egrave;nements.<br />";
			//Ajout du champ limitation dans spip_genespip_individu
            spip_query("ALTER TABLE spip_genespip_individu ADD format_signature TEXT NOT NULL");
			//Ajout de la table journal
           spip_query("CREATE TABLE spip_genespip_journal (id_journal INT NOT NULL AUTO_INCREMENT, action TINYTEXT NOT NULL, descriptif TEXT NOT NULL, id_individu INT NOT NULL, id_auteur INT NOT NULL, date_update DATETIME NOT NULL, PRIMARY KEY (id_journal)) TYPE=MyISAM") or die ("Requête spip_genespip_journal invalide");
           echo "Base journal cr&eacute;&eacute;e.<br />";
			
            ecrire_meta('genespip_base_version',$current_version=0.1);
        }
		
		if ($current_version>0.6){
			//Suppression spip_genespip_parametres
            spip_query("DROP TABLE spip_genespip_parametres");
            ecrire_meta('genespip_base_version',$current_version=0.6);
        }
        ecrire_metas();
    }
}
?>