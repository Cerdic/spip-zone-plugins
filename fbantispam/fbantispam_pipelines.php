<?php
/**
 * Plugin FB Antispam
 * (c) 2013 Fabio Bertagnin - FBServices - www.fbservices.fr
 * Inspiré de "nospam" de Cedric Morin pour www.yterium.net (http://www.spip-contrib.net/?rubrique1165)
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajouter le champ de formulaire 'nobot' au besoin
 *
 * @param array $flux
 * @return array
 */
function fbantispam_recuperer_fond($flux) {
	// echo "<pre>";print_r($flux);echo "</pre>";
	$fond = strval($flux['args']['fond']);
	if (false !== $pos = strpos($fond, 'formulaires/forum')) 
	{
		// on ajoute le champ 'nobot' si pas present dans le formulaire
		$texte = &$flux['data']['texte'];
		$pos = strpos($texte, '</form>');
		$nobot = recuperer_fond("inclure/nobot", array('nobot' => ''));
		$texte = substr_replace($texte, $nobot, $pos, 0);

		//$pos = strrpos($texte, '<p class="boutons">', -$pos);
		// On ajoute le champ 'captcha' avant le bouton de submit
		// On commence de la fin, pour se positionner dans le formulaire de saisie et non dans
		// celui de prévisualisation
		$pos = strrpos($texte, '<p class="boutons">', 0);
		// S'il n'a pas trouvé le bouton, on se positionne à la fin du formulaire (moins joli !)
		if (!$pos) $pos = strrpos($texte, '</form', 0);
		if ($pos)
		{
			$cp = fbantispam_get_captcha();
			$cps = "$cp[0]$cp[1]$cp[2]$cp[3]";
			$captcha = recuperer_fond("inclure/captcha", array('captcha' => $cps, 'c0' => "$cp[0]", 'c1' => "$cp[1]", 'c2' => "$cp[2]", 'c3' => "$cp[3]"));
			$texte = substr_replace($texte, $captcha, $pos, 0);
		}
	}
	return $flux;
}

/**
 */
function fbantispam_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	$je_suis_poste = false;
	if (isset($flux['args']['je_suis_poste']) && $flux['args']['je_suis_poste'] == 1) $je_suis_poste = true;
	if ($form == "forum" && !$je_suis_poste) 
	{
		include_spip("inc/fbantispam");
		if ($charger_formulaire = charger_fonction("charger_formulaire_forum", "fbantispam", true)) 
		{
			$flux = $charger_formulaire($flux);
		}
	}
	return $flux;
}

/**
 */
function fbantispam_formulaire_verifier($flux) {
	// echo "<h2>fbantispam_formulaire_verifier</h2>";
	// echo "<pre>";print_r($flux);echo "</pre>";
	$form = $flux['args']['form'];
	$previsu = false;
	if (isset($flux['data']['previsu']) && $flux['data']['previsu'] != '') $previsu = true;
	// echo "<h2>fbantispam_formulaire_verifier form=$form previsu=$previsu</h2>";

	$res = array();

	if ($form == "forum" && $previsu) 
	{
		
		$captcha = _request('captcha');
		$cp0 = _request('c0');
		$cp1 = _request('c1');
		$cp2 = _request('c2');
		$cp3 = _request('c3');
		$cps = "$cp0"."$cp1"."$cp2"."$cp3";
		// echo "<h4>fbantispam_formulaire_verifier VERIFIER... captcha=$captcha code=$cps</h4>";
		if ($captcha == '')
		{
			// echo "<h2>return erreur</h2>";
			$res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'a pas été saisi</p>';
			return $res;
		}
		if ($captcha != $cps) 
		{
			// echo "<h2>return erreur</h2>";
			$res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
			return $res;
			
		}
		$cmod = _request('cmod');
		if ($cmod != 'fbantispam') 
		{
			// echo "<h2>return erreur</h2>";
			$res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : message non accepté (identifié à un SPAM)</p>';
			return $res;
			
		}
	}
	return $flux;
}

function fbantispam_formulaire_traiter($flux) {
	return $flux;
}


/**
 */
function fbantispam_pre_edition($flux) {
	return $flux;
}

function fbantispam_get_captcha()
{
	$ret = array();
	for($i=0; $i<4; $i++)
	{
		$ret[$i] = rand(0,9);
	}
	return $ret;
}


?>
