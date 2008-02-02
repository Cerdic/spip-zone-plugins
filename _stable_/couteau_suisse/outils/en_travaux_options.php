<?php
// force une action en travaux si on n'est pas en zone ecrire ni admin

// tentative pour prendre en compte tous les cas possibles d'exception
$exceptions = 	
	(_en_travaux_ADMIN == 1 && $GLOBALS['auteur_session']['statut'] == '0minirezo')
	|| (strpos($_SERVER["PHP_SELF"],'/ecrire') !== false)
	|| isset($_GET['action'])
	|| isset($_POST['action'])
	|| in_array($_GET['page'], array('login',
		'style_prive',       // filtrage de la feuille de style admin mise en squelette
		'style_prive_ie'))   // idem IE
	|| (strpos($_GET['page'],'.js') !== false) // filtrage de jquery.js par exemple qui sert pour la partie admin
	|| (strpos($_GET['page'],'.css') !== false); // on sait jamais...

// si ya pas d'exception, on bloque le site pour travaux
if (!$exceptions)
	$_GET['action'] = "cs_travaux";

// nettoyage
unset($exceptions);

function action_cs_travaux(){
	include_spip('public/assembler');
	echo recuperer_fond('fonds/en_travaux', array(
		'message'=>_en_travaux_MESSAGE, 
		'titre'=>defined('_en_travaux_TITRE')?_T('info_travaux_titre'):$GLOBALS['meta']['nom_site'],
		'login'=>_en_travaux_ADMIN==1?'oui':'',
	));
	return true;
}
?>