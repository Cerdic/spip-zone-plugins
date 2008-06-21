<?php 
	// inc/spiplistes_agenda.php
	// CP-20080621
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

define("_SPIPLISTES_AGENDA_PERIODE_HEBDO", "hebdo");
define("_SPIPLISTES_AGENDA_PERIODE_MOIS", "mois");
define("_SPIPLISTES_AGENDA_PERIODE_DEFAUT", _SPIPLISTES_AGENDA_PERIODE_MOIS);

function spiplistes_boite_agenda () {
	$result = ""
		. "<!-- boite agenda spiplistes -->\n"
		. debut_cadre_relief("statistiques-24.gif", true)
		. "<span class='verdana2 titre-petite-boite'>"
		. _T('spiplistes:boite_agenda_titre_').":"
		. "</span><br />"
		. "<div id='spiplistes-boite-agenda'>\n"
		. spiplistes_boite_agenda_contenu(time(), _SPIPLISTES_AGENDA_PERIODE_DEFAUT)
		. "</div>\n"
		. "En d&eacute;veloppement. Bient&oacute;t disponible."
		. fin_cadre_relief(true)
		. "<!-- fin boite agenda spiplistes -->\n"
		;
	return($result);
}

function spiplistes_boite_agenda_contenu ($time_debut, $periode) {
	$result = "";
	return($result);
}

//
?>