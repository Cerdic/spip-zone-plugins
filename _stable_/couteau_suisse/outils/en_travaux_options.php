<?php
// force une action en travaux si on n'est pas en zone ecrire ni admin

// tentative pour prendre en compte tous les cas possibles d'exception
$exceptions = 	
	(_en_travaux_ADMIN == 1 && cout_autoriser())
	|| (strpos($_SERVER["PHP_SELF"],'/ecrire') !== false)
	|| isset($_GET['action'])
	|| isset($_POST['action'])
//	|| ($_POST['formulaire_action']=='login') // TODO : formulaire SPIP 2.0
	|| in_array($_GET['page'], array('login',
		'style_prive',       // filtrage de la feuille de style admin mise en squelette
		'style_prive_ie'))   // idem IE
	|| (strpos($_GET['page'],'.js') !== false) // filtrage de jquery.js par exemple qui sert pour la partie admin
	|| (strpos($_GET['page'],'.css') !== false); // on sait jamais...

// si ya pas d'exception, on bloque le site pour travaux
if (!$exceptions)
	$_GET['action'] = "cs_travaux";
//echo'=';print_r($GLOBALS['auteur_session']);$controler_date_rss=true;
// nettoyage
unset($exceptions);

function action_cs_travaux(){
	include_spip('public/assembler');
	echo recuperer_fond(defined('_SPIP19300')?'fonds/en_travaux2':'fonds/en_travaux', array(
		'message'=>_en_travaux_MESSAGE, 
		'titre'=>defined('_en_travaux_TITRE')?_T('info_travaux_titre'):$GLOBALS['meta']['nom_site'],
		// SPIP 2.0 : suppression pour l'instant de la possibilite de se logger directement pour un admin
		// car les redacteurs pourraient acceder qd meme au site (1 seule page, mais 1 page de trop)
		 'login'=>_en_travaux_ADMIN==1?'oui':'',
		//'login'=>defined('_SPIP19300')?'':(_en_travaux_ADMIN==1?'oui':''),
	));
	return true;
}
?>