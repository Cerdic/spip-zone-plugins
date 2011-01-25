<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_spip_actualiser_dist($last) {

	include_spip('inc/utils');
	include_spip('inc/deboussoler');

	// On verifie que la boussole SPIP est bien ajoutee
	$meta_boussole = 'boussole_infos_spip';
	if (lire_meta($meta_boussole)) {
		// On appelle donc la fonction d'actualisation
		if (!$url = boussole_localiser_xml('', 'standard')) {
			// Le fichier est introuvable
			spip_log("ERREUR ACTUALISATION CRON : fichier xml introuvable", 'boussole');
		}
		else {
			if (!boussole_valider_xml($url, $erreur)) {
				// Le fichier ne suit pas la DTD (boussole.dtd)
				spip_log("ERREUR ACTUALISATION CRON : fichier xml invalide", 'boussole');
			}
			else {
				// On insere la boussole dans la base
				// et on traite le cas d'erreur fichier ($retour['message_erreur']) non conforme
				// si c'est encore possible apres avoir valide le fichier avec la dtd
				$ok = boussole_ajouter($url, $message);
			
				// Determination des messages de retour
				if (!$ok) {
					spip_log("ERREUR ACTUALISATION CRON : " . $message, 'boussole');
				}
				else {
					spip_log("ACTUALISATION CRON OK", 'boussole');
				}
			}
		}
	}

	return 1;
}

?>
