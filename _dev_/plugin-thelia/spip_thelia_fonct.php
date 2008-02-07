<?php
function spip_thelia_header_prive($texte) {
	if (!file_exists("../fonctions/moteur.php")) 
		echo ("erreur : th&eacute;lia introuvable, v&eacute;rifiez que les sous-r&eacute;pertoires de th&eacute;lia et spip sont dans le m&ecirc;me r&eacute;pertoire.");
	if (!function_exists('lire_config'))
		echo ("erreur : le plugin CFG est n'est pas install&eacute;.");
	return $texte;
}

function spip_thelia_appeler_moteur_thelia($texte)
{
	//récupérer la version de thélia : valeurs possibles :
	//	- supérieure ou égale à 1.3.4 = "1_3_4" (par défaut)  
	//	- 1.3.3 = "1_3_3"

	if (function_exists('lire_config')) {
		$version_thelia = lire_config("spip_thelia/version_thelia_spip_thelia", "after_1_3_4");
	} else { 
		echo ("erreur : le plugin CFG est n'est pas install&eacute;.");
		return $texte;
	}
	
	//si pas de boucle ou de balise thélia dans la page on sort	
	if ((strpos($texte, "THELIA-") === FALSE) && (strpos($texte, "<THELIA") == FALSE))
		return $texte;
	
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
	
	//conflit sur la variable $page. 
	global $page;
	$page = new stdclass;
	$page = "";
	
	
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
	ob_start();
	
	if ($version_thelia == "1_3_3") {
		//si version = 1.3.3 : surcharge dans le plugin
		include_once(_DIR_PLUGIN_SPIP_THELIA."moteur-thelia-1_3_3.php");
	} else {
		//si version >= 1.3.4 : plus de surcharge dans le plugin, on appelle directement le moteur de Thélia
		include_once("fonctions/moteur.php");
	}

	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	return $texte;	
	
}

function remplacement_sortie_thelia($in_thelia)
{
    	//renommage action en thelia_action. méthode provisoire à revoir.
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
function spip_thelia_ajouter_boutons($boutons_admin){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo") {
	  	// on voit le bouton dans la barre "naviguer"
	  	$boutons_admin['naviguer']->sousmenu['spip_thelia_catalogue']= new Bouton(
		_DIR_PLUGIN_SPIP_THELIA.'img_pack/logo_thelia_petit.png', 'Catalogue Th&eacute;lia');
	}
	return $boutons_admin;
}
?>