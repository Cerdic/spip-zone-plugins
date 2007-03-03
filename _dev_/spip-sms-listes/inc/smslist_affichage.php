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

// l'argument align n'est plus jamais fourni
// http://doc.spip.org/@icone
function icone_etendue($texte, $lien, $fond, $fonction="", $align="", $afficher='oui', $expose=false){
	global $spip_display;

	if ($fonction == "supprimer.gif") {
		$style = '-danger';
	} else {
		$style = '';
		if ($expose) $style='-on';
		if (strlen($fonction) < 3) $fonction = "rien.gif";
	}

	if ($spip_display == 1){
		$hauteur = 20;
		$largeur = 100;
		$title = $alt = "";
	}
	else if ($spip_display == 3){
		$hauteur = 30;
		$largeur = 30;
		$title = "\ntitle=\"$texte\"";
		$alt = $texte;
	}
	else {
		$hauteur = 70;
		$largeur = 100;
		$title = '';
		$alt = $texte;
	}

	$size = 24;
	if (preg_match("/-([0-9]{1,3})[.](gif|png)$/i",$fond,$match))
		$size = $match[1];
	if ($spip_display != 1 AND $spip_display != 4){
		if ($fonction != "rien.gif"){
		  $icone = http_img_pack($fonction, $alt, "$title width='$size' height='$size'\n" .
					  http_style_background($fond, "no-repeat center center"));
		}
		else {
			$icone = http_img_pack($fond, $alt, "$title width='$size' height='$size'");
		}
	} else $icone = '';

	if ($spip_display != 3){
		$icone .= "<span>$texte</span>";
	}

	// cas d'ajax_action_auteur: faut defaire le boulot 
	// (il faudrait fusionner avec le cas $javascript)
	if (preg_match(",^<a\shref='([^']*)'([^>]*)>(.*)</a>$,i",$lien,$r))
	  list($x,$lien,$atts,$texte)= $r;
	else $atts = '';
	$lien = "\nhref='$lien'$atts";

	$icone = "\n<table cellpadding='0' class='pointeur' cellspacing='0' border='0' width='$largeur'"
	. ">\n<tr><td class='icone36$style'>"
	. ($expose?"":"<a"
	. $lien
	. '>')
	. $icone
	. ($expose?"":"</a>")
	. "</td></tr></table>\n";

	if ($afficher == 'oui')	echo $icone; else return $icone;
}

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
	
	$gerer = generer_url_ecrire("smslist_boiteenvois_tous");
	$out .= icone_etendue(_T("smslist:icone_boite_d_envoi"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist_boiteenvoi-64.png", "rien.gif","", false, $page_actuelle=="gerer_boiteenvoi");

	$gerer = generer_url_ecrire("smslist_config");
	$out .= icone_etendue(_T("smslist:icone_configurer"), $gerer, _DIR_PLUGIN_SMSLIST. "img_pack/smslist-config-64.png", "rien.gif", "", false, $page_actuelle=="configurer");
	return $out;
}
?>