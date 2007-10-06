<?php

/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_listes_toutes(){
	
	include_spip('inc/presentation');
	include_spip('inc/affichage');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_lister_courriers_listes');
	
	global $connect_statut;

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	// la gestion des abonn�s est r�serv�e aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	// MODE LISTES: afficher les listes --------------------------------------------
	
	$page_result = "";
	
	foreach(array(_SPIPLISTES_PRIVATE_LIST, _SPIPLISTES_PUBLIC_LIST, _SPIPLISTES_MONTHLY_LIST, _SPIPLISTES_TRASH_LIST) as $statut) {
		$page_result .= ""
			. spiplistes_lister_courriers_listes(
				spiplistes_items_get_item("tab_t", $statut)
					.	(
						($desc = spiplistes_items_get_item("desc", $statut))
						? "<br /><span style='font-weight:normal;'>$desc</span>"
						: ""
						)
				, spiplistes_items_get_item("icon", $statut)
				, 'listes'
				, $statut
				, false
				, 'position'
				, _SPIPLISTES_EXEC_LISTE_GERER
			)
			;
	}
	
	echo($page_result);
		
	// MODE EDIT LISTES FIN --------------------------------------------------------
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'abonn�s et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/
?>
