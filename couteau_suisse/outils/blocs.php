<?php

/*
   Cet outil 'blocs' permet aux redacteurs d'un site spip d'inclure des blocs visibles ou invisibles dans leurs textes
   balises : <bloc></bloc> ou <invisible></invisible>, et <visible></visible>
   le titre est obtenu en sautant deux lignes a l'interieur du bloc
   Attention : seules les balises en minuscules sont reconnues.
*/

@define('_BLOC_TITRE_H', 'h4');

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function blocs_raccourcis() {
	return _T('couteauprive:blocs:aide');
}

function blocs_callback($matches) {
	list($titre, $corps) = preg_split(',(\n\n|\r\n\r\n|\r\r),', trim($matches[3]), 2);
	// pas de corps !
	if(!strlen($corps=trim($corps))) {
		$corps = $titre;
		$titre = preg_replace(',[\n\r]+,s', ' ', couper(propre($titre), 30));
	}
	// pas d'intertitre !
	$titre = preg_replace(',^{{{(.*)}}}$,', '$1', trim($titre));
	if(!strlen($titre)) $titre = '???';
	// un resume facultatif
	if(preg_match(',<resume>(.*)</resume>\s?(.*)$,ms', $corps, $res))
		{ $corps = $res[2]; $res = $res[1]; } else $res = '';
	// types de blocs : bloc|invisible|visible
	if ($matches[1]=='visible' || defined('_CS_PRINT')) {
		$h = $d = '';
		$r = ' blocs_invisible blocs_slide';
	} else {
		$h = ' blocs_replie';
		$d = ' blocs_invisible blocs_slide';
		$r = '';
	}

	// blocs numerotes
	$b = strlen($matches[2])?" cs_bloc$matches[2]":''; 
	return "<div class='cs_blocs$b'><"._BLOC_TITRE_H." class='blocs_titre$h blocs_click'><a href='javascript:;'>$titre</a></"._BLOC_TITRE_H.">"
		.(strlen($res)?"<div class='blocs_resume$r'>\n$res\n</div>":"")
		."<div class='blocs_destination$d'>\n".blocs_rempl($corps)."\n</div></div>";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function blocs_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// balises blocs|visible|invisible : il faut un callback pour analyser l'interieur du texte
	return preg_replace_callback(',<(bloc#?|visible#?|invisible#?|blocintertitre#?)([0-9]*)>(.*?)</\1\2>,ms', 'blocs_callback', $texte);
}

// fonction pipeline
function blocs_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// on remplace apres echappement
	return cs_echappe_balises('', 'blocs_rempl', $texte);
}

// 2 fonctions pour le plugin Porte Plume, s'il est present (SPIP>=2.0)
function blocs_CS_pre_charger($flux) {
	$r = array(array(
		"id" => 'blocs_bloc',
		"name" => _T('couteau:pp_blocs_bloc'),
		"className" => 'blocs_bloc',
		"replaceWith" => "\n<bloc>"._T('couteau:pp_un_titre')."\n\n"._T('couteau:pp_votre_texte')."\n</bloc>\n",
		"display" => true), array(
		"id" => 'blocs_visible',
		"name" => _T('couteau:pp_blocs_visible'),
		"className" => 'blocs_visible',
		"replaceWith" => "\n<visible>"._T('couteau:pp_un_titre')."\n\n"._T('couteau:pp_votre_texte')."\n</visible>\n",
		"display" => true));
	foreach(cs_pp_liste_barres('blocs') as $b)
		$flux[$b] = isset($flux[$b])?array_merge($flux[$b], $r):$r;
	return $flux;
}
function blocs_PP_icones($flux) {
	$flux['blocs_bloc'] = 'bloc_invisible.png';
	$flux['blocs_visible'] = 'bloc_visible.png';
	return $flux;
}

?>