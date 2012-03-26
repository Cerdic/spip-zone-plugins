<?php

function zippeur_declarer_tables_principales($table){
	$table['spip_zippeur'] = array(
		'field'=>array(
			'id_zip'		=> "INT",
			'nom'			=> "text",
			'date_modif'	=> "datetime",
			'fichiers'=>"INT"
			),
			
		'key'=> array('PRIMARY KEY'=>'id_zip')
		
		);	
	return $table;
}

?>