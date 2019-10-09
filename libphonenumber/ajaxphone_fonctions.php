<?php
/**
 * vérification des numéros internationaux
 * 
 * @plugin     libphonenumber for SPIP
 * @copyright  2019
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * (c) 2019 - Distribue sous licence GNU/GPL
 *
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function is_phone_ok($telephone,$pays){
	$newtelephone = '';
	$verifier = charger_fonction('verifier', 'inc');
	$erreur_telephone = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
	if ($erreur_telephone) {
			$newtelephone = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
	}
	return $newtelephone;
}