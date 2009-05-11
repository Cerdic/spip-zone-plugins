<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_mes_fichiers_sauver() {

	// SŽcurisation: aucun argument attendu
 
	// Autorisation
	if(!autoriser('sauvegarder','mes_fichiers')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// Contenu de la sauvegarde	
	include_spip('inc/pclzip');
	include_spip('inc/mes_fichiers_utils');
	$liste = _request('a_sauver');
	spip_log('*** MES_FICHIERS (action_mes_fichiers_sauver) :');
	spip_log($liste);

	// Archivage du contenu
	if (!@is_dir(_DIR_MES_FICHIERS))
		$dir = sous_repertoire(_DIR_TMP,"mes_fichiers");
	$mes_fichiers = new PclZip(_DIR_MES_FICHIERS . 'mf2_'.date("Ymd_His").'.zip');
	$erreur = $mes_fichiers->create($liste, PCLZIP_OPT_COMMENT, serialize($liste));
	if ($erreur == 0) {
		spip_log('*** MES_FICHIERS (action_mes_fichiers_sauver) : erreur '.$mes_fichiers->errorInfo(true));
		redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=nok_sauve', true));
	}

	// Redirection vers la page mes_fichiers avec l'Žtat ok
	redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=ok_sauve', true));
}
?>