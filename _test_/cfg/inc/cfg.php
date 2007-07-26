<?php


// Renvoie la liste des configurations disponibles dans le path
function liste_cfg() {
	// Faire la liste des éléments qui ont un cfg ; ca peut etre des plugins
	// mais aussi des squelettes ou n'importe quoi
	$liste = array();
	foreach (creer_chemin() as $dir) {
		if (basename($dir) != 'cfg')
			$liste =
				array_merge($liste, preg_files($dir.'fonds/', '/cfg_.*html$'));
	}

	if ($liste) {
		$l = array();
		foreach($liste as $cfg) {
			$fonds = substr(basename($cfg,'.html'),4);
			$l[$fonds] = $cfg;
		}
		ksort($l);
		return $l;
	}
}

// Renvoie une icone avec un lien vers la page de configuration d'un repertoire
// donne
function icone_lien_cfg($dir) {
	$ret = '';
	if (basename($dir) != 'cfg')
	if ($l = preg_files($dir.'/fonds/', '/cfg_.*html$')) {
		foreach($l as $cfg) {
			$fonds = substr(basename($cfg,'.html'),4);
			$ret .= '<a href="'.generer_url_ecrire('cfg', 'cfg='.$fonds).'">'
				.'<img src="'._DIR_PLUGIN_CFG.'cfg-16.png"
					width="16" height="16"
					alt="'._L('configuration').' '.$fonds.'"
					title="'._L('configuration').' '.$fonds.'"
				/></a>';
		}
	}

	return $ret;
}

?>
