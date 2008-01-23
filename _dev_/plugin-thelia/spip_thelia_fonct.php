<?php
function spip_thelia_appeler_moteur_thelia($texte)
{
	//ne pas appeler thelia dans l'espace privé
	if ($_REQUEST['exec']!="") return $texte;

	//problème à investiguer avec les pages forums
	if ($_REQUEST['page'] == "forum") return $texte;	
	
	
	//parsonnalisation des variables thélia
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

	include_once("classes/Navigation.class.php");
	
	session_start();
	
	//conflit entre spip et thélia sur le nommage des langues. on force provisoirement le français dans thélia.
	if ($_SESSION['navig']->lang != '') {
		$_SESSION['navig']->lang=0;
	}

	//réaffectation des variables de thélia qui ont étées renommées dans les squelettes pour éviter les conflits avec spip
	$_REQUEST['action'] = $_REQUEST['thelia_action'];
	$_REQUEST['page'] = $_REQUEST['page_thelia'];
	
	
	//on prépare le flux à envoyer au moteur thélia
	$res = $texte;
	$res = str_replace("THELIA-", "#", $res);

	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	ob_start("remplacement_sortie_thelia");
	include_once(_DIR_PLUGINS."plugin-thelia/moteur-thelia-1_3_3.php");

	$texte = ob_end_flush();
		


	return "";	
}
function remplacement_sortie_thelia($in_thelia)
{
    	//renommage action en thelia_action
	$in_thelia = str_replace("?action", "?thelia_action", $in_thelia);
	$in_thelia = str_replace("&action", "&thelia_action", $in_thelia);

	//renommage des pages au format spip
	$in_thelia = str_replace("adresse.php?", "spip.php?page=adresse&", $in_thelia);
	$in_thelia = str_replace("cheque.php?", "spip.php?page=cheque&", $in_thelia);
	$in_thelia = str_replace("commande.php?", "spip.php?page=commande&", $in_thelia);
	$in_thelia = str_replace("commande_detail.php?", "spip.php?page=commande_detail&", $in_thelia);
	$in_thelia = str_replace("commande_visualiser.php?", "spip.php?page=commande_visualiser&", $in_thelia);
	$in_thelia = str_replace("compte_modifier.php?", "spip.php?page=compte_modifier&", $in_thelia);
	$in_thelia = str_replace("compte_modifiererr.php?", "spip.php?page=compte_modifiererr&", $in_thelia);
	$in_thelia = str_replace("connexion.php?", "spip.php?page=connexion&", $in_thelia);
	$in_thelia = str_replace("creercompte.php?", "spip.php?page=creercompte&", $in_thelia);
	$in_thelia = str_replace("imgpop.php?", "spip.php?page=imgpop&", $in_thelia);
	$in_thelia = str_replace("livraison_adresse.php?", "spip.php?page=livraison_adresse&", $in_thelia);
	$in_thelia = str_replace("mdpoublie.php?", "spip.php?page=mdpoublie&", $in_thelia);
	$in_thelia = str_replace("merci.php?", "spip.php?page=merci&", $in_thelia);
	$in_thelia = str_replace("moncompte.php?", "spip.php?page=moncompte&", $in_thelia);
	$in_thelia = str_replace("nouveau.php?", "spip.php?page=nouveau&", $in_thelia);
	$in_thelia = str_replace("panier.php?", "spip.php?page=panier&", $in_thelia);
	$in_thelia = str_replace("produit.php?", "spip.php?page=produit&", $in_thelia);
	$in_thelia = str_replace("regret.php?", "spip.php?page=regret&", $in_thelia);
	$in_thelia = str_replace("rubrique.php?", "spip.php?page=rubrique_thelia&", $in_thelia);
	$in_thelia = str_replace("virement.php?", "spip.php?page=virement&", $in_thelia);

	$in_thelia = str_replace("adresse.php", "spip.php?page=adresse", $in_thelia);
	$in_thelia = str_replace("cheque.php", "spip.php?page=cheque", $in_thelia);
	$in_thelia = str_replace("commande.php", "spip.php?page=commande", $in_thelia);
	$in_thelia = str_replace("commande_detail.php", "spip.php?page=commande_detail", $in_thelia);
	$in_thelia = str_replace("commande_visualiser.php", "spip.php?page=commande_visualiser", $in_thelia);
	$in_thelia = str_replace("compte_modifier.php", "spip.php?page=compte_modifier", $in_thelia);
	$in_thelia = str_replace("compte_modifiererr.php", "spip.php?page=compte_modifiererr", $in_thelia);
	$in_thelia = str_replace("connexion.php", "spip.php?page=connexion", $in_thelia);
	$in_thelia = str_replace("creercompte.php", "spip.php?page=creercompte", $in_thelia);
	$in_thelia = str_replace("imgpop.php", "spip.php?page=imgpop", $in_thelia);
	$in_thelia = str_replace("livraison_adresse.php", "spip.php?page=livraison_adresse", $in_thelia);
	$in_thelia = str_replace("mdpoublie.php", "spip.php?page=mdpoublie", $in_thelia);
	$in_thelia = str_replace("merci.php", "spip.php?page=merci", $in_thelia);
	$in_thelia = str_replace("moncompte.php", "spip.php?page=moncompte", $in_thelia);
	$in_thelia = str_replace("nouveau.php", "spip.php?page=nouveau", $in_thelia);
	$in_thelia = str_replace("panier.php", "spip.php?page=panier", $in_thelia);
	$in_thelia = str_replace("produit.php", "spip.php?page=produit", $in_thelia);
	$in_thelia = str_replace("regret.php", "spip.php?page=regret", $in_thelia);
	$in_thelia = str_replace("rubrique.php", "spip.php?page=rubrique_thelia", $in_thelia);
	$in_thelia = str_replace("virement.php", "spip.php?page=virement", $in_thelia);

	//iso vers utf8
	$in_thelia = str_replace('é', '&eacute;', $in_thelia);
	$in_thelia = str_replace('è', '&egrave;', $in_thelia);
	$in_thelia = str_replace('à', '&agrave;', $in_thelia);
	$in_thelia = str_replace('ê', '&ecirc;', $in_thelia);
	$in_thelia = str_replace('î', '&icirc;', $in_thelia);
	$in_thelia = str_replace('ï', '&iuml;', $in_thelia);
	$in_thelia = str_replace('Î', '&Icirc;', $in_thelia);
	$in_thelia = str_replace('É', '&Eacute;', $in_thelia);
	$in_thelia = str_replace('ç', '&ccedil;', $in_thelia);
	$in_thelia = str_replace('ô', '&ocirc;', $in_thelia);
	$in_thelia = str_replace('ë', '&euml;', $in_thelia); 

	return $in_thelia;
}
?>