<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/commun");

function cron_histo_archivage($time)  
{
	spip_log("archivage de la page d'historique...");
	$destdir = _DIR_PLUGIN_HA."/archives";
	
	// calcule la date au bout de laquelle on 
	// efface les fichiers, utilise la constante
	// NB_JOURS definies dans inc/commun.php
	$now = time();
	$troismois = $now - HA_NB_JOURS * 3600 * 24;
	$ds = date("Ymd", $troismois);

	// supprime tous les fichiers avant 3 mois
	$ft = "ar_".$ds.".txt";
	$d = dir($destdir);
	while (false !== ($entry = $d->read())) 
	{
		if (preg_match("/^ar_[0-9]*\.txt$/", $entry))
		{
			// comparaison alphanum, les noms des 
			// fichiers permettent cela
			if ($entry < $ft)
			{
				$fd = $destdir."/".$entry;
				spip_log("efface : ".$fd);
				unlink($fd);
			}
		}
	}

	// fabrique le fichier d'aujourd'hui en appelant la
	// page de squelette histo.html liee au plugin
	$dt = date("Ymd");
	$fn = $destdir."/ar_".$dt.".txt";
	$fdi = fopen("http://".$_SERVER["SERVER_NAME"]."/spip.php?page=histo&var_mode=recalcul&datelim=".$dt, "r");
	$fdo = fopen($fn, "w");

	$deb = false;
	while($line = fread($fdi, 1024))
	{
		// ne commence a tracer qu'au debut 
		// de la page (enleve le javascript etc.)
		if (($pos = strpos($line, "<div id=\"sep\"")) > 0)
		{
			if ($deb == false)
				$line = substr($line, $pos);
			$deb = true;
		}
		if ($deb)
			fwrite($fdo, $line);
	}

	fclose($fdo);
	fclose($fdi);

}

?>
