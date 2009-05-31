<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function action_w3cgh_selectionne_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$validateurs = _request('validateurs');
	if (!is_array($validateurs)) $validateurs=array();
	
	ecrire_meta('w3cgh_validateurs_actifs',serialize($validateurs));
	ecrire_metas();
	
	$redirect = urldecode(_request('redirect'));
	if ($redirect)
		redirige_par_entete($redirect);
}

?>