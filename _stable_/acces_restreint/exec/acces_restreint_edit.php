<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint');
include_spip('inc/acces_restreint_gestion');

function exec_acces_restreint_edit(){
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
	$publique = $row['publique'];
	$privee = $row['privee'];

	$retour = '';
	if (isset($_GET['retour']))
		$retour = $_GET['retour'];

	debut_cadre_relief();
	echo generer_url_post_ecrire('acces_restreint_edit',"id_zone=$id_zone".($retour?"&retour=".urlencode($retour):""));
	AccesRestreint_formulaire_zone($id_zone, $titre, $descriptif, $publique, $privee);

	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	// on fait un double appel à la liste des rubriques, une fois pour le privé, une fois pour le public
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:rubriques_zones_acces');
	echo " - ";
	echo _T('accesrestreint:restreindre_publique');
	echo "</div>";
	echo "<div>\n";
	echo AccesRestreint_selecteur_rubrique_html($id_zone, $publique=TRUE);
	echo "</div>\n";
	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:rubriques_zones_acces');
	echo " - ";
	echo _T('accesrestreint:restreindre_espace_prive');
	echo "</div>";
	echo "<div>\n";
	echo AccesRestreint_selecteur_rubrique_html($id_zone, $publique=FALSE);
	echo "</div>\n";
	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	
	echo "</form>\n";

	fin_cadre_relief();

	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";

	if (!$retour)
		$retour = generer_url_ecrire("acces_restreint");

	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", "rien.gif");
	echo "</div>\n";
	
	fin_page();
}

?>