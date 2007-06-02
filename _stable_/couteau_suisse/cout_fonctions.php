<?php
// compatibilite SPIP < 1.92
if ($GLOBALS['spip_version_code']<1.92) {
	function interprete_argument_balise($n,$p) {
		if (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n))
			return calculer_liste($p->param[0][$n],	$p->descr, $p->boucles,	$p->id_boucle);	
		else return NULL;
	}
}

// fichier charge a chaque recalcul
	global $cs_metas_pipelines;

	// inclusion des fonctions pre-compilees
	cs_log("appel de cout_fonctions : strlen=" . strlen($cs_metas_pipelines['fonctions']));
	if (!$GLOBALS['cs_fonctions']) include_once(sous_repertoire(_DIR_TMP, "couteau-suisse").'mes_fonctions.php');
	cs_log(' -- appel cout_fonctions achevé... cs_fonctions = ' . intval($GLOBALS['cs_fonctions']));

/*
	if(!$GLOBALS['cs_fonctions']) {
		if (isset($cs_metas_pipelines['fonctions'])) eval($cs_metas_pipelines['fonctions']);
cs_log(' -- appel mes_fonctions achevé par eval()... cs_fonctions = '.intval($GLOBALS['cs_fonctions']));
	}
*/
?>