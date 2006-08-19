<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

//
// Le bandeau des gadgets s'affiche en deux temps :
// 1. On affiche un minimum de <div> permettant aux boutons de jouer
//    du on/off au survol
//    -> fonction bandeau_gadgets()
// 2. En fin de page on envoie le vrai contenu (bien lourd) via innerHTML
//    -> fonction dessiner_gadgets()
//

function bandeau_gadgets($largeur, $options, $id_rubrique) {
	global $connect_id_auteur, $connect_login, $connect_statut, $couleur_claire,$couleur_foncee, $spip_lang_left, $spip_lang_right, $spip_ecran;

	$bandeau = "<div id='bandeau-gadgets' style='width:{$largeur}px;'>"
	//"\n<div style='position: relative; z-index: 1000;height:1%'>"
	//"\n<table width='$largeur' cellpadding='0' cellspacing='0' align='center'><tr><td>\n<div style='position: relative; z-index: 1000;'>"
	
	// GADGET Menu rubriques
	. "\n<div id='bandeautoutsite' class='bandeau_couleur_sous' style='$spip_lang_left: 0px;'>"
	. "<a href='" . generer_url_ecrire("articles_tous") . "' class='lien_sous'>"._T('icone_site_entier')."</a>"
	. "<div id='gadget-rubriques'></div>"
	. "</div>";
	// FIN GADGET Menu rubriques


	// GADGET Navigation rapide
	$bandeau .= "<div id='bandeaunavrapide' class='bandeau_couleur_sous' style='$spip_lang_left: 30px; width: 300px;'>"
	. "<a href='" . generer_url_ecrire("brouteur", ($id_rubrique ? "id_rubrique=$id_rubrique" : '')) . "' class='lien_sous'>" . _T('icone_brouteur') . "</a>"
	. "<div id='gadget-navigation'></div>\n"
	. "</div>\n";
	// FIN GADGET Navigation rapide

	// GADGET Recherche
	$bandeau .= "<div id='bandeaurecherche' class='bandeau_couleur_sous' style='width: 146px; $spip_lang_left: 60px;'>"
	. "<form method='get' style='margin: 0px; position: relative;' action='" . generer_url_ecrire("recherche") . "'>"
	. "<input type='hidden' name='exec' value='recherche' />"
	. "<input type=\"text\" id=\"form_recherche\" style=\"width: 140px;\" size=\"10\" value=\""._T('info_rechercher')."\" name=\"recherche\" onkeypress=\"t=window.setTimeout('lancer_recherche(\'form_recherche\',\'resultats_recherche\')', 200);\" autocomplete=\"off\" class=\"formo\" accesskey=\"r\" />"
	. "</form>"
	. "</div>";
	// FIN GADGET recherche

	// GADGET Agenda
	$bandeau .= "<div id='bandeauagenda' class='bandeau_couleur_sous' style='$spip_lang_left: 100px;'>"
	. "<a href='" . generer_url_ecrire("calendrier","type=semaine") . "' class='lien_sous'>"
	. _T('icone_agenda')
	. "</a>"
	
	. "<div id='gadget-agenda'></div>\n"
	. "</div>\n";
	// FIN GADGET Agenda

	// GADGET Messagerie
	$gadget = '';
	$gadget .= "<div id='bandeaumessagerie' class='bandeau_couleur_sous' style='$spip_lang_left: 130px; width: 200px;'>";
	$gadget .= "<a href='" . generer_url_ecrire("messagerie") . "' class='lien_sous'>";
	$gadget .= _T('icone_messagerie_personnelle');
	$gadget .= "</a>";
	$gadget .= "<div id='gadget-messagerie'></div>\n";
	$gadget .= "</div>";

	$bandeau .= $gadget;

	// FIN GADGET Messagerie


	// Suivi activite
	$bandeau .= "<div id='bandeausynchro' class='bandeau_couleur_sous' style='$spip_lang_left: 160px;'>";
	$bandeau .= "<a href='" . generer_url_ecrire("synchro") . "' class='lien_sous'>";
	$bandeau .= _T('icone_suivi_activite');
	$bandeau .= "</a>";
	$bandeau .= "<div id='gadget-suivi'></div>\n";
	$bandeau .= "</div>";
	
		// Infos perso
	$bandeau .= "<div id='bandeauinfoperso' class='bandeau_couleur_sous' style='width: 200px; $spip_lang_left: 200px;'>";
	$bandeau .= "<a href='" . generer_url_ecrire("auteurs_edit","id_auteur=$connect_id_auteur") . "' class='lien_sous'>";
	$bandeau .= _T('icone_informations_personnelles');
	$bandeau .= "</a>";
	$bandeau .= "</div>";

		
		//
		// -------- Affichage de droite ----------
	
		// Deconnection
	$bandeau .= "<div class='bandeau_couleur_sous' id='bandeaudeconnecter' style='$spip_lang_right: 0px;'>";
	$bandeau .= "<a href='" . generer_url_action("logout","logout=prive") . "' class='lien_sous'>"._T('icone_deconnecter')."</a>".aide("deconnect");
	$bandeau .= "</div>";
	
	$decal = 0;
	$decal = $decal + 150;

	$bandeau .= "<div id='bandeauinterface' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";
	$bandeau .= _T('titre_changer_couleur_interface');
	$bandeau .= "</div>";
		
	$decal = $decal + 70;
		
	$bandeau .= "<div id='bandeauecran' class='bandeau_couleur_sous' style='width: 200px; $spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";
	if ($spip_ecran == "large") 
			$bandeau .= "<div><a href='".parametre_url(self(),'set_ecran', 'etroit')."' class='lien_sous'>"._T('info_petit_ecran')."</a>/<b>"._T('info_grand_ecran')."</b></div>";
	else
			$bandeau .= "<div><b>"._T('info_petit_ecran')."</b>/<a href='".parametre_url(self(),'set_ecran', 'large')."' class='lien_sous'>"._T('info_grand_ecran')."</a></div>";
	$bandeau .= "</div>";
		
	$decal = $decal + 110;
		
	// En interface simplifiee, afficher en permanence l'indication de l'interface
	if ($options != "avancees") {
			$bandeau .= "<div id='displayfond' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right; visibility: visible; background-color: white; color: $couleur_foncee; z-index: -1000; border: 1px solid $couleur_claire; border-top: 0px;'>";
			$bandeau .= "<b>"._T('icone_interface_simple')."</b>";
			$bandeau .= "</div>";
	}
	$bandeau .= "<div id='bandeaudisplay' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";

	if ($options != 'avancees') {
		$bandeau .= "<b>"._T('icone_interface_simple')."</b>/<a href='$lien' class='lien_sous'>"._T('icone_interface_complet')."</a>";
	} else {
		$bandeau .= "<a href='$lien' class='lien_sous'>"._T('icone_interface_simple')."</a>/<b>"._T('icone_interface_complet')."</b>";
	}

	if ($options != "avancees") {
		$bandeau .= "<div>&nbsp;</div><div style='width: 250px; text-align: $spip_lang_left;'>"._T('texte_actualite_site_1')."<a href='./?set_options=avancees'>"._T('texte_actualite_site_2')."</a>"._T('texte_actualite_site_3')."</div>";
	}

	$bandeau .= "</div>";
	$bandeau .= "</div>";
	//$bandeau .= "</td></tr></table>";


	$bandeau .= '</div>';
	
	return $bandeau;
}

