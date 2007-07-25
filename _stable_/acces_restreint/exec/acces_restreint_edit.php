<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint');
include_spip('inc/acces_restreint_gestion');

function exec_acces_restreint_edit(){
	global $couleur_claire;
	global $spip_lang_right;
	
	$new = _request('new');
	
	include_ecrire('inc_presentation');

	$id_zone = intval($_GET['id_zone']);

	if (isset($_POST['Enregistrer'])) {
		if(_request('id_zone') == 0){
			$id_zone = AccesRestreint_cree_zone();
			set_request('id_zone', $id_zone);
		}
		AccesRestreint_enregistrer_zone();
	}
	
	debut_page(_T('accesrestreint:page_zones_acces'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('accesrestreint:titre_zones_acces'));
	debut_gauche();
	
	// Boite info
	 if ($id_zone) {
		debut_boite_info();
		echo "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>" ;
		echo _T('accesrestreint:zone_numero');
		echo "<br /><span class='spip_xx-large'>";
		echo "$id_zone";
		echo '</span></div>';
		$nb_rub = count(AccesRestreint_liste_contenu_zone_rub($id_zone));
		$nb_aut = count(AccesRestreint_liste_contenu_zone_auteur($id_zone));
		$s = "";
		if ($nb_rub>0){
			$s .= "$nb_rub "._T('accesrestreint:rubriques');
			if ($nb_aut>0) $s.=", ";
		}
		if ($nb_aut>0)
			$s .= "$nb_aut "._T('accesrestreint:auteurs');
		echo "<div style='text-align:center;'>$s</div>";
		fin_boite_info();
	}

//Raccourcis
	$res = icone_horizontale(_T('accesrestreint:voir_toutes'), generer_url_ecrire("acces_restreint",''), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", 'rien.gif',false);
	echo bloc_des_raccourcis($res);
	
	debut_droite();
	$requete = "SELECT * FROM spip_zones WHERE id_zone=$id_zone";
	$res = spip_query($requete);
	$row = spip_fetch_array($res);

	if (!autoriser('modifier','zone') OR (!$row && $new!='oui')) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if ($row) {
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$publique = $row['publique'];
		$privee = $row['privee'];
	} else if ($new='oui') {
		$titre = _T('accesrestreint:titre');
		$descriptif = _T('accesrestreint:descriptif');
		$publique = 'oui';
		$privee = 'non';
		$id_zone = 0;
	}

	$retour = '';
	if (isset($_GET['retour']))
		$retour = $_GET['retour'];

	debut_cadre_relief();
	if ($new == 'oui')
		// URL temporaire pour éviter d'afficher un id_zone nul
		echo generer_url_post_ecrire('acces_restreint_edit',"new=zone_cree".($retour?"&retour=".urlencode($retour):""));
	else
		echo generer_url_post_ecrire('acces_restreint_edit',"id_zone=$id_zone".($retour?"&retour=".urlencode($retour):""));
	AccesRestreint_formulaire_zone($id_zone, $titre, $descriptif, $publique, $privee);

	if ($new == 'oui' && autoriser('modifier','zone')){
	echo "<div class='verdana2'>";
	echo "<input type='checkbox' name='auto_attribue_droits' value='oui' checked='checked' id='droits_admin'> <label for='droits_admin'>"._T("accesrestreint:ajouter_droits_auteur")."</label><br>";
	echo "</div>";
	}

	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('accesrestreint:rubriques_zones_acces');
	echo "</div>";
	echo "<div>\n";
	echo AccesRestreint_selecteur_rubrique_html($id_zone);
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


	// Selecteur d'auteurs sympathique
	if ($GLOBALS['spip_version_code'] >= 1.9253) {
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		echo $editer_auteurs('zone', $id_zone, $flag_editable=true, _request('cherche_auteur'), _request('ids'));
	}
	// Vieux SPIP
	else {
		echo AccesRestreint_afficher_auteurs('<b>' . _T('accesrestreint:info_auteurs_lies_zone') . '</b>', array("FROM" => 'spip_auteurs AS auteurs, spip_zones_auteurs AS lien', 'WHERE' => "lien.id_zone='$id_zone' AND lien.id_auteur=auteurs.id_auteur", 'ORDER BY' => "auteurs.nom DESC"));
	}


	fin_page();
}

?>