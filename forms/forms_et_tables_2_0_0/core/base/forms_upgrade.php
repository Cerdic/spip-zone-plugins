<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

/**
 * Transformer la structure serializee en table sql
 *
 * @param array $row
 * @param bool $clean
 */
function forms_structure2table($row,$clean=false){
	$id_form=$row[id_form];
	// netoyer la structure precedente en table
	if ($clean){
		sql_delete("spip_forms_champs","id_form=".intval($id_form));
		sql_delete("spip_forms_champs_choix","id_form=".intval($id_form));
	}

	$structure = unserialize($row['structure']);
	if ($structure) { //  precaution pour cas tordus
		$rang = 1;
		$ins_champs = array();
		$ins_choix = array();
		foreach($structure as $cle=>$val){
			$type_ext = $val['type_ext'];
			$extra_info = isset($type_ext['id_groupe']) ? $type_ext['id_groupe']:'';
			$extra_info = isset($type_ext['taille']) ? $type_ext['taille']:$extra_info;
			$ins_champs[] = array(
				"id_form"=>$id_form,
				"rang"=>$rang++,
				"champ"=>$champ=$val['code'],
				"titre"=>$val['nom'],
				"type"=>$val['type'],
				"obligatoire"=>$val['obligatoire'],
				"extra_info"=>$extra_info,
			);
			if ($type=='select' OR $type=='multiple'){
				$rangchoix = 1;
				foreach($type_ext as $choix=>$titre){
					$ins_choix[] = array(
						"id_form"=>$id_form,
						"champ"=>$champ,
						"choix"=>$choix,
						"titre"=>$titre,
						"rang"=>$rangchoix++,
					);
				}
			}
		}
		if (count($ins_champs)){
			sql_insertq_multi("spip_forms_champs",$ins_champs);
			if (count($ins_choix))
				sql_insertq_multi("spip_forms_champs_choix",$ins_choix);
		}
	}
}

/**
 * Transformer la structure serializee en table pour tous les forms
 *
 * @param unknown_type $clean
 */
function forms_allstructure2table($clean=false){
	$tows = sql_allfetsel("*","spip_forms");
	foreach($rows as $row)
		forms_structure2table($row,$clean);
}

