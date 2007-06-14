<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */
include_spip("inc/forms_tables_affichage");

function tables_ou_donnees($type_form,$retour){
	$res = spip_query("SELECT id_form FROM spip_forms WHERE type_form="._q($type_form));
	if (spip_num_rows($res)==0){
		return generer_url_ecrire("spip_sms_listes");
	}
	elseif (spip_num_rows($res)==1){
		$row = spip_fetch_array($res);
		return generer_url_ecrire("donnees_tous","id_form=".$row['id_form']."&retour=".urlencode($retour));
	}
	else
		return generer_url_ecrire($type_form."s_tous","retour=".urlencode($retour));
}

function smslist_barre_nav_gauche($page_actuelle){
	$out = "<style>
	.icone36-on{text-align:center;text-decoration:none;}
	.icone36-on img {-moz-border-radius-bottomleft:5px;-moz-border-radius-bottomright:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;
background-color:#FFFFFF;border:2px solid #666666;display:inline;margin:0pt;padding:4px;}
.icone36-on span {color:#000000;display:block;font-family:Verdana,Arial,Sans,sans-serif;font-size:10px;font-weight:bold;margin:2px;width:100%;}
</style>";
	$retour = generer_url_ecrire('spip_sms_listes');

	$gerer = generer_url_ecrire("spip_sms_listes");
	$out .= icone_etendue(_T("smslist:spip_sms_liste"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/spip-sms-list-64.png", "rien.gif","", false, $page_actuelle=="accueil");
	
	$gerer = generer_url_ecrire("smslist_messages_tous");
	$out .= icone_etendue(_T("smslist:icone_gerer_messages"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist_message-64.png", "rien.gif","", false, $page_actuelle=="gerer_messages");
		
	$gerer = generer_url_ecrire("smslist_listes_tous");
	$out .= icone_etendue(_T("smslist:icone_gerer_listes"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist_liste-64.png", "rien.gif","", false, $page_actuelle=="gerer_listes");

	$gerer = generer_url_ecrire("smslist_abonnes_tous");
	$out .= icone_etendue(_T("smslist:icone_gerer_abonnes"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist_abonne-64.png", "rien.gif","", false, $page_actuelle=="gerer_abonnes");
	
	#$gerer = generer_url_ecrire("smslist_boiteenvois_tous");
	#$out .= icone_etendue(_T("smslist:icone_boite_d_envoi"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist_boiteenvoi-64.png", "rien.gif","", false, $page_actuelle=="gerer_boiteenvoi");

	$gerer = generer_url_ecrire("smslist_config");
	$out .= icone_etendue(_T("smslist:icone_configurer"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist-config-64.png", "rien.gif", "", false, $page_actuelle=="configurer");
	return $out;
}
?>