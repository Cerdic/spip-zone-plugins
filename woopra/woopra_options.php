<?php

// Recalculer le cache si la config du site change
$GLOBALS['marqueur'] .= ":".md5($GLOBALS['meta']['woopra']); // Sur un conseil de Cedric : http://permalink.gmane.org/gmane.comp.web.spip.zone/6258

function woopra_affichage_final($texte){
	$wroopa_id = lire_config('woopra/woopra_id','');
	if ($wroopa_id<>'') {
		$code = "<script type=\"text/javascript\">
	var woopra_id = '".$wroopa_id."';
	</script>
	<script src=\"http://static.woopra.com/js/woopra.js\" type=\"text/javascript\"></script>";
		$texte=preg_replace(",(</body>),i","$code\n</body>",$texte);
	}
	return $texte;
}
?>