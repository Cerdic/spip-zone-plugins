<?php
	// fichier mes options
	// force une action en travaux si on n'est pas en zone ecrire ni admin
if ($GLOBALS['meta']['en_travaux']=='true')
{
	// tentative pour prendre en compte tous es cas possible
	// penser � ajouter le test qui v�rifie si on est un admin pour faire propre voir o� le caser 
	$en_travaux_mode_admin = (false);
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR (strlen(strstr($_SERVER["PHP_SELF"],'/ecrire'))>0));
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR (isset($page) && $page=='login'));
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR isset($_GET['action']));
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR isset($_POST['action']));
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR $_GET['page']== 'style_prive'); // filtrage de la feuille de style admin mise en squelette
	$en_travaux_mode_admin = ($en_travaux_mode_admin OR $_GET['page']== 'jquery.js');   // filtrage de jquery qui sert pour la partie admin
	
	if ($en_travaux_mode_admin) {
		// ne rien faire si zone ecrire 
	}
	else {
		$_GET['action']="en_travaux";
		//dans tous les autres cas on force l'execution de l'affichage
	}
}
function action_en_travaux(){
	$texte = trim($GLOBALS['meta']['en_travaux_message']);
	if (!strlen($GLOBALS['meta']['en_travaux_message'])) $texte=_T('info_travaux_titre');

	global $spip_version;
	include_spip('inc/minipres');
	$page = install_debut_html($texte).install_fin_html();
	if ($spip_version>=1.92) echo $page; // a partir de spip 1.9.2 ces fonctions ne font plus l'echo directement

	return true;
}
?>
