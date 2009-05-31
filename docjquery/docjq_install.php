<?php

//$GLOBALS['tables_principales']['spip_docjq'] =
//	array('field' => &$docjq_field, 'key' => &$docjq_key);
//$GLOBALS['table_des_tables']['docjq'] = 'docjq';

function docjq_install($action) {
	if($action=='test') {
		include_spip('base/abstract_sql');
		$desc = spip_abstract_showtable("docjq", '', true);
		return isset($desc['field']['reference']);
	} elseif($action=='install') {
		docjq_upgrade();
	} elseif($action=='uninstall') {
		docjq_vider_tables();
	}
}

function docjq_upgrade() {
	$docjq_field = array(
		"id" => "int(11) NOT NULL",
		"reference" => "int(11) NOT NULL default '0'",
		"nom" => "varchar(100) default NULL",
		"params" => "varchar(255) default NULL",
		"nbparams" => "int(11) default NULL",
		"lang" => "varchar(10) default NULL",
		"etat" => "varchar(3) default NULL",
		"modif" => "datetime default NULL",
		"xml" => "text"
	);
	$docjq_key = array(
		"PRIMARY KEY"	=> "id",
		"KEY" =>"reference"
	);

	return spip_mysql_create("docjq", $docjq_field, $docjq_key, true);
}

function docjq_vider_tables() {
	include_spip('base/abstract_sql');
	spip_query("drop table docjq");
}

?>
