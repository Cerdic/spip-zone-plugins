<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function saisie_case_vue($saisie) {
	$saisie = unserialize($saisie);
	if ($saisie['valeur_non'] or $saisie['valeur_oui']) {
		if ($saisie['valeur'] == $saisie['valeur_oui']) {
			return $saisie['valeur'];
		} elseif ($saisie['valeur'] == $saisie['valeur_non']) {
			return $saisie['valeur'];
		}
	} elseif ($saisie['valeur']) {
		return _T('item_oui');
	} else {
		return _T('item_non');
	}
}
