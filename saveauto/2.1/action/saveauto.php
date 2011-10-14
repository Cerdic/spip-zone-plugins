<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Action de génération de sauvegardes
 */
function action_saveauto_dist(){

	/**
	 * Erreur si l'on n'est pas autorisé à sauvegarder
	 */
	if(!autoriser('sauvegarder','saveauto')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	$err = '';

	$sauver = charger_fonction('saveauto','inc');
	$err = $sauver();

	$redirect = _request('redirect') ? _request('redirect') : self();
	if($redirect){
		if(!$err)
			$err = '';

		$redirect = parametre_url($redirect,'err',$err,'&');
		redirige_par_entete($redirect);
	}
}
?>