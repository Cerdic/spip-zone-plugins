<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/dump');
include_spip('inc/autoriser');

/**
 * Telecharger un dump quand on est webmestre
 * (adaptation de plugins_dist/dump/action/telecharger_dump.php de SPIP 3.0.17)
 * @param string $arg
*/
function action_acs_telecharger_dump_dist($arg=null){
	if (!$arg) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$file = _DIR_DUMP.'acs/'.basename($arg,'.php').'.php';

	if (file_exists($file) AND autoriser('webmestre')) {
		$f = basename($file);
		// ce content-type est necessaire pour eviter des corruptions de zip dans ie6
		header('Content-Type: application/octet-stream');

		header("Content-Disposition: attachment; filename=\"$f\";");
		header("Content-Transfer-Encoding: binary");

		// fix for IE catching or PHP bug issue
		header("Pragma: public");
		header("Expires: 0"); // set expiration time
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		if ($cl = filesize($file))
			header("Content-Length: ". $cl);
		readfile($file);
	}
	else{
		http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur').' 404',
				_T('info_acces_interdit'));
	}

	// et on finit comme ca d'un coup
	exit;
}
?>