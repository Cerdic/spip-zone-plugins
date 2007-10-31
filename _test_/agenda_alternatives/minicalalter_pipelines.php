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
	if ($GLOBALS['spip_version_code']<1.92) {
		include_spip('inc/agenda_gestion'); // spip 1.9
	 	if (function_exists('Agenda_install')) Agenda_install();
	}
	return $flux;
}

function CalAlter_insert_head($flux){
	if ($GLOBALS['spip_version_code']<1.92){
		include_spip('inc/agenda_gestion'); // spip 1.9
		if (!function_exists('Agenda_install')) {
			include_spip('inc/minipres');
			minipres(_T('minicalalter:activer_agenda'));
		}
	}
	$flux .= "
	<style type=\"text/css\">
		table.agenda abbr {
			border-bottom: none;
			cursor: help;
		}
		.agenda {
			line-height:1.4em;
		}
		td.agendaBold {
			font-weight: bold;
			font-size:108%;
		}
		td.agendaThisDay { 
/* couleur jaune. retirer la ligne ci-dessous pour retrouver le bleu d'origine */		
			background-color: #FFFFDD;
/*			border:1px solid #000000;*/
		} 
		td.agendaThisDayNotThisMonth {
			background-color:#BBC9E3;
/*			border:1px solid #000000;*/
		}
/* Espaceur de blocs */
		.nettoie { 
			clear: both; margin: 0; padding: 0; border: none; height: 0; line-height: 1px; font-size: 1px; 
		}
	</style>";
	return $flux;
}

?>