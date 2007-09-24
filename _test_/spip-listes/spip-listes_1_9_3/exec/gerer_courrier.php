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

/*
	_SPIPLISTES_EXEC_COURRIER_MODIF
	
	Affiche un courrier. 
	Le formulaire permet :
	- l'envoi sur mail de test
	- l'attachement d'une liste
	Dans les deux cas, le statut du courrier passe à _SPIPLISTES_STATUT_READY 
	(la meleuse prend en charge les courriers en statut _SPIPLISTES_STATUT_READY)
	
*/
function exec_gerer_courrier(){

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// initialise les variables postées par le formulaire
	foreach(array(
		'type'
		, 'id_message' // devrait être id_courrier. A corriger dans les autres scripts et supprimer ici.
		, 'id_courrier'
		, 'modifier_message', 'titre', 'texte' // (formulaire edition) _SPIPLISTES_EXEC_COURRIER_EDIT
		, 'new' // idem
		, 'btn_envoi_test', 'adresse_test' // (formulaire local) demande un envoi sur mail de test
		, 'btn_choisir_liste', 'destinataire' // (formulaire local) sélection d'une liste de destination
		, 'change_statut' // (formulaire spiplistes_boite_autocron) 'publie' pour annuler envoi
		, 'btn_confirmer_envoi' // (formulaire local) confirmer envoi
		, 'supp_dest'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_message','id_courrier') as $key) {
		$$key = intval($$key);
	}
	foreach(array('adresse_test','titre','texte') as $key) {
		$$key = trim($$key);
	}
	if($id_message>0) $id_courrier = $id_message;
	
	$page_result = $message_erreur = $str_destinataire = "";

	//////////////////////////////////////////////////////
	// Modification de courrier
	////
	// effectue les modifications demandées si retour local ou retour editeur
	if($id_courrier > 0) {
		// charge le courrier demandé
		$sql_select = "titre,texte,email_test,statut,id_liste,id_auteur";
		if($row = spip_fetch_array(spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1"))) {
			foreach(explode(",", $sql_select) as $key) {
				$current[$key] = trim($row[$key]);
			}
		}
		$id_auteur = $current['id_auteur'];
		
		if($btn_envoi_test) {
		//////////////////////////////////////////////////////
		// demande d'envoi à mail de test (formulaire local)
			if(email_valide($adresse_test)) {
				if(spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE email='$adresse_test' LIMIT 1"))==0) {
				// vérifie si l'adresse est connue des auteurs
					// si inconnue, refuse d'envoyer
					$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_inconnue'), true);
				}
				else {
					// Ok. Enregistre l'adresse et passe le courrier ready pour la meleuse
					spip_query("UPDATE spip_courriers SET email_test='$adresse_test',total_abonnes=1 WHERE id_courrier=$id_courrier LIMIT 1");
					//passer le mail en pret a l envoi
					$change_statut = _SPIPLISTES_STATUT_READY;
					$str_destinataire = "l'email de test : $adresse_test";
					$pret_envoi = true;
				}
			}
			else {
				$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_invalide'), true);
			}
		} // end if($btn_envoi_test)
		else if($btn_choisir_liste) {
		//////////////////////////////////////////////////////
		// demande d'envoi à une liste (formulaire local)
			$id_liste = intval($str_destinataire);
			if($id_liste > 0){
				if(($nb_abos = spiplistes_nb_abonnes_count($id_liste)) > 0) {
					if($row = spip_fetch_array(spip_query ("SELECT titre FROM spip_listes WHERE id_liste = $id_liste LIMIT 1"))) {
					// va chercher le nom de la liste + nb abos
						$str_destinataire = "la liste : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE,'id_liste='.$id_liste)."'>".$row['titre']."</a>"
							. spiplistes_nb_abonnes_liste_str_get($id_liste, $nb_abos);
							
						// Ok. Met à jour le panier
						spip_query("UPDATE spip_courriers SET id_liste=$id_liste WHERE id_courrier=$id_courrier LIMIT 1");
						
						$change_statut = _SPIPLISTES_STATUT_READY;
						$pret_envoi = true;
					}
				}
				else {
					$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_liste_vide'), true);
				}
			}
		} // end if($btn_choisir_liste)
		else if ($modifier_message == "oui") {
		// retour de l éditeur
			if(!empty($titre)) {
				spip_query("UPDATE spip_courriers SET titre="._q($titre).",texte="._q($texte)." WHERE id_courrier=$id_courrier LIMIT 1");	
			}
			else {
				$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
			}
		}
		else if ($btn_confirmer_envoi 
			&& ($statut == _SPIPLISTES_STATUT_READY)
			&& (!empty($email_test) || ($id_liste > 0))
			&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur))
			) {
			$change_statut = _SPIPLISTES_STATUT_ENCOURS;
			spip_query("UPDATE spip_courriers SET statut='$change_statut' WHERE id_courrier=$id_courrier LIMIT 1");
			spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$id_courrier");
			if(intval($id_liste) OR ($id_liste==0 AND $test!='oui') )
				spiplistes_remplir_liste_envois($id_courrier,$id_liste) ;
		}

		if($change_statut == _SPIPLISTES_STATUT_READY) {
			$titre = spiplistes_propre($titre);
			$texte = spiplistes_propre($texte);
			spip_query("UPDATE spip_courriers SET titre="._q($titre).",texte="._q($texte).",statut=$change_statut WHERE id_courrier=$id_courrier LIMIT 1");
		}
		else if($change_statut == _SPIPLISTES_STATUT_PUBLIE){
			// si on annule un envoi par spiplistes_boite_autocron, effacer les abonnes en attente
			spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$id_courrier");
			spiplistes_log("ID_COURRIER #$id_courrier CANCELLED BY ID_AUTEUR #$connect_id_auteur");
		}
		
		// FIN DES MODIFICATIONS

		// Ok. recharge les données pour compléter le formulaire
		$sql_select = "titre,texte,email_test,statut";
		if($row = spip_fetch_array(spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1"))) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
		}
	} // end if($id_courrier > 0)
	//////////////////////////////////////////////////////
	// Nouveau courrier
	////
	else if($new == 'oui') {
	// retour éditeur. Création du courrier
		if(!empty($titre)) {
			$statut = _SPIPLISTES_STATUT_REDAC;
			$type = 'nl';
			$result = spip_query("INSERT INTO spip_courriers (titre,texte,date,statut,type,id_auteur) 
				VALUES ("._q($titre).","._q($texte).",NOW(),'$statut','$type',"._q($connect_id_auteur).")"); 
			$id_courrier = spip_insert_id(); 
		}
		else {
			$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
		}
	}

	
	//////////////////////////////////////////////////////
	// préparation des boutons si droits
	$gros_bouton_modifier = 
		$gros_bouton_supprimer = 
		$gros_bouton_arreter_envoi = ""
		;
	if(($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur))) {
		
		if(($statut == _SPIPLISTES_STATUT_REDAC) || ($statut == _SPIPLISTES_STATUT_READY)) {
		// Le courrier peut-être modifié si en préparation 
			$gros_bouton_modifier = 
				icone (
					_T('spiplistes:Modifier_ce_courrier') // légende bouton
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'id_courrier='.$id_courrier) // lien
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_mail.gif" // image du fond
					, "edit.gif" // image de la fonction. Ici, le crayon
					, '' // alignement
					, false // pas echo, demande retour
					)
				;
		}
		
		if($statut != _SPIPLISTES_STATUT_PUBLIE) {
		// Le courrier peut-être supprimé s'il n'a pas été publié
			$gros_bouton_supprimer = 
				"<div style='margin-top:1ex;'>"
				. icone (
					_T('spiplistes:Supprimer_ce_courrier')
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_supprimer_courrier=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'poubelle_msg.gif'
					, ""
					, "right"
					, false
					)
				. "</div>\n"
				;
		}
	
		if($statut == _SPIPLISTES_STATUT_ENCOURS) {
		// L'envoi d'un courrier en cours peut être stoppé
			$gros_bouton_arreter_envoi = 
				icone (
					_T('spiplistes:Arreter_envoi')
					// si arreter envoi, passe la main à exec/spip_listes
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_arreter_envoi=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_redac-24.png"
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stop-top-right-24.png"
					, "right"
					, false
					)
				. fin_cadre_relief(true)
				;
		}
	}

	//////////////////////////////////////////////////////
	// recharge le courrier pour édition
	if($id_courrier > 0) {
		$sql_select = "id_liste,email_test,date,titre,texte,message_texte,type,statut,id_auteur"
			. ",total_abonnes,nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html"
			. "date_debut_envoi,date_fin_envoi";
		if($row = spip_fetch_array(spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_courrier)." LIMIT 1"))) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			foreach(array('id_liste','id_auteur',total_abonnes,nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html) as $key) {
				$$key = intval($$key);
			}
			foreach(array('titre') as $key) {
				$$key = typo($$key);
			}
		}
	}
	
