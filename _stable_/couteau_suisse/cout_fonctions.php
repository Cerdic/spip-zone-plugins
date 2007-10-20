<?php
// Ce fichier est charge a chaque recalcul //

// attention, ici il se peut que le plugin ne soit pas initialise (cas des .js/.css par exemple)
// et donc, pas de fonction cs_log !!
if(defined('_LOG_CS')) spip_log('COUTEAU-SUISSE. appel de cout_fonctions : strlen=' . strlen($cs_metas_pipelines['fonctions']));

// plugin initialise ?
if($GLOBALS['cs_options']) {

	// compatibilite SPIP < 1.92
	if ($GLOBALS['spip_version_code']<1.92) {
		function interprete_argument_balise($n,$p) {
			if (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n))
				return calculer_liste($p->param[0][$n],	$p->descr, $p->boucles,	$p->id_boucle);	
			else return NULL;
		}
		function f_insert_head($texte) {
			if (!$GLOBALS['html']) return $texte;
			//include_spip('public/admin'); // pour strripos
			($pos = stripos($texte, '</head>'))	|| ($pos = stripos($texte, '<body>'))|| ($pos = 0);
			if (false === strpos(substr($texte, 0,$pos), '<!-- insert_head -->')) {
				$insert = "\n".pipeline('insert_head','<!-- f_insert_head -->')."\n";
				$texte = substr_replace($texte, $insert, $pos, 0);
			}
			return $texte;
		}
	}
	
	// liste de filtres qui sert a la balise #INTRODUCTION
	$GLOBALS['cs_introduire'] = array();
	// fonction appelant une liste de fonctions qui permettent de nettoyer un texte original de ses raccourcis indesirables
	function cs_introduire($texte) {
		$liste = array_unique($GLOBALS['cs_introduire']);
		foreach($liste as $f)
			if (function_exists($f)) $texte = $f($texte);
		return $texte;
	}
	
	// Filtre creant un lien <a> sur un texte
	// Exemple d'utilisation : [(#EMAIL*|cs_lien{#NOM})]
	function cs_lien($lien, $texte='') {
		$mem = $GLOBALS['toujours_paragrapher'];
		$GLOBALS['toujours_paragrapher'] = false;
		$lien = propre("[{$texte}->{$lien}]");
		$GLOBALS['toujours_paragrapher'] = $mem;
		return $lien;
	}
	
	// inclusion des fonctions pre-compilees
	if (!$GLOBALS['cs_fonctions']) include_once(_DIR_CS_TMP.'mes_fonctions.php');
	cs_log(' -- appel cout_fonctions achevé... cs_fonctions = ' . intval($GLOBALS['cs_fonctions']));

} else {
	spip_log('COUTEAU-SUISSE.  -- appel de cout_fonctions achevé sans inclusion');
}

?>