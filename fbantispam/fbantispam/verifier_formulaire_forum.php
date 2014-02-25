<?php

/**
 * Plugin FB Antispam
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 */
function fbantispam_verifier_formulaire_forum_dist($flux){
	$ret = array();
	$form = $flux['args']['form'];
	if ($form == "forum")
	{
		$texte = _request('texte');
		$captcha = _request('captcha');
		$cp0 = _request('c1');
		$cp1 = _request('c0');
		$cp2 = _request('c2');
		$cp3 = _request('c3');
		$cps = "$cp0"."$cp1"."$cp2"."$cp3";
		include_spip("inc/fbantispam");

		if ($captcha != $cps) 
		{
			$ret['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
		}
	}

	return $ret;
}
