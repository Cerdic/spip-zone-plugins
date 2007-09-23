<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_abonnes_tous () {

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/affichage');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion des abonnés est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	//
	// Recherche d'auteur
	//
	
	spiplistes_cherche_auteur();
	
	//Compter tous les abonnes a des listes 
	$result_pile = spip_query(
	  'SELECT listes.statut, COUNT(abonnements.id_auteur)
	   FROM spip_listes AS listes LEFT JOIN spip_auteurs_listes AS abonnements USING (id_liste)
	   GROUP BY listes.statut');
	$nb_abonnes = array('liste'=>0, 'inact'=>0);
	while ($row = spip_fetch_array($result_pile, SPIP_NUM)) {
		$nb_abonnes[$row[0]] = intval($row[1]);
	}

	//evaluer les formats de tous les auteurs + compter tous les auteurs
	$result = spip_query("SELECT `spip_listes_format`, COUNT(`spip_listes_format`) FROM spip_auteurs_elargis GROUP BY `spip_listes_format`");
	$nb_inscrits = 0;

	//repartition des formats
	$cmpt = array('texte'=>0, 'html'=>0, 'non'=>0);
	
	while ($row = spip_fetch_array($result, SPIP_NUM)) {
		$nb_inscrits += $row[1];
		$abo =$row[0];
		if ($abo) {
			$cmpt[$abo] += $row[1];
		}
	}
	
	//Total des auteurs qui ne sont pas abonnes a une liste
	$abonnes_a_rien = spip_query(
		"SELECT a.id_auteur, COUNT(d.id_liste) FROM spip_auteurs AS a  
	      LEFT JOIN spip_auteurs_listes AS d ON a.id_auteur =  d.id_auteur GROUP BY a.id_auteur HAVING COUNT(d.id_liste) = 0;"
		); 
	$nb_abonnes_a_rien = spip_num_rows($abonnes_a_rien);

	$total = $cmpt['html'] + $cmpt['texte'] + $cmpt['non']; // ?

	$page_result = ""
		. debut_cadre_trait_couleur("forum-interne-24.gif", true)
		//. bandeau_titre_boite2(_T('spiplistes:abonnes_titre'), "", "white", "black", false)
		. bandeau_titre_boite2(_T('spiplistes:abonnes_titre'), "", "", "black", false)
		. "<div class='verdana2' style='position:relative;margin:1ex;height:5em;'>"
		// bloc de gauche. Répartition des abonnés.
		. "<div style='position:absolute;top:0;left:0;width:250px;' id='info_abo'>"
		. "<p style='margin:0;'>"._T('spiplistes:repartition_abonnes')." : </p>"
		. "<ul style='margin:0;padding:0 1ex;list-style: none;'>"
		// Total des abonnés qui ont un format html ou texte
		//. "<li>- "._T('spiplistes:nbre_abonnes') . ($cmpt['html'] + $cmpt['texte']) . "</li>"
		// Total des abonnés listes publiques
		. "<li>- "._T('spiplistes:abonnes_liste_pub') . $nb_abonnes['liste'] . "</li>"
		// Total des abonnés listes privées (internes)
		. "<li>- "._T('spiplistes:abonnes_liste_int') . $nb_abonnes['inact'] . "</li>"
	 	. "<li>- ". _T('spiplistes:abonne_aucune_liste') . " : ". ($nb_abonnes_a_rien - $cmpt['non']) . "</li>"
		. "</ul>"
		. "</div>\n"
		// bloc de droite. Répartition des formats.
		. "<div style='position:absolute;top:0;right:0;width:180px;' id='info_fmt'>\n"
		. "<p style='margin:0;'>"._T('spiplistes:repartition_formats')." : </p>\n"
		. "<ul style='margin:0;padding:0 1ex;list-style: none;'>"
		. "<li>- "._T('spiplistes:html')." : {$cmpt['html']}</li>"
		. "<li>- "._T('spiplistes:texte')." : {$cmpt['texte']}</li>"
		. "<li>- "._T('spiplistes:format_aucun')." : {$cmpt['non']}</li>"
		. "</ul>"
		. "</div>\n"
		// fin des infos
		. "</div>\n"
		;

	// formulaire de recherche si plus de 10 auteurs dans la base
	$row = spip_fetch_array(spip_query("SELECT COUNT(id_auteur) AS nb FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' LIMIT 11"));
	if ($row['nb'] > 10) {
		$page_result .= ""
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE)."' method='post' style='margin:0.5ex;border-top:1px solid black;padding:0.5ex 1ex 0;' class='verdana2'>"
			. "<div style='text-align:center;'>\n"
			. "<label for='chercher_un_auteur' style='display:none;'>"._T('spiplistes:chercher_un_auteur')." : </label>"
			. "<input type='text' title='"._T('spiplistes:chercher_un_auteur')."' name='cherche_auteur' id='chercher_un_auteur' class='fondl' value='' size='20' />"
			. " <input type='submit' title='"._T('spiplistes:chercher_un_auteur')."' name='Chercher' value='"._T('bouton_chercher')."' class='fondo' />"
			. "</div></form>\n"
			;
	}
	
	$page_result .= ""
		. fin_cadre_trait_couleur(true)
		;
	
	echo ($page_result);

	// auteur
	
	$retour = generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE);
	
	$tri = _request('tri') ? _request('tri') : 'nom';
	$retour = parametre_url($retour,"tri",$tri);
	
	//
	// Construire la requete
	//
	
	$sql_visible="1=1"; 
	$partri = " " . _T('info_par_tri', array('tri' => $tri));
	
	$sql_sel = '';
	
	// tri
	// CP: switch à revoir, 3 cases ne servent à rien. Peut-être ajouter 'format' ?
	switch ($tri) {
		case 'nombre':
			$sql_order = ' ORDER BY compteur DESC, unom';
			$type_requete = 'nombre';
			$partri = " "._T('info_par_nombre_article');
			break;
		case 'statut':
			$sql_order = ' ORDER BY statut, login = "", unom';
			$type_requete = 'auteur';
			$sql_visible = " aut.statut!='5poubelle'";
			break;
		case 'nom':
			$sql_order = ' ORDER BY unom';
			$type_requete = 'auteur';
			$sql_visible = " aut.statut!='5poubelle'";
			break;
		case 'email':
			$sql_order = ' ORDER BY LOWER(email)';
			$type_requete = 'auteur';
			break;
		case 'multi':
		default:
			$type_requete = 'auteur';
			$sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
			$sql_order = " ORDER BY multi";
	}

	
	//
	// La requete de base est tres sympa
	//
	
	$query = "SELECT
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.login AS login,
		aut.nom AS nom,
		aut.email AS email,
		aut.url_site AS url_site,
		aut.messagerie AS messagerie,
		UPPER(aut.nom) AS unom,
		count(lien.id_liste) as compteur
		$sql_sel
		FROM spip_auteurs as aut
		LEFT JOIN spip_auteurs_listes AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_listes AS art ON (lien.id_liste = art.id_liste)
		WHERE
		$sql_visible
		GROUP BY aut.id_auteur
		$sql_order";

	spiplistes_afficher_auteurs($query, generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE));

	// MODE STATUT FIN -------------------------------------------------------------
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
}

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>
