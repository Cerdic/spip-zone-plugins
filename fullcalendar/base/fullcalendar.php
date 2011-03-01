<?php

/* * * * * * * * * * * * * * * * * * * *
 * 
 *     - FullCalendar pour SPIP -
 * 
 * Description des tables à créer dans MySQL
 * 
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 16/02/2011
 * 
 */

global $tables_principales;

/*
 * spip_fullcalendar_main
 *
 * +-----------------+------------+------+-----+---------+----------------+
 * | Field           | Type       | Null | Key | Default | Extra          |
 * +-----------------+------------+------+-----+---------+----------------+
 * | id_fullcalendar | int(11)    | NO   | PRI | NULL    | auto_increment |
 * | type            | varchar(7) | NO   |     | NULL    |                |
 * | nom             | text       | NO   |     | NULL    |                |
 * +-----------------+------------+------+-----+---------+----------------+
 */ 

$spip_fullcalendar_main = array(
    "id_fullcalendar" => "BIGINT(21) NOT NULL auto_increment",
    "type" => "VARCHAR(7) NOT NULL",
    "nom" => "TEXT NOT NULL"
);

$spip_fullcalendar_key = array(
	"PRIMARY KEY" => "id_fullcalendar"
);

$tables_principales['spip_fullcalendar_main'] = array(
	'field' => &$spip_fullcalendar_main,
	'key' => &$spip_fullcalendar_key
);

/*
 * spip_fullcalendar_events
 *
 * +-----------------+----------+------+-----+---------+----------------+
 * | Field           | Type     | Null | Key | Default | Extra          |
 * +-----------------+----------+------+-----+---------+----------------+
 * | id_event        | int(11)  | NO   | PRI | NULL    | auto_increment |
 * | id_fullcalendar | int(11)  | NO   |     | NULL    |                |
 * | id_style        | int(11)  | NO   |     | NULL    |                |
 * | titre           | text     | NO   |     | NULL    |                |
 * | lien            | text     | NO   |     | NULL    |                |
 * | start           | datetime | NO   |     | NULL    |                |
 * | end             | datetime | NO   |     | NULL    |                |
 * +-----------------+----------+------+-----+---------+----------------+
 */

$spip_fullcalendar_events = array(
    "id_event" => "BIGINT(21) NOT NULL auto_increment",
    "id_fullcalendar" => "BIGINT(21) NOT NULL",
    "id_style" => "BIGINT(21) NOT NULL",
    "titre" => "TEXT NOT NULL",
    "lien" => "TEXT NOT NULL",
    "start" => "DATETIME NOT NULL",
    "end" => "DATETIME NOT NULL"
);

$spip_fullcalendar_events_key = array(
	"PRIMARY KEY" => "id_event"
);

$tables_principales['spip_fullcalendar_events'] = array(
	'field' => &$spip_fullcalendar_events,
	'key' => &$spip_fullcalendar_events_key
);


/*
 * spip_fullcalendar_styles
 *
 * +-----------------+------------+------+-----+---------+----------------+
 * | Field           | Type       | Null | Key | Default | Extra          |
 * +-----------------+------------+------+-----+---------+----------------+
 * | id_style        | int(11)    | NO   | PRI | NULL    | auto_increment |
 * | titre           | text       | NO   |     | NULL    |                |
 * | bordercolor     | varchar(7) | NO   |     | NULL    |                |
 * | bgcolor         | varchar(7) | NO   |     | NULL    |                |
 * | textcolor       | varchar(7) | NO   |     | NULL    |                |
 * +-----------------+------------+------+-----+---------+----------------+
 */

$spip_fullcalendar_styles = array(
    "id_style" => "BIGINT(21) NOT NULL auto_increment",
    "titre" => "TEXT NOT NULL",
    "bordercolor" => "VARCHAR(7) NOT NULL",
    "bgcolor" => "VARCHAR(7) NOT NULL",
    "textcolor" => "VARCHAR(7) NOT NULL"
);

$spip_fullcalendar_styles_key = array(
	"PRIMARY KEY" => "id_style"
);

$tables_principales['spip_fullcalendar_styles'] = array(
	'field' => &$spip_fullcalendar_styles,
	'key' => &$spip_fullcalendar_styles_key
);

/*
 function boucle_fullcalendar_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_fullcalendar";  

		if (!$GLOBALS['var_preview']) {
			if (!$boucle->statut) {
				$boucle->where[]= array("'IN'", "'$id_table.id_fullcalendar'", "''");
			}
		}
		return calculer_boucle($id_boucle, $boucles); 
}
*/

?>
