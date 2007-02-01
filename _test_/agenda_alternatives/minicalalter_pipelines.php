<?php
/*
	Mini Calendrier pour Alternatives
 	Patrice VANNEUFVILLE - patrice.vanneufville<@>laposte.net
	(c) 2007 - Distribue sous licence GPL
	Plugin pour spip 1.9
	Licence GNU/GPL
*/

function CalAlter_insert_head($flux){
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