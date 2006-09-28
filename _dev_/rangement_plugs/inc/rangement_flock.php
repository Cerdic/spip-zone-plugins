<?php
function rangement_plugs_preg_files_plugs($dir, $pattern=-1 /* AUTO */, $maxfiles = 10000, $recurs=array()) {
	$nbfiles = 0;
	if ($pattern == -1)
		$pattern = "^$dir";
	$fichiers = array();
	// revenir au repertoire racine si on a recu dossier/truc
	// pour regarder dossier/truc/ ne pas oublier le / final
// 	$dir = preg_replace(',/[^/]*$,', '', $dir);
// 	if ($dir == '') $dir = '.';

	if (@is_dir($dir) AND is_readable($dir) AND $d = @opendir($dir)) {
		while (($f = readdir($d)) !== false && ($nbfiles<$maxfiles)) {
			if ($f[0] != '.' # ignorer . .. .svn etc
			AND $f != 'CVS'
			AND $f != 'remove.txt'
			AND is_readable($f = "$dir/$f")) {
				if (is_file($f)) {
					if (preg_match(",$pattern,iS", $f))
					{
						$fichiers[] = $f;
						$nbfiles++;
					}
				} 
				else if (is_dir($f) AND is_array($recurs)){
					$rp = @realpath($f);
					if (!is_string($rp) OR !strlen($rp)) $rp=$f; # realpath n'est peut etre pas autorise
					if (!isset($recurs[$rp])) {
						$recurs[$rp] = true;
						$beginning = $fichiers;
						$end = preg_files("$f/", $pattern,
							$maxfiles-$nbfiles, $recurs);
						$fichiers = array_merge((array)$beginning, (array)$end);
						$nbfiles = count($fichiers);
					}
				}
			}
		}
		closedir($d);
	}
	else {
		spip_log("repertoire $dir absent ou illisible");
	}
	sort($fichiers);
	return $fichiers;
}
?>