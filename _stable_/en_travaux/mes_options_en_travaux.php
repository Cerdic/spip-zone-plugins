<?php
	
	include_spip('inc/vieilles_defs');
	
	// fichier mes options
	// force une action en travaux si on n'est pas en zone ecrire ni admin
if ($GLOBALS['meta']['en_travaux']=='true')
{
	// tentative pour prendre en compte tous les cas possibles
	// penser a ajouter le test qui verifie si on est un admin pour faire propre voir ou le caser 
	$en_travaux_mode_admin = false;
	$en_travaux_mode_admin |= strlen(strstr($_SERVER["PHP_SELF"],'/ecrire'))>0;
	$en_travaux_mode_admin |= isset($page) && ($page == 'login');
	$en_travaux_mode_admin |= isset($_GET['action']);
	$en_travaux_mode_admin |= isset($_POST['action']);
	$en_travaux_mode_admin |= $_GET['page'] == 'style_prive'; // filtrage de la feuille de style admin mise en squelette
	$en_travaux_mode_admin |= $_GET['page'] == 'style_prive_ie'; // idem IE
	$en_travaux_mode_admin |= $_GET['page'] == 'jquery.js';   // filtrage de jquery qui sert pour la partie admin
	
	if ($en_travaux_mode_admin) {
		// ne rien faire si zone ecrire 
	}
	else {
		// dans tous les autres cas on force l'execution de l'affichage
		$_GET['action']="en_travaux";
	}
}

function action_en_travaux(){
	include_spip('inc/minipres');
	include_spip('inc/charsets');
	include_spip('inc/texte');
	$corps = charset2unicode(propre($GLOBALS['meta']['en_travaux_message']));
	$page = minipres(_T('info_travaux_titre'), $corps);
	// a partir de spip 1.9.2 ces fonctions ne font plus l'echo directement
	if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) echo $page;
	return true;
}
?>
