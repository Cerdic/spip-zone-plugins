<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_w3cgh_reset_test_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$nom = $securiser_action();

	$nom = explode("-",$nom);
	if (!is_array($nom)) $nom=array($nom);
	include_spip("inc/validateur_api");
	foreach($nom as $n)
		validateur_reset_tests($n);

	$redirect = urldecode(_request('redirect'));
	if ($redirect)
		redirige_par_entete($redirect);
}

?>