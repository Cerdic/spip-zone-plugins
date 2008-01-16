<?php

// inc/spiplistes_api_presentation.php
	
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/presentation');

/*
	Les fonctions affichage et présentation dans l'esapce privé
*/


	// SPIP < 193
if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
	function spiplistes_gros_titre($titre, $ze_logo='', $aff=true) {
		$r = gros_titre($titre, $ze_logo, $aff);
		if(!$aff) return($r);
	}
}
else {
	// SPIP >= 193
	function spiplistes_gros_titre($titre, $ze_logo='', $aff=true) {
		$ze_logo = ""; // semble ne plus être utilisé dans exec/*
		$r = gros_titre($titre, $ze_logo, $aff);
		if(!$aff) return($r);
	}
}

/*
	Les onglets dans la rubrique Edition (naviguer)
*/
function spiplistes_onglets ($rubrique, $onglet, $return = false) {

	$result = "";
	
	if ($rubrique == _SPIPLISTES_RUBRIQUE){
		$result = ""
			. "<br />"
			. debut_onglet()
			. onglet(_T('spiplistes:Casier_a_courriers'), generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_hyperlink-mail-and-news-24.gif")
			. onglet(_T('spiplistes:listes_de_diffusion_'), generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif")
			. onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."addressbook-24.gif")
			. fin_onglet()
		;
	}

	if($return) return($result);
	else echo($result);
} // end spiplistes_onglets()

?>