<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once('inc_acces_restreint_base.php');
include_once('inc_acces_restreint.php');

function acces_restreint_edit(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
	global $spip_lang_right;
  include_ecrire('inc_presentation');
	
	$id_zone = intval($_GET['id_zone']);

	if (isset($_POST['Enregistrer']))
		AccesRestreint_enregistrer_zone();
	  
	debut_page(_T('accesrestreint:page_zones_acces'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('accesrestreint:titre_zones_acces'));
	debut_gauche();
	
	/*debut_boite_info();
	echo propre("Cette page vous permet de gerer les zones d'acces restreint de votre site");	
	fin_boite_info();*/
	
	debut_droite();
	$requete = "SELECT * FROM spip_zones WHERE id_zone=$id_zone";
	$res = spip_query($requete);
	$row = spip_fetch_array($res);

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques OR !$row) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	$titre = $row['titre'];
	$descriptif = $row['descriptif'];

	$retour = '';
	if (isset($_GET['retour']))
		$retour = $_GET['retour'];

	debut_cadre_relief();
	echo generer_url_post_ecrire("acces_restreint_edit","id_zone=$id_zone".($retour?"&retour=".urlencode($retour):""));
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:titre_zones_acces');
	echo "</div>";
	echo "<p>";
	echo _T('accesrestreint:titre')."<br/>";
	echo "<input type='input' name='titre' value='".entites_html($titre)."' class='formo' />";
	echo "</p>";
	echo "<p>";
	echo _T('accesrestreint:descriptif')."<br/>";
	echo "<textarea name='descriptif' class='formo'>";
	echo entites_html($descriptif);
	echo "</textarea>";
	echo "</p>";
	echo "<input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo' />";
	echo "</div>";

	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:rubriques_zones_acces');
	echo "</div>";
	echo "<div>\n";
	echo AccesRestreint_selecteur_rubrique_html($id_zone);
	echo "</div>\n";
	echo "</form>\n";

	fin_cadre_relief();

	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";

	if (!$retour)
		$retour = generer_url_ecrire("acces_restreint");

	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png", "rien.gif");
	echo "</div>\n";
	
	fin_page();
}

?>