/**
 * Mise a jour de la base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function forms_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		// bug sur l'ecriture numerique anterieure des vieilles versions
		if ($current_version == 0.2)
			$current_version = '0.20';
		if ($current_version == 0.3)
			$current_version = '0.30';
		if ($current_version == 0.4)
			$current_version = '0.40';
		
		include_spip('base/serial');
		include_spip('base/aux');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// attention on vient peut etre d'une table spip-forms 1.8
			if ($desc = sql_showtable("spip_forms"))
				$current_version='0.10';
			else {
				creer_base();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
				ecrire_meta('forms_et_tables',serialize(array('associer_donnees_articles'=>0,'associer_donnees_rubriques'=>0,'associer_donnees_auteurs'=>0)));
			}
		}
		if (version_compare($current_version,'0.11','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			sql_alter("spip_forms CHANGE email email TEXT NOT NULL ");
			$rows = sql_allfetsel("*","spip_forms");
			foreach($rows as $row){
				$email = $row['email'];
				$id_form = $row['id_form'];
				if (unserialize($email)==FALSE){
					$email=serialize(array('defaut'=>$email));
					sql_update("spip_forms",array('email'=>"'".addslashes($email)."'"),"id_form=".intval($id_form));
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.11');
		}
		if (version_compare($current_version,'0.12','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			sql_alter("TABLE spip_forms CHANGE descriptif descriptif TEXT");
			sql_alter("TABLE spip_forms CHANGE schema schema TEXT");
			sql_alter("TABLE spip_forms CHANGE email email TEXT");
			sql_alter("TABLE spip_forms CHANGE texte texte TEXT");
			ecrire_meta($nom_meta_base_version,$current_version='0.12');
		}
		if (version_compare($current_version,'0.13','<')){
			sql_alter("TABLE spip_forms CHANGE schema structure TEXT");
			ecrire_meta($nom_meta_base_version,$current_version='0.13');
		}
		if (version_compare($current_version,'0.14','<')){
			sql_alter("TABLE spip_reponses ADD id_article_export BIGINT( 21 ) NOT NULL AFTER id_auteur ");
			ecrire_meta($nom_meta_base_version,$current_version='0.14');
		}
		if (version_compare($current_version,'0.15','<')){
			sql_alter("TABLE spip_reponses ADD url VARCHAR(255) NOT NULL AFTER id_article_export ");
			ecrire_meta($nom_meta_base_version,$current_version='0.15');
		}
		// maj en version 0.16 annulee et remplacee par 0.17
		if (version_compare($current_version,'0.17','<')){
			if (sql_count(sql_select("structure","spip_forms"))){
					// virer les tables temporaires crees manuellement sur les serveurs ou ca foirait
					sql_drop_table("spip_forms_champs");
					sql_drop_table("spip_forms_champs_choix");
	
				$trouver_table = charger_fonction("trouver_table","base");
				$trouver_table(""); // vider le cache
				// virer les tables vides crees lors dun creer base precedent avec spip_forms_donnees dans la definition
				if ($trouver_table("spip_reponses") AND !sql_countsel("spip_forms_donnees")){
					sql_drop_table("spip_forms_donnees");
					sql_drop_table("spip_forms_donnees_champs");
					// renommer les tables qui changent de nom, pour recuperer les donees
					sql_alter("TABLE spip_reponses RENAME spip_forms_donnees");
					sql_alter("TABLE spip_reponses_champs RENAME spip_forms_donnees_champs");
				}
				// creer toutes les nouvelles tables
				include_spip('base/forms');
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				forms_allstructure2table();
	
	  		sql_alter("TABLE spip_forms DROP structure");
	  		sql_alter("TABLE spip_forms CHANGE sondage type_form VARCHAR(255) NOT NULL");
				sql_alter("TABLE spip_forms ADD moderation VARCHAR(10) DEFAULT 'posteriori' NOT NULL AFTER texte");
				sql_alter("TABLE spip_forms ADD public ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER moderation");
				if (sql_countsel("spip_forms",sql_in("type_form","'public','prot','non'"))){
					sql_update("spip_forms",array("public"=>"'non'")); // par securite
					sql_update("spip_forms",array("type_form"=>"'sondage'", "public"=>"'oui'"),"type_form='public'");
					sql_update("spip_forms",array("type_form"=>"'sondage'", "public"=>"'non'"),"type_form='prot'");
					sql_update("spip_forms",array("type_form"=>"''", "public"=>"'non'"),"type_form='non'");
				}
	
				sql_alter("TABLE spip_forms_donnees CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL AUTO_INCREMENT");
				sql_alter("TABLE spip_forms_donnees_champs CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL");
				sql_alter("TABLE spip_forms_donnees_champs DROP INDEX id_reponse ,ADD INDEX id_donnee (id_donnee) ");
	
				sql_alter("TABLE spip_forms_champs ADD specifiant ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER extra_info");
				sql_alter("TABLE spip_forms_champs ADD public ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER specifiant");
				sql_alter("TABLE spip_forms_champs ADD aide text AFTER public");
				sql_alter("TABLE spip_forms_champs ADD html_wrap text AFTER aide");
				sql_update("spip_forms_champs",array("specifiant"=>"'non'", "public"=>"'non'")); // par securite
	
				sql_alter("TABLE spip_forms_donnees CHANGE statut confirmation VARCHAR(10) NOT NULL");
				sql_alter("TABLE spip_forms_donnees ADD statut VARCHAR(10) NOT NULL AFTER confirmation");
				sql_update("spip_forms_donnees",array("statut"=>"'publie'")); // par securite
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.17');
		}
		if (version_compare($current_version,'0.18','<')){
			sql_alter("TABLE spip_forms ADD linkable ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER public");

			// init la valeur par defaut de extra_info sur les champs select (aurait du etre fait en 0.17
			$rows = sql_allfetsel("*","spip_forms_champs","type='select'");
			foreach($rows as $row){
				if (!in_array($row['extra_info'],array('liste','radio'))){
					$extra_info = 'liste';
					if ($n = sql_getfetsel("COUNT(choix) as n","spip_forms_champs_choix","id_form=".intval($row['id_form'])." AND champ=".sql_quote($row['champ']))
					  AND $n<6) 
						$extra_info='radio';
					sql_update("spip_forms_champs",array("extra_info"=>"'".addslashes($extra_info)."'"),"id_form=".intval($row['id_form'])." AND champ=".sql_quote($row['champ']));
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.18');
		}
		if (version_compare($current_version,'0.19','<')){
			sql_alter("TABLE spip_forms ADD html_wrap text AFTER linkable");
			ecrire_meta($nom_meta_base_version,$current_version='0.19');
		}
		if (version_compare($current_version,'0.20','<')){
			sql_alter("TABLE spip_forms_champs CHANGE champ champ varchar(100) NOT NULL");
			sql_alter("TABLE spip_forms_champs_choix CHANGE champ champ varchar(100) NOT NULL");
			// on rappelle creer base car la creation de forms_champs et forms_champs_choix a pu echouer sur mysql 3.23
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version='0.20');
		}
		if (version_compare($current_version,'0.21','<')){
			sql_alter("TABLE spip_forms ADD forms_obligatoires VARCHAR(255) DEFAULT '' AFTER type_form");
			sql_alter("TABLE spip_forms ADD modifiable ENUM('non', 'oui') DEFAULT 'non' AFTER type_form");
			sql_alter("TABLE spip_forms ADD multiple ENUM('non', 'oui') DEFAULT 'non' AFTER type_form");
			ecrire_meta($nom_meta_base_version,$current_version='0.21');
		}
		if (version_compare($current_version,'0.22','<')){
			// creer toutes la nouvelle table spip_documents_donnees
			include_spip('base/forms');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('documents_donnee','oui');
			ecrire_meta($nom_meta_base_version,$current_version='0.22');
		}
		if (version_compare($current_version,'0.23','<')){
			sql_alter("TABLE spip_forms ADD documents ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER linkable");
			ecrire_meta($nom_meta_base_version,$current_version='0.23');
		}
		if (version_compare($current_version,'0.24','<')){
			if (!sql_select("rang","spip_forms_donnees")){
				sql_alter("TABLE spip_forms_donnees ADD rang bigint(21) NOT NULL AFTER cookie");
				$rows = sql_allfetsel("id_form","spip_forms");
				foreach($rows as $row){
					$rows2 = sql_allfetsel("id_donnee","spip_forms_donnees","id_form=".intval($row['id_form']),"","id_donnee");
					$rang=1;
					foreach($rows2 as $row2)
						sql_update("spip_forms_donnees",array("rang"=>$rang++),"id_donnee=".intval($row2['id_donnee']));
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.24','non');
		}
		if (version_compare($current_version,'0.25','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version='0.25','non');
		}
		if (version_compare($current_version,'0.26','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "forms update @ 0.26<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.26','non');
		}
		if (version_compare($current_version,'0.27','<')){
			sql_alter("TABLE spip_forms_donnees_articles ADD article_ref ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER id_article");
			sql_alter("TABLE spip_forms_donnees_donnees ADD donnee_ref ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER id_donnee");
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "forms update @ 0.27<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.27','non');
		}
		if (version_compare($current_version,'0.29','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			sql_alter("TABLE spip_forms_donnees ADD bgch bigint(21) NOT NULL AFTER rang");
			sql_alter("TABLE spip_forms_donnees ADD bdte bigint(21) NOT NULL AFTER bgch");
			sql_alter("TABLE spip_forms_donnees ADD niveau bigint(21) DEFAULT '0' NOT NULL AFTER bdte");
			echo "forms update @ 0.29<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.29','non');
		}
		if (version_compare($current_version,'0.31','<')){
			sql_alter("TABLE spip_forms_champs ADD listable ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER specifiant");
			echo "forms update @ 0.31<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.31','non');
		}
		if (version_compare($current_version,'0.32','<')){
			sql_alter("TABLE spip_forms_champs CHANGE listable listable_admin ENUM('non', 'oui') DEFAULT 'oui' NOT NULL");
			if (!sql_getfetsel("listable","spip_forms_champs","","","","0,1")){
				sql_alter("TABLE spip_forms_champs ADD listable ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER listable_admin");
				sql_update("spip_forms_champs",array("listable"=>"'specifiant'")); // valeur par defaut pour iso fonctionnalite cote public
			}
			echo "forms update @ 0.32<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.32','non');
		}
		if (version_compare($current_version,'0.33','<')){
			sql_alter("TABLE spip_forms_donnees_champs CHANGE valeur valeur TEXT NOT NULL");
			echo "forms update @ 0.33<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.33','non');
		}
		if (version_compare($current_version,'0.34','<')){
			sql_alter("TABLE spip_forms_donnees_champs DROP INDEX champ , ADD UNIQUE champ ( champ ( 128 ) , id_donnee , valeur ( 128 ) )");
			echo "forms update @ 0.34<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.34','non');
		}
		if (version_compare($current_version,'0.35','<')){
			sql_alter("TABLE spip_forms ADD arborescent ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER documents");
			echo "forms update @ 0.35<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.35','non');
		}
		if (version_compare($current_version,'0.36','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "forms update @ 0.36<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.36','non');
		}
		if (version_compare($current_version,'0.37','<')){
			sql_alter("TABLE spip_forms_champs ADD taille bigint(21) NOT NULL NULL AFTER type");
			echo "forms update @ 0.37<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.37','non');
		}
		if (version_compare($current_version,'0.38','<')){
			ecrire_meta('forms_et_tables',serialize(array('associer_donnees_articles'=>1,'associer_donnees_rubriques'=>0,'associer_donnees_auteurs'=>0)));
			echo "forms update @ 0.38<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.38','non');
		}
		if (version_compare($current_version,'0.39','<')){
			sql_alter("TABLE spip_forms_articles DROP INDEX id_form");
			sql_alter("TABLE spip_forms_articles ADD PRIMARY KEY ( id_form , id_article )");
			sql_alter("TABLE forms_donnees_articles DROP INDEX id_donnee");
			sql_alter("TABLE forms_donnees_articles ADD PRIMARY KEY ( id_donnee , id_article )");
			sql_alter("TABLE spip_forms_rubriques DROP INDEX id_donnee");
			sql_alter("TABLE spip_forms_rubriques ADD PRIMARY KEY ( id_donnee , id_rubrique )");
			sql_alter("TABLE forms_donnees_donnees DROP INDEX id_donnee");
			sql_alter("TABLE forms_donnees_donnees ADD PRIMARY KEY ( id_donnee , id_donnee_liee )");
			sql_alter("TABLE forms_donnees_auteurs DROP INDEX id_donnee");
			sql_alter("TABLE forms_donnees_auteurs ADD PRIMARY KEY ( id_donnee , id_auteur )");
			echo "forms update @ 0.39<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.39','non');
		}
		if (version_compare($current_version,'0.40','<')){
			sql_alter("TABLE spip_forms_champs ADD saisie ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER public");
			echo "forms update @ 0.40<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.40','non');
		}
		if (version_compare($current_version,'0.41','<')){
			sql_alter("TABLE spip_forms ADD documents_mail ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER documents");
			echo "forms update @ 0.41<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.41','non');
		}
		
		$trouver_table = charger_fonction("trouver_table","base");
		$trouver_table(""); // raz du cache des descriptions de table
	}
}


/**
 * Suppression des tables a la desinstallation
 *
 * @param string $nom_meta_base_version
 */
function forms_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_forms");
	sql_drop_table("spip_forms_champs");
	sql_drop_table("spip_forms_champs_choix");
	sql_drop_table("spip_forms_donnees");
	sql_drop_table("spip_forms_donnees_champs");
	sql_drop_table("spip_forms_donnees_donnees");
	sql_drop_table("spip_forms_articles");
	sql_drop_table("spip_forms_donnees_articles");
	sql_drop_table("spip_forms_documents_donnees");
	effacer_meta($nom_meta_base_version);
}

?>