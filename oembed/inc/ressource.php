<?php

/*
 * transforme un raccourci de ressource en un joli html a embed
 *
 *
 */

define('_EXTRAIRE_RESSOURCES', ',' . '<"?(https?://|[\w][\w -]*\.[\w -]+).*>'.',UimsS');

function traiter_ressources($r) {
	if ($ressource = charger_fonction('ressource', 'inc', true)) {
		$html = $ressource($r[0]);
	} else {
		$html = htmlspecialchars($r[0]);
	}

	return $html;
}

function inc_ressource_dist($html) {
	return tw_traiter_ressources(array(0=>$html));
}

function tw_traiter_ressources($r) {
	$html = null;

	include_spip('inc/lien');
	$url = explode(' ', trim($r[0], '<>'));
	$url = $url[0];
	# <http://url/absolue>
	if (preg_match(',^https?://,i', $url)) {
		$html = PtoBR(propre("<span class='ressource spip_out'>&lt;[->" . $url . ']&gt;</span>'));
	} # <url/relative>
	else {
		if (false !== strpos($url, '/')) {
			$html = PtoBR(propre("<span class='ressource spip_in'>&lt;[->" . $url . ']&gt;</span>'));
		} # <fichier.rtf>
		else {
			if (preg_match(',\.([^.]+)$,', $url, $regs)
			  and file_exists($f = _DIR_IMG . $regs[1] . '/' . $url)) {
				$html = PtoBR(propre("<span class='ressource spip_in'>&lt;[" . $url . '->' . $f . ']&gt;</span>'));
			} else {
				$html = PtoBR(propre("<span class='ressource'>&lt;" . $url . '&gt;</span>'));
			}
		}
	}

	return '<html>' . $html . '</html>';
}
