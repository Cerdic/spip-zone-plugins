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
		, 'new'
		, 'id_message' // devrait être id_courrier. A corriger dans les autres scripts et supprimer ici.
		, 'id_courrier'
		, 'modifier_message'
		, 'titre', 'texte'
		, 'envoi_test', 'change_statut', 'supp_dest', 'envoi', 'adresse_test', 'choisir_dest', 'destinataire'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_message','id_courrier') as $key) {
		$$key = intval($$key);
	}
	if($id_message>0) $id_courrier = $id_message;

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
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	// GERER COURRIER: Affichage d'un courrier---------------------------------------

	//Ajouter le destinataire  si on le connait 
	$result = spip_query("SELECT id_liste, texte, email_test FROM spip_courriers WHERE id_courrier="._q($id_courrier));

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
					spip_query("UPDATE spip_courriers SET email_test="._q($adresse_test).", total_abonnes=1 WHERE id_courrier="._q($id_courrier));
					$change_statut = "ready";
				}
			}
			//definir liste d envoi	
			if(intval($destinataire)){
				spip_query("UPDATE spip_courriers SET id_liste="._q($destinataire)." WHERE id_courrier="._q($id_courrier));
				//passer le mail en pret a l envoi
				$change_statut = "ready";
			}
			
			//definir liste d envoi de tous les contacts : pas dans emil test
			if($destinataire=="tous")
				$change_statut = "ready";
		}
	}

	if(intval($id_courrier)){
		if ($modifier_message == "oui")
			spip_query("UPDATE spip_courriers SET titre="._q($titre).", texte="._q($texte)." WHERE id_courrier="._q($id_courrier));	
		
		if ($change_statut) {
			spip_query("UPDATE spip_courriers SET statut="._q($change_statut)." WHERE id_courrier="._q($id_courrier));
			if($change_statut == "ready"){
				//enregistrer le texte propre dans la base pour envoi
				$result_m = spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_courrier));
				
				while($row = spip_fetch_array($result_m)) {
					$titre = $row['titre'];
					$texte = $row['texte'];
				}
				$texte = spiplistes_propre($texte);
				spip_query("UPDATE spip_courriers SET titre="._q($titre).", texte="._q($texte)." WHERE id_courrier="._q($id_courrier));
			}
			
			if($change_statut == 'publie'){
				// si on annule un envoi, effacer les abonnes en attente
				spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier="._q($id_courrier));
			}
		}
		
		// A securiser ?
		if ($envoi) {
			spip_query("UPDATE spip_courriers SET statut='encour' WHERE id_courrier="._q($id_courrier));
			spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier="._q($id_courrier));
		
			spip_log("test ? ->".$test."idliste->$id_liste");
			if(intval($id_liste) OR ($id_liste==0 AND $test!='oui') )
				spiplistes_remplir_liste_envois($id_courrier,$id_liste) ;
		}
	}

	//le message
	$result_m = spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_courrier));
	while($row = spip_fetch_array($result_m)) {
		$id_courrier = $row['id_courrier'];
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
			$destinataire = 'la liste : <a href="'.generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE,'id_liste='.$id_liste).'">'.$row['titre'].'</a>';
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
			echo "<span style='font-size:120%;color:red;font-weight:bold'>"._T('spiplistes:message_en_cours')." <br />"._T('spiplistes:modif_envoi')."</span>";
		}
		
		if ($statut == 'ready' && $pret_envoi) {
			echo "<span style='font-size:120%;color:red'>
			<b>"._T('spiplistes:message_presque_envoye')."</b></span><br /> "._T('spiplistes:a_destination').$destinataire."<br />"._T('spiplistes:confirme_envoi');
			echo "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_MODIF,"id_courrier=$id_courrier")."' method='post'>";
			echo "<div style='text-align:center'><input type='submit' name='envoi' value='"._T('spiplistes:envoyer')."' class='fondo' /></div>";
			echo "</form>";
		}
		
		if ($statut == 'encour'){
			if ($expediteur == $connect_id_auteur  OR ($type == 'nl' AND $connect_statut == '0minirezo') OR ($type == 'auto' AND $connect_statut == '0minirezo')) {
				echo "<div style='float:right'>";
				echo icone (_T('icone_supprimer_message'), generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE,'detruire_message='.$id_courrier), _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif', _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif');
				echo "</div>";
			}
			echo "<p><span style='font-size:120%;color:red'>
			<b>"._T('spiplistes:envoi_program')."</b></span><br /> "._T('spiplistes:a_destination').$destinataire."<br /><br />
			<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a></p>";
		}
		
		if ($statut == 'publie')  {
			echo "<span style='font-size:120%;color:red'>
			<b>"._T('spiplistes:message_arch')."</b></span>";
			echo "<ul>";
			echo "<li>"._T('spiplistes:envoyer_a').$destinataire."</li>";
			echo "<li>"._T('spiplistes:envoi_date').$date_heure."</li>";
			echo "<ul>";
			echo "<li>"._T('spiplistes:envoi_debut').$debut_envoi."</li>";
			echo "<li>"._T('spiplistes:envoi_fin').$fin_envoi."</li>";
			echo "</ul>";
			echo "<li>"._T('spiplistes:nbre_abonnes').$total_abonnes."</li>";
			echo "<ul>";
			echo "<li>"._T('spiplistes:format_html').$nb_emails_html."</li>";
			echo "<li>"._T('spiplistes:format_texte').$nb_emails_texte."</li>";
			echo "<li>"._T('spiplistes:desabonnes').": ".$nb_emails_non_envoyes."</li>";
			echo "</ul>";
			echo "<li>"._T('spiplistes:erreur_envoi').$nb_emails_echec."</li>";
			echo "</ul>";
		}
		
		echo fin_cadre_relief();
		
		$texte_original = $texte;
		if($statut != 'encour' AND $statut != 'publie' AND $statut != 'ready')
			$texte = spiplistes_propre($texte);
		
		echo "<div style='margin-top:20px;border: 1px solid $la_couleur; background-color: $couleur_fond; padding: 5px;' class='cadre cadre-r'>"; // debut cadre de couleur
		//debut_cadre_relief("messagerie-24.gif");
		echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td width=100%>";
		if ($statut=="redac" OR $statut=="ready") {
			echo "<div style='float:right; margin:10px'>";
			echo icone (_T('spiplistes:bouton_modifier'),generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'id_message='.$id_courrier), _DIR_PLUGIN_SPIPLISTES."img_pack/stock_mail.gif");
			echo "</div>";
		}
		
		echo "<span style='font-size:120%;color:$la_couleur'><b>$le_type</b></span><br />";
		echo "<h3>$titre</h3>";
		echo "<br class='nettoyeur' />";
		echo debut_boite_info();
		echo _T('spiplistes:version_html')." <a href=\"".generer_url_ecrire('courrier_preview','id_message='.$id_courrier)."\" title=\""._T('spiplistes:plein_ecran')."\"><small>(+)</small></a><br />\n";
		echo "<iframe src=\"?exec=courrier_preview&id_message=$id_courrier\" width=\"100%\" height=\"500\"></iframe>\n";
		echo fin_boite_info();    
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
		
		if($statut=="redac" OR $statut=="ready"){
			//envoi de test 
			echo "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_MODIF,'id_message='.$id_courrier)."' method='post'>";
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
			echo icone (_T('icone_supprimer_message'), '?exec=spip_listes&detruire_message='.$id_courrier, _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif', _DIR_PLUGIN_SPIPLISTES.'img_pack/poubelle_msg.gif');
			echo "</div>";
		}
		echo "</div>"; // fin du cadre de couleur
		
	}//while		

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
