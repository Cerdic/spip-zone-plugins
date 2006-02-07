<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_once("inc_forms_base.php");
define_once('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS . basename(dirname(__FILE__))));

//
// Formulaires
//

// A reintegrer dans echapper_html()
function forms_avant_propre($texte) {
	static $reset;
//echo "forms_avant_propre::";
	// Mecanisme de mise a jour des liens
	$forms = array();
	$maj_liens = ($GLOBALS['flag_ecrire'] AND $id_article = intval($_POST['id_article']));
	if ($maj_liens) {
		if (!$reset) {
			$query = "DELETE FROM spip_forms_articles WHERE id_article=$id_article";
			spip_query($query);
			$reset = true;
		}
	}

	// Remplacer les raccourcis de type <formXXX>
	if (is_int(strpos($texte, '<form')) &&
		preg_match_all(',<form(\d+)>,', $texte, $regs, PREG_SET_ORDER)) {
		include_once("inc_forms.php");
		foreach ($regs as $r) {
			$id_form = $r[1];
			$forms[$id_form] = $id_form;
			$cherche = $r[0];
			$remplace = "<html>".afficher_formulaire($id_form)."</html>";
			$texte = str_replace($cherche, $remplace, $texte);
		}
	}
	if ($maj_liens && $forms) {
		$query = "INSERT INTO spip_forms_articles (id_article, id_form) ".
			"VALUES ($id_article, ".join("), ($id_article, ", $forms).")";
		spip_query($query);
	}

	return $texte;
}

function generer_url_sondage($id_form) {
	return "plug.php?exec=sondage&id_form=$id_form";
}

// Hack crade a cause des limitations du compilateur
function _afficher_reponses_sondage($id_form) {
echo 'afficher_reponse';
	include_once("inc_forms.php");
	return afficher_reponses_sondage($id_form);
}


// Code a rapatrier dans inc-public et inc_forms
// (NB : le reglage du cookie doit se faire avant l'envoi de tout HTML au client)
function poser_cookie_sondage() {
	if ($id_form = intval($_POST['id_form'])) {
		$nom_cookie = 'spip_cookie_form_'.$id_form;
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!$cookie = $_COOKIE[$nom_cookie]) {
			include_ecrire("inc_session.php");
			$cookie = creer_uniqid();
		}
		$GLOBALS['cookie_form'] = $cookie; // pour utilisation dans inc_forms...
		// Expiration dans 30 jours
		setcookie($nom_cookie, $cookie, time() + 30 * 24 * 3600);
	}
}

if ($GLOBALS['ajout_reponse'] == 'oui' && $GLOBALS['ajout_cookie_form'] == 'oui')
	poser_cookie_sondage();

?>
