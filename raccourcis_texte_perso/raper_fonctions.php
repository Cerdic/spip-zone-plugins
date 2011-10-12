<?php

// raper_fonctions.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// Nota: n'est plus utilisé !

// la balise du RaPer

// Ce script ne sert plus qu'à illustrer la balise #T
// Elle n'est plus activée dans Raper v.1


if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/raper_api_globales');
raper_log("lecture fonctions");
function balise_T ($p) {
	if(
		($t = (isset($p->param[0][1][0])) 
			? $p->param[0][1][0]
			: null)
		&& isset($t->type)
		&& ($t->type == 'texte')
		&& isset($t->texte)
	) {
		$t = $t->texte;
	}
	raper_log("balise_T: ".$t);
	$p->code = "calcul_T($t)";
	$p->statut = 'php';
	return($p);
}

function calcul_T ($text) {
	
	global $spip_lang;
	raper_log("calcul_T " . $text);
	$prefs = raper_lire_preferences($prefs_modifiees);
	$result = false;
	
	if(!empty($text)) {
		$result =
			(isset($prefs['raccourcis'][$text]))
			? extraire_multi($prefs['raccourcis'][$text])
			// si pas dans le Raper, prendre public
			: _T('public:'.$text)
			;
	}
	return($result);
}


?>