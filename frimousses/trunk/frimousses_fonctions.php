<?php

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/**
 * Lister les smileys disponibles, et pour chaque les raccourcis ascii usuels
 * @return array
 */
function frimousses_liste_smileys($hexa=null){

	/*
	 * Listes des hexa emoji a associer aux smileys
	 * http://www.unicode.org/emoji/charts/full-emoji-list.html
	 */
	$les_smileys = Array
	(
		'1F604' => Array
		(
			':-))',
			':-D',
			':))'
		)
	,
		'1F617' => Array
		(
			':-)*',
			':-*'
		)
	,
		'1F642' => Array
		(
			':-)',
			':O)',
			':o)',
			':0)'
		)
	,
		'1F607' => Array
		(
			'o:)',
			'O:)',
			'0:)'
		)
	,
		'1F633' => Array
		(
			'%-)',
			'8-)'
		)
	,
		'1F609' => Array
		(
			';-)'
		)
	,
		'1F641' => Array
		(
			':-((',
			':-('
		)
	,
		'1F621' => Array
		(
			':-O',
			':-0'
		)
	,
		'1F610' => Array
		(
			':-|',
			'|-)'
		)
	,
		'1F615' => Array
		(
			':-/'
		)
	,
		'1F61B' => Array
		(
			':-p',
			':-P'
		)
	,
		'1F622' => Array
		(
			':\'-(',
			':\'(',
			':~('
		)
	,
		'1F60A' => Array
		(
			':-...',
			':...',
			':-…',
			':…',
			':-&hellip;',
			':&hellip;',
			':-..',
			':..',
			':-.',
			':.'
		)
	,
		'1F910' => Array
		(
			':-x'
		)
	,
		'1F60E' => Array
		(
			'B-)'
		)
	,
		'1F634' => Array
		(
			':-@'
		)
	,
		'1F911' => Array
		(
			':-$'
		)
	,
		'1F644' => Array
		(
			':-!'
		)

	);

	if($hexa) {
		return (isset($les_smileys[$hexa]) ? $les_smileys[$hexa] : null);
	}

	return $les_smileys;
}

/**
 * Filtre SMILEYS - 19 Dec. 2004
 *
 * pour toute suggestion, remarque, proposition d'ajout d'un
 * smileys, etc ; reportez vous au forum de l'article :
 * @param string $chaine
 * @return string
 */
function frimousses_pre_typo($chaine){
	if (strpos($chaine, ':')===false && strpos($chaine, ')')===false){
		return $chaine;
	}

	static $replace1 = null;
	static $replace2 = null;
	if (!$replace1 OR !$replace2){
		foreach (frimousses_liste_smileys() as $hexa => $smileys){
			$title = _T('smileys:' . $smileys[0]);
			$r = frimousse_affiche_smiley($hexa, $title);
			// 4 regexp simples qui accrochent sur le premier char
			// sont plus rapides qu'une regexp complexe qui oblige a des retour en arriere
			foreach ($smileys as $index => $smiley){
				$smiley = preg_quote($smiley, '/');
				$replace1['/^' . $smiley . '/imsS'] = "<html>$r</html>";
				$replace1['/\s' . $smiley . '/imsS'] = "<html>&nbsp;$r</html>";
				$replace2['/^&nbsp;' . $smiley . '/imsS'] = "<html>$r</html>";
				$replace2['/&nbsp;' . $smiley . '/imsS'] = "<html>&nbsp;$r</html>";
			}
		}
	}

	$chaine = preg_replace(array_keys($replace1), array_values($replace1), $chaine);
	if (strpos($chaine, '&nbsp')!==false){
		$chaine = preg_replace(array_keys($replace2), array_values($replace2), $chaine);
	}

	return $chaine;
}

/**
 * Lister les smileys dispos (outil developpeur)
 * @param $p
 * @return mixed
 */
function balise_SMILEY_DISPO($p){

	$p->code = '"<ul class=\"listes-items smileys\">';
	$frimousses = frimousses_liste_smileys();

	foreach ($frimousses as $hexa => $smileys){
		$title = _T('smileys:' . $smileys[0]);
		$smileys = "<span class=\\\"smiley_nom_variante\\\">" . implode("</span> <span class=\\\"smiley_nom_variante\\\">", $smileys) . "</span>";
		$p->code .= "<li class=\\\"item smiley\\\"><span class=\\\"smiley_nom\\\">$hexa $smileys</span> " . str_replace('"',"\\\"", frimousse_affiche_smiley($hexa, $title)) . " <span class=\\\"smiley_alt\\\" />$title</span></li>\n";
	}
	$p->code .= '</ul>"';
	$p->type = 'html';

	return $p;
}

