<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_mes_fichiers_sauver() {

	// Securisation: aucun argument attendu

	// Autorisation
	if(!autoriser('sauvegarder','mes_fichiers')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$liste = _request('a_sauver');
	if(is_null($liste))
		$liste = array();

	$sauver = charger_fonction('mes_fichiers_sauver','inc');

	$erreur = $sauver($liste);

	if (_request('redirect')) {
		if($erreur){
			$redirect = parametre_url(urldecode(_request('redirect')),
			'etat', 'nok_sauve', '&');
		}else{
			$redirect = parametre_url(urldecode(_request('redirect')),
			'etat', 'ok_sauve', '&');
		}
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return $erreur;

}
?>