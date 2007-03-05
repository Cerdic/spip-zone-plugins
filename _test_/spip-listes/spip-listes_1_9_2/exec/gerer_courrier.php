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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/barre');
include_spip('inc/affichage');
include_spip('base/spip-listes');

function exec_gerer_courrier(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;

	$type = _request('type');
	$new = _request('new');
	$id_message = _request('id_message');
	$modifier_message = _request('modifier_message');
	$titre = _request('titre');
	$texte = _request('texte');

	$envoi_test = _request('envoi_test');
	$change_statut = _request('change_statut');
	$supp_dest = _request('supp_dest');
	$envoi = _request('envoi');
	$adresse_test = _request('adresse_test');
	$choisir_dest = _request('choisir_dest');
	$destinataire = _request('destinataire');

	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 

	// Admin SPIP-Listes
	echo debut_page("Spip listes", "redacteurs", "spiplistes");

	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}

	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
		$statut_auteur=$statut;
		spip_listes_onglets("messagerie", "Spip listes");
	}

	debut_gauche();
	spip_listes_raccourcis();
	creer_colonne_droite();
	debut_droite("messagerie");

	// MODE COURRIER: Affichage d'un courrier---------------------------------------

	//Ajouter le destinataire  si on le connait 
	$result = spip_query("SELECT id_liste, texte, email_test FROM spip_courriers WHERE id_courrier="._q($id_message));

	while($row = spip_fetch_array($result)) {
		$id_liste = $row["id_liste"] ;
		if(	email_valide($row["email_test"]) )
			$test = 'oui' ;
		
		if(($choisir_dest OR $envoi_test)){
			if($envoi_test AND email_valide($adresse_test)){
				$destinataire = $adresse_test ;
				$res3 = spip_query("SELECT id_auteur FROM spip_auteurs WHERE email = "._q($adresse_test)." ORDER BY id_auteur ASC ");
				if(spip_num_rows($res3)==0){
					$erreur_mail_pas_bon = "<h3>"._T('spiplistes:sans_envoi')."</h3>\n"; 
				}
				else{
					//definir adresse email de test
					spip_query("UPDATE spip_courriers SET email_test="._q($adresse_test).", total_abonnes=1 WHERE id_courrier="._q($id_message));
					$change_statut = "ready";
				}
			}
			//definir liste d envoi	
			if(intval($destinataire)){
				spip_query("UPDATE spip_courriers SET id_liste="._q($destinataire)." WHERE id_courrier="._q($id_message));
				//passer le mail en pret a l envoi
				$change_statut = "ready";
			}
			
			//definir liste d envoi de tous les contacts : pas dans emil test
			if($destinataire=="tous")
				$change_statut = "ready";
		}
	}

	if(intval($id_message)){
		if ($modifier_message == "oui")
			spip_query("UPDATE spip_courriers SET titre="._q($titre).", texte="._q($texte)." WHERE id_courrier="._q($id_message));	
		
		if ($change_statut) {
			spip_query("UPDATE spip_courriers SET statut="._q($change_statut)." WHERE id_courrier="._q($id_message));
			if($change_statut == "ready"){
				//enregistrer le texte propre dans la base pour envoi
				$result_m = spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_message));
				
				while($row = spip_fetch_array($result_m)) {
					$titre = $row['titre'];
					$texte = $row['texte'];
				}
				$texte = spiplistes_propre($texte);
				spip_query("UPDATE spip_courriers SET titre="._q($titre).", texte="._q($texte)." WHERE id_courrier="._q($id_message));
			}
			
			if($change_statut == 'publie'){
				// si on annule un envoi, effacer les abonnes en attente
				spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier="._q($id_message));
			}
		}
		
		// A securiser ?
		if ($envoi) {
			spip_query("UPDATE spip_courriers SET statut='encour' WHERE id_courrier="._q($id_message));
			spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier="._q($id_message));
		
			spip_log("test ? ->".$test."idliste->$id_liste");
			if(intval($id_liste) OR ($id_liste==0 AND $test!='oui') )
				remplir_liste_envois($id_message,$id_liste) ;
		}
	}

	//le message
	$result_m = spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_message));
	while($row = spip_fetch_array($result_m)) {
		$id_message = $row['id_courrier'];
		$id_liste = $row['id_liste'];
		$email_test = $row['email_test'];
		
		$date_heure = $row["date"];
		$titre = typo($row["titre"]);
		$texte = $row["texte"];
		$message_texte = $row["message_texte"];
		$type = $row["type"];
		$statut = $row["statut"];
		$expediteur = $row['id_auteur'];		
		
		$le_type = _T('spiplistes:message_type');
		$la_couleur = "red";
		
		$total_abonnes = $row["total_abonnes"];
		$nb_emails_envoyes = $row["nb_emails_envoyes"];
		$nb_emails_echec = $row["nb_emails_echec"];
		$nb_emails_non_envoyes = $row["nb_emails_non_envoyes"];
		$nb_emails_texte = $row["nb_emails_texte"];
		$nb_emails_html = $row["nb_emails_html"];
		$debut_envoi = $row["date_debut_envoi"];
		$fin_envoi = $row["date_fin_envoi"];
		
		//trouver un destinataire
		$destinataire = ''; //secu
		$pret_envoi=false;
		
		if($email_test !=''){
			$destinataire = $email_test ;
			if(email_valide($destinataire)){				
				$destinataire = "l'email de test : ".$destinataire ;
				$pret_envoi=true;
			}
			else{
				$erreur_mail == 'oui';
			}
		}
		
		elseif(intval($id_liste) !=0){
			$query_ = spip_query ("SELECT * FROM spip_listes WHERE id_liste = "._q($id_liste));
			$row = spip_fetch_array($query_);
			$destinataire = 'la liste : <a href="'.generer_url_ecrire('listes','id_liste='.$id_liste).'">'.$row['titre'].'</a>';
			//ajouter le nombre d'inscrits
			// ici
			$pret_envoi=true;
		}
		elseif( ($statut == 'ready' OR $statut == 'encour') && $id_liste == 0){
			$destinataire = _T('spiplistes:abonees');
			$pret_envoi=true;
		}
	
		echo debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail_send.gif');
		//echo "tklo $destinataire, $email_test , $id_liste";
		
		if($erreur_mail){
			echo "<h3>"._T('spiplistes:sans_envoi')."</h3>" ;
		}
		if ($statut == 'redac' && !$pret_envoi) {
			echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'><b>"._T('spiplistes:message_en_cours')." <br />"._T('spiplistes:modif_envoi')."</b></font>";
		}
		
		if ($statut == 'ready' && $pret_envoi) {
			echo "<span style='font-family:Verdana,Arial,Sans,sans-serif;font-size:120%;color:red'>
			<b>"._T('spiplistes:message_presque_envoye')."</b></span><br /> &agrave; destination de $destinataire<br />"._T('spiplistes:confirme_envoi');
			echo "<form action='?exec=gerer_courrier&id_message=".$id_message."' method='post'>";
			echo "<div style='text-align:center'><input type='submit' name='envoi' value='"._T('spiplistes:envoyer')."' class='fondo' /></div>";
			echo "</form>";
		}
		
		if ($statut == 'encour'){
			if ($expediteur == $connect_id_auteur  OR ($type == 'nl' AND $connect_statut == '0minirezo') OR ($type == 'auto' AND $connect_statut == '0minirezo')) {
				echo "<div style='float:right'>";
				icone (_T('icone_supprimer_message'), generer_url_ecrire('spip_listes','detruire_message='.$id_message), _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif', _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif');
				echo "</div>";
			}
			echo "<p><span style='font-family:Verdana,Arial,Sans,sans-serif;font-size:120%;color:red'>
			<b>"._T('spiplistes:envoi_program')."</b></span><br /> &agrave; destination de $destinataire<br /><br />
			<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a></p>";
		}
		
		if ($statut == 'publie')  {
			echo "<span style='font-family:Verdana,Arial,Sans,sans-serif;font-size:120%;color:red'>
			<b>"._T('spiplistes:message_arch')."</b></span>";
			echo "<ul>";
			echo "<li>Envoy&eacute; &agrave $destinataire</li>";
			echo "<li>Date de l'envoi : $date_heure</li>";
			echo "<ul>";
			echo "<li>Debut de l'envoi : $debut_envoi</li>";
			echo "<li>Fin de l'envoi : $fin_envoi</li>";
			echo "</ul>";
			echo "<li>Nombre d'inscrits : $total_abonnes</li>";
			echo "<ul>";
			echo "<li>Format html : $nb_emails_html</li>";
			echo "<li>Format texte : $nb_emails_texte</li>";
			echo "<li>D&eacute;sabonn&eacute;s : $nb_emails_non_envoyes</li>";
			echo "</ul>";
			echo "<li>Nombre d'envois en erreur : $nb_emails_echec</li>";
			echo "</ul>";
		}
		
		echo fin_cadre_relief();
		
		$texte_original = $texte;
		if($statut != 'encour' AND $statut != 'publie' AND $statut != 'ready')
			$texte = spiplistes_propre($texte);
		
		echo "<div style='margin-top:20px;border: 1px solid $la_couleur; background-color: $couleur_fond; padding: 5px;'>"; // debut cadre de couleur
		//debut_cadre_relief("messagerie-24.gif");
		echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td width=100%>";
		if ($statut=="redac" OR $statut=="ready") {
			echo "<div style='float:right'>";
			icone ('Modifier ce courrier',generer_url_ecrire('courrier_edit','id_message='.$id_message), _DIR_PLUGIN_SPIPLISTES."img_pack/stock_mail.gif");
			echo "</div>";
		}
		
		echo "<span style='font-family:Verdana,Arial,Sans,sans-serif;font-size:120%;color:$la_couleur'><b>$le_type</b></span><br />";
		echo "<span style='font-family:Verdana,Arial,Sans,sans-serif'><h3>$titre</h3></span>";
		
		echo "<br /><font face='Georgia,Garamond,Times,serif' size=3>";
		echo debut_boite_info();
		echo "<b>Version HTML</b> <a href=\"".generer_url_ecrire('courrier_preview','id_message='.$id_message)."\" title=\""._T('spiplistes:plein_ecran')."\"><small>(+)</small></a><br />\n";
		echo "<iframe src=\"?exec=courrier_preview&id_message=$id_message\" width=\"100%\" height=\"500\"></iframe>\n";
		echo fin_boite_info();    
		echo "<p>";
		echo debut_boite_info();
		
		if($message_texte !=''){
			$alt = 'Calcul avec le patron version texte';
		}
		else{
			$alt = 'Calcul depuis la version HTML du message';
			$message_texte = version_texte($texte);
		}
		
		echo "<b> Version texte </b> <a href='#' title='$alt'><small>(?)</small></a><br />";
		
		echo "<textarea name='texte' rows='20' class='formo' cols='40' wrap=soft>";
		echo $message_texte ;
		echo "</textarea><p>\n";
		
		echo fin_boite_info();
		echo "</font><br />";
		
		if($statut=="redac" OR $statut=="ready"){
			//envoi de test 
			echo "<form action='".generer_url_ecrire('gerer_courrier','id_message='.$id_message)."' method='post'>";
			echo debut_boite_info();
			echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
			if(!$pret_envoi){
				echo "<b>"._T('spiplistes:envoi')."</b><p style='font-familly : Georgia,Garamond,Times,serif'>"._T('spiplistes:envoi_texte')."</p>";
				echo debut_cadre_enfonce();
				echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
				echo "<div style='float:right'><input type='submit' name='envoi_test' value='"._T('spiplistes:email_tester')."' class='fondo'  /></div>";
				echo "<input type='text' name='adresse_test' value='"._T('spiplistes:email_adresse')."' class='fondo' size='35' onfocus=\"this.value=''\" />" ;
				echo "</div>" ;
				echo fin_cadre_enfonce() ;
				
				$list = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste' OR statut = 'inact' ");
				echo "<div style='font-size:14px;font-weight:bold'>"._T('spiplistes:destinataires')."</div>";
				echo "<div style='float:right'><input type='submit' name='choisir_dest' value='"._T('spiplistes:choisir_cette')."' class='fondo' /></div>";
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
		if($statut != 'publie'){
			echo "<div style='margin:auto;margin-top:10px'>";
			icone (_T('icone_supprimer_message'), '?exec=spip_listes&detruire_message='.$id_message, _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif', _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif');
			echo "</div>";
		}
		echo "</div>"; // fin du cadre de couleur
		
		echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;
		
	}//while		

	echo fin_gauche(), fin_page();
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
