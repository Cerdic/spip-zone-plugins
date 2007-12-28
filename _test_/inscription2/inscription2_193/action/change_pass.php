<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/minipres'); # charge lang et execute utiliser_lang
include_spip('inc/acces'); # pour generer_htpass
include_spip('public/assembler'); # pour calculer la page
include_spip('inc/filtres'); # pour email_valide()

// Ce fichier est celui d'une balise dynamique qui s'ignore.


// fonction qu'on peut redefinir pour filtrer les adresses mail 

function test_change_pass($email)
{
	if (!email_valide($email) ) 
		return _T('pass_erreur_non_valide', array('email_oubli' => htmlspecialchars($email)));
	return array('mail' => $email);
}

// http://doc.spip.org/@message_oubli
function message_change_pass($email, $param)
{
	if (function_exists('test_change_pass'))
		$f = 'test_change_pass';
	else 
		$f = 'test_oubli_dist';
	$declaration = $f($email);

	if (!is_array($declaration))
		return $declaration;

	$res = sql_select("id_auteur,statut,pass", "spip_auteurs", "email =" . _q($declaration['mail']));

	if (!$row = sql_fetch($res)) 
		return _T('pass_erreur_non_enregistre', array('email_oubli' => htmlspecialchars($email)));

	if ($row['statut'] == '5poubelle' OR $row['pass'] == '')
		return  _T('pass_erreur_acces_refuse');

	include_spip('inc/acces'); # pour creer_uniqid
	$cookie = creer_uniqid();
	sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=" . $row['id_auteur']);

	$nom = $GLOBALS['meta']["nom_site"];
	$envoyer_mail = charger_fonction('envoyer_mail','inc');

	if ($envoyer_mail($email,
			  ("[$nom] " .  _T('inscription2:pass_oubli_mot')),
			  _T('pass_mail_passcookie',
			     array('nom_site_spip' => $nom,
				   'adresse_site' => url_de_base(),
				   'sendcookie' => generer_url_action('change_pass', "$param=$cookie", true)))) )
	  return _T('inscription2:pass_recevoir_mail');
	else
	  return  _T('pass_erreur_probleme_technique');
}


// http://doc.spip.org/@formulaire_oubli_dyn
function formulaire_change_pass_dyn($p, $change_pass)
{

$message = '';

// au 3e appel la variable P est positionnee et oubli = mot passe.
// au 2e appel, P est vide et oubli vaut le mail a qui envoyer le cookie
// au 1er appel, P et oubli sont vides

 if (!$p) {
	  if ($change_pass) $message = message_change_pass($change_pass, 'p');
 } else {
	$res = sql_select("login", "spip_auteurs", "cookie_oubli=" . _q($p) . " AND statut<>'5poubelle' AND pass<>''");
	if (!$row = sql_fetch($res)) 
		$message = _T('pass_erreur_code_inconnu');
	else {
		if ($change_pass) {
			$mdpass = md5($change_pass);
			$htpass = generer_htpass($change_pass);
			sql_updateq('spip_auteurs', array('htpass' =>$htpass, 'pass'=>$mdpass, 'alea_actuel'=>'', 'cookie_oubli'=>''), "cookie_oubli=" . _q($p));

			$login = $row['login'];
			$message = "<b>" . _T('pass_nouveau_enregistre') . "</b>".
			"<p>" . _T('pass_rappel_login', array('login' => $login));
		}
	}
 }
 return array('formulaires/inscription2_changepass', 0, 
	      array('p' => $p,
		    'message' => $message,
		    'action' => generer_url_action('change_pass')));
}

function change_pass_debut_html($titre = '', $onLoad = '') {
	global $spip_lang_right,$spip_lang_left;
	
	utiliser_langue_visiteur();

	http_no_cache();

	if ($titre)
		$titre= "<h1>".$titre."<h1>";

	# le charset est en utf-8, pour recuperer le nom comme il faut
	# lors de l'installation
	if (!headers_sent())
		header('Content-Type: text/html; charset=utf-8');
	$dir_img_pack = _DIR_IMG_PACK;
	
	return  _DOCTYPE_ECRIRE.
		html_lang_attributes().
		"<head>\n".
		"<title>".
		textebrut($titre).
		"</title>
		<link rel='stylesheet' href='".find_in_path('minipres.css')."' type='text/css' media='all' />
		<link rel='stylesheet' href='".find_in_path('habillage.css')."' type='text/css' media='all' />
		<script type='text/javascript' src='" . _DIR_JAVASCRIPT . "spip_barre.js'></script>\n". // cet appel permet d'assurer un copier-coller du nom du repertoire a creer dans tmp (esj)
#	"<script type='text/javascript' src='" . _DIR_JAVASCRIPT . "jquery.js'></script>".
"</head>
<body".$onLoad.">
	<div id='minipres'><div>\n";
}

// http://doc.spip.org/@action_pass_dist
function action_change_pass_dist()
{
	utiliser_langue_visiteur();
	echo change_pass_debut_html("", " class='pass'");
	inclure_balise_dynamique(formulaire_change_pass_dyn(_request('p'), _request('change_pass')));
	echo install_fin_html();
}
?>
