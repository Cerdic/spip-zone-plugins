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

	if ($erreur) {
		spip_log('*** MES_FICHIERS (action_mes_fichiers_sauver) ERREUR '.$erreur,'test');
		redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=nok_sauve', true));
	}else{
		redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=ok_sauve', true));
	}

}
?>