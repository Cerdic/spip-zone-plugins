<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function a2a_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();

	$maj['create'] = array(
		array('creer_base'),
		array('a2a_maj_050'),
		array('a2a_maj_070'),
	);

	$maj['0.2.0'] = array(array('maj_tables',array('spip_articles_lies')));
	$maj['0.3.0'] = array(array('sql_alter',array('TABLE spip_articles_lies CHANGE rang rang bigint(21) NOT NULL DEFAULT "0"')));
	$maj['0.4.0'] = array(array('maj_tables',array('spip_articles_lies')));
	$maj['0.5.0'] = array(array('a2a_maj_050'));
	$maj['0.6.0'] = array(array('a2a_maj_060'));
	$maj['0.7.0'] = array(array('a2a_maj_070'));
	$maj['0.9.0'] = array(array('a2a_maj_090'));
	$maj['0.10.0'] = array(array('a2a_maj_0100'));
	$maj['0.11.0'] = array(
		array('sql_updateq',"spip_articles_lies",array('type_liaison'=>''),"type_liaison is null"),
		array('sql_alter','TABLE spip_articles_lies CHANGE type_liaison type_liaison varchar(255) not null default "" ')
	);

	$maj['0.12.0'] = array(array('a2a_maj_0120'));

	$maj['0.13.0'] = array(
		array('sql_updateq',"spip_articles_lies",array('type_liaison'=>''),"type_liaison is null"),
		array('sql_alter','TABLE spip_articles_lies CHANGE type_liaison type_liaison varchar(255) not null default "" ')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function a2a_maj_050(){
	ecrire_config('a2a/types_liaisons',lire_config('a2a/types'));
	effacer_config('a2a/types');

}

function a2a_maj_060(){
	sql_alter("TABLE spip_articles_lies CHANGE type_liaison type_liaison  VARCHAR(25)");
	sql_alter("TABLE spip_articles_lies DROP PRIMARY KEY");
	sql_alter("TABLE spip_articles_lies ADD PRIMARY KEY (id_article,id_article_lie,type_liaison)");
}

function a2a_maj_070(){
	if (!lire_config('a2a/types_liaisons'))
		ecrire_config('a2a/types_liaisons',array());
}

function a2a_maj_090(){
	sql_alter("TABLE spip_articles_lies DROP PRIMARY KEY");
	sql_alter("TABLE spip_articles_lies ADD PRIMARY KEY (id_article,id_article_lie,type_liaison)");
}
function a2a_maj_0100(){
	sql_alter("TABLE spip_articles_lies CHANGE type_liaison type_liaison  VARCHAR(25) DEFAULT ''");
}

/**
 * Si on est en sqlite, supprimer les éventuelles entrées doublons
 *
**/
function a2a_maj_0120(){
	if ($GLOBALS['db_ok']['type'] == 'sqlite3' or $GLOBALS['db_ok']['type'] == 'sqlite2') {
		$res = sql_select('*', 'spip_articles_lies', '', '', 'rang' );
		sql_delete('spip_articles_lies');
		$done = array();
		$rangs = array();
		while ($row = sql_fetch($res)) {
			$id_article = $row['id_article'];
			$copy = $row;
			unset($copy['rang']);
			if (!in_array($copy, $done)) {
				if (!isset($rangs[$id_article])) {
					$rangs[$id_article] = 1;
				} else {
					$rangs[$id_article] = $rangs[$id_article] + 1;
				}
				$row['rang'] = $rangs[$id_article];
				sql_insertq('spip_articles_lies',$row);
				$done[] = $copy;
			}
		}
	}
}
function a2a_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_articles_lies");
	effacer_config('a2a');
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}


