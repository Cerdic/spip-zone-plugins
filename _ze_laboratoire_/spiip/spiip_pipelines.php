<?php

function spiip_header_prive($flux){
	global $spip_lang, $couleur_claire, $couleur_foncee;
	$args = "couleur_claire=" .
		substr($couleur_claire,1) .
		'&couleur_foncee=' .
		substr($couleur_foncee,1) .
		'&ltr=' . 
		$GLOBALS['spip_lang_left'];
		
	if (_request('jqdb')!==NULL)
		$flux = '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>'.$flux;
	else
		$flux = '<script src="'.find_in_path('jquery-1.0a.js').'" type="text/javascript"></script>'.$flux;

	$flux .= '<link rel="stylesheet" href="'.generer_url_public('style_spiip_prive',$args).'" type="text/css" media="projection, screen" />';
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
	function spiip_inclure_modele($squelette, $type, $id) {
	static $compteur;


		if (++$compteur>4) return ''; # ne pas boucler indefiniment

		$type = strtolower($type);

		$fond = 'modeles/'.$type;
		if (preg_match(',^([a-z_0-9-]+)([|]|$),i', $squelette, $sub)) {
			if (in_array(strtolower($sub[1]),
			array('left', 'right', 'center')))
				$align = $sub[0];

			$fond = 'modeles/'.$type.'_'.$sub[1];

			if (!find_in_path($fond.'.html')) {
				$fond = 'modeles/'.$type;
				if (!$align)
					$class = $sub[1];
			}
		}

		// en cas d'echec on passe la main au suivant
		if (!find_in_path($fond.'.html'))
			return false;

		include_spip('public/assembler');

		// raccourcis specifiques img, doc, emb
		if (in_array($type, array('img', 'doc', 'emb')))
			$id_type = 'id_document';
		else
			$id_type = 'id_'.$type;

		$contexte = array(
			$id_type => $id,
			'fond' => $fond,
			'lang' => $GLOBALS['spip_lang']
		);
		if ($align)
			$contexte['align'] = $align;

		if ($class)
			$contexte['class'] = $class;

		// cas particulier des parametres :
		// <emb12|autostart=true> ou <doc1|lang=en>
		$contexte = array_merge($contexte,
			creer_contexte_de_modele(explode('|', $squelette)));

#	var_dump($type);
#	var_dump($contexte);

		$page = recuperer_fond($fond, $contexte);

		$compteur--;

		return $page;
	}

	/* static public */ 
	function spiip_traiter_modeles($texte) {

		if (preg_match_all(',<([a-z_-]+)([0-9]+)([|]([^>]+))?'.'>,iS',
		$texte, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $regs) {
				$modele = spiip_inclure_modele($regs[4], $regs[1], $regs[2]);
				if ($modele !== false) {
					$rempl = code_echappement($modele);
					$cherche = $regs[0];

					// XHTML : remplacer par une <div onclick> le lien
					// dans le cas [<docXX>->lien] ; sachant qu'il n'existe
					// pas de bonne solution en XHTML pour produire un lien
					// sur une div (!!)...
					if (substr($rempl, 0, 5) == '<div '
					AND preg_match(
					',(<a [^>]+>)\s*'.preg_quote($regs[0]).'\s*</a>,Uims',
					$texte, $r)) {
						$lien = extraire_attribut($r[1], 'href');
						$cherche = $r[0];
						$rempl = '<div style="cursor:pointer;cursor:hand;" '
						.'onclick="document.location=\''.$lien
						.'\'"'
##						.' href="'.$lien.'"' # href deviendra legal en XHTML2
						.'>'
						.$rempl
						.'</div>';
					}

					$texte = str_replace($cherche, $rempl, $texte);
				}
			}
		}

		return $texte;
	}


?>
