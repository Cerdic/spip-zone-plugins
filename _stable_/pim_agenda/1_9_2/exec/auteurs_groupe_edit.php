<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/pim_agenda');
include_spip('inc/pim_agenda');
include_spip('inc/acces_restreint_gestion');

function exec_auteurs_groupe_edit(){
	global $couleur_claire;
	global $spip_lang_right;
	global $new;
	include_ecrire('inc_presentation');

	$id_groupe = intval($_GET['id_groupe']);

	if (isset($_POST['Enregistrer'])) {
		if(_request('id_groupe') == 0){
			$id_groupe = PIMAgenda_cree_groupe();
			set_request('id_groupe', $id_groupe);
		}
		PIMAgenda_enregistrer_groupe();
	}
	
	echo debut_page(_T('pimagenda:page_groupes_acces'));
	echo "<br /><br /><br />";
	gros_titre(_T('pimagenda:titre_groupes_acces'));
	debut_gauche();
	
	// Boite info
	if ($id_groupe) {
		debut_boite_info();
		echo "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>" ;
		echo _T('pimagenda:groupe_numero');
		echo "<br /><span class='spip_xx-large'>";
		echo "$id_groupe";
		echo '</span></div>';
		$nb_aut = count(PIMAgenda_liste_contenu_groupe_auteur($id_groupe));
		$s = "";
		if ($nb_aut>0)
			$s .= "$nb_aut "._T('pimagenda:auteurs');
		echo "<div style='text-align:center;'>$s</div>";
		fin_boite_info();
	}

	//Raccourcis
	//$res = icone_horizontale(_T('pimagenda:voir_toutes'), generer_url_ecrire("acces_restreint",''), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/groupes-acces-24.gif", 'rien.gif',false);
	//echo bloc_des_raccourcis($res);
	
	debut_droite();
	$res = spip_query("SELECT * FROM spip_groupes WHERE id_groupe="._q($id_groupe));
	$row = spip_fetch_array($res);

	if (!autoriser('modifier','groupe') OR (!$row && $new!='oui')) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if ($row) {
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
	} else if ($new='oui') {
		$titre = _T('pimagenda:titre');
		$descriptif = _T('pimagenda:descriptif');
		$id_groupe = 0;
	}

	$retour = '';
	if (isset($_GET['retour']))
		$retour = $_GET['retour'];

	debut_cadre_relief();
	if ($new == 'oui')
		// URL temporaire pour eviter d'afficher un id_groupe nul
		echo generer_url_post_ecrire('auteurs_groupe_edit',"new=groupe_cree".($retour?"&retour=".urlencode($retour):""));
	else
		echo generer_url_post_ecrire('auteurs_groupe_edit',"id_groupe=$id_groupe".($retour?"&retour=".urlencode($retour):""));

	echo PIMAgenda_formulaire_groupe($id_groupe, $titre, $descriptif);

	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	echo "</form>\n";

	fin_cadre_relief();

	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";

	if (!$retour)
		$retour = generer_url_ecrire("auteurs");

	echo icone(_T('icone_retour'), $retour, _DIR_IMG_PACK . "auteur-24.gif", "rien.gif", "", 'non');
	echo "</div>\n";


	// Selecteur d'auteurs sympathique
	if ($GLOBALS['spip_version_code'] >= 1.9253) {
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		echo $editer_auteurs('groupe', $id_groupe, $flag_editable=true, _request('cherche_auteur'), _request('ids'));
	}
	// Vieux SPIP
	else {
		echo AccesRestreint_afficher_auteurs('<b>' . _T('pimagenda:info_auteurs_lies_groupe') . '</b>', array("FROM" => 'spip_auteurs AS auteurs, spip_groupes_auteurs AS lien', 'WHERE' => "lien.id_groupe='$id_groupe' AND lien.id_auteur=auteurs.id_auteur", 'ORDER BY' => "auteurs.nom DESC"));
	}


	fin_page();
}

function PIMAgenda_formulaire_groupe($id_groupe, $titre, $descriptif){
	global $couleur_claire;
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
	echo _T('pimagenda:titre_groupe');
	echo "</div>";
	echo "<p>";
	echo _T('info_titre')."<br/>";
	echo "<input type='input' name='titre' value='".entites_html($titre)."' class='formo' />";
	echo "</p>";
	echo "<p>";
	echo _T('info_descriptif')."<br/>";
	echo "<textarea name='descriptif' class='formo'>";
	echo entites_html($descriptif);
	echo "</textarea>";
	echo "</p>";

	echo "</div>";
	return;
}

?>