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
include_spip('inc/mots');
include_spip('inc/lang');
include_spip('inc/affichage');
include_spip('base/spip-listes');

function exec_listes_dist(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	global $spip_lang_left,$spip_lang_right;

	$new = _request('new');
	$id_liste = _request('id_liste');
	$titre = _request('titre');
	$texte = _request('texte');
	$pied_page = _request('pied_page');
	//on peut plus ajouter un auteur la c buggue
	//global $flag_auteur;
	$creer_auteur = _request('creer_auteur');
	//global $ajout_auteur;
	$supp_auteur = _request('supp_auteur');
	$cherche_auteur = _request('cherche_auteur');
	//global $nouv_auteur;
	$changer_lang = _request('changer_lang');
	
	$Valider_auto = _request('Valider_auto');
	$auto = _request('auto');
	$changer_extra = _request('changer_extra');
	$email_envoi = _request('email_envoi');
	$patron = _request('patron');
	$periode = _request('periode');
	$sujet_message = _request('sujet_message');
	$envoyer_direct = _request('envoyer_direct');
	
	$debut = _request('debut');
 
 	$ok_nouveau_statut  = _request('ok_nouveau_statut');
	$statut_nouv = _request('statut_nouv');
 
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

	// Creer une liste -----------------------------------------
	////

	if ($id_liste==0) {
		if ($new=='oui') {
			if ($titre=='') $titre = _T('spiplistes:liste_sans_titre');
	
			spip_query("INSERT INTO spip_listes (statut, date, lang) VALUES ('inact', NOW(),"._q($langue_new).")");
			$id_liste = spip_insert_id();
			//Auteur de la liste (moderateur)
			spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_mod_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
			//abonner le moderateur a sa liste
			spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste = "._q($id_liste).")");
			spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
		} 
	}

	debut_gauche();
	echo debut_boite_info();

	echo '<div align="center">
	<font face="Verdana,Arial,Sans,sans-serif" size="1"><b>'._T('spiplistes:liste_numero').'&nbsp;:</b></font>
	<br><font face="Verdana,Arial,Sans,sans-serif" size="6"><b>'.$id_liste.'</b></font>
	</div>';

	echo fin_boite_info();
	spip_listes_raccourcis();
	creer_colonne_droite();

	debut_droite("messagerie");

	//////////////////////////////////////////////////////
	// Determiner les droits d'edition de la liste
	//

	$result = spip_query("SELECT statut, titre, maj FROM spip_listes WHERE id_liste="._q($id_liste));
	if ($row = spip_fetch_array($result)) {
		$statut_article = $row['statut'];
		$titre_article = $row['titre'];
		$maj = $row['maj'];
	}
	else {
		$statut_article = '';
	}

	$result_auteur = spip_query("SELECT * FROM spip_auteurs_mod_listes WHERE id_liste="._q($id_liste)." AND id_auteur="._q($connect_id_auteur));

	//
	// Droits mieux structure que ca ?
	//

	$flag_auteur=true;
	$flag_editable = ($flag_auteur AND ($statut_article == 'inact' OR $statut_article == 'liste' OR $statut_article == 'poublist'));

	//
	// Appliquer les modifications sur la liste
	//

	$ok_nouveau_statut = false;

	//Modifier le statut de la liste
	if ($statut_nouv) {
		if ($flag_auteur) {
		     //il faut etre admin et abonn�pour modifer une liste
			if ($statut_nouv == 'liste' AND $statut_article == 'inact')
				$ok_nouveau_statut = true;
			else if ($statut_nouv == 'inact' AND $statut_article == 'poublist')
				$ok_nouveau_statut = true;
			else if ($statut_nouv == 'poublist' AND $statut_article == 'inact')
				$ok_nouveau_statut = true;
			else if ($statut_nouv == 'poublist' AND $statut_article == 'liste')
				$ok_nouveau_statut = true;
			else if ($statut_nouv == 'inact' AND $statut_article == 'liste')
				$ok_nouveau_statut = true;
			else if ($statut_nouv == 'liste' AND $statut_article == 'poublist')
				$ok_nouveau_statut = true;
		}
		if ($ok_nouveau_statut)
			$result = spip_query("UPDATE spip_listes SET statut="._q($statut_nouv)." WHERE id_liste="._q($id_liste));
	}

	//modifier la date
	if ($jour=intval(_request('jour')) && $flag_editable) {
		$mois = intval(_request('mois'));
		if (($annee=intval(_request('annee'))) == "0000") $mois = "00";
		if ($mois == "00") $jour = "00";
		$result = spip_query("UPDATE spip_listes SET date='$annee-$mois-$jour' WHERE id_liste="._q($id_liste));
	}

	// Enregistrer les modifs sur la liste

	if ($titre && !$ajout_forum && $flag_editable) {
		$titre = corriger_caracteres($titre);
		$descriptif = corriger_caracteres($descriptif);
		$texte = corriger_caracteres($texte);
		$pied_page = corriger_caracteres($pied_page);
		
		$result = spip_query("UPDATE spip_listes SET titre="._q($titre).",descriptif="._q($descriptif).",texte="._q($texte).",pied_page="._q($pied_page)." WHERE id_liste="._q($id_liste));
		// afficher le nouveau titre dans la barre de fenetre
		$titre_article = $titre;
	}

	if($changer_lang)
		$result = spip_query("UPDATE spip_listes SET lang="._q($changer_lang)." WHERE id_liste="._q($id_liste));

	// prendre en compte les modifs sur les extras
	if($Valider_auto){
		if($auto == "oui"){
			$result = spip_query("UPDATE spip_listes SET message_auto='oui' WHERE id_liste="._q($id_liste));
			if($maj=="0000-00-00 00:00:00"){
				$result = spip_query("UPDATE spip_listes SET maj=NOW() WHERE id_liste="._q($id_liste));
			}
		}
		elseif ($auto == "non"){
			$result = spip_query("UPDATE spip_listes SET message_auto='non', maj='0000-00-00 00:00:00' WHERE id_liste="._q($id_liste));
		}
		if(email_valide($email_envoi)){
			$result = spip_query("UPDATE spip_listes SET email_envoi="._q($email_envoi)." WHERE id_liste="._q($id_liste));
		}
		if(($changer_extra == "oui") AND ($auto == "oui") ){
			$result = spip_query("UPDATE spip_listes SET patron="._q($patron).", periode="._q($periode).", titre_message="._q($sujet_message)." WHERE id_liste="._q($id_liste));
			if($envoyer_direct){
				$majnouv = (time() - ($periode * 3600*24));
				$result = spip_query("UPDATE spip_listes SET maj=FROM_UNIXTIME($majnouv), periode="._q($periode)." WHERE id_liste="._q($id_liste));
			}
		}
	}

	//
	// Lire la liste
	//

	$result = spip_query("SELECT * FROM spip_listes WHERE id_liste="._q($id_liste));

	if ($row = spip_fetch_array($result)) {
		$id_liste = $row["id_liste"];
		$titre = $row["titre"];
		$titre_message = $row["titre_message"];
		$pied_page = $row["pied_page"];
		$texte = $row["texte"];
		$date = $row["date"];
		$statut_article = $row["statut"];
		$maj_nouv = $row["maj"];
		$email_envoi=$row["email_envoi"];
		$message_auto = $row["message_auto"];
		$periode = $row["periode"];
		$patron = $row["patron"];
		$lang = $row["lang"];
	}

	changer_typo('','liste'.$id_liste);

	echo debut_cadre_relief();
	echo "<center>";

	//
	// Titre, surtitre, sous-titre
	//

	if($statut == 'liste') $logo_statut = 'puce-verte.gif';
	if($statut == 'inact') $logo_statut = 'puce-blanche.gif';
	if($statut == 'poublist') $logo_statut = 'puce-blanche.gif';

	echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
	echo "<tr width='100%'><td width='100%' valign='top'>";

	gros_titre($titre, $logo_statut);

	echo "<div style='margin:10px 0px 10px 0px'>";
	echo justifier(propre($texte));
	echo "</div>";
	
	echo "</td>";

	if ($flag_editable) {
		echo "<td><img src='"._DIR_IMG_PACK."rien.gif' width='5'></td>\n";
		echo "<td align='center'>";
		echo listes_edit_presentation($id_liste);
		echo "</td>";
	}
	echo "</tr></table>\n";

	echo fin_cadre_relief();

	//////////////////////////////////////////////////////
	// Modifier le statut de la liste
	//

	echo "
	<script type='text/javascript'><!--
	function change_bouton(selObj){
		var selection=selObj.options[selObj.selectedIndex].value;
		if (selection=='liste'){
			document.statut.src='"._DIR_IMG_PACK."puce-verte.gif';
		}
		if (selection=='inact'){
			document.statut.src='"._DIR_IMG_PACK."puce-blanche.gif';
		}
		if (selection=='poublist'){
			document.statut.src='"._DIR_IMG_PACK."puce-poubelle.gif';
		}
	}
	// --></script>";

	if ($connect_statut == '0minirezo' ) {
		echo debut_cadre_relief("racine-site-24.gif");
		echo "<form action='".generer_url_ecrire('listes',"id_liste=$id_liste")."' method='get'>";
		echo "<input type='hidden' name='exec' value='listes' />";
		echo "<input type='hidden' name='id_liste' value='$id_liste' />";

		echo "<b>"._T('spiplistes:Cette_liste_est').": </b> ";
		
		echo "<select name='statut_nouv' size='1' class='fondl' onChange='change_bouton(this)'>";
		echo "<option" . mySel("inact", $statut_article) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n";
		echo "<option" . mySel("liste", $statut_article) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n";
		echo "<option" . mySel("poublist", $statut_article) . " style='background:url("._DIR_IMG_PACK."rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n";
		
		echo "</select>";
		echo " \n";
		
		if ($statut_article=='liste') {
			echo "<img src='"._DIR_IMG_PACK."/puce-verte.gif' alt='' width='13' height='14' border='0' name='statut'>";
		}
		elseif ($statut_article=='inact') {
			echo "<img src='"._DIR_IMG_PACK."/puce-blanche.gif' alt='' width='13' height='14' border='0' name='statut'>";
		}
		elseif ($statut_article == 'poublist') {
			echo "<img src='"._DIR_IMG_PACK."/puce-poubelle.gif' alt='' width='13' height='14' border='0' name='statut'>";
		}
		echo " \n";
		
		echo "<input type='submit' name='Modifier' value='"._T('bouton_modifier')."' class='fondo' />";
		echo aide ("artstatut");
		echo "</form>";	
		
		echo "<div style='margin:10px 0px 10px 0px'>";
		echo menu_langues('changer_lang', $lang , '<strong>Langue :</strong>&nbsp;','', '');
		echo "</div>";
		
		//regler email d'envoi de la liste
		echo "<form action='".generer_url_ecrire('listes',"id_liste=$id_liste")."' method='post'>";
				
		$email_defaut = entites_html(lire_meta("email_webmaster"));
		$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;
		
		echo "<strong>";
		echo _T('spiplistes:retour')."</strong><br />";
		
		echo "<p>"._T('spiplistes:adresse')."</p>";
		echo "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='20' class='fondl' />&nbsp;";
				
		if($id_liste)
			echo "<input type='hidden' name='id_liste' value='$id_liste' />";
		if($new)
			echo "<input type='hidden' name='new' value='$new' />";
		echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo' />";
				
		echo "</form>";
				
		echo fin_cadre_relief();
	}

	echo debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES."img_pack/stock_timer.gif");
	echo "<form action='".generer_url_ecrire('listes',"id_liste=$id_liste")."' method='post'>";
	 
	// programmer un courrier automatique
	echo "<h3>"._T('spiplistes:program')."</h3>";

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	echo "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>";
	if ($message_auto != "oui")
		echo _T('spiplistes:non_program');
	else {
		if(($changer_extra == "oui") AND ($auto == "oui") )
			echo "<h2>"._T('spiplistes:date_act')."</h2>" ;
		echo "<h3> Sujet du courrier automatique : $titre_message</h3>";
		echo _T('spiplistes:env_esquel')." <em>".$patron."</em> " ;
		echo "<br />"._T('spiplistes:Tous_les')."  <b>".$periode."</b>  "._T('info_jours') ;
		
		$dernier_envoi =  strtotime($maj_nouv)  ;
		$sablier = (time() - $dernier_envoi) ;
			
		$proch = round(  (( (24*3600*$periode) - $sablier) / (3600*24)) ) ;
		$last = round(  ($sablier / (3600*24)) ) ;
		echo "<br />Dernier envoi il y a <b>$last</b> "._T('spiplistes:jours')."<br />";
		if($proch != 0) {
			echo "<br />"._T('spiplistes:prochain_envoi_prevu_dans')."<b>$proch</b> "._T('spiplistes:jours')."<br />";
		}
		else {
			echo "<br />"._T('spiplistes:prochain_envoi_aujd')."<br />";
		}
	}

	echo "</td></tr>";
	echo "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>";

	$checked = ($message_auto=='oui')?"checked='checked'":"";
	echo "<input type='radio' name='auto' value='oui' id='auto_oui' "
	 . $checked
	 ." onchange=\"jQuery('#auto_oui_detail').show();\" />";
	echo $checked?"<b>":"";
	echo "<label for='auto_oui'>"._T('spiplistes:prog_env')."</label>";
	echo $checked?"</b>":"";
	echo "<input type='hidden' name='changer_extra' value='oui'>";
	echo "<div id='auto_oui_detail'>";
	
	$sujet_message = ($titre_message=='') ? $titre." de ".$nomsite : $titre_message ;
	
	echo "<ul style='list-style-type:none;'>";
	echo "<li>Sujet : <input type='titre_message' name='sujet_message' value='".$sujet_message."' size='50' class='fondl' /> </li>" ;
	echo "<li>"._T('spiplistes:squel');
	
	
	$liste_patrons = find_all_in_path("patrons/","[.]html$");
	echo "<select name='patron'>";
	foreach($liste_patrons as $titre_option) {
		$titre_option = basename($titre_option,".html");
		$selected ="";
		if ($patron == $titre_option)
			$selected = "selected='selected";
		echo "<option ".$selected." value='".$titre_option."'>".$titre_option."</option>\n";
	}
	echo "</select>";
	
	echo "</li>";

	echo "<li>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$periode."' size='4' class='fondl' /> "._T('info_jours')."</li>" ;

	if(!$envoyer_direct)
		echo " <li><input type='checkbox' class='checkbox' name='envoyer_direct' id='box' class='fondl' /><label for='box'>"._T('spiplistes:env_maint')."</label></li>";

	echo "</ul></div>";
	$checked = ($message_auto=='non')?"checked='checked'":"";
	echo "<br /><input type='radio' name='auto' value='non' id='auto_non' "
	 . $checked
	 ." onchange=\"jQuery('#auto_oui_detail').hide();\" />";
	echo $checked?"<b>":"";
	echo " <label for='auto_non'>"._T('spiplistes:prog_env_non')."</label> ";
	echo $checked?"</b>":"";
	if ($message_auto=='non')
		echo "<script type='text/javascript'><!--
		jQuery('#auto_oui_detail').hide();
		--></script>";
	
	echo "</td></tr>\n";
	
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	if($id_liste)
		echo "<input type='hidden' name='id_liste' value='$id_liste' />";
	if($new)
		echo "<input type='hidden' name='new' value='$new' />";
	echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</td></tr>";
	echo "</table>\n";
	
	echo "</form>";

	echo fin_cadre_relief();

	//
	// Liste des abonnes
	//

	//
	// Appliquer les modifications sur les abonnes
	//

	echo "<a name='auteurs'></a>";

	//
	// Afficher les abonnes
	//

	//
	// Liste des abonnes a la liste
	//

	$editer_auteurs = charger_fonction('editer_auteurs','inc');
	echo $editer_auteurs('liste',$id_liste,$flag_editable, _request('cherche_auteur'),_request('ids'), 
		_T('spiplistes:abon'),
		'listes',
		'abonne_edit');
	
	////
	// MODE EDIT LISTE FIN ---------------------------------------------------------
	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;
	echo fin_gauche(), fin_page();
}

function listes_edit_presentation($id_liste){
	return icone(_T('spiplistes:modifier_liste'), generer_url_ecrire("liste_edit","id_liste=$id_liste"),_DIR_PLUGIN_SPIPLISTES."img_pack/reply-to-all-24.gif", "edit.gif");
}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Généale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

?>