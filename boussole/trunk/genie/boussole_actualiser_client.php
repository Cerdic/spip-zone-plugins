<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_client_dist($last) {

	include_spip('inc/deboussoler');

	// Recherche des metas commençant par "boussole_infos" pour connaitre la liste des boussoles ajoutées par le client
	$boussoles_ajoutees = sql_allfetsel('valeur', 'spip_meta', array('nom LIKE ' . sql_quote('boussole_infos%')));
	if ($boussoles_ajoutees) {
		$infos = array_map('unserialize', array_map('reset', $boussoles_ajoutees));
		foreach($infos as $_infos) {
			list($ok, $message) = boussole_ajouter($_infos['alias'], $_infos['serveur']);
			if (!$ok)
				spip_log("Actualisation périodique en erreur (boussole = " . $_infos['alias'] . ") : " . $message, 'boussole' . _LOG_ERREUR);
			else
				spip_log("Actualisation périodique ok (boussole = " . $_infos['alias'] . ")", 'boussole' . _LOG_INFO);
		}
	}

	return 1;
}

?>
