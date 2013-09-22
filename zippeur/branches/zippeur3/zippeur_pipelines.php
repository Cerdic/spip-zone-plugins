<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function zippeur_declarer_tables_principales($table){
	$table['spip_zippeur'] = array(
		'field'=>array(
			'id_zip'		=> "INT",
			'nom'			=> "text",
			'date_modif'	=> "datetime",
			'date_zip'		=> "datetime",
			'delai_suppression'=>"INT",
			'fichiers'=>"INT"
			),
			
		'key'=> array('PRIMARY KEY'=>'id_zip')
		
		);	
	return $table;
}

function zippeur_taches_generales_cron($taches){
	$taches['zippeur_effacer_zip'] = _ZIPPEUR_EFFACER_ZIP;	
	return $taches;
}

?>