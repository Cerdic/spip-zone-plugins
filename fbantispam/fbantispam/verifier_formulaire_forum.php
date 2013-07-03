<?php

/**
 * Plugin FB Antispam
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 */
function fbantispam_verifier_formulaire_forum_dist($flux){
	// echo "<h2>fbantispam_verifier_formulaire_forum_dist</h2>";
	$ret = array();
	$form = $flux['args']['form'];
	// echo "<pre>";print_r($flux);echo "</pre>";
	if ($form == "forum")
	{
		$texte = _request('texte');
		$captcha = _request('captcha');
		$cp0 = _request('c0');
		$cp1 = _request('c1');
		$cp2 = _request('c2');
		$cp3 = _request('c3');
		$cps = "$cp0"."$cp1"."$cp2"."$cp3";
		include_spip("inc/fbantispam");

		if ($captcha != $cps) 
		{
			// echo "<h2>fbantispam_verifier_formulaire_forum_dist ERROR CAPTCHA=$captcha code=$cps</h2>";
			$ret['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
		}
		else
		{
		}
	}

	return $ret;
}
