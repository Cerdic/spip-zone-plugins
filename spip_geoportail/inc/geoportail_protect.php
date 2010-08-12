<?php

/** Verifier que l'action est securisee **/
function geoportail_good_referer($action=null)
{	/*
	$site = $GLOBALS['meta']['adresse_site'];
	// Test sur HTTP_REFERER (! peu fiable : peut facilement etre modifiee par le client)
	if (!substr ($_SERVER['HTTP_REFERER'], 0, strlen($site)) == $site) return false;
	*/
	// Verifier si action OK
	if ($action)
	{	charger_fonction('securiser_action','inc');
		return (verifier_action_auteur ($action,_request('hash')));
	}
	return true;
}

?>