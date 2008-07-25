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
include_spip('inc/vieilles_defs');

function notation_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/notation');
			creer_base();
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
			// insertion de "articles" dans les champs "objet" des deux tables (les données présentes avant la maj ne concernent que des articles)
			$objet = "articles";
			$res = sql_select("spip_notations_objets.id_article","spip_notations_objets");
			if ($n = sql_count($res)){
				while ($t = sql_fetch($res)) {
					sql_updateq("spip_notations_objets", array("objet" => $objet), "id_article=" . sql_quote($t["id_article"]));
				}
			}
			$res = sql_select("spip_notations.id_article","spip_notations");
			if ($n = sql_count($res)){
				while ($t = sql_fetch($res)) {
					sql_updateq("spip_notations", array("objet" => $objet), "id_article=" . sql_quote($t["id_article"]));
				}
			}
			//on vire les metas dans la verison précédente (maintenant on se sert de CFG)
			sql_delete("spip_meta","nom =" .sql_quote("notation_acces"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_ip"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_nb"));
			sql_delete("spip_meta","nom =" .sql_quote("notation_ponderation"));		
			
			ecrire_meta($nom_meta_base_version,$current_version="0.5.0");
		}
		ecrire_metas();
	}
}

function notation_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_notations");
	sql_drop_table("spip_notations_objets");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>