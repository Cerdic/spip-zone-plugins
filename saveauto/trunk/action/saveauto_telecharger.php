<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_saveauto_telecharger() {

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!@is_readable($arg)) {
		$redirect = urldecode(_request('redirect'));
		if($redirect){
			$redirect = parametre_url($redirect,'etat','nok_tele');
		}else{
			return false;
		}
	}

	// Autorisation
	if(!autoriser('sauvegarder','mes_fichiers')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// Telechargement du fichier
	header("Content-type: application/force-download;");
	header("Content-Transfer-Encoding: application/zip");
	header("Content-Length: ".filesize($arg));
	header("Content-Disposition: attachment; filename=\"".basename($arg)."\"");
	header("Pragma: no-cache");
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
	readfile($arg);

	if($redirect = _request('redirect')){
		$redirect = parametre_url(urldecode($redirect),'etat','ok_tele','&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	return;
}
?>