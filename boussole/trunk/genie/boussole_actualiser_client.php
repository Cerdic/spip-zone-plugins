<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_client_dist($last) {

	include_spip('inc/utils');
	include_spip('inc/deboussoler');

	// On verifie que la boussole SPIP est bien ajoutee
	$meta_boussole = 'boussole_infos_spip';
	if (lire_meta($meta_boussole)) {
		// On appelle donc la fonction d'actualisation
		$xml = 'http://zone.spip.org/trac/spip-zone/export/HEAD/_galaxie_/boussole.spip.org/boussole_spip.xml';
		if (!$url = boussole_localiser_xml($xml)) {
			// Le fichier est introuvable
			spip_log("ERREUR ACTUALISATION CRON : fichier xml introuvable", 'boussole' . _LOG_ERREUR);
		}
		else {
			if (!boussole_valider_xml($url, $erreur)) {
				// Le fichier ne suit pas la DTD (boussole.dtd)
				spip_log("ERREUR ACTUALISATION CRON : fichier xml invalide", 'boussole' . _LOG_ERREUR);
			}
			else {
				// On insere la boussole dans la base
				// et on traite le cas d'erreur fichier ($retour['message_erreur']) non conforme
				// si c'est encore possible apres avoir valide le fichier avec la dtd
				list($ok, $message) = boussole_ajouter($url);
			
				// Determination des messages de retour
				if (!$ok) {
					spip_log("ERREUR ACTUALISATION CRON : " . $message, 'boussole' . _LOG_ERREUR);
				}
				else {
					spip_log("ACTUALISATION CRON OK", 'boussole' . _LOG_INFO);
				}
			}
		}
	}

	return 1;
}

?>
