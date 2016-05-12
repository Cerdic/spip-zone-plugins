<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function lier_villebe($id_villebe, $objet, $id_objet) {
	include_spip('action/editer_liens');

	$res = objet_associer(
		array('villes_belge' => $id_villebe),
		array($objet => $id_objet)
	);

	return $res;
}
