<?php
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_import_export(){

	include_spip('inc/presentation');
	include_spip('inc/acces');
	include_spip('inc/affichage');

	global $connect_statut;

	// initialise les variables postées par le formulaire
	foreach(array(
		'abos_liste', 'format_abo'	// retour import
		, 'export_txt', 'export_id' // retour export
		) as $key) {
		$$key = _request($key);
	}

	// generation du fichier export ?
	if ($export_txt && $export_id && ($connect_statut == "0minirezo")) {
		$str_export = "# ".__plugin_html_signature(true, false)."\n"
			. "# "._T('spiplistes:membres_liste')."\n"
			. "# liste id: $export_id\n"
			. "# ".$GLOBALS['meta']['nom_site']."\n"
			. "# ".$GLOBALS['meta']['adresse_site']."\n"
			. "# date: ".date("Y-m-d")."\n"
			. "# nb abos: ".$nb_inscrits."\n\n"
			;
		if (intval($export_id)>0) {
		// exportation d'une liste ID ? 
			$sql_query = 
				"SELECT a.email,a.nom,a.login FROM spip_auteurs AS a, spip_auteurs_listes AS l 
				WHERE l.id_liste = "._q($export_id)." AND a.id_auteur=l.id_auteur AND a.statut!='5poubelle' "
				;
		}
		else {
		// autre type de liste
			if($export_id == "sans_abonnement") {
				$sql_query = 
					"SELECT a.email, a.nom, a.login FROM spip_auteurs AS a
					WHERE a.statut!='5poubelle' AND a.id_auteur NOT IN (SELECT l.id_auteur FROM spip_auteurs_listes AS l)";
			}
			if($export_id == "desabo") {
				$sql_query = 
					"SELECT a.email,a.nom,a.login,f.`spip_listes_format` FROM spip_auteurs AS a, spip_auteurs_elargis AS f 
					WHERE a.id_auteur=f.id_auteur 
					AND a.statut!='5poubelle' 
					AND f.`spip_listes_format`='non' "
					;
			}
		}
		$sql_result = spip_query($sql_query);
		$nb_inscrits = spip_num_rows($sql_result);
		while($row = spip_fetch_array($sql_result)) {
			$str_export .= $row['email']."\t".$row['login']."\t".$row['nom']."\n";
		}
		// envoie le fichier
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=\"export_liste_$export_id-".date("Y-m-d").".txt\"");
		echo ($str_export);
		exit;
	}
	// fin de generation du fichier export

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion du courrier est réservée aux admins 
	if ($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_authorisee() . fin_page());
	}

	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_raccourcis();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result = "";
	
	if(count($_FILES) && is_array($fichier_import = $_FILES['fichier_import']) && !$fichier_import['error']
		&& !($abos_liste && is_array($abos_liste) && count($abos_liste))) {
		// A oublié de sélectionner une liste de destination
			$page_result .= __boite_alerte(_T('spiplistes:Selectionnez_une_liste_pour_import'), true);
	}
	// import form
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'listes_in-24.png', true, "", _T('spiplistes:Importer'))
		. "<p class='verdana2'>"._T('spiplistes:_aide_import')."</p>\n"
		;
	if(count($_FILES) && is_array($fichier_import = $_FILES['fichier_import']) && !$fichier_import['error']) {
		if($abos_liste && is_array($abos_liste) && count($abos_liste)) {
			include_spip('inc/spiplistes_import');
			$page_result .= ""
				. debut_cadre_formulaire("background-color:white;margin-bottom:1em;", true)
				. bandeau_titre_boite2(_T('spiplistes:Resultat_import'),"","white","black",false)
				. spiplistes_import($fichier_import['tmp_name'], $fichier_import['name'], $abos_liste, $format_abo, true)
				. fin_cadre_formulaire(true)
				;
		}
	}
	$list = spip_query ("SELECT id_liste,titre,texte FROM spip_listes 
		WHERE statut = '"._SPIPLISTES_PUBLIC_LIST."' OR statut = '"._SPIPLISTES_PRIVATE_LIST."' OR statut = '"._SPIPLISTES_MONTHLY_LIST."' ");
	$nb_listes = spip_num_rows($list);
	
	if(!$nb_listes) {
		$page_result .= __boite_alerte(_T('spiplistes:Pas_de_liste_pour_import'), true);
	} 
	else {
		$page_result .= ""
			. "<form action='" . generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT) . "' method='post' enctype='multipart/form-data'name='importform'> "
			. debut_cadre_relief("", true, "", _T('spiplistes:Liste_de_destination'))
			. "<p class='verdana2'>"._T('spiplistes:Selectionnez_une_liste_de_destination')."</p>"
			. "<ul style='padding-left:0;list-style:none;' class='verdana2'>\n"
			;
		// liste des listes
		$ii = 0;
		while($row = spip_fetch_array($list)) {
			$id_liste = $row['id_liste'] ;
			$titre = $row['titre'] ;
			$checked = ($nb_listes == 1) ? "checked='checked'" : "";
			$label = _T('spiplistes:Liste_de_destination').": $titre";
			$page_result .= ""
				. "<li style='padding:4px;background-color:#".(($ii++ % 2) ? "fff" : "ccc").";'>"
				. "<input name='abos_liste[]' type='checkbox' id='abos_$id_liste' value='$id_liste' title=\"$label\" $checked />\n"
				. "<label for='abos_$id_liste'><strong>".$row['titre']."</strong> <em>".propre($row['texte'])."</em></label>\n"
				. "</li>\n"
				;
		}
		$page_result .= ""
			. "</ul>"
			. fin_cadre_relief(true)
			//
			// Sélection du format de réception
			. debut_cadre_relief("", true, "", _T('spiplistes:Format_de_reception'))
			. "<ul style='padding-left:0;list-style:none;' class='verdana2'>\n"
			. "<li><input name='format_abo' value='html' checked='checked' type='radio' id='fhtml'><label for='fhtml'>"._T('spiplistes:html')."</label></li>"
			. "<li><input name='format_abo' value='texte' type='radio' id='ftexte'><label for='ftexte'>"._T('spiplistes:texte')."</label></li>"
			. "<li><input name='format_abo' value='non' type='radio' id='fhtml'><label for='fnon'>"._T('spiplistes:desabonnement')."</label></li>"
			. "</ul>"
			. fin_cadre_relief(true)
			//
			// cadre insertion nom de fichier
			. debut_cadre_relief("", true, "", _T('spiplistes:importer_fichier'))
			. "<input type='file' size='40' name='fichier_import' />"
			. fin_cadre_relief(true)
			. "<div style='text-align:right;'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' onclick='Soumettre()' /></div>"
			. "</form>"
			;
	} // end else
	$page_result .= fin_cadre_trait_couleur(true);
	// fin formulaire import

	// export //(original from erational.org)
	// formulaire d'export
	$sql_query = "SELECT id_liste,titre FROM spip_listes 
		WHERE statut = '"._SPIPLISTES_PUBLIC_LIST."' 
		OR statut = '"._SPIPLISTES_PRIVATE_LIST."' 
		OR statut = '"._SPIPLISTES_MONTHLY_LIST."'";
	$list = spip_query($sql_query);
	$nb_listes = spip_num_rows($list);
	if ($nb_listes > 0) {
		$page_result .= ""
			. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'listes_out-24.png', true, "", _T('spiplistes:Exporter'))
			// exportation par listes
			. debut_cadre_relief("", true, "", _T('spiplistes:Exporter_une_liste_d_abonnes'))
			. "<form action='" . generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT) . "' method='post' name='retour_export'>\n"
			;
		while($row = spip_fetch_array($list)) {
			$id_liste = $row['id_liste'] ;
			$titre = $row['titre'];
			$checked = ($nb_listes==1) ? " checked='checked'" : "";
			$page_result .= ""
				. "<div class='verdana2'>"
				. "<input type='radio' name='export_id' id='export_id_$id_liste' value='$id_liste' $checked />"
				. "<label for='export_id_$id_liste'><strong>$titre</strong> <em>".spiplistes_nb_abonnes_liste_str_get($id_liste)."</em></label>\n"
				. "</div>"
				;
		}
		$page_result .= ""
			. fin_cadre_relief(true)
			//
			// exportation autres
			. debut_cadre_relief("", true, "", _T('spiplistes:Exporter_une_liste_de_non_abonnes'))
			. "<div class='verdana2'>"
			. "<input id='sansliste' type='radio' name='export_id' value='sans_abonnement' />"
			. "<label for='sansliste'>"._T('spiplistes:abonne_aucune_liste')."</label>"."<br />\n"
			. "<input id='desabonnes' type='radio' name='export_id' value='desabo' />"
			. "<label for='desabonnes'>"._T('spiplistes:desabonnes')."</label>"
			. "</div>"
			. fin_cadre_relief(true)
			//
			. "<div style='text-align:right;'><input type='submit' name='export_txt' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
			. "</form>\n"
			. fin_cadre_trait_couleur(true)
			;
	}

	echo($page_result);
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
}
?>
