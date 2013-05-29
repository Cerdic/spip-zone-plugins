<?php

function inc_lien_court($url) {
	$long_url = defined('_MAX_LONG_URL') ? _MAX_LONG_URL : 40;
	$coupe_url = defined('_MAX_COUPE_URL') ? _MAX_COUPE_URL : 35;
	if (mb_strlen($url)>$long_url) {
		$url = lien_court($url, $coupe_url, true);
	}
	return $url;
}

// $masquer = false raccourcis les URL "en dur"
// $masquer = true se contente d'integrer les parties a masquer dans des span
function lien_court($url, $longueur = 50, $masquer = true) {
	//$masquer = false;

	$intitule = trim($url);
	$intitule = preg_replace(",/$,", "", $intitule);

	$intitule = explode("/", $intitule);
	
	
	// $test va servir uniquement a calculer ce qu'on garde
	$test = $intitule;
	$total = count($test);
	
	$garder = array();
	$garder[2] = preg_replace(",^www\.,", "", $test[2]);
		
	for ($i = $total - 1; $i > 2 && strlen(join("/",$garder)) < ($longueur - 3) ; $i--) {
		$garder[$i] = $test[$i];
	}
		
	if (mb_strlen(join("/",$garder),  "utf-8") > $longueur - 3) {
		if ($i+1 != $total-1) $garder[$i+1] = "";
		$diff = mb_strlen(join("/",$garder),  "utf-8") - $longueur;
		// penser aux 3 petits points
		$diff = $diff + 3;
	}
	
	if ($masquer) $ret = "<span class='lien_protocol'>".$intitule[0]."//</span>";
	for ($i = 2; $i < $total; $i++) {
		$t = $intitule[$i];
		
		if ($i == 2) {
			$slash = "";
			if ($total > 3) $slash = "/";
			
			
			if ($masquer) {
				$t = preg_replace(",^www\.,", "<span class='lien_www'>www.</span>", $t);
				$t = "<span class='lien_host'>$t</span>";
				// Si on a raccourci l'URL, ajouter une classe
				if (mb_strlen($garder[3],  "utf-8") < 1 && $total > 3) {
					$t = "<span class='lien_racine lien_raccourci'>$t$slash</span>";
				} else {
					$t = "<span class='lien_racine'>$t$slash</span>";
				}
			}
			else  {
				$t = "<b>".preg_replace(",^www\.,", "", $t)."</b>$slash";
			}
		} else if ($i == $total-1) {
			if (mb_strlen($garder[$i],  "utf-8") > 1) {
				if ($diff > 0) {
					$long_t = mb_strlen($t,  "utf-8") - $diff;
					$debut = mb_substr($t, 0, $long_t,  "utf-8");
					$fin = mb_substr($t, $long_t, $longueur,  "utf-8");
					if ($masquer) $t = "<span class='lien_fin_coupee'>$debut</span><span class='lien_fin_cachee'>$fin</span>";
					else  $t = "$debut…";
				} else {
					// pas de decoupe
				}
				if ($masquer) $t = "<span class='lien_fin'>$t</span>";
			} else {
				// Quand racine tres longue,
				// pas de chemin final du tout.
				if ($masquer) $t = "<span class='lien_fin_coupee'></span><span class='lien_fin_cachee'>$t</span>";
				else  $t = "";
			}
		} else {
			if (mb_strlen($garder[$i], "utf-8") > 1) {
				$t = $t."/";
			} else {
				// Lien cache
				if ($masquer) $t = "<span class='lien_off'>$t/</span>";
				else if ($i == 3) $t = ".../";
				else $t = "";
			}
		}
		
		$ret .= $t;
		
	}

	return "<span class='lien_court'>$ret</span>";	

}

function lien_court_insert_head($flux) {
	$fichier = find_in_path('lien_court.css');
	if ($fichier) $ret = "<link href='$fichier' rel='stylesheet' type=\"text/css\" />";
	
	return $flux.$ret;
}

?>
