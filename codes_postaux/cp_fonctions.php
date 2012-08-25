<?php
/*
 * Plugin cp
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

include_spip("cp_config");
function balise_CP_TABLE($p)
{
$p->code = "cp_config_tab_table()";
return $p;
}

////////////////////////////////////////
// Pour l'espace privé en version 2.1
///////////////////////////////////////
function filtre_cp_bloc_des_raccourcis($bloc,$titre=""){
global $spip_display;
if($titre=='')
$titre=_T('titre_cadre_raccourcis');
include_spip('inc/presentation');
	return "\n"
	. creer_colonne_droite('',true)
	. debut_cadre_enfonce('',true)
	. (($spip_display != 4)
	     ? ("\n<div style='font-size: x-small' class='verdana1'><b>"
		.$titre
		."</b>")
	       : ( "<h3>".$titre."</h3><ul>"))
	. $bloc
	. (($spip_display != 4) ? "</div>" :  "</ul>")
	. fin_cadre_enfonce(true);
}

function filtre_cp_icone_horizontale($lien, $texte="", $fond = "",  $fonction = "",  $javascript='') {
include_spip('inc/presentation');
return icone_horizontale($texte, $lien, $fond, $fonction, false, $javascript);
}



?>
