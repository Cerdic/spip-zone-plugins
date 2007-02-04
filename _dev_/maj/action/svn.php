<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_svn() {
	include_spip('inc/mise_a_jour');
	$spip_loader_liste = spip_loader_liste(_SPIP_MAJ_FILE);
	$paquet = _request('paquet');
	$elements_fichier = $spip_loader_liste[$paquet];

	define(
		'_URL_PAQUET_ZIP',
		$spip_loader_liste[$paquet][0]
	);
	define(
		'_DEST_PAQUET_ZIP',
		$spip_loader_liste[$paquet][1]
	);
	$redirect = 'spip.php?action=mise_a_jour&paquet='.$paquet.'&redirect='.generer_url_ecrire('mise_a_jour');
	$revision = $spip_loader_liste[$paquet][2];
	$user = $spip_loader_liste[$paquet][3];

	//creation du sous repertoire ?
	if(_DEST_PAQUET_ZIP != '' AND !is_dir(_DEST_PAQUET_ZIP)){
		mkdir_r(_DEST_PAQUET_ZIP);
	}	

	//svn
	$retour = update_svn(_URL_PAQUET_ZIP, _DEST_PAQUET_ZIP, $revision, $user);

	if ($redirect){
		redirige_par_entete($redirect);
	}
	exit;
}

// la fonction qui fait le travail
function update_svn($src, $dest, $rev = '', $user = '') {
	if (!preg_match(',^(https?|svn)://,', $src))
		return $src; // erreur

	// Checkout ?
	if (!file_exists($entries = "$dest/.svn/entries")) {
		$command = "checkout $src/ $dest/";
	}

	else {
		$entries = join("\n", file($entries));
		if (!preg_match(',\surl="([^"]+)",', $entries, $r))
			return _L("fichier .svn/entries non conforme ou illisible");
		$old_src = $r[1];

		// Switch ?
		if ($old_src != $src) {
			$command = "switch --relocate $old_src/ $src/ $dest/";
		}
		
		// Update
		else {
			if ($rev)
				$command = "update --revision $rev $dest/";
			else
				$command = "update $dest/";
		}
	}

	if ($command) {
		$command = _SVN_COMMAND." $user ".$command;
		$out = array();
		$return = false;
		$test = exec($command,$out, $return);
		array_unshift($out, $command, $return, $test);
		return $out;
	}

}

?>