<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/autoriser');
include_spip('inc/formidable');
include_spip('inc/formidable_fichiers');
/**
 * Récupère, si on est autorisé à voir la réponse du formulaire, 
 * un fichier 
 * et l'envoi en http
 **/
function action_formidable_recuperer_fichier() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = unserialize($arg);
	
	// test si autorisation de voir la réponse par cookie
	$cookie_ok = False;
	if (isset($arg['cookie'])) {
		$nom_cookie = formidable_generer_nom_cookie($arg['formulaire']);
		if (isset($_COOKIE[$nom_cookie])) {
			if ($arg['cookie'] == sha1($_COOKIE[$nom_cookie].secret_du_site())) {
				$cookie_bdd = sql_getfetsel('cookie', 'spip_formulaires_reponses', 'id_formulaires_reponse='.sql_quote($arg['reponse']));
				if ($cookie_bdd = $_COOKIE[$nom_cookie]) {
					$cookie_ok = True;
				}
			}
		}
	}
	if (autoriser('voir', 'formulairesreponse', $arg['formulaire']) or $cookie_ok) {
		$chemin_fichier = _DIR_FICHIERS_FORMIDABLE
			."formulaire_".$arg['formulaire'] 
			."/reponse_".$arg['reponse']
			."/".$arg['saisie']
			."/".$arg['fichier'];
		if (@file_exists($chemin_fichier)){
			$f = $arg['fichier'];
			formidable_retourner_fichier($chemin_fichier, $f);
		}
		else {
			include_spip('inc/minipres');
			echo minipres(_T("formidable:erreur_fichier_introuvable"));
		}
	} else {
		include_spip('inc/minipres');
    echo minipres();
	}

	exit;	

}