//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion des listes de courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, false);
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	/////////////////////
	// prépare le message statut du courrier
	if($id_courrier > 0) {
		$le_type = _T('spiplistes:message_type');
		$la_couleur = "red";
		
		$str_statut_courrier = "";
		switch($statut) {
			case _SPIPLISTES_STATUT_REDAC:
				$str_statut_courrier = _T('spiplistes:message_en_cours')."<br />"._T('spiplistes:modif_envoi');
				break;
			case _SPIPLISTES_STATUT_READY:
				if($pret_envoi) {
					$str_statut_courrier = ""
						. "<p class='verdana2'>"._T('spiplistes:message_presque_envoye') . "<br /> "
						._T('spiplistes:a_destination').$str_destinataire . "<br />"
						._T('spiplistes:confirme_envoi')
						. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_MODIF,"id_courrier=$id_courrier")."' method='post'>"
						. "<p style='text-align:center'><input type='submit' name='btn_confirmer_envoi' value='"._T('spiplistes:envoyer')."' class='fondo' /></p>"
						. "</form>"
						;
				}
				break;
			case _SPIPLISTES_STATUT_ENCOURS:
				$str_statut_courrier = ""
					. _T('spiplistes:envoi_program')."<br />"._T('spiplistes:a_destination').$str_destinataire."<br /><br />"
					. "<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a>"
					;
				break;
			case _SPIPLISTES_STATUT_PUBLIE:
				$str_statut_courrier = ""
					. "<span>"
					. "<strong>"._T('spiplistes:message_arch')."</strong></span>"
					. "<ul>"
					. " <li>"._T('spiplistes:envoyer_a').$str_destinataire."</li>"
					. " <li>"._T('spiplistes:envoi_date').$date."</li>"
					. " <ul>"
					. "  <li>"._T('spiplistes:envoi_debut').$date_debut_envoi."</li>"
					. "  <li>"._T('spiplistes:envoi_fin').$date_fin_envoi."</li>"
					. " </ul>"
					. " <li>"._T('spiplistes:nbre_abonnes').$total_abonnes."</li>"
					. " <ul>"
					. "  <li>"._T('spiplistes:format_html').$nb_emails_html."</li>"
					. "  <li>"._T('spiplistes:format_texte').$nb_emails_texte."</li>"
					. "  <li>"._T('spiplistes:desabonnes').": ".$nb_emails_non_envoyes."</li>"
					. " </ul>"
					. " <li>"._T('spiplistes:erreur_envoi').$nb_emails_echec."</li>"
					. "</ul>"
					;
		}
		if(!empty($str_statut_courrier)) {
			$str_statut_courrier = "<div class='verdana2'>".$str_statut_courrier."</div>";
		}
		
	/////////////////////
	// construction du ventre
		$page_result = ""
			. $message_erreur
			. debut_cadre_relief(spiplistes_items_get_item('icon', $statut), true)
			. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>"
			. "<tr>"
			. "<td>".gros_titre($titre, spiplistes_items_get_item('puce', $statut), false)."</td>"
			. "<td rowspan='2'>".$gros_bouton_modifier."</td>"
			. "</tr>"
			. "<tr> "
			. "<td>$str_statut_courrier</td>"
			. "</tr>"
			. "</table>"
			. $gros_bouton_arreter_envoi
			//
			// boite courrier au format html
			. debut_cadre_couleur_foncee('', true)
			. _T('spiplistes:version_html')." <a href=\"".generer_url_ecrire('courrier_preview','id_message='.$id_courrier)."\" title=\""._T('spiplistes:plein_ecran')."\"><small>(+)</small></a><br />\n"
			. "<iframe style='background-color:#fff;' src=\"?exec=courrier_preview&id_message=$id_courrier\" width=\"100%\" height=\"500\"></iframe>\n"
			. fin_cadre_couleur_foncee(true)
			//
			// fin de la boite
			. fin_cadre_relief(true)
			;
