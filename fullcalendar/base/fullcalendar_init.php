<?php

/* * * * * * * * * * * * * * * * * * * *
 * 
 *     - FullCalendar pour SPIP -
 * 
 * Création des tables dans la base MySQL
 * 
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 04/04/2011
 * 
 */

	
$GLOBALS['fullcalendar_version'] = 0.1;
	
function fullcalendar_verifier_base(){			
	$version_base = $GLOBALS['fullcalendar_version'];
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['fullcalendar_base_version']) )
	|| (($current_version = $GLOBALS['meta']['fullcalendar_base_version'])!=$version_base)) {
		include_spip('base/fullcalendar');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('fullcalendar_base_version',$current_version=$version_base);
			ecrire_meta('fullcalendar','a:19:{s:11:"defaultView";s:5:"month";s:8:"useTheme";s:4:"true";s:11:"aspectRatio";s:4:"1.35";s:8:"weekends";s:4:"true";s:8:"firstDay";s:1:"1";s:10:"headerLeft";s:5:"today";s:12:"headerCenter";s:5:"title";s:11:"headerRight";s:9:"prev,next";s:17:"month_titleFormat";s:9:"MMMM yyyy";s:18:"month_columnFormat";s:4:"dddd";s:16:"month_timeFormat";s:0:"";s:16:"week_titleFormat";s:34:"d [MMMM] [ yyyy]{  -  d MMMM yyyy}";s:17:"week_columnFormat";s:6:"dddd d";s:21:"week_timeFormat_basic";s:6:"H(:mm)";s:22:"week_timeFormat_agenda";s:6:"H(:mm)";s:15:"day_titleFormat";s:16:"dddd d MMMM yyyy";s:16:"day_columnFormat";s:11:"dddd d MMMM";s:20:"day_timeFormat_basic";s:13:"H:mm{ - H:mm}";s:21:"day_timeFormat_agenda";s:13:"H:mm{ - H:mm}";}');
		}				
		ecrire_metas();
	}
}

function fullcalendar_effacer_tables(){
	$table_prefix = $GLOBALS['table_prefix'] ;
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE ".$table_prefix."_fullcalendar_main");
	spip_query("DROP TABLE ".$table_prefix."_fullcalendar_events");
	spip_query("DROP TABLE ".$table_prefix."_fullcalendar_styles");
	effacer_meta('fullcalendar_base_version');
	effacer_meta('fullcalendar');
	ecrire_metas();
}	

function fullcalendar_install($action){
	$version_base = $GLOBALS['fullcalendar_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['fullcalendar_base_version']) 
			AND ($GLOBALS['meta']['fullcalendar_base_version']>=$version_base));
			break;
		case 'install':
			fullcalendar_verifier_base();
			break;
		case 'uninstall':
			fullcalendar_effacer_tables();
			break;
	}
}	
?>
