<?php

// compatibilite trans 1.9.1-1.9.2
// Cadre formulaires
// http://doc.spip.org/@debut_cadre_formulaire
function Panoramas_debut_cadre_formulaire($style='', $return=false){
	$x = "\n<div class='cadre-formulaire'" .
	  (!$style ? "" : " style='$style'") .
	   ">";
	if ($return) return  $x; else echo $x;
}

// http://doc.spip.org/@fin_cadre_formulaire
function Panoramas_fin_cadre_formulaire($return=false){
	if ($return) return "</div>\n"; else echo "</div>\n";
}

//
// Edition des visites virtuelles
//
function Panoramas_boite_proprietes($id_visite, $row, $focus, $action_link, $redirect) {
	

	$out = "";
	$out .= "<p>";
	$out .= Panoramas_debut_cadre_formulaire('',true);

	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<div class='verdana2'>";
	$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir'" .
		" style='border: 0px; margin: 0px;'>" .
		form_hidden($action_link_noredir) .
		"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
		"<input type='hidden' name='idtarget' value='proprietes' />" ;

	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	
	$out .= "<strong><label for='titre_form'>"._T("panoramas:titre_visite")."</label></strong> "._T('info_obligatoire_02');
	$out .= "<br />";
	$out .= "<input type='text' name='titre' id='titre_visite' class='formo $focus' ".
		"value=\"".$titre."\" size='40' /><br />\n";

	$out .= "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='descriptif' id='desc_visite' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $descriptif;
	$out .= "</textarea><br />\n";

	
	$out .= "<div style='text-align:right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	

	$out .= Panoramas_fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;


}

?>