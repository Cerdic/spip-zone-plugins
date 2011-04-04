<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) et b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
**/
include_spip('inc/meta');
include_spip('base/create');

function notation_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/notation');
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
		if (version_compare($current_version,"0.5.0","<")){
			//modifications de la table notations_articles en notations_objets
			sql_alter("TABLE spip_notations_articles RENAME TO spip_notations_objets");
			sql_alter("TABLE spip_notations_objets CHANGE nb nombre_votes BIGINT(21) NOT NULL");
			sql_alter("TABLE spip_notations_objets MODIFY id_article BIGINT(21) NOT NULL DEFAULT '0'");
			sql_alter("TABLE spip_notations_objets ADD COLUMN id_forum BIGINT(21) NOT NULL DEFAULT '0' AFTER id_article");
			sql_alter("TABLE spip_notations_objets DROP PRIMARY KEY");
			sql_alter("TABLE spip_notations_objets ADD COLUMN objet varchar(21) DEFAULT '' NOT NULL FIRST");
			sql_alter("TABLE spip_notations_objets ADD INDEX (objet)");
			sql_alter("TABLE spip_notations_objets ADD INDEX (id_article)");
			sql_alter("TABLE spip_notations_objets ADD INDEX (id_forum)");
			sql_alter("TABLE spip_notations_objets ADD INDEX (nombre_votes)");
			//modifications de la table notations
			sql_alter("TABLE spip_notations ADD COLUMN id_forum BIGINT(21) NOT NULL DEFAULT '0' AFTER id_article");
			sql_alter("TABLE spip_notations ADD COLUMN objet varchar(21) DEFAULT '' NOT NULL AFTER id_notation");
			sql_alter("TABLE spip_notations ADD INDEX (id_forum)");
			sql_alter("TABLE spip_notations ADD INDEX (objet)");
			// insertion de "articles" dans les champs "objet" des deux tables 
			// (les donnees presentes avant la maj ne concernent que des articles)
			// change ensuite (0.6) en 'article' (comme le core - cf spip_documents_liens)
			sql_updateq("spip_notations", array("objet" => "articles"));
			sql_updateq("spip_notations_objets", array("objet" => "articles"));

			//on vire les metas dans la verison precedente (maintenant on se sert de CFG)
			sql_delete("spip_meta","nom =" .sql_quote("notation_acces"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_ip"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_nb"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_ponderation"));		
			
			ecrire_meta($nom_meta_base_version,$current_version="0.5.0");
		}
		
		// mise a jour pour transformer les id_article et id_forum en id_objet
		if (version_compare($current_version,"0.6.1","<")){
			// ajout des champ id_objet
			sql_alter("TABLE spip_notations ADD COLUMN id_objet BIGINT(21) NOT NULL DEFAULT '0' AFTER objet");
			sql_alter("TABLE spip_notations_objets ADD COLUMN id_objet BIGINT(21) NOT NULL DEFAULT '0' AFTER objet");
			// remplissage des valeurs deja existantes
			sql_update("spip_notations", array("id_objet" => "id_article", "objet" => sql_quote("article")), "id_article>".sql_quote(0));
			sql_update("spip_notations", array("id_objet" => "id_forum", "objet" => sql_quote("forum")), "id_forum>".sql_quote(0));
			sql_update("spip_notations_objets", array("id_objet" => "id_article", "objet" => sql_quote("article")), "id_article>".sql_quote(0));
			sql_update("spip_notations_objets", array("id_objet" => "id_forum", "objet" => sql_quote("forum")), "id_forum>".sql_quote(0));
			// suppression des index
			sql_alter("TABLE spip_notations DROP INDEX id_article");
			sql_alter("TABLE spip_notations DROP INDEX id_forum");
			sql_alter("TABLE spip_notations_objets DROP INDEX id_article");
			sql_alter("TABLE spip_notations_objets DROP INDEX id_forum");
			// suppression des vieux champs id_article et id_forum
			sql_alter("TABLE spip_notations DROP COLUMN id_article");
			sql_alter("TABLE spip_notations DROP COLUMN id_forum");
			sql_alter("TABLE spip_notations_objets DROP COLUMN id_article");
			sql_alter("TABLE spip_notations_objets DROP COLUMN id_forum");
			// recreation d'index sur id_objet
			sql_alter("TABLE spip_notations ADD INDEX (id_objet)");
			// creation d'une cle primaire multiple sur la table notations_objets
			sql_alter("TABLE spip_notations_objets DROP INDEX objet");
			sql_alter("TABLE spip_notations_objets ADD PRIMARY KEY (objet, id_objet)");
			// corriger le 'articles' en 'article' ocazou il en resterait 
			sql_updateq("spip_notations", array("objet" => "article"), "objet=".sql_quote($a="articles"));
			sql_updateq("spip_notations_objets", array("objet" => "article"), "objet=".sql_quote($a="articles"));
		
			ecrire_meta($nom_meta_base_version,$current_version="0.6.1");
		}
		if (version_compare($current_version,"0.6.2","<")){
			maj_tables(array('spip_articles'));
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version="0.6.2");
		}
	}
}

function notation_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_notations");
	sql_drop_table("spip_notations_objets");
	effacer_meta($nom_meta_base_version);
}

?>
