<?php
function spip_thelia_appeler_moteur_thelia($texte)
{
	if ($_REQUEST['exec']!="") return $texte;
	if ($_REQUEST['page'] == "forum") return $texte;	
	include_once("classes/Navigation.class.php");

	session_start();

	foreach ($_POST as $key => $value) $$key = $value;
	foreach ($_GET as $key => $value) $$key = $value;
	switch($page){
		case 'panier' : $securise=1; $pageret=1; break;
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

	
	include_once(_DIR_PLUGINS."plugin-thelia/moteur-thelia-1_3_3.php");
}
?>