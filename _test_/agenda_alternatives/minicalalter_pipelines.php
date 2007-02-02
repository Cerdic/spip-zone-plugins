<?php
/*
	Mini Calendrier pour Alternatives
 	Patrice VANNEUFVILLE - patrice.vanneufville<@>laposte.net
	(c) 2007 - Distribue sous licence GPL
	Plugin pour spip 1.9
	Licence GNU/GPL
*/

function CalAlter_header_prive($flux){
	// s'assurer en prive que les tables sont crees
	include_spip('inc/agenda_gestion'); // spip 1.9
	if (!function_exists('Agenda_install')) include_spip('base/agenda_upgrade'); // spip 1.9.2
	if (function_exists('Agenda_install')) Agenda_install();
	return $flux;
}

function CalAlter_insert_head($flux){
	include_spip('inc/agenda_gestion'); // spip 1.9
	if (!function_exists('Agenda_install')) include_spip('base/agenda_upgrade'); // spip 1.9.2
	if (!function_exists('Agenda_install')) {
		include_spip('inc/minipres');
		minipres(_T('minicalalter_activer_agenda'));
	}
	$flux .= "
	<style type=\"text/css\">
		.agenda {
			line-height:1.4em;
		}
		td.agendaBold {
			font-weight: bold;
			font-size:108%;
		}
		td.agendaThisDay { 
			background-color: #FFFFDD;
			border:1px solid #000000;
		} 
		td.agendaThisDayNotThisMonth {
			background-color:#BBC9E3;
			border:1px solid #000000;
		}
	</style>";
	return $flux;
}

?>