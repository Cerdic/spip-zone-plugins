<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/autoriser');
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
	if (autoriser('voir', 'formulaires_reponse', $arg['formulaire'])) {
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

