<?php


function pp_codes_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans les 2 barres de SPIP
	foreach (array('edition' => true,'forum' => false) as $nom=>$visible) {
		$barre = &$barres[$nom];
		$barre->ajouterApres('grpCaracteres', array(
					"id" => "sepCode",
					"separator" => "---------------",
					"display"   => $visible,
		));
		$barre->ajouterApres('sepCode', array(
					// groupe code et bouton <code>
					"id"          => 'grpCode',
					"name"        => _T('pp_codes:outil_inserer_code'),
					"className"   => 'outil_code',
					"openWith" => "<code>",
					"closeWith" => "</code>",		
					"display"     => $visible,
					"dropMenu"    => array(
						// bouton <cadre>
						array(
							"id"          => 'cadre_spip',
							"name"        => _T('pp_codes:outil_inserer_cadre_spip'),
							"className"   => 'outil_cadre_spip', 
							"openWith" => "<cadre class='spip'>\n",
							"closeWith" => "\n</cadre>",
							"display"     => $visible,
						),
						// bouton <cadre>
						array(
							"id"          => 'cadre_php',
							"name"        => _T('pp_codes:outil_inserer_cadre_php'),
							"className"   => 'outil_cadre_php', 
							"openWith" => "<cadre class='php'>\n",
							"closeWith" => "\n</cadre>",
							"display"     => $visible,
						),
						// bouton <pre>
						array(
							"id"          => 'pre',
							"name"        => _T('pp_codes:outil_inserer_pre'),
							"className"   => 'outil_pre', 
							"openWith" => "<pre>",
							"closeWith" => "</pre>",
							"display"     => $visible,
						),
						// bouton <var>
						array(
							"id"          => 'var',
							"name"        => _T('pp_codes:outil_inserer_var'),
							"className"   => 'outil_var', 
							"openWith" => "<var>",
							"closeWith" => "</var>",
							"display"     => $visible,
						),
						// bouton <samp>
						array(
							"id"          => 'samp',
							"name"        => _T('pp_codes:outil_inserer_samp'),
							"className"   => 'outil_samp',
							"openWith" => "<samp>",
							"closeWith" => "</samp>",
							"display"     => $visible,
						),
						// bouton <kbd>
						array(
							"id"          => 'kbd',
							"name"        => _T('pp_codes:outil_inserer_kbd'),
							"className"   => 'outil_kbd',
							"openWith" => "<kbd>",
							"closeWith" => "</kbd>",
							"display"     => $visible,
						),
						// Lien vers Trac 
						// trop specifique a SPIP pour etre affiche par defaut...
						array(
							"id"          => 'lienTrac',
							"name"        => _T('pp_codes:outil_inserer_lien_trac'),
							"className"   => 'outil_lien_trac',
							"openWith" => "[?",
							"closeWith" => "#trac]",
							"display"     => false,
						),
					),
				));
	}
	return $barres;
}


function pp_codes_porte_plume_barre_charger($barres){
	// en fonction de la config, afficher ou non sur barre d'edition et forum
	// par defaut : edition = oui, forum = non
	// ce que donne deja pre_charger par ailleurs
	$pp = @unserialize($GLOBALS['meta']['porte_plume']);
	if (isset($pp['codes'])) {
		$activer = array();
		if ($pp['codes']['activer_barre_edition'] == 'on') {$activer[] = 'edition';}
		if ($pp['codes']['activer_barre_forum'] == 'on') {$activer[] = 'forum';}
		foreach ($activer as $nom) {
			if (isset($barres[$nom])) {
				$barre = &$barres[$nom];
				$barre->afficher(array('sepCode', 'grpCode', 'cadre_spip', 'cadre_php', 'pre', 'var', 'samp', 'kbd'));
			}
		}
	}
	return $barres;
}


function pp_codes_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_code'=>'tag.png',
		'outil_cadre'=>'page_white_code.png',
		'outil_cadre_spip'=>'cadre_spip.png',
		'outil_cadre_php'=>'page_white_php.png',
		'outil_pre'=>'page_white_code_red.png',
		'outil_samp'=>'application_osx_terminal.png',
		'outil_var'=>'tag.png',
		'outil_kbd'=>'keyboard.png',
		'outil_lien_trac'=>'trac_logo16.png',
	));
}
?>
