<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) et b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');

function notation_upgrade($nom_meta_base_version,$version_cible){

	// mettre les metas par defaut
	$config = charger_fonction('config','inc');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_notations','spip_notations_objets','spip_articles')),
		array($config),
	);

	$maj['0.5.0'] = array(
		array('sql_alter',"TABLE spip_notations_articles RENAME TO spip_notations_objets"),
		array('sql_alter',"TABLE spip_notations_objets CHANGE nb nombre_votes BIGINT(21) NOT NULL"),
		array('sql_alter',"TABLE spip_notations_objets MODIFY id_article BIGINT(21) NOT NULL DEFAULT '0'"),
		array('sql_alter',"TABLE spip_notations_objets ADD COLUMN id_forum BIGINT(21) NOT NULL DEFAULT '0' AFTER id_article"),
		array('sql_alter',"TABLE spip_notations_objets DROP PRIMARY KEY"),
		array('sql_alter',"TABLE spip_notations_objets ADD COLUMN objet varchar(21) DEFAULT '' NOT NULL FIRST"),
		array('sql_alter',"TABLE spip_notations_objets ADD INDEX (objet)"),
		array('sql_alter',"TABLE spip_notations_objets ADD INDEX (id_article)"),
		array('sql_alter',"TABLE spip_notations_objets ADD INDEX (id_forum)"),
		array('sql_alter',"TABLE spip_notations_objets ADD INDEX (nombre_votes)"),
		//modifications de la table notations
		array('sql_alter',"TABLE spip_notations ADD COLUMN id_forum BIGINT(21) NOT NULL DEFAULT '0' AFTER id_article"),
		array('sql_alter',"TABLE spip_notations ADD COLUMN objet varchar(21) DEFAULT '' NOT NULL AFTER id_notation"),
		array('sql_alter',"TABLE spip_notations ADD INDEX (id_forum)"),
		array('sql_alter',"TABLE spip_notations ADD INDEX (objet)"),
		// insertion de "articles" dans les champs "objet" des deux tables
		// (les donnees presentes avant la maj ne concernent que des articles)
		// change ensuite (0.6) en 'article' (comme le core - cf spip_documents_liens)
		array('sql_updateq',"spip_notations", array("objet" => "articles")),
		array('sql_updateq',"spip_notations_objets", array("objet" => "articles")),

		//on vire les metas dans la verison precedente (maintenant on se sert de CFG)
		array('sql_delete',"spip_meta","nom =" .sql_quote("notation_acces")),
		array('sql_delete',"spip_meta","nom =" .sql_quote("notation_ip")),
		array('sql_delete',"spip_meta","nom =" .sql_quote("notation_nb")),
		array('sql_delete',"spip_meta","nom =" .sql_quote("notation_ponderation")),
	);

	$maj['0.6.1'] = array(
		// ajout des champ id_objet
		array('sql_alter',"TABLE spip_notations ADD COLUMN id_objet BIGINT(21) NOT NULL DEFAULT '0' AFTER objet"),
		array('sql_alter',"TABLE spip_notations_objets ADD COLUMN id_objet BIGINT(21) NOT NULL DEFAULT '0' AFTER objet"),
		// remplissage des valeurs deja existantes
		array('sql_updateq',"spip_notations", array("id_objet" => "id_article", "objet" => sql_quote("article")), "id_article>".sql_quote(0)),
		array('sql_updateq',"spip_notations", array("id_objet" => "id_forum", "objet" => sql_quote("forum")), "id_forum>".sql_quote(0)),
		array('sql_updateq',"spip_notations_objets", array("id_objet" => "id_article", "objet" => sql_quote("article")), "id_article>".sql_quote(0)),
		array('sql_updateq',"spip_notations_objets", array("id_objet" => "id_forum", "objet" => sql_quote("forum")), "id_forum>".sql_quote(0)),
		// suppression des index
		array('sql_alter',"TABLE spip_notations DROP INDEX id_article"),
		array('sql_alter',"TABLE spip_notations DROP INDEX id_forum"),
		array('sql_alter',"TABLE spip_notations_objets DROP INDEX id_article"),
		array('sql_alter',"TABLE spip_notations_objets DROP INDEX id_forum"),
		// suppression des vieux champs id_article et id_forum
		array('sql_alter',"TABLE spip_notations DROP COLUMN id_article"),
		array('sql_alter',"TABLE spip_notations DROP COLUMN id_forum"),
		array('sql_alter',"TABLE spip_notations_objets DROP COLUMN id_article"),
		array('sql_alter',"TABLE spip_notations_objets DROP COLUMN id_forum"),
		// recreation d'index sur id_objet
		array('sql_alter',"TABLE spip_notations ADD INDEX (id_objet)"),
		// creation d'une cle primaire multiple sur la table notations_objets
		array('sql_alter',"TABLE spip_notations_objets DROP INDEX objet"),
		array('sql_alter',"TABLE spip_notations_objets ADD PRIMARY KEY (objet, id_objet)"),
		// corriger le 'articles' en 'article' ocazou il en resterait
		array('sql_updateq',"spip_notations", array("objet" => "article"), "objet=".sql_quote("articles")),
		array('sql_updateq',"spip_notations_objets", array("objet" => "article"), "objet=".sql_quote("articles")),
	);

	$maj['0.6.2'] = array(
		array('maj_tables',array('spip_articles')),
		array($config),
	);

	$maj['0.6.3'] = array(
		// Pour ceux qui ont installe une 0.6.2 directement avant la correction creant 'accepter_note'
		array('maj_tables',array('spip_articles')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

function notation_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_notations");
	sql_drop_table("spip_notations_objets");
	sql_alter("TABLE spip_articles DROP COLUMN accepter_note");
	effacer_meta($nom_meta_base_version);
}

?>
