<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function pp_codes_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans les 2 barres de SPIP
	foreach (array('edition','forum') as $nom) {
		$barre = &$barres[$nom];

		$barre->ajouterPlusieursApres('cadre', array(
			// bouton <cadre spip>
			array(
				"id"          => 'cadre_spip',
				"name"        => _T('pp_codes:outil_inserer_cadre_spip'),
				"className"   => 'outil_cadre_spip', 
				"openWith" => "<cadre class='spip'>\n",
				"closeWith" => "\n</cadre>",
				"display"     => false,
			), 
			// bouton <cadre php>
			array(
				"id"          => 'cadre_php',
				"name"        => _T('pp_codes:outil_inserer_cadre_php'),
				"className"   => 'outil_cadre_php', 
				"openWith" => "<cadre class='php'>\n",
				"closeWith" => "\n</cadre>",
				"display"     => false,
			),
			// bouton <cadre html>				
			array(
				"id"			=> 'cadre_html',
				"name"			=> _T('pp_codes:outil_inserer_cadre_html'),
				"className"		=> "outil_cadre_html",
				"openWith"		=> "<cadre class='html4strict'>\n",
				"closeWith"		=> "\n</cadre>",
				"display"		=> false,
			),
			// bouton <cadre css>
			array(
				"id"          => 'cadre_css',
				"name"        => _T('pp_codes:outil_inserer_cadre_css'),
				"className"   => 'outil_cadre_css', 
				"openWith" => "<cadre class='css'>\n",
				"closeWith" => "\n</cadre>",
				"display"     => false,
			),
			// bouton <cadre xml>
			 array(
				"id"          => 'cadre_xml',
				"name"        => _T('pp_codes:outil_inserer_cadre_xml'),
				"className"   => 'outil_cadre_xml', 
				"openWith" => "<cadre class='xml'>\n",
				"closeWith" => "\n</cadre>",
				"display"     => false,
			),
			// bouton <pre>
			array(
				"id"          => 'pre',
				"name"        => _T('pp_codes:outil_inserer_pre'),
				"className"   => 'outil_pre', 
				"openWith" => "<pre>",
				"closeWith" => "</pre>",
				"display"     => false,
			),
			// bouton <var>
			array(
				"id"          => 'var',
				"name"        => _T('pp_codes:outil_inserer_var'),
				"className"   => 'outil_var', 
				"openWith" => "<var>",
				"closeWith" => "</var>",
				"display"     => false,
			),
			// bouton <samp>
			array(
				"id"          => 'samp',
				"name"        => _T('pp_codes:outil_inserer_samp'),
				"className"   => 'outil_samp',
				"openWith" => "<samp>",
				"closeWith" => "</samp>",
				"display"     => false,
			),
			// bouton <kbd>
			array(
				"id"          => 'kbd',
				"name"        => _T('pp_codes:outil_inserer_kbd'),
				"className"   => 'outil_kbd',
				"openWith" => "<kbd>",
				"closeWith" => "</kbd>",
				"display"     => false,
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
		));
	}
	return $barres;
}


function pp_codes_porte_plume_barre_charger($barres){
	// en fonction de la config, afficher ou non sur barre d'edition et forum
	// par defaut : edition = oui, forum = non
	// ce que donne deja pre_charger par ailleurs
	$pp = @unserialize($GLOBALS['meta']['porte_plume']);

	if (isset($pp['codes']) and $codes = $pp['codes']) {
		$activer = array();
		
		if ($codes['activer_barre_edition'] == 'on') {$activer[] = 'edition';}
		if ($codes['activer_barre_forum'] == 'on') {$activer[] = 'forum';}

		foreach ($activer as $nom) {
			if (isset($barres[$nom])) {
				$barre = &$barres[$nom];
				
				$outils_actifs = (isset($codes['outils_actifs']) and is_array($codes['outils_actifs'])) ? $codes['outils_actifs'] : array();
				if ($outils_actifs) {
					$barre->afficher($outils_actifs);
					$barre->afficher(array('sepCode', 'grpCode'));
				}
			}
		}
	}
	return $barres;
}


function pp_codes_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_cadre_spip'=>'cadre_spip.png',
		'outil_cadre_php'=>'page_white_php.png',
		'outil_cadre_html'=>'html.png',
		'outil_cadre_xml'=>'page-xml.png',
		'outil_cadre_css'=>'css.png',
		'outil_pre'=>'page_white_code_red.png',
		'outil_samp'=>'application_osx_terminal.png',
		'outil_var'=>'tag.png',
		'outil_kbd'=>'keyboard.png',
		'outil_lien_trac'=>'trac_logo16.png',
	));
}
?>
