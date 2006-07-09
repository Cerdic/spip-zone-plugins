<?php
include_spip('base/mnogo_base');

function balise_MNOGO_RESUME_RESULTATS_dist($p) {
	$p->code = "isset(\$GLOBALS['mnogo_resultats_synthese']['MNOGO_RESUME_RESULTATS'])?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_RESUME_RESULTATS']:''";
	return $p;
}
function balise_MNOGO_TOTAL_dist($p) {
	$p->code = "isset(\$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'])?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL']:''";
	return $p;
}

function mnogo_nettoyer_resume($texte){
	return preg_replace('/\s+[a-z]*\s*:\s*0\s*,/is',' ',$texte);
}
?>