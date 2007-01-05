<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_w3cgh_reset_test_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$nom = $arg;
	include_spip("inc/validateur_api");
	validateur_reset_tests($nom);	

	$redirect = urldecode(_request('redirect'));
	if ($redirect)
		redirige_par_entete($redirect);
}

?>