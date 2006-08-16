<?php

function spiip_header_prive($flux){
	global $spip_lang, $couleur_claire, $couleur_foncee;
	$args = "couleur_claire=" .
		substr($couleur_claire,1) .
		'&couleur_foncee=' .
		substr($couleur_foncee,1) .
		'&ltr=' . 
		$GLOBALS['spip_lang_left'];
		
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('style_spiip_prive',$args).'" type="text/css" media="projection, screen" />';
	if (_request('jqdb')!==NULL)
		$flux .= '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>';
	else
		$flux .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
	$flux .= "<script type='text/javascript' src='".find_in_path('dist_back/gadget-rubriques.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('dist_back/gadget-rubriques.css')).'" type="text/css" media="projection, screen" />';
	return $flux;
}

function spiip_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('style_spiip_public',$args).'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".find_in_path('dist_back/pagination-ahah.js')."'></script>\n";
	return $flux;
}


/*
 * Code repris du plugin modeles/
 */

	// Calcule le modele et retourne la mini-page ainsi calculee
	function spiip_inclure_modele($squelette, $type, $id, $default) {
	static $compteur;

		if (++$compteur>4) return ''; # ne pas boucler indefiniment

		$fond = 'modeles/'.$type;
		if ($squelette)
			$fond .= "_$squelette";

		// en cas d'echec on passe la main au suivant
		if (!find_in_path($fond.'.html'))
			return $default;

		include_spip('public/assembler');

		if (in_array($type, array('img', 'doc', 'emb')))
			$type = 'document';

		$contexte = array('id_'.$type => $id);

		$page = recuperer_fond($fond, $contexte);

		$compteur--;

		return $page;
	}

	/* static public */ 
	function spiip_traiter_modeles($texte) {

		$regexp = ',<([a-z_-]+)([0-9]+)([|]([a-z_0-9]+))?'.'>,';
		if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
			foreach ($matches as $regs) {
				$modele = spiip_inclure_modele($regs[4], $regs[1], $regs[2], $regs[0]);
				$texte = str_replace($regs[0], code_echappement($modele), $texte);
			}

		return $texte;
	}


?>