// la suite demain

		echo($page_result);
		
		$page_result = "";
		
		
		$texte_original = $texte;
		if($statut != 'encour' AND $statut != 'publie' AND $statut != 'ready')
			$texte = spiplistes_propre($texte);
		
		echo "<div style='margin-top:20px;border: 1px solid $la_couleur; background-color: $couleur_fond; padding: 5px;' class='cadre cadre-r'>"; // debut cadre de couleur
		echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td width=100%>";
		echo "<span style='font-size:120%;color:$la_couleur'><strong>$le_type</strong></span><br />";
		echo "<p>";
		echo debut_boite_info();
		
		if($message_texte !=''){
			$alt = _T('spiplistes:calcul_patron');
		}
		else{
			$alt = _T('spiplistes:calcul_html');
			$message_texte = version_texte($texte);
		}
		
		echo _T('spiplistes:version_texte')." <a href='#' title='$alt'><small>(?)</small></a><br />";
		
		echo "<textarea name='texte' rows='20' class='formo' cols='40' wrap=soft>";
		echo $message_texte ;
		echo "</textarea><p>\n";
		
		echo fin_boite_info();
		echo "<br />";
		
		if(($statut==_SPIPLISTES_STATUT_REDAC) || ($statut==_SPIPLISTES_STATUT_READY)) {
		// propose l'envoi test 
			$page_result =""
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_MODIF, "id_courrier=$id_courrier")."' method='post'>"
				. debut_boite_info(true)
				. "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>"
				;
			// le form est étrangement découpé. A revoir.
			echo($page_result);
			if(!$pret_envoi){
				echo "<strong>"._T('spiplistes:envoi')."</strong><p style='font-familly : Georgia,Garamond,Times,serif'>"._T('spiplistes:envoi_texte')."</p>";
				echo debut_cadre_enfonce();
				echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
				echo "<div style='float:right'><input type='submit' name='btn_envoi_test' value='"._T('spiplistes:email_tester')."' class='fondo'  /></div>";
				echo "<input type='text' name='adresse_test' value='"._T('spiplistes:email_adresse')."' class='fondo' size='35' onfocus=\"this.value=''\" />" ;
				echo "</div>" ;
				echo fin_cadre_enfonce() ;
				
				$list = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste' OR statut = 'inact' ");
				echo "<div style='font-size:14px;font-weight:bold'>"._T('spiplistes:destinataires')."</div>";
				echo "<div style='float:right'><input type='submit' name='btn_choisir_liste' value='"._T('spiplistes:Choisir_cette_liste')."' class='fondo' /></div>";
				echo "<select name='destinataire' >";
				echo "<option value='tous'>"._T('spiplistes:toutes')."</option>" ;
				while($row = spip_fetch_array($list)) {
					$id_liste = $row['id_liste'] ;
					$titre = $row['titre'] ;
					echo "<option value='$id_liste'>$titre</option>" ;
				}
				echo "</select>";
			}
			else{
				echo "<p style='text-align:center;font-weight:bold'>"._T('spiplistes:confirme_envoi')."</p>";
			}
		}
		echo "</div>";

		echo fin_boite_info();
		echo "</form>";

		echo "</td></tr></table>";
		echo "</div>"; // fin du cadre de couleur
		
		$page_result .= ""
			. $gros_bouton_supprimer
			;
		
	} // end if
	else {
		$page_result .= ""
			. __boite_alerte (_T('spiplistes:Erreur_courrier_introuvable'), true)
			;
	}

	echo($page_result);
	
	// GERER COURRIER: FIN DE PAGE
	
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