/**
 * Genere le code html d'un smiley a partir de son hexa et title
 * @param $hexa
 * @param null|string $title
 * @return string
 */
function frimousse_affiche_smiley($hexa, $title=null) {

	if (is_null($title)) {
		$title = '';
		if ($smileys = frimousses_liste_smileys($hexa)) {
			$title = $smileys[0];
		}
	}

	$title = attribut_html($title);
	return '<b class="smiley" title="' . $title . '" class="smiley">'.frimousses_hexaToString($hexa).'</b>';
}

/**
 * Converti le code hexa de l'emoji en chaine utf8/utf16
 * @param string $hexa
 * @return string
 */
function frimousses_hexaToString($hexa) {
	//return eval("\\u"."{".$hexa."}");
	$em = hexdec($hexa);
	if ($em>0x10000){
		$first = (($em-0x10000) >> 10)+0xD800;
		$second = (($em-0x10000)%0x400)+0xDC00;
		return json_decode('"' . sprintf("\\u%X\\u%X", $first, $second) . '"');
	} else {
		return json_decode('"' . sprintf("\\u%X", $em) . '"');
	}
}


/**
 * Pipeline pre_charger du porteplume
 * @param $barres
 * @return mixed
 */
function frimousses_porte_plume_barre_pre_charger($barres){
	// Commun aux 2 barres
	$frimousses = frimousses_liste_smileys();
	$outil_frimousses = array();
	$compteur = 0;
	foreach ($frimousses as $hexa => $smiley){
		$outil_frimousses[] = array(
			"id" => "barre_frimousse$compteur",
			"name" => _T('smileys:' . $smiley[0]) . ' ' . implode(' ', $smiley),
			"className" => "outil_frimousses$compteur",
			"replaceWith" => ' ' . $smiley[0] . ' ',
			"display" => true,
		);
		$compteur++;
	}

	// On rajoute les boutons aussi bien pour l'édition du contenu que pour les forums
	foreach (array('edition', 'forum') as $nom){
		$barre = &$barres[$nom];

		$smiley_par_defaut = ':-)';
		$barre->ajouterApres('grpCaracteres', array(
			"id" => 'barre_frimousses',
			"name" => _T("smileys:$smiley_par_defaut") . ' ' . $smiley_par_defaut,
			"className" => "outil_frimousses",
			"replaceWith" => " $smiley_par_defaut ",
			"display" => true,
			"dropMenu" => $outil_frimousses,
		));
	}
	return $barres;
}

/**
 * Piepline lien_classe_vers_icone du porte plume
 * @param $flux
 * @return array
 */
function frimousses_porte_plume_lien_classe_vers_icone($flux){
	$fimousse_img= find_in_path('img/frimousses-72.png');
	$svg = find_in_path("img/frimousses.svg");
	$svg = file_get_contents($svg);
	$svg = base64_encode($svg);
	$outils_frimousses["outil_frimousses"] = array($fimousse_img, "0;background-image:url('data:image/svg+xml;base64,{$svg}');background-size:contain;margin:-1px;width:18px!important;height:18px!important;");

	$frimousses = array_keys(frimousses_liste_smileys());
	foreach ($frimousses as $compteur => $hexa){
		$svg = find_in_path("frimousses/".strtolower($hexa).".svg");
		if ($svg) {
			$svg = file_get_contents($svg);
			$svg = base64_encode($svg);
			$outils_frimousses["outil_frimousses$compteur"] = array($fimousse_img, "0;background-image:url('data:image/svg+xml;base64,{$svg}');background-size:contain;margin:-4px;width:20px!important;height:20px!important;");
		}
	}

	return array_merge($flux, $outils_frimousses);
}

/**
 * Pipeline barre_charger du porte_plumme
 * @param $barres
 * @return mixed
 */
function frimousses_porte_plume_barre_charger($barres){
	if (isset($barres['forum'])){
		$barre = &$barres['forum'];
		$barre->afficher('barre_frimousses', 'barre_frimousse0', 'barre_frimousse1');
	}
	return $barres;
}