function gadget_agenda() {
	global $connect_id_auteur;

	$gadget = '';
	$today = getdate(time());
	$jour_today = $today["mday"];
	$mois_today = $today["mon"];
	$annee_today = $today["year"];
	$date = date("Y-m-d", mktime(0,0,0,$mois_today, 1, $annee_today));
	$mois = mois($date);
	$annee = annee($date);
	$jour = jour($date);
	$gadget .= "<table><tr>";
	$gadget .= "<td valign='top' width='200'>";
	$gadget .= "<div>";
	$gadget .= http_calendrier_agenda($annee_today, $mois_today, $jour_today, $mois_today, $annee_today, false, generer_url_ecrire('calendrier'));
	$gadget .= "</div>";
	$gadget .= "</td>";

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_messages AS messages WHERE id_auteur=$connect_id_auteur AND statut='publie' AND type='pb' AND rv!='oui' LIMIT 1"));
	if (!$n['n'])
		$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE ((lien.id_auteur='$connect_id_auteur' AND lien.id_message=messages.id_message) OR messages.type='affich') AND messages.rv='oui' AND messages.date_heure > DATE_SUB(NOW(), INTERVAL 1 DAY) AND messages.date_heure < DATE_ADD(NOW(), INTERVAL 1 MONTH) AND messages.statut='publie' GROUP BY messages.id_message ORDER BY messages.date_heure LIMIT 1"));
	if ($n['n']) {
		$gadget .= "<td valign='top' width='10'> &nbsp; </td>";
		$gadget .= "<td valign='top' width='200'>";
		$gadget .= "<div>&nbsp;</div>";
		$gadget .= "<div style='color: black;'>";
		$gadget .= http_calendrier_rv(sql_calendrier_taches_annonces(),"annonces");
		$gadget .=  http_calendrier_rv(sql_calendrier_taches_pb(),"pb");
		$gadget .=  http_calendrier_rv(sql_calendrier_taches_rv(), "rv");
		$gadget .= "</div>";
		$gadget .= "</td>";
	}
	$gadget .= "</tr></table>";

	return $gadget;
}

