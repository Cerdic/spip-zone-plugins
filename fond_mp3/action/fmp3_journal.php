<?php 

	// action/fmp3_journal.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');
include_spip('inc/fmp3_api_journal');

/**
 * Ajax, renvoie le contenu du log
 * @return string
 */
function action_fmp3_journal () {
	
	fmp3_log("Appel action journal", null, _FMP3_DEBUG);
	
	global $connect_toutes_rubriques, $connect_login, $connect_statut, $spip_lang_rtl;
	
	if (!$connect_statut) {
		$auth = charger_fonction('auth', 'inc');
		$auth = $auth();
	}

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	$autoriser_lire = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	
	if($autoriser_lire) {

		fmp3_log("Suite action journal", null, _FMP3_DEBUG);
		
		$result = fmp3_journal_lire(_FMP3_PREFIX);
	
		echo($result);
		
	}
}

?>