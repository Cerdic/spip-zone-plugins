<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once('inc_acces_restreint_base.php');
include_once('inc_acces_restreint.php');

function acces_restreint(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
  include_ecrire('inc_presentation');
	include_ecrire('inc_base');
	creer_base(); // au cas ou
	  
	debut_page(_T('accesrestreint:page_zones_acces'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('accesrestreint:titre_zones_acces'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('accesrestreint:info_page'));	
	fin_boite_info();
	
	debut_droite();
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	if (isset($_POST['creer']))
		AccesRestreint::cree_zone();
	if (isset($_GET['supp_zone']))
		AccesRestreint::supprimer_zone();

	$requete = "SELECT zones.* FROM spip_zones AS zones";

	$table = "";
	$tranches = afficher_tranches_requete($requete, 5);
	if ($tranches) {
	 	$result = spip_query($requete);

		$vals = '';
		$vals[] = _T('accesrestreint:colonne_id');
		$vals[] = _T('accesrestreint:titre');
		$vals[] = _T('accesrestreint:descriptif');
		$vals[] = '';
		$vals[] = '';
		$table[] = $vals;
		
		while ($row = spip_fetch_array($result)){
			$vals = array();
			$id_zone = $row['id_zone'];
			$nb_rub = count(AccesRestreint::liste_contenu_zone_rub($id_zone));
			$nb_aut = count(AccesRestreint::liste_contenu_zone_auteur($id_zone));
			
			$s = $row['id_zone'];
			$vals[] = $s;

			$s = "";
			$s .= "<a href='".generer_url_ecrire("acces_restreint_edit","id_zone=$id_zone")."'>";
			$s .= $row['titre'];
			$s .= "</a>";
			$vals[] = $s;

			$s = propre($row['descriptif']);
			$vals[] = $s;
			
			$s = "";
			if ($nb_rub>0){
				$s .= "$nb_rub "._T('accesrestreint:rubriques');
				if ($nb_aut>0) $s.=", ";
			}
			if ($nb_aut>0)
				$s .= "$nb_aut "._T('accesrestreint:auteurs');
			$vals[] = $s;
			
			$s="";
			$s = icone_horizontale (_T('accesrestreint:icone_supprimer_zone'), generer_url_ecrire('acces_restreint', "supp_zone=$id_zone"), "../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png", "supprimer.gif", false);
			$vals[] = $s;

			$table[] = $vals;
		}
	}

	// on affiche la table
	$titre_table = _T('accesrestreint:titre_table');
	$icone = "../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png";
	//if ($titre_table) echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
	echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
	echo $tranches;
	$largeurs = array('','','','','');
	$styles = array('arial11', 'arial1', 'arial1','arial1','arial1');
	afficher_liste($largeurs, $table, $styles);
	echo "</table>";
	echo "</div>";

	echo "<br/>";

	debut_cadre_relief();
	echo generer_url_post_ecrire("acces_restreint");
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:icone_creer_zone');
	echo "</div>";
	echo "<p>";
	echo _T('accesrestreint:titre')."<br/>";
	echo "<input type='input' name='titre' value='titre' class='formo' />";
	echo "</p>";
	echo "<p>";
	echo _T('accesrestreint:descriptif')."<br/>";
	echo "<textarea name='descriptif' class='formo'>";
	echo "descriptif";
	echo "</textarea>";
	echo "</p>";
	echo "<input type='submit' name='creer' value='"._T('accesrestreint:bouton_creer_la_zone')."' />";
	echo "</div>";
	echo "</form>";
	fin_cadre_relief();

	fin_page();
}

?>