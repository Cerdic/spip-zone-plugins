<?php

//echo '<pre>' . print_r($GLOBALS['meta'], 1).'</pre>';
//exit(0);


function recuperer_toutes_les_compositions($informer=false)
{
	include_spip('inc/compositions');
	$rep_compositions = compositions_chemin();
	$match = "/([a-z]+)("._COMPOSITIONS_MATCH.")?[.]html$";
	$liste = find_all_in_path($rep_compositions, $match);

	$res = array();
	if (count($liste)){
		foreach($liste as $s) {
			$base = preg_replace(',[.]html$,i','',$s);
			if (preg_match(",$match,ims",$s,$regs))
				$composition = compositions_charger_infos($base);
				if ($composition)
					$res[$regs[1]][$regs[3]] = $composition;
				else if (''!=$regs[3])
					$res[$regs[1]][$regs[3]] = array('nom' => $regs[3]);
			// retenir les skels qui ont un xml associe
		}
	}
	return $res;
}

function recuperer_compositions_sans_base()
{
	include_spip('inc/compositions');
	$rep_compositions = compositions_chemin();
	$match = "/([a-z]+)("._COMPOSITIONS_MATCH.")?[.]html$";
	$liste = find_all_in_path($rep_compositions, $match);

	$res = array();
	if (count($liste)){
		foreach($liste as $s) {
			$base = preg_replace(',[.]html$,i','',$s);
			if (preg_match(",$match,ims",$s,$regs))
				if (!($composition = compositions_charger_infos($base)))
				if (''!=$regs[3])
					$res[$regs[1]][$regs[3]] = array('nom' => $regs[3], 'base' => $base);
			// retenir les skels qui n'ont pas de xml associe
		}
	}
	return $res;	
}

?>