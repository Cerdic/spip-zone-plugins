<?php
# a modifier pour #formulaire_xx
function cfg_exemples_affiche_milieu($flux) {
	if ($flux['args']['exec']=='articles'){
		$flux['data'] .= cfg_ajoute_formulaire('exemple_article_prive', $flux['args']['id_article'],true);
	}
	return $flux;
}

/* ajouter la balise #formulaire_xx */
function cfg_ajoute_formulaire($nom,$id='', $ajax=false){
	include_spip('inc/flock');
	sous_repertoire(_DIR_CACHE,'cfg');
	$base = _DIR_CACHE . 'cfg/formulaire_'.$nom;

	if (!file_exists($f = $base . ".html")
	OR  ($GLOBALS['var_mode']=='recalcul')) {
		$c = '#FORMULAIRE_'.strtoupper($nom).'{#ENV{cfg_id}}';
		if ($ajax) $c =  "<div class='ajax'>$c</div>";
		ecrire_fichier($f, $c);
	}

	include_spip('public/assembler');
	return recuperer_fond($base, array('cfg_id' => $id));
}

?>
