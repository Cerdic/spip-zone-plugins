<?php


function pp_codes_porte_plume_barre_pre_charger($flux){

	$barre = &$flux['spip'];
	$barre->ajouterApres('grpCaracteres', array(
				"id" => "sepCode",
				"separator" => "---------------",
				"display"   => true,
	));
	$barre->ajouterApres('sepCode', array(
				// groupe code et bouton <code>
				"id"          => 'grpCode',
				"name"        => _T('pp_code:outil_inserer_code'),
				"className"   => 'outil_code',
				"openWith" => "<code>",
				"closeWith" => "</code>",		
				"display"     => true,
				"dropMenu"    => array(
					// bouton <cadre>
					array(
						"id"          => 'cadre_spip',
						"name"        => _T('pp_code:outil_inserer_cadre_spip'),
						"className"   => 'outil_cadre', 
						"openWith" => "<cadre class='spip'>\n",
						"closeWith" => "\n</cadre>",
						"display"     => true,
					),
					// bouton <cadre>
					array(
						"id"          => 'cadre_php',
						"name"        => _T('pp_code:outil_inserer_cadre_php'),
						"className"   => 'outil_cadre', 
						"openWith" => "<cadre class='php'>\n",
						"closeWith" => "\n</cadre>",
						"display"     => true,
					),
					// bouton <pre>
					array(
						"id"          => 'pre',
						"name"        => _T('pp_code:outil_inserer_pre'),
						"className"   => 'outil_pre', 
						"openWith" => "<pre>",
						"closeWith" => "</pre>",
						"display"     => true,
					),
					// bouton <var>
					array(
						"id"          => 'var',
						"name"        => _T('pp_code:outil_inserer_var'),
						"className"   => 'outil_var', 
						"openWith" => "<var>",
						"closeWith" => "</var>",
						"display"     => true,
					),
					// bouton <samp>
					array(
						"id"          => 'samp',
						"name"        => _T('pp_code:outil_inserer_samp'),
						"className"   => 'outil_samp',
						"openWith" => "<samp>",
						"closeWith" => "</samp>",
						"display"     => true,
					),
					// bouton <kbd>
					array(
						"id"          => 'kbd',
						"name"        => _T('pp_code:outil_inserer_kbd'),
						"className"   => 'outil_kbd',
						"openWith" => "<kbd>",
						"closeWith" => "</kbd>",
						"display"     => true,
					),
					// Lien vers Trac 
					array(
						"id"          => 'lienTrac',
						"name"        => _T('pp_code:outil_inserer_lien_trac'),
						"className"   => 'outil_lien_trac',
						"replaceWith" => "function(h){return outil_inserer_lien_trac(h);}", 
						"display"     => true,
					),
				),
			));
	$barre->ajouterFonction("
				// cree un lien [chemin/fichier.php->http://trac.rezo.net/trac/spip/browser/branches/spip-2.0/chemin/fichier.php]
				function outil_inserer_lien_trac(h){
					if (s = h.selection) {
						s = '[' + s + '->' + 'http://trac.rezo.net/trac/spip/browser/branches/spip-2.0/' + s + ']';
					}
					return s;
				}
	");
	return $flux;
}

function pp_codes_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_code'=>'page_white_php.png',
		'outil_cadre'=>'page_white_code.png',
		'outil_pre'=>'page_white_code_red.png',
		'outil_samp'=>'application_osx_terminal.png',
		'outil_var'=>'tag.png',
		'outil_kbd'=>'keyboard.png',
		'outil_lien_trac'=>'trac_logo16.png',
	));
}
?>