function gadget_messagerie() {
	global $connect_statut;

	$gadget = "<div>&nbsp;</div>";
	$gadget .= icone_horizontale(_T('lien_nouvea_pense_bete'),generer_url_ecrire("message_edit","new=oui&type=pb"), "pense-bete.gif", '', false);
	$gadget .= icone_horizontale(_T('lien_nouveau_message'),generer_url_ecrire("message_edit","new=oui&type=normal"), "message.gif", '', false);
	if ($connect_statut == "0minirezo") {
		  $gadget .= icone_horizontale(_T('lien_nouvelle_annonce'),generer_url_ecrire("message_edit","new=oui&type=affich"), "annonce.gif", '', false);
		}
	return $gadget;
}

function dessiner_gadgets($id_rubrique) {
	global $connect_id_auteur;
	global $couleur_claire;
	global $couleur_foncee;
	$args_coul="couleur_claire=".substr($couleur_claire,1)."&couleur_foncee=".substr($couleur_foncee,1);
	if ($_COOKIE['spip_accepte_ajax'] != -1) {
		return "\n<!-- javascript gadgets -->\n" .
		http_script(
		"$('#bandeautoutsite').load('".generer_url_prive('inc-gadget-rubriques','lang='.$GLOBALS['spip_lang'],'&')."');\n".
		"$('#bandeaunavrapide').load('".generer_url_prive('inc-gadget-navigation',"id_auteur=$connect_id_auteur&lang=".$GLOBALS['spip_lang'].
	($id_rubrique ? '&id_rubrique='.$id_rubrique : ''),'&')."');\n
		\n" .
#		"document.getElementById('gadget-recherche').innerHTML = \""
#		. addslashes(strtr(gadget_recherche($id_rubrique),"\n\r","  "))
#		. "\";\n" .

# agenda via InnerHTML
#		"document.getElementById('gadget-agenda').innerHTML = \""
#		. addslashes(strtr(gadget_agenda($id_rubrique),"\n\r","  "))
#		. "\";\n" .

# ou via .load()
		"$('#gadget-agenda').load('".
			generer_url_prive('inc-gadget-agenda','','&')."');\n
		\n" .

		"document.getElementById('gadget-messagerie').innerHTML = \""
		. addslashes(strtr(gadget_messagerie($id_rubrique),"\n\r","  "))
		. "\";\n" .
#		"document.getElementById('gadget-suivi').innerHTML = \""
#		. addslashes(strtr(gadget_suivi($id_rubrique),"\n\r","  "))
#		. "\";\n" .

		'');
	}
}

?>
