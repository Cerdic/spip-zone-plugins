<?php
// force une action en travaux si on n'est pas en zone ecrire ni admin

// tentative pour prendre en compte tous les cas possibles d'exception
$exceptions = 	
	(_en_travaux_ADMIN == 1 && $GLOBALS['auteur_session']['statut'] == '0minirezo')
	|| (strpos($_SERVER["PHP_SELF"],'/ecrire') !== false)
	|| isset($_GET['action'])
	|| isset($_POST['action'])
	|| ($_GET['page'] == 'login')
	|| ($_GET['page'] == 'style_prive') // filtrage de la feuille de style admin mise en squelette
	|| (strpos($_GET['page'],'.js') !== false) // filtrage de jquery.js par exemple qui sert pour la partie admin
	|| (strpos($_GET['page'],'.css') !== false); // on sait jamais...

// si ya pas d'exception, on force bloque le site pour travaux
if (!$exceptions)
	$_GET['action'] = "cs_travaux";

// nettoyage
unset($exceptions);

function action_cs_travaux(){
	include_spip('inc/minipres');
	include_spip('inc/charsets');
	include_spip('inc/texte');
	$login = '';
	if(_en_travaux_ADMIN==1) {
		include_spip('public/assembler');
		include_spip('balise/login_public');
		$login = inclure_balise_dynamique(balise_LOGIN_PUBLIC_dyn('', ''), false);
		if(strlen($login))
			$login = "<div style='text-align:left'><b>"._T('cout:acces_admin')."</b></div><hr><div style='margin-left:4em;'>$login</div>";
		else $login = '<br/><hr/><b>'._T('cout:reserve_admin').'</b>';
	}
	$page = minipres(
		defined('_en_travaux_TITRE')?_T('info_travaux_titre'):$GLOBALS['meta']['nom_site'],
		charset2unicode(propre(_en_travaux_MESSAGE)) . $login
	);
	// a partir de spip 1.9.2 ces fonctions ne font plus l'echo directement
	if ($GLOBALS['spip_version']>=1.92) echo $page;
	return true;
}
?>