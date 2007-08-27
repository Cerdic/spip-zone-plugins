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
include_spip('inc/affichage');
include_spip ('base/spip-listes');
include_spip('inc/plugin');

function spiplistes_afficher_pile_messages(){
	
	if ($GLOBALS['spip_version_code']<1.9204){
		include_spip('base/spiplistes_upgrade');
		if (!spiplistes_install('test'))
			spiplistes_install('install');
	}
	$out = "";
	$list = spip_query ("SELECT * FROM spip_listes WHERE message_auto='oui' ");
	$message_pile = spip_num_rows($list);
	if ($message_pile == 0) return $out; 
	$out .= debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_timer.gif',true);
	$out .= "<div  class='chapo' style='border-top:1px #cccccc;width:100%;font-weight:bold;font-size:14px'>"._T('spiplistes:Messages_automatiques')."</div>";
	$out .= "<style>
	table.tab td {
	text-align:center;
	padding:3px;
	width:33%;
	background-color:#cccccc;
	}
	table.tab {
	margin-top:5px;
	}
	tr.row_even {
	background-color:#cccccc;
	}
	</style>";
	$out .= "<table class='tab'>" ;	
	$out .= "<tr style='padding:5px'>";
	$out .= "<td style='font-weight:bold;background-color:#eeeecc'>"._T('spiplistes:envoi_patron')."</td>";
	$out .= "<td style='font-weight:bold;background-color:#eeeecc'>"._T('spiplistes:sur_liste')."</td>";
	$out .= "<td style='font-weight:bold;background-color:#eeeecc'>"._T('spiplistes:prochain_envoi_prevu')."</td>";
	$out .= "</tr>";

	while($row = spip_fetch_array($list)) {
		$id_article = $row['id_liste'] ;
		$titre = $row['titre'] ;
		$sablier = time() - strtotime($row['maj']) ;
		$proch = round( ( (24*3600*$row['periode']) - $sablier) / (3600*24) ) ;
	
		if($i == 0){
			$out .= "<tr style='padding:5px'>" ;
			$i = 1 ;
		}else {
			$out .= "<tr style='padding:5px' class='row_even'>" ;
			$i = 0 ;
		}
	
  	$date_dernier = strtotime($row['maj']) ;
    $date_dernier = date(_T('spiplistes:format_date'),$date_dernier) ;

		$out .= "<td><a href='".generer_url_public('patron_switch',"patron=".$row['patron']."&date=".$date_dernier)."'> ".$row['patron']."</a><br />"._T('spiplistes:Tous_les')." ".$row['periode']." "._T('spiplistes:jours')."</td><td><a href='?exec=listes&id_liste=$id_article'>$titre</a><br />" ;
		$out .= "</td>" ;
		$out .= "<td>";
		if($proch != 0)
			$out .= _T('spiplistes:dans_jours')." <b>$proch</b> "._T('spiplistes:jours')."</td>";
		else 
			$out .= "<b>"._T('date_aujourdhui')."</b></td>";
		$out .= "</tr>" ;
	}
	$out .= "</table>" ;
	$out .= fin_cadre_enfonce(true);
	return $out;
}

function exec_spip_listes() {
	
	include_spip ('inc/acces');
	include_spip ('inc/filtres');
	include_spip ('inc/config');
	include_spip ('inc/barre');
	
	include_spip ('inc/mots');
	include_spip ('inc/documents');
	
	
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	global $supp_dest;
	
	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 
	
	// Admin SPIP-Listes
	echo debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}
	
	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
		$statut_auteur=$statut;
		spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	}
	
	debut_gauche();
	spip_listes_raccourcis();
	creer_colonne_droite();

	debut_droite("messagerie");
	
	// MODE HISTORIQUE: Historique des envois --------------------------------------
	
	if ($detruire_message = _request('detruire_message')) {
		spip_query("DELETE FROM spip_courriers WHERE id_courrier="._q($detruire_message));
		spip_query("DELETE FROM spip_auteurs_messages WHERE id_message="._q($detruire_message));
		spip_query("DELETE FROM spip_forum WHERE id_message="._q($detruire_message));
	}
	
	/// afficher un tableau de messages
	
	///
	
	$messages_vus = '';
	
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_encours'), _DIR_PLUGIN_SPIPLISTES.'img_pack/24_send-receive.gif', 'messages', 'encour', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_redac'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'redac', '', 'position') ;
	
	
	// afficher les messages auto
	echo spiplistes_afficher_pile_messages();
	
	echo "<br /><br />";

	echo spiplistes_afficher_en_liste(_T('spiplistes:messages_auto_envoye'),_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'auto', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_envoye'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'publie', '', 'position') ;
	
	
	// MODE HISTORIQUE FIN ---------------------------------------------------------
	
	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."</p>" ;
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
