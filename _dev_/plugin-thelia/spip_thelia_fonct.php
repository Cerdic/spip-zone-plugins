<?php
function spip_thelia_appeler_moteur_thelia($texte)
{
	//ne pas appeler thelia dans l'espace priv�
	if ($_REQUEST['exec']!="") return $texte;

	if (!file_exists("fonctions")) {
		echo ("erreur : th&eacute;lia introuvable, v&eacute;rifiez que les sous-r&eacute;pertoires de th&eacute;lia et spip sont dans le m&ecirc;me r&eacute;pertoire.");
		return $texte;	
	}
	
	
	//parsonnalisation des variables th�lia
	switch($_REQUEST['page']){
		case 'panier' : $securise=0; $pageret=1; break;
		case 'adresse' : $securise=1; $pageret=1; break;
		case 'cheque' : $securise=1; $pageret=1;$reset=1; break;
		case 'commande' : $securise=1; $pageret=1; $panier=1; $transport=1; break;
		case 'commande_detail' : $securise=1; break;
		case 'commande_visualiser' : $securise=1; break;
		case 'compte_modifier' : $formulaire=1; $securise=1; $obligetelfixe=1; break;
		case 'compte_modifiererr' : $formulaire=1; $securise=1; $obligetelfixe=1; break;
		case 'connexion' : $pageret=0; break;
		case 'livraison_adresse' : $securise=1; break;
		case 'livraison_modifier' : $securise=1; break;
		case 'moncompte' : $pageret=1; break;
		case 'nouveau' : $securise=1; break;
		case 'regret' : $pageret=1; break;	
		case 'virement' : $securise=1; $pageret=1; $reset=1; break;	
	}
	
	
	//conflit sur la variable $page. 
	global $page;
	$page = new stdclass;
	$page = "";
	
	
	include_once("classes/Navigation.class.php");
	
	session_start();
	
	//conflit entre spip et th�lia sur le nommage des langues. on force provisoirement le fran�ais dans th�lia.
	if ($_SESSION['navig']->lang != '') {
		$_SESSION['navig']->lang=0;
	}

	//r�affectation des variables de th�lia qui ont �t�es renomm�es dans les squelettes pour �viter les conflits avec spip
	$_REQUEST['action'] = $_REQUEST['thelia_action'];
	$_REQUEST['page'] = $_REQUEST['page_thelia'];
	
	
	//on pr�pare le flux � envoyer au moteur th�lia
	$res = $texte;
	$res = str_replace("THELIA-", "#", $res);

	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	ob_start();
	include_once(_DIR_PLUGINS."plugin-thelia/moteur-thelia-1_3_3.php");

	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	return $texte;	
	
}

function remplacement_sortie_thelia($in_thelia)
{
    	//renommage action en thelia_action. m�thode provisoire � revoir.
	$in_thelia = str_replace("adresse.php?action", "adresse.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("cheque.php?action", "cheque.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande.php?action", "commande.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande_detail.php?action", "commande_detail.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande_visualiser.php?action", "commande_visualiser.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("compte_modifier.php?action", "compte_modifier.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("compte_modifiererr.php?action", "compte_modifiererr.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("connexion.php?action", "connexion.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("creercompte.php?action", "creercompte.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("formulerr.php?action", "formulerr.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("imgpop?action", "imgpop?thelia_action", $in_thelia);
	$in_thelia = str_replace("livraison_adresse.php?action", "livraison_adresse.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("mdpoublie.php?action", "mdpoublie.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("merci.php?action", "merci.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("moncompte.php?action", "moncompte.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("nouveau.php?action", "nouveau.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("panier.php?action", "panier.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("produit.php?action", "produit.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("regret.php?action", "regret.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("virement.php?action", "virement.php?thelia_action", $in_thelia);
	
	//iso vers utf8
	$in_thelia = str_replace('�', '&eacute;', $in_thelia);
	$in_thelia = str_replace('�', '&egrave;', $in_thelia);
	$in_thelia = str_replace('�', '&agrave;', $in_thelia);
	$in_thelia = str_replace('�', '&ecirc;', $in_thelia);
	$in_thelia = str_replace('�', '&icirc;', $in_thelia);
	$in_thelia = str_replace('�', '&iuml;', $in_thelia);
	$in_thelia = str_replace('�', '&Icirc;', $in_thelia);
	$in_thelia = str_replace('�', '&Eacute;', $in_thelia);
	$in_thelia = str_replace('�', '&ccedil;', $in_thelia);
	$in_thelia = str_replace('�', '&ocirc;', $in_thelia);
	$in_thelia = str_replace('�', '&euml;', $in_thelia); 

	return $in_thelia;
}
?>