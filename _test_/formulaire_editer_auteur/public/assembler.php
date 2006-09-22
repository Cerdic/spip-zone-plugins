<?php

include(_DIR_RESTREINT.'/public/assembler.php');

function public_assembler($fond) {
	  global $auteur_session, $forcer_lang, $ignore_auth_http,
	  $var_confirm, $var_mode;

	// multilinguisme
	if ($forcer_lang AND ($forcer_lang!=='non') AND !count($_POST)) {
		include_spip('inc/lang');
		verifier_lang_url();
	}
	if (isset($_GET['lang'])) {
		include_spip('inc/lang');
		lang_select($_GET['lang']);
	}

	// Si envoi pour un forum, enregistrer puis rediriger
	if (isset($_POST['confirmer_forum'])
	OR (isset($_POST['ajouter_mot']) AND $GLOBALS['afficher_texte']=='non')) {
		$f = charger_fonction('forum_insert', 'inc');
		redirige_par_entete($f());
	}

	// Si envoi pour un forum, enregistrer puis rediriger
	if (isset($_POST['modifier_auteur'])) {
		$f = charger_fonction('auteur_update', 'inc');
		redirige_par_entete($f());
	}

	// si signature de petition, l'enregistrer avant d'afficher la page
	// afin que celle-ci contienne la signature
	if (isset($_GET['var_confirm'])) {
		include_spip('balise/formulaire_signature');
		reponse_confirmation($var_confirm);
	}

	//  refus du debug si l'admin n'est pas connecte
	if ($var_mode=='debug') {
		if ($auteur_session['statut'] == '0minirezo')
			spip_log('debug !');
		else
			redirige_par_entete(generer_url_public('login',
			'url='.rawurlencode(
			parametre_url(self(), 'var_mode', 'debug', '&')
			), true));
	}

	return assembler_page ($fond);
}

?>