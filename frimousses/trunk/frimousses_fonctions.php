<?php

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/**
 * Lister les smileys disponibles, et pour chaque les raccourcis ascii usuels
 * @return array
 */
function frimousses_liste_smileys(){

	/*Listes des images a associer aux smileys*/

	$les_smileys = Array
	(
		'smiley-lol-16.png' => Array
		(
			':-))',
			':-D',
			':))'
		)
	,
		'smiley-kiss-16.png' => Array
		(
			':-)*',
			':-*'
		)
	,
		'smiley-16.png' => Array
		(
			':-)',
			':O)',
			':o)',
			':0)'
		)
	,
		'smiley-angel-16.png' => Array
		(
			'o:)',
			'O:)',
			'0:)'
		)
	,
		'smiley-eek-16.png' => Array
		(
			'%-)',
			'8-)'
		)
	,
		'smiley-wink-16.png' => Array
		(
			';-)'
		)
	,
		'smiley-sad-16.png' => Array
		(
			':-((',
			':-('
		)
	,
		'smiley-yell-16.png' => Array
		(
			':-O',
			':-0'
		)
	,
		'smiley-neutral-16.png' => Array
		(
			':-|',
			'|-)'
		)
	,
		'smiley-confuse-16.png' => Array
		(
			':-/'
		)
	,
		'smiley-razz-16.png' => Array
		(
			':-p',
			':-P'
		)
	,
		'smiley-cry-16.png' => Array
		(
			':\'-(',
			':\'(',
			':~('
		)
	,
		'smiley-red-16.png' => Array
		(
			':-...',
			':...',
			':-..',
			':..',
			':-.',
			':.'
		)
	,
		'smiley-zipper-16.png' => Array
		(
			':-x'
		)
	,
		'smiley-cool-16.png' => Array
		(
			'B-)'
		)
	,
		'smiley-sleep-16.png' => Array
		(
			':-@'
		)
	,
		'smiley-money-16.png' => Array
		(
			':-$'
		)
	,
		'smiley-roll-16.png' => Array
		(
			':-!'
		)

	);

	return $les_smileys;
}

//
//
//
//
// http://www.spip-contrib.net/Smileys-III-Un-point-d-entree-pour

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
		foreach (frimousses_liste_smileys() as $file => $smileys){
			$alt = _T('smileys:' . $smileys[0]);
			$alt = attribut_html($alt);
			$r = "<img src=\"" . find_in_path('frimousses/' . $file) . '" width="16" height="16" alt="' . $alt . '" title="' . $alt . '" class="smiley" />';
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

	foreach ($frimousses as $file => $smiley){
		$alt = _T('smileys:' . $smiley[0]);
		$alt = attribut_html($alt);
		$smiley = "<span class=\\\"smiley_nom_variante\\\">" . implode("</span> <span class=\\\"smiley_nom_variante\\\">", $smiley) . "</span>";
		$p->code .= "<li class=\\\"item smiley\\\"><span class=\\\"smiley_nom\\\">$smiley</span> <img  class=\\\"smiley_image\\\" src=\\\"" . find_in_path("frimousses/$file") . "\\\" width=\\\"16\\\" height=\\\"16\\\" alt=\\\"$alt\\\" title=\\\"$alt\\\"/> <span class=\\\"smiley_alt\\\" />$alt</span></li>\n";
	}
	$p->code .= '</ul>"';
	$p->type = 'html';

	return $p;
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
	foreach ($frimousses as $file => $smiley){
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

		$module_barre = "barre_outils";
		if (intval($GLOBALS['spip_version_branche'])>2){
			$module_barre = "barreoutils";
		}

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
	$outils_frimousses["outil_frimousses"] = array(find_in_path('img/frimousses-16.png'), '0');

	$frimousses = array_keys(frimousses_liste_smileys());
	foreach ($frimousses as $compteur => $file){
		$outils_frimousses["outil_frimousses$compteur"] = array(find_in_path('frimousses/' . $file), '0');
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
