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
	
	include_once(_DIR_PLUGINS."plugin-thelia/moteur-thelia-1_3_3.php");
	
}
?>