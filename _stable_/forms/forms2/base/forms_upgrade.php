<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
	function Forms_structure2table($row,$clean=false){
		$id_form=$row[id_form];
		// netoyer la structure precedente en table
		if ($clean){
			spip_query("DELETE FROM spip_forms_champs WHERE id_form="._q($id_form));
			spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form="._q($id_form));
		}
		
		$structure = unserialize($row['structure']);
		$rang = 1;
		foreach($structure as $cle=>$val){
			$champ = $val['code'];
			$titre = $val['nom'];
			$type = $val['type'];
			$obligatoire = $val['obligatoire'];
			$type_ext = $val['type_ext'];
			$extra_info = isset($type_ext['id_groupe']) ? $type_ext['id_groupe']:0;
			$extra_info = isset($type_ext['taille']) ? $type_ext['taille']:$extra_info;
			$obligatoire = $val['obligatoire'];
			spip_query("INSERT INTO spip_forms_champs (id_form,rang,champ,titre,type,obligatoire,extra_info) 
				VALUES("._q($id_form).","._q($rang++).","._q($champ).","._q($titre).","._q($type).","._q($obligatoire).","._q($extra_info).")");
			if ($type=='select' OR $type=='multiple'){
				$rangchoix = 1;
				foreach($type_ext as $choix=>$titre){
					spip_query("INSERT INTO spip_forms_champs_choix (id_form,champ,choix,titre,rang) 
						VALUES("._q($id_form).","._q($champ).","._q($choix).","._q($titre).","._q($rangchoix++).")");
				}
			}
		}
	}
	function Forms_allstructure2table($clean=false){
		$res = spip_query("SELECT * FROM spip_forms");
		while ($row=spip_fetch_array($res))
			Forms_structure2table($row,$clean);
	}

	function Forms_upgrade(){
		$version_base = 0.17;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['forms_base_version']) )
				&& (($current_version = $GLOBALS['meta']['forms_base_version'])==$version_base))
			return;

		include_spip('base/forms');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// attention on vient peut etre d'une table spip-forms 1.8
			$desc = spip_abstract_showtable('spip_forms','',true);
			if (isset($desc['field'])) 
				$current_version=0.1;
			else {
				creer_base();
				ecrire_meta('forms_base_version',$current_version=$version_base);
			}
		}
		if ($current_version<0.11){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			$query = "ALTER TABLE spip_forms CHANGE `email` `email` TEXT NOT NULL ";
			$res = spip_query($query);
			$query = "SELECT * FROM spip_forms";
			$res = spip_query($query);
			while ($row = spip_fetch_array($res)){
				$email = $row['email'];
				$id_form = $row['id_form'];
				if (unserialize($email)==FALSE){
					$email=addslashes(serialize(array('defaut'=>$email)));
					$query = "UPDATE spip_forms SET email='$email' WHERE id_form=$id_form";
					spip_query($query);
				}
			}
			ecrire_meta('forms_base_version',$current_version=0.11);
		}
		if ($current_version<0.12){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			spip_query("ALTER TABLE spip_forms CHANGE `descriptif` `descriptif` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `schema` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `email` `email` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `texte` `texte` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.12);
		}
		if ($current_version<0.13){
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `structure` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.13);
		}
		if ($current_version<0.14){
			spip_query("ALTER TABLE spip_reponses ADD `id_article_export` BIGINT( 21 ) NOT NULL AFTER `id_auteur` ");
			ecrire_meta('forms_base_version',$current_version=0.14);
		}
		if ($current_version<0.15){
			spip_query("ALTER TABLE spip_reponses ADD `url` VARCHAR(255) NOT NULL AFTER `id_article_export` ");
			ecrire_meta('forms_base_version',$current_version=0.15);
		}
		if ($current_version<0.17){
			// virer les tables temporaires crees manuellement sur les serveurs ou ca foirait
			spip_query("DROP TABLE spip_forms_champs");
			spip_query("DROP TABLE spip_forms_champs_choix");
			// passer les tables temporaires en permanentes
			include_spip('base/forms');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			Forms_allstructure2table();

			spip_query("ALTER TABLE spip_forms CHANGE sondage type_form VARCHAR(255) NOT NULL");
			spip_query("UPDATE spip_forms SET type_form='sondage-public' WHERE type_form='public'");
			spip_query("UPDATE spip_forms SET type_form='sondage-prot' WHERE type_form='prot'");
			spip_query("UPDATE spip_forms SET type_form='' WHERE type_form='non'");
			spip_query("ALTER TABLE spip_forms ADD moderation VARCHAR(10) DEFAULT 'posteriori' NOT NULL AFTER texte");
			spip_query("ALTER TABLE spip_reponses RENAME spip_forms_donnees");
			spip_query("ALTER TABLE spip_forms_donnees CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL AUTO_INCREMENT");
			spip_query("ALTER TABLE spip_reponses_champs RENAME spip_forms_donnees_champs");
			spip_query("ALTER TABLE spip_reponses_champs CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL");
			spip_query("ALTER TABLE spip_reponses_champs DROP INDEX id_reponse ,ADD INDEX id_donnee (id_donnee) ");

			spip_query("ALTER TABLE spip_forms_champs ADD specifiant ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER extra_info");
			spip_query("ALTER TABLE spip_forms_champs ADD public ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER specifiant");
			spip_query("ALTER TABLE spip_forms_champs ADD aide text AFTER public");
			spip_query("ALTER TABLE spip_forms_champs ADD html_wrap text AFTER aide");
			spip_query("UPDATE spip_forms_champs SET specifiant='non', public='non'"); // par securite

			//ecrire_meta('forms_base_version',$current_version=0.17);
		}
		ecrire_metas();
	}
	
?>