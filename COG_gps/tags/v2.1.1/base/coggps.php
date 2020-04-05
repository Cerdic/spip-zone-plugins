<?php


/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */

function coggps_declarer_tables_objets_sql($tables){

$table_coggps_field = array(
	"lon"           =>	"DECIMAL(10,6) NULL  COMMENT 'Longitude'",
	"lat"           =>	"DECIMAL(10,6) NULL  COMMENT 'Lattitude'",
	"zoom"           =>	"TINYINT NULL  COMMENT 'Zoom'",
	"elevation"		=>	"INT NULL  COMMENT 'elevation de la commune'",
	"elevation_moyenne"		=>	"INT NULL  COMMENT 'elevation moyenne de la commune'",
	"population"	=>	"BIGINT NULL  COMMENT 'elevation moyenne de la commune'",
	"autre_nom"		=>	"TEXT NULL   COMMENT 'Nom dans d''autre langue'");
	if(isset($tables['spip_cog_communes']['field']))
		$tables['spip_cog_communes']['field'] = array_merge($tables['spip_cog_communes']['field'],$table_coggps_field);

return $tables;

}

?>