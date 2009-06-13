<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

include_spip('inc/filtres');

/**
 * Verification de la validite des donnees saisies et postees par #FORMULAIRE_INVITER_AMI
 *
 * @return array $erreurs
 */
function formulaires_inviter_ami_verifier_dist(){

	$erreurs = array();
	foreach(array('email') as $obli)
		if (!_request($obli))
			$erreurs[$obli] = (isset($erreurs[$obli])?$erreurs[$obli]:'') . _T('formulaires:info_obligatoire_rappel');

	if ($e=_request('email')){
		if (!email_valide($e))
			$erreurs['email'] = (isset($erreurs['email'])?$erreurs['email']:'') . _T('formulaires:email_invalide');
	}

	return $erreurs;
}

